<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

ob_start();

define('LARAVEL_START', microtime(true));

if (!defined('SMSLORD_BASE')) {
    define('SMSLORD_BASE', __DIR__);
}

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
| Composer must NEVER autoload project helper files (fatal if missing on server).
| Load helpers here first; small inline fallback if files are absent.
*/
foreach ([
    __DIR__.'/app/helpers.php',
    __DIR__.'/bootstrap/helpers_bootstrap.php',
    __DIR__.'/bootstrap/helpers_early.php',
    __DIR__.'/bootstrap/load_helpers.php',
    __DIR__.'/vendor/composer/smslord_boot.php',
] as $helperFile) {
    if (!is_file($helperFile)) {
        continue;
    }
    try {
        require_once $helperFile;
    } catch (\Throwable) {
    }
}

if (!function_exists('static_asset')) {
    function static_asset(string $path): string
    {
        $path = ltrim($path, '/');
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $projectRoot = realpath(SMSLORD_BASE) ?: '';
        if ($docRoot !== '' && $projectRoot !== '' && $docRoot === $projectRoot) {
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

if (!function_exists('send_notification')) {
    function send_notification($message): void
    {
        if (function_exists('telegram_notify')) {
            telegram_notify((string) $message, 'primary');
        }
    }
}

if (!function_exists('send_notification2')) {
    function send_notification2($message): void
    {
        if (function_exists('telegram_notify')) {
            telegram_notify((string) $message, 'secondary');
        }
    }
}

if (!function_exists('send_admin_notification')) {
    function send_admin_notification(string $message): void
    {
        if (function_exists('telegram_notify')) {
            telegram_notify($message, 'primary');
            telegram_notify($message, 'secondary');
        }
    }
}

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
