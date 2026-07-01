<?php

/**
 * Critical helpers for shared hosting — loaded before Composer/Laravel.
 * Keeps layouts working when bootstrap/helpers_early.php is missing or is an empty stub.
 */

if (!function_exists('deployed_from_project_root')) {
    function deployed_from_project_root(): bool
    {
        $base = dirname(__DIR__);

        if (!is_file($base.'/index.php')) {
            return false;
        }

        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $projectRoot = realpath($base) ?: '';
        $publicRoot = realpath($base.'/public') ?: '';

        if ($docRoot !== '' && $projectRoot !== '' && $docRoot === $projectRoot) {
            return true;
        }

        return $publicRoot !== '' && $docRoot !== '' && $docRoot !== $publicRoot;
    }
}

if (!function_exists('static_asset')) {
    function static_asset(string $path): string
    {
        $path = ltrim($path, '/');

        if (deployed_from_project_root()) {
            $path = 'public/'.$path;
        }

        if (function_exists('asset')) {
            return asset($path);
        }

        $root = '';
        if (function_exists('config')) {
            try {
                $root = rtrim((string) config('app.url'), '/');
            } catch (\Throwable $e) {
            }
        }

        if ($root === '' && !empty($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $root = $scheme.'://'.$_SERVER['HTTP_HOST'];
        }

        return $root === '' ? '/'.$path : $root.'/'.$path;
    }
}

if (!function_exists('telegram_notify')) {
    function telegram_notify(string $message, string $channel = 'primary'): void
    {
        if ($message === '' || !function_exists('app_config')) {
            return;
        }

        if ($channel === 'secondary') {
            $token = (string) (app_config('TELEGRAM_BOT_TOKEN_2', '') ?: app_config('TELEGRAM_BOT_TOKEN', ''));
            $chatId = (string) (app_config('TELEGRAM_CHAT_ID_2', '') ?: app_config('TELEGRAM_ADMIN_CHAT_ID', ''));
        } else {
            $token = (string) app_config('TELEGRAM_BOT_TOKEN', '');
            $chatId = (string) app_config('TELEGRAM_ADMIN_CHAT_ID', '');
        }

        if ($token === '' || $chatId === '') {
            return;
        }

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.telegram.org/bot{$token}/sendMessage",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'chat_id' => $chatId,
                    'text' => $message,
                ],
            ]);
            curl_exec($curl);
            curl_close($curl);
        } catch (\Throwable) {
        }
    }
}

if (!function_exists('send_admin_notification')) {
    function send_admin_notification(string $message): void
    {
        telegram_notify($message, 'primary');
        telegram_notify($message, 'secondary');
    }
}

if (!function_exists('send_notification')) {
    function send_notification($message): void
    {
        telegram_notify((string) $message, 'primary');
    }
}

if (!function_exists('send_notification2')) {
    function send_notification2($message): void
    {
        telegram_notify((string) $message, 'secondary');
    }
}
