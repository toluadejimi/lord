<?php

namespace App\Services;

use App\Models\EsimOrder;
use App\Models\Setting;
use App\Models\User;
use App\Services\Esim\PikaSimClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EsimOrderService
{
    public const SETTING_ID = 8;

    public function __construct(
        protected PikaSimClient $client,
        protected AppConfigService $config,
        protected PricingService $pricing,
        protected WalletService $wallet,
    ) {}

    public function moduleEnabled(): bool
    {
        return $this->config->getBool('provider_pikasim_enabled', false);
    }

    public function pricingConfigured(): bool
    {
        $setting = Setting::find(self::SETTING_ID);

        return (float) ($setting->rate ?? 0) > 0;
    }

    /**
     * Convert provider price (cents) to customer NGN.
     */
    public function ngnFromProviderCents(int $cents): float
    {
        $usd = $cents / 100;

        return $this->pricing->ngnFromUsd($usd, self::SETTING_ID);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array{packages: list<array<string, mixed>>, pagination: array<string, mixed>, error?: string}
     */
    public function packagesForDisplay(array $query = []): array
    {
        if (!$this->client->configured()) {
            return ['packages' => [], 'pagination' => [], 'error' => 'Service is being configured. Please check back soon.'];
        }

        if (!$this->pricingConfigured()) {
            return ['packages' => [], 'pagination' => [], 'error' => 'Pricing is not configured yet. Please contact support.'];
        }

        try {
            $result = $this->client->packages(array_merge([
                'type' => 'data',
                'limit' => 50,
            ], $query));

            $rows = [];
            foreach ($result['packages'] as $pkg) {
                if (!is_array($pkg) || empty($pkg['packageCode'])) {
                    continue;
                }
                // Skip daily/unlimited plans (not available via API / pricingType per_day)
                if (!empty($pkg['isUnlimited']) || ($pkg['pricingType'] ?? 'fixed') === 'per_day') {
                    continue;
                }

                $cents = (int) ($pkg['price'] ?? 0);
                $priceNgn = $this->ngnFromProviderCents($cents);
                if ($priceNgn <= 0) {
                    continue;
                }

                $rows[] = [
                    'package_code' => (string) $pkg['packageCode'],
                    'name' => (string) ($pkg['name'] ?? $pkg['packageCode']),
                    'location' => (string) ($pkg['locationCode'] ?? $pkg['location'] ?? $pkg['region'] ?? ''),
                    'volume_gb' => (float) ($pkg['volumeGB'] ?? 0),
                    'duration_days' => (int) ($pkg['duration'] ?? $pkg['validityDays'] ?? 0),
                    'speed' => (string) ($pkg['speed'] ?? ''),
                    'price_cents' => $cents,
                    'price_usd' => round($cents / 100, 2),
                    'price_ngn' => $priceNgn,
                    'max_privacy' => (bool) ($pkg['maxPrivacy'] ?? false),
                    'plan_type' => (string) ($pkg['planType'] ?? 'data'),
                ];
            }

            return [
                'packages' => $rows,
                'pagination' => $result['pagination'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::warning('Esim packages failed', ['error' => $e->getMessage()]);

            return ['packages' => [], 'pagination' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, message?: string, order?: EsimOrder}
     */
    public function purchase(User $user, string $packageCode, float $expectedNgn): array
    {
        if (!$this->moduleEnabled()) {
            return ['success' => false, 'message' => 'Esim is not available right now.'];
        }

        if (!$this->client->configured()) {
            return ['success' => false, 'message' => 'Service is not configured. Contact support.'];
        }

        if (!$this->pricingConfigured()) {
            return ['success' => false, 'message' => 'Pricing is not configured yet.'];
        }

        try {
            $pkg = $this->client->package($packageCode);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Package not found: '.$e->getMessage()];
        }

        if (!empty($pkg['isUnlimited']) || ($pkg['pricingType'] ?? 'fixed') === 'per_day') {
            return ['success' => false, 'message' => 'This package is not available for purchase.'];
        }

        $cents = (int) ($pkg['price'] ?? 0);
        $priceNgn = $this->ngnFromProviderCents($cents);
        if ($priceNgn <= 0) {
            return ['success' => false, 'message' => 'Invalid package price.'];
        }

        if (abs($priceNgn - $expectedNgn) > 1) {
            return ['success' => false, 'message' => 'Price changed. Please refresh and try again.'];
        }

        if ((float) $user->wallet < $priceNgn) {
            return ['success' => false, 'message' => 'Insufficient wallet balance.'];
        }

        $refId = 'ESIM-'.strtoupper(Str::random(12));

        return DB::transaction(function () use ($user, $pkg, $packageCode, $priceNgn, $cents, $refId) {
            $debit = $this->wallet->debit($user, $priceNgn, $refId, 4);
            if (!$debit) {
                return ['success' => false, 'message' => 'Insufficient wallet balance.'];
            }

            $order = EsimOrder::create([
                'user_id' => $user->id,
                'ref_id' => $refId,
                'package_code' => $packageCode,
                'package_name' => (string) ($pkg['name'] ?? $packageCode),
                'location' => (string) ($pkg['locationCode'] ?? $pkg['location'] ?? ''),
                'volume_gb' => isset($pkg['volumeGB']) ? (float) $pkg['volumeGB'] : null,
                'duration_days' => (int) ($pkg['duration'] ?? $pkg['validityDays'] ?? 0) ?: null,
                'amount_ngn' => $priceNgn,
                'amount_usd' => round($cents / 100, 4),
                'provider_price_cents' => $cents,
                'status' => 'processing',
            ]);

            try {
                $remote = $this->client->createOrder($packageCode, $refId);
                $order->update([
                    'provider_order_id' => (string) ($remote['orderId'] ?? ''),
                    'provider_response' => $remote,
                    'status' => (string) ($remote['status'] ?? 'processing'),
                ]);
            } catch (\Throwable $e) {
                $this->wallet->refund($user, $priceNgn, $refId.'-RF');
                $order->update([
                    'status' => 'failed',
                    'failure_reason' => $e->getMessage(),
                ]);

                return ['success' => false, 'message' => 'Order failed: '.$e->getMessage()];
            }

            return [
                'success' => true,
                'message' => 'Order placed. Your eSIM details will appear under My orders when ready.',
                'order' => $order->fresh(),
            ];
        });
    }

    public function markProvisioned(EsimOrder $order, array $esim, ?array $fullPayload = null): void
    {
        if ($order->isCompleted() || $order->isFailed()) {
            return;
        }

        $order->update([
            'status' => 'completed',
            'iccid' => (string) ($esim['iccid'] ?? $order->iccid),
            'qr_code_url' => $esim['qrCodeUrl'] ?? $order->qr_code_url,
            'activation_code' => $esim['activationCode'] ?? $order->activation_code,
            'short_url' => $esim['shortUrl'] ?? $order->short_url,
            'esim_status' => 'GOT_RESOURCE',
            'provider_response' => $fullPayload ?? $order->provider_response,
            'completed_at' => now(),
            'failure_reason' => null,
        ]);
    }

    public function markFailed(EsimOrder $order, string $reason, bool $refund = true): void
    {
        if ($order->isFailed()) {
            return;
        }

        DB::transaction(function () use ($order, $reason, $refund) {
            $order->update([
                'status' => 'failed',
                'failure_reason' => $reason,
            ]);

            if ($refund && $order->amount_ngn > 0) {
                $this->wallet->refund(
                    $order->user,
                    (float) $order->amount_ngn,
                    $order->ref_id.'-RF'
                );
            }
        });
    }
}
