<?php

namespace App\Services;

use App\Models\EsimOrder;
use App\Models\Setting;
use App\Models\User;
use App\Services\Esim\PikaSimClient;
use Illuminate\Support\Facades\Cache;
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
     * Country codes available for the dropdown (code => display name).
     *
     * @return array<string, string>
     */
    public function countriesForDropdown(string $type = 'data'): array
    {
        if (!$this->client->configured() || !$this->pricingConfigured()) {
            return [];
        }

        $type = in_array($type, ['data', 'phone', 'all'], true) ? $type : 'data';

        return Cache::remember('esim_countries_'.$type, 1800, function () use ($type) {
            try {
                $result = $this->client->packages([
                    'type' => $type,
                    'limit' => 200,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Esim countries fetch failed', ['error' => $e->getMessage()]);

                return [];
            }

            $countries = [];
            foreach ($result['packages'] as $pkg) {
                if (!is_array($pkg)) {
                    continue;
                }
                if (!empty($pkg['isUnlimited']) || ($pkg['pricingType'] ?? 'fixed') === 'per_day') {
                    continue;
                }

                $code = strtoupper(trim((string) ($pkg['locationCode'] ?? $pkg['location'] ?? '')));
                if ($code === '' || strlen($code) > 8) {
                    continue;
                }

                if (!isset($countries[$code])) {
                    $countries[$code] = $this->countryDisplayName($code, $pkg);
                }
            }

            asort($countries, SORT_NATURAL | SORT_FLAG_CASE);

            return $countries;
        });
    }

    /**
     * Duration options available for the dropdown (days => label).
     *
     * @return array<int, string>
     */
    public function durationsForDropdown(string $type = 'data', ?string $country = null): array
    {
        if (!$this->client->configured() || !$this->pricingConfigured()) {
            return [];
        }

        $type = in_array($type, ['data', 'phone', 'all'], true) ? $type : 'data';
        $countryKey = $country ? strtoupper($country) : 'all';

        return Cache::remember('esim_durations_'.$type.'_'.$countryKey, 1800, function () use ($type, $country) {
            try {
                $query = [
                    'type' => $type,
                    'limit' => 200,
                ];
                if ($country) {
                    $query['country'] = strtoupper($country);
                }
                $result = $this->client->packages($query);
            } catch (\Throwable $e) {
                Log::warning('Esim durations fetch failed', ['error' => $e->getMessage()]);

                return [];
            }

            $durations = [];
            foreach ($result['packages'] as $pkg) {
                if (!is_array($pkg)) {
                    continue;
                }
                if (!empty($pkg['isUnlimited']) || ($pkg['pricingType'] ?? 'fixed') === 'per_day') {
                    continue;
                }

                $days = (int) ($pkg['duration'] ?? $pkg['validityDays'] ?? 0);
                if ($days <= 0) {
                    continue;
                }

                $durations[$days] = $days === 1 ? '1 day' : $days.' days';
            }

            ksort($durations, SORT_NUMERIC);

            return $durations;
        });
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

                $code = strtoupper(trim((string) ($pkg['locationCode'] ?? $pkg['location'] ?? $pkg['region'] ?? '')));
                $rows[] = [
                    'package_code' => (string) $pkg['packageCode'],
                    'name' => (string) ($pkg['name'] ?? $pkg['packageCode']),
                    'location' => $code,
                    'location_name' => $this->countryDisplayName($code, $pkg),
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

            if (!empty($query['duration'])) {
                $wanted = (int) $query['duration'];
                $rows = array_values(array_filter(
                    $rows,
                    fn (array $row) => (int) ($row['duration_days'] ?? 0) === $wanted
                ));
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
     * @param  array<string, mixed>  $pkg
     */
    protected function countryDisplayName(string $code, array $pkg = []): string
    {
        $fromApi = (string) data_get($pkg, 'locationNetworkList.0.locationName', '');
        if ($fromApi !== '') {
            return $fromApi;
        }

        if ($code !== '' && class_exists(\Locale::class)) {
            $name = \Locale::getDisplayRegion('-'.$code, 'en');
            if (is_string($name) && $name !== '' && $name !== $code) {
                return $name;
            }
        }

        return $code !== '' ? $code : 'Global / Regional';
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
