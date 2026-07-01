<?php

namespace App\Services;

/**
 * Thin wrapper around legacy Telegram helpers so controllers never depend on
 * optional app/Support classes that may be missing on some deployments.
 */
class TelegramNotifier
{
    public function notify(string $message): void
    {
        $this->send($message);
    }

    public function send(string $message): void
    {
        if (function_exists('send_admin_notification')) {
            send_admin_notification($message);

            return;
        }

        if (function_exists('send_notification')) {
            send_notification($message);
        }

        if (function_exists('send_notification2')) {
            send_notification2($message);
        }
    }
}
