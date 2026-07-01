<?php

namespace App\Services;

class TelegramNotifier
{
    public static function send(string $message): void
    {
        self::dispatch(
            (string) app(AppConfigService::class)->get('TELEGRAM_BOT_TOKEN', ''),
            (string) app(AppConfigService::class)->get('TELEGRAM_ADMIN_CHAT_ID', ''),
            $message
        );
    }

    public static function sendSecondary(string $message): void
    {
        $config = app(AppConfigService::class);

        $token = (string) ($config->get('TELEGRAM_BOT_TOKEN_2', '') ?: $config->get('TELEGRAM_BOT_TOKEN', ''));
        $chatId = (string) ($config->get('TELEGRAM_CHAT_ID_2', '') ?: $config->get('TELEGRAM_ADMIN_CHAT_ID', ''));

        self::dispatch($token, $chatId, $message);
    }

    protected static function dispatch(string $token, string $chatId, string $message): void
    {
        if ($token === '' || $chatId === '' || $message === '') {
            return;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.telegram.org/bot{$token}/sendMessage",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'chat_id' => $chatId,
                'text' => $message,
            ],
        ]);
        curl_exec($curl);
        curl_close($curl);
    }
}
