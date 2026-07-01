<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;


ob_start();


define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

// Load application helpers before Composer (shared-hosting safe).
$appHelpers = __DIR__.'/app/helpers.php';
if (is_file($appHelpers)) {
    require_once $appHelpers;
}

// Shared hosting: load layout + notification helpers before Composer autoload.
$bootstrapHelpers = __DIR__.'/bootstrap/helpers_bootstrap.php';
if (is_file($bootstrapHelpers)) {
    require_once $bootstrapHelpers;
}

// Older vendor autoload may still require this path to exist.
$earlyHelpers = __DIR__.'/bootstrap/helpers_early.php';
$needsEarlyFile = !is_file($earlyHelpers) || @filesize($earlyHelpers) < 100;
if ($needsEarlyFile) {
    $bootstrapDir = __DIR__.'/bootstrap';
    if (!is_dir($bootstrapDir)) {
        @mkdir($bootstrapDir, 0775, true);
    }
    if (is_file($bootstrapHelpers)) {
        @copy($bootstrapHelpers, $earlyHelpers);
    } elseif (!is_file($earlyHelpers)) {
        @file_put_contents($earlyHelpers, "<?php\n");
    }
}

require __DIR__.'/vendor/autoload.php';

if (is_file(__DIR__.'/bootstrap/helpers_early.php')) {
    require_once __DIR__.'/bootstrap/helpers_early.php';
}

if (is_file(__DIR__.'/bootstrap/helpers_legacy.php')) {
    require_once __DIR__.'/bootstrap/helpers_legacy.php';
}

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
