<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Services\Sms\HeroHandlerProvider;
use App\Services\Sms\SmsPoolProvider;
use App\Services\Sms\UnlimitedPortalProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VerificationOrderService
{
    public function __construct(
        protected WalletService $wallet,
        protected PricingService $pricing,
        protected SmsPoolProvider $smsPool,
        protected HeroHandlerProvider $heroHandler,
        protected UnlimitedPortalProvider $usa2,
        protected AppConfigService $config,
        protected WebhookDispatchService $webhooks,
    ) {}

    public function completeVerification(Verification $verification, string $code, ?string $fullSms = null): void
    {
        $verification->update([
            'status' => 2,
            'sms' => $code,
            'full_sms' => $fullSms ?? $code,
        ]);
        $this->webhooks->dispatchForVerification($verification->fresh());
    }

    public function cancelAndRefund(Verification $verification): array
    {
        if ((int) $verification->status !== 1) {
            return ['success' => false, 'message' => 'Only pending orders can be cancelled.'];
        }

        if ((int) $verification->type === 4) {
            $seconds = now()->diffInSeconds($verification->created_at);
            if ($seconds < 120) {
                return ['success' => false, 'message' => 'USA2 orders cannot be cancelled within 120 seconds.'];
            }
        }

        $cancelled = $this->cancelAtProvider($verification);
        if (!$cancelled) {
            return ['success' => false, 'message' => 'Provider could not cancel this order yet.'];
        }

        $user = User::find($verification->user_id);
        $ref = 'REFUND-'.$verification->id.'-'.Str::upper(Str::random(6));
        $this->wallet->refund($user, (float) $verification->cost, $ref);
        $verification->update(['status' => 99]);

        return ['success' => true, 'message' => 'Order cancelled and wallet refunded.'];
    }

    protected function cancelAtProvider(Verification $verification): bool
    {
        return match ((int) $verification->type) {
            1 => $this->heroHandler->cancel('usa1', (string) $verification->order_id),
            2, 8 => (bool) ($this->smsPool->cancel((string) $verification->order_id)->success ?? false),
            4 => (bool) ($this->usa2->reject((string) $verification->order_id)->success ?? true),
            9 => $this->heroHandler->cancel('hero', (string) $verification->order_id),
            10 => $this->heroHandler->cancel('sv3', (string) $verification->order_id),
            default => true,
        };
    }

    public function orderSmsPool(User $user, string $country, string $service, float $ngnPrice, float $apiCost): array
    {
        if (!$this->config->getBool('provider_smspool_enabled', true)) {
            return ['success' => false, 'message' => 'Service is not enabled.'];
        }

        if ((float) $user->wallet < $ngnPrice) {
            return ['success' => false, 'message' => 'Insufficient wallet balance.'];
        }

        $result = $this->smsPool->purchase($country, $service);
        if (!($result->success ?? 0)) {
            return ['success' => false, 'message' => $result->message ?? 'Provider error.'];
        }

        if ((float) $user->fresh()->wallet < $ngnPrice) {
            $this->smsPool->cancel((string) $result->order_id);

            return ['success' => false, 'message' => 'Insufficient wallet balance.'];
        }

        $ref = 'SMS-'.Str::upper(Str::random(10));
        if (!$this->wallet->debit($user, $ngnPrice, $ref)) {
            $this->smsPool->cancel((string) $result->order_id);

            return ['success' => false, 'message' => 'Wallet debit failed.'];
        }

        $verification = Verification::create([
            'user_id' => $user->id,
            'phone' => ($result->cc ?? '').($result->phonenumber ?? ''),
            'order_id' => $result->order_id,
            'country' => $result->country ?? $country,
            'service' => $result->service ?? $service,
            'cost' => $ngnPrice,
            'api_cost' => $apiCost,
            'status' => 1,
            'type' => 8,
            'expires_in' => isset($result->expires_in) ? ($result->expires_in / 10 - 20) : null,
            'ip' => request()->ip(),
        ]);

        return ['success' => true, 'verification' => $verification];
    }

    public function orderHeroStyle(User $user, string $provider, string $service, ?string $country, float $ngnPrice, float $apiCostUsd, ?string $maxPrice = null): array
    {
        $enabledKey = $provider === 'hero' ? 'provider_hero_enabled' : 'provider_sv3_enabled';
        if (!$this->config->getBool($enabledKey)) {
            return ['success' => false, 'message' => 'Service is not enabled.'];
        }

        if ((float) $user->wallet < $ngnPrice) {
            return ['success' => false, 'message' => 'Insufficient wallet balance.'];
        }

        $rent = $this->heroHandler->getNumber($provider, $service, $country, $maxPrice);
        if (!$rent['success']) {
            return ['success' => false, 'message' => $rent['error'] ?? 'Could not rent number.'];
        }

        $ref = 'SMS-'.Str::upper(Str::random(10));
        if (!$this->wallet->debit($user, $ngnPrice, $ref)) {
            $this->heroHandler->cancel($provider, (string) $rent['order_id']);

            return ['success' => false, 'message' => 'Wallet debit failed.'];
        }

        $type = $provider === 'hero' ? 9 : 10;
        $verification = Verification::create([
            'user_id' => $user->id,
            'phone' => $rent['phone'],
            'order_id' => $rent['order_id'],
            'country' => $country,
            'service' => $service,
            'cost' => $ngnPrice,
            'api_cost' => $apiCostUsd,
            'status' => 1,
            'type' => $type,
            'ip' => request()->ip(),
        ]);

        return ['success' => true, 'verification' => $verification];
    }

    public function orderUsa2(User $user, string $service, float $ngnPrice, float $apiCost, array $extra = []): array
    {
        if (!$this->config->getBool('provider_usa2_enabled')) {
            return ['success' => false, 'message' => 'Service is not enabled.'];
        }

        if ((float) $user->wallet < $ngnPrice) {
            return ['success' => false, 'message' => 'Insufficient wallet balance.'];
        }

        $result = $this->usa2->requestNumber($service, $extra);
        $orderId = $result->id ?? $result->order_id ?? null;
        $phone = $result->phone ?? $result->number ?? null;

        if (!$orderId || !$phone) {
            return ['success' => false, 'message' => $result->message ?? 'Could not rent USA number.'];
        }

        $ref = 'USA2-'.Str::upper(Str::random(10));
        if (!$this->wallet->debit($user, $ngnPrice, $ref)) {
            $this->usa2->reject((string) $orderId);

            return ['success' => false, 'message' => 'Wallet debit failed.'];
        }

        $verification = Verification::create([
            'user_id' => $user->id,
            'phone' => $phone,
            'order_id' => $orderId,
            'country' => 'US',
            'service' => $service,
            'cost' => $ngnPrice,
            'api_cost' => $apiCost,
            'status' => 1,
            'type' => 4,
            'ip' => request()->ip(),
        ]);

        return ['success' => true, 'verification' => $verification];
    }

    public function pollVerification(Verification $verification): ?array
    {
        if ((int) $verification->status !== 1) {
            return null;
        }

        return match ((int) $verification->type) {
            2, 8 => $this->pollSmsPool($verification),
            4 => $this->pollUsa2($verification),
            9 => $this->pollHero($verification, 'hero'),
            10 => $this->pollHero($verification, 'sv3'),
            default => null,
        };
    }

    public function pollVerificationIfDue(Verification $verification, int $minSeconds = 10): ?array
    {
        if ((int) $verification->status !== 1) {
            return null;
        }

        $cacheKey = 'verification.provider_poll.'.$verification->id;

        if (Cache::has($cacheKey)) {
            return null;
        }

        Cache::put($cacheKey, true, $minSeconds);

        return $this->pollVerification($verification);
    }

    protected function pollSmsPool(Verification $verification): ?array
    {
        $result = $this->smsPool->checkSms((string) $verification->order_id);
        if (($result->status ?? null) == 3) {
            $this->completeVerification($verification, (string) ($result->sms ?? ''), (string) ($result->full_sms ?? $result->sms ?? ''));

            return ['code' => $result->sms ?? ''];
        }

        return null;
    }

    protected function pollUsa2(Verification $verification): ?array
    {
        $result = $this->usa2->readSms((string) $verification->order_id);
        $code = $result->sms ?? $result->code ?? null;
        if ($code) {
            $this->completeVerification($verification, (string) $code, (string) ($result->text ?? $code));

            return ['code' => $code];
        }

        return null;
    }

    protected function pollHero(Verification $verification, string $provider): ?array
    {
        $result = $this->heroHandler->getStatus($provider, (string) $verification->order_id);
        if (($result['status'] ?? '') === 'ok') {
            $this->completeVerification($verification, (string) $result['code'], (string) ($result['full_sms'] ?? $result['code']));

            return ['code' => $result['code']];
        }

        return null;
    }
}
