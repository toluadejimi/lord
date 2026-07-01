<?php

/**
 * SMSLORD global helpers — canonical copy (self-contained, no other requires).
 * Loaded from index.php before Composer on shared hosting.
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

if (!function_exists('app_config')) {
    function app_config(string $key, ?string $default = null): ?string
    {
        return app(\App\Services\AppConfigService::class)->get($key, $default);
    }
}

if (!function_exists('app_config_bool')) {
    function app_config_bool(string $key, bool $default = false): bool
    {
        return app(\App\Services\AppConfigService::class)->getBool($key, $default);
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

if (!function_exists('get_s_countries')) {
    function get_s_countries(): array
    {
        if (!function_exists('app_config')) {
            return [];
        }

        $token = app_config('SIMTOKEN');
        if (!$token) {
            return [];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/guest/countries');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
            'Accept: application/json',
        ]);

        $var = curl_exec($ch);
        curl_close($ch);
        $inputArray = json_decode($var, true);

        if (!is_array($inputArray)) {
            return [];
        }

        $result = [];
        foreach ($inputArray as $key => $value) {
            $result[$key] = $value['text_en'] ?? $key;
        }

        return $result;
    }
}

if (!function_exists('get_s_product_cost')) {
    function get_s_product_cost(string $operator, string $country, string $product): float|int
    {
        if (!function_exists('app_config')) {
            return 0;
        }

        $token = app_config('SIMTOKEN');
        if (!$token) {
            return 0;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/guest/products/'.$country.'/'.$operator);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
            'Accept: application/json',
        ]);

        $var = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($var, true);

        if (!is_array($data)) {
            return 0;
        }

        $filteredData = array_filter($data, function ($key) use ($product) {
            return stripos($key, $product) !== false;
        }, ARRAY_FILTER_USE_KEY);

        if ($filteredData === []) {
            return 0;
        }

        $prices = [];
        foreach ($filteredData as $item) {
            if (isset($item['Price'])) {
                $prices[] = $item['Price'];
            }
        }

        if ($prices === []) {
            return 0;
        }

        if (!class_exists(\App\Models\Setting::class)) {
            return 0;
        }

        $sRate = \App\Models\Setting::find(3);
        if (!$sRate) {
            return 0;
        }

        return ($sRate->rate * $prices[0]) + $sRate->margin;
    }
}
