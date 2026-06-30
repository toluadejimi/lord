<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

class PricingService
{
    public function ngnFromUsd(float $usd, int $settingId, ?User $user = null, bool $smsPoolLowUsdBoost = false): float
    {
        if ($smsPoolLowUsdBoost && $usd < 4) {
            $usd *= 1.3;
        }

        if ($user && $user->api_percentage) {
            $usd *= (float) $user->api_percentage;
        }

        $setting = Setting::find($settingId);
        $rate = (float) ($setting->rate ?? 0);
        $margin = (float) ($setting->margin ?? 0);

        return round(($usd * $rate) + $margin, 2);
    }

    public function usaSurcharge(float $ngn, bool $hasAreaOrCarrier): float
    {
        if ($hasAreaOrCarrier) {
            return round($ngn * 1.2, 2);
        }

        return $ngn;
    }

    public function settingIdForType(int $type): int
    {
        return match ($type) {
            1 => 1,
            2, 8 => 2,
            3 => 3,
            4 => 4,
            9 => 5,
            10 => 6,
            default => 2,
        };
    }
}
