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

require __DIR__.'/vendor/autoload.php';

if (!function_exists('smslord_load_helpers')) {
    foreach ([
        __DIR__.'/vendor/composer/smslord_boot.php',
        __DIR__.'/bootstrap/load_helpers.php',
    ] as $bootFile) {
        if (is_file($bootFile)) {
            require_once $bootFile;
            break;
        }
    }
}

if (!function_exists('smslord_load_helpers')) {
    function smslord_load_helpers(?string $base = null): void
    {
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
    }
}

smslord_load_helpers(__DIR__);

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
