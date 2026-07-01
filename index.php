<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

ob_start();

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

if (!defined('SMSLORD_BASE')) {
    define('SMSLORD_BASE', __DIR__);
}

$loader = __DIR__.'/bootstrap/load_helpers.php';
if (is_file($loader)) {
    require_once $loader;
}

if (!function_exists('smslord_load_helpers')) {
    function smslord_load_helpers(?string $base = null): void
    {
        $base = $base ?? SMSLORD_BASE;

        foreach ([
            $base.'/app/helpers.php',
            $base.'/bootstrap/helpers_bootstrap.php',
            $base.'/bootstrap/helpers_early.php',
            $base.'/bootstrap/helpers_legacy.php',
            $base.'/smslord_bootstrap.php',
        ] as $helperFile) {
            if (!is_file($helperFile)) {
                continue;
            }
            try {
                require_once $helperFile;
            } catch (\Throwable) {
            }
        }

        if (!function_exists('deployed_from_project_root')) {
            function deployed_from_project_root(): bool
            {
                $base = SMSLORD_BASE;
                $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
                $projectRoot = realpath($base) ?: '';

                return $docRoot !== '' && $projectRoot !== '' && $docRoot === $projectRoot;
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
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? '';

                return $host === '' ? '/'.$path : $scheme.'://'.$host.'/'.$path;
            }
        }

        if (!function_exists('app_config')) {
            function app_config(string $key, ?string $default = null): ?string
            {
                if (!function_exists('app')) {
                    return $default;
                }

                return app(\App\Services\AppConfigService::class)->get($key, $default);
            }
        }

        if (!function_exists('app_config_bool')) {
            function app_config_bool(string $key, bool $default = false): bool
            {
                if (!function_exists('app')) {
                    return $default;
                }

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
                        CURLOPT_POSTFIELDS => ['chat_id' => $chatId, 'text' => $message],
                    ]);
                    curl_exec($curl);
                    curl_close($curl);
                } catch (\Throwable) {
                }
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

        if (!function_exists('send_admin_notification')) {
            function send_admin_notification(string $message): void
            {
                telegram_notify($message, 'primary');
                telegram_notify($message, 'secondary');
            }
        }
    }
}

smslord_load_helpers(__DIR__);

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
