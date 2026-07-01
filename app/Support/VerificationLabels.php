<?php

namespace App\Support;

class VerificationLabels
{
    public static function providerName(int $type): string
    {
        return match ($type) {
            1 => 'USA1 (Legacy)',
            2 => 'Legacy World',
            3 => 'Server 1 — 5SIM',
            4 => 'Server 2 — Unlimited',
            8 => 'SMSPool World',
            9 => 'Server 3 — HeroSMS',
            10 => 'Server 4 — SMS Bower',
            default => 'Unknown',
        };
    }

    public static function providerBadgeClass(int $type): string
    {
        return match ($type) {
            3 => 'info',
            4 => 'warning',
            8 => 'primary',
            9 => 'dark',
            10 => 'success',
            default => 'secondary',
        };
    }
}
