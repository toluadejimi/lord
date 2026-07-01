<?php

namespace App\Services;

use App\Support\LegacyHelpers;

/**
 * @deprecated Use LegacyHelpers::sendAdminNotification() instead.
 */
class TelegramNotifier
{
    public static function send(string $message): void
    {
        LegacyHelpers::sendAdminNotification($message);
    }

    public static function sendSecondary(string $message): void
    {
        LegacyHelpers::sendAdminNotification($message);
    }
}
