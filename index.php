<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

ob_start();

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| SMSLORD helpers (before Composer — shared hosting safe)
|--------------------------------------------------------------------------
| Composer must NEVER require project helper files (fatal if missing).
| Load helpers here; inline fallback if deploy is incomplete.
*/
foreach ([
    __DIR__.'/app/helpers.php',
    __DIR__.'/bootstrap/helpers_bootstrap.php',
    __DIR__.'/smslord_bootstrap.php',
    __DIR__.'/bootstrap/helpers_early.php',
] as $helperFile) {
    if (!is_file($helperFile) || @filesize($helperFile) < 100) {
        continue;
    }
    try {
        require_once $helperFile;
    } catch (\Throwable) {
        continue;
    }
    if (function_exists('static_asset')) {
        break;
    }
}

if (!function_exists('static_asset')) {
    function deployed_from_project_root(): bool
    {
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $projectRoot = realpath(__DIR__) ?: '';

        return $docRoot !== '' && $projectRoot !== '' && $docRoot === $projectRoot;
    }

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
        $root = $host !== '' ? $scheme.'://'.$host : '';

        return $root === '' ? '/'.$path : $root.'/'.$path;
    }
}

require __DIR__.'/vendor/autoload.php';

if (is_file(__DIR__.'/bootstrap/helpers_legacy.php')) {
    require_once __DIR__.'/bootstrap/helpers_legacy.php';
}

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
