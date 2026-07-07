<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TelegramPremiumOrder;
use App\Models\User;
use App\Services\TelegramPremium\IStarClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TelegramPremiumOrderService
{
    public function __construct(
        protected IStarClient $istar,
        protected AppConfigService $config,
        protected PricingService $pricing,
        protected WalletService $wallet,
    ) {}

    public function moduleEnabled(): bool
    {
        return $this->config->getBool('provider_telegram_blue_tick_enabled', false);
    }

    public function pricingConfigured(): bool
    {
        foreach (config('telegram_premium.valid_months', [3, 6, 12]) as $months) {
            $fixed = $this->config->get('telegram_premium_price_'.$months);
            if ($fixed !== null && $fixed !== '' && (float) $fixed > 0) {
                return true;
            }
        }

        $setting = Setting::find(7);
        $rate = (float) ($setting->rate ?? 0);

        return $rate > 0;
    }

    /**
     * @return list<array{months: int, label: string, usd: float, price_ngn: float}>
     */
    public function packagesForDisplay(): array
    {
        $raw = $this->istar->premiumPackages();
        $labels = config('telegram_premium.month_labels', []);
        $valid = config('telegram_premium.valid_months', [3, 6, 12]);
        $rows = [];

        foreach ($raw as $pkg) {
            $months = (int) $pkg['months'];
            if (!in_array($months, $valid, true)) {
                continue;
            }
            $usd = (float) $pkg['usd_value'];
            $priceNgn = $this->ngnPriceForMonths($months, $usd);
            if ($priceNgn <= 0) {
                continue;
            }
            $rows[] = [
                'months' => $months,
                'label' => $labels[$months] ?? ($months.' months'),
                'usd' => $usd,
                'price_ngn' => $priceNgn,
            ];
        }

        return $rows;
    }

    public function ngnPriceForMonths(int $months, float $usdValue): float
    {
        $fixed = $this->config->get('telegram_premium_price_'.$months);
        if ($fixed !== null && $fixed !== '' && (float) $fixed > 0) {
            return round((float) $fixed, 2);
        }

        return $this->pricing->ngnFromUsd($usdValue, 7);
    }

    /**
     * @return array{success: bool, message?: string, order?: TelegramPremiumOrder, recipient?: array<string, mixed>}
     */
    public function lookupRecipient(User $user, string $username, int $months): array
    {
        if (!$this->moduleEnabled()) {
            return ['success' => false, 'message' => 'Telegram Blue Tick is not available right now.'];
        }

        if (!$this->istar->configured()) {
            return ['success' => false, 'message' => 'Service is not configured. Contact support.'];
        }

        if (!in_array($months, config('telegram_premium.valid_months', [3, 6, 12]), true)) {
            return ['success' => false, 'message' => 'Invalid subscription duration.'];
        }

        try {
            $recipient = $this->istar->searchPremiumRecipient($username, $months);

            if (!empty($recipient['myself'])) {
                return ['success' => false, 'message' => 'You cannot gift Premium to your own account.'];
            }

            return ['success' => true, 'recipient' => $recipient];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, message?: string, order?: TelegramPremiumOrder}
     */
    public function purchase(
        User $user,
        string $username,
        string $recipientHash,
        int $months,
        float $expectedNgn,
        ?string $recipientName = null,
    ): array {
        if (!$this->moduleEnabled()) {
            return ['success' => false, 'message' => 'Telegram Blue Tick is not available right now.'];
        }

        if (!$this->istar->configured()) {
            return ['success' => false, 'message' => 'Service is not configured. Contact support.'];
        }

        $packages = collect($this->packagesForDisplay())->keyBy('months');
        $pkg = $packages->get($months);
        if (!$pkg) {
            return ['success' => false, 'message' => 'Selected package is unavailable.'];
        }

        $ngnPrice = (float) $pkg['price_ngn'];
        if (abs($ngnPrice - $expectedNgn) > 0.02) {
            return ['success' => false, 'message' => 'Price changed. Please refresh and try again.'];
        }

        if ((float) $user->wallet < $ngnPrice) {
            return ['success' => false, 'message' => 'Insufficient wallet balance.'];
        }

        $refId = 'TG-BT-'.Str::upper(Str::random(12));

        return DB::transaction(function () use ($user, $username, $recipientHash, $months, $ngnPrice, $pkg, $refId, $recipientName) {
            $debit = $this->wallet->debit($user, $ngnPrice, $refId, 4);
            if (!$debit) {
                return ['success' => false, 'message' => 'Insufficient wallet balance.'];
            }

            $order = TelegramPremiumOrder::create([
                'user_id' => $user->id,
                'ref_id' => $refId,
                'username' => ltrim(trim($username), '@'),
                'recipient_hash' => $recipientHash,
                'recipient_name' => $recipientName,
                'months' => $months,
                'amount_ngn' => $ngnPrice,
                'amount_usd' => (float) $pkg['usd'],
                'status' => 'pending',
            ]);

            try {
                $provider = $this->istar->createPremiumOrder($order->username, $recipientHash, $months);
            } catch (\Throwable $e) {
                $this->wallet->refund($user, $ngnPrice, $refId.'-RF');
                $order->update([
                    'status' => 'refunded',
                    'failure_reason' => $e->getMessage(),
                ]);

                return ['success' => false, 'message' => $e->getMessage()];
            }

            $order->update([
                'istar_order_id' => (string) ($provider['order_id'] ?? ''),
                'provider_response' => $provider,
            ]);

            if (function_exists('send_admin_notification')) {
                send_admin_notification(sprintf(
                    "Telegram Blue Tick order #%d — @%s — %d mo — ₦%s — pending",
                    $order->id,
                    $order->username,
                    $order->months,
                    number_format($ngnPrice, 2)
                ));
            }

            return [
                'success' => true,
                'message' => 'Order placed. Premium will be delivered shortly.',
                'order' => $order->fresh(),
            ];
        });
    }

    public function markCompleted(TelegramPremiumOrder $order, ?string $txHash = null): void
    {
        if ($order->isCompleted()) {
            return;
        }

        $order->update([
            'status' => 'completed',
            'tx_hash' => $txHash,
            'completed_at' => now(),
        ]);
    }

    public function markFailed(TelegramPremiumOrder $order, string $reason, bool $refund = true): void
    {
        if (in_array($order->status, ['completed', 'refunded', 'failed'], true)) {
            return;
        }

        DB::transaction(function () use ($order, $reason, $refund) {
            if ($refund && $order->status === 'pending') {
                $this->wallet->refund(
                    $order->user,
                    (float) $order->amount_ngn,
                    $order->ref_id.'-RF'
                );
                $order->status = 'refunded';
            } else {
                $order->status = 'failed';
            }

            $order->failure_reason = $reason;
            $order->save();
        });
    }
}
