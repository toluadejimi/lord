<?php

use App\Http\Controllers\Api\V1\ResellerApiController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Legacy webhooks (kept for backward compatibility)
Route::any('webhook', [HomeController::class, 'smspool_webhook']);
Route::any('webhook2', [HomeController::class, 'diasy_webhook']);

// Provider webhooks
Route::post('world-sms-webhook', [WebhookController::class, 'smsPool']);
Route::post('hero-sms-webhook', [WebhookController::class, 'hero']);
Route::post('world-sv3-webhook', [WebhookController::class, 'sv3']);
Route::post('w-webhook', [WebhookController::class, 'worldLegacy']);
Route::post('webhooks/sprintpay', [WebhookController::class, 'sprintPay']);
Route::post('webhooks/istar', [WebhookController::class, 'iStar']);

// Legacy wallet API
Route::any('e_fund', [ProductController::class, 'e_fund']);
Route::any('e_check', [HomeController::class, 'e_check']);
Route::any('verify', [ProductController::class, 'verify_username']);

// Reseller API v1
Route::prefix('v1')->middleware('throttle:120,1')->group(function () {
    Route::match(['get', 'post'], 'balance', [ResellerApiController::class, 'balance']);
    Route::match(['get', 'post'], 'get-sms', [ResellerApiController::class, 'getOrderSms']);
    Route::match(['get', 'post'], 'cancel-sms', [ResellerApiController::class, 'cancelOrderSms']);

    Route::match(['get', 'post'], 'server-1/countries', [ResellerApiController::class, 'server1Countries']);
    Route::match(['get', 'post'], 'server-1/prices', [ResellerApiController::class, 'server1Prices']);
    Route::match(['get', 'post'], 'server-1/price', [ResellerApiController::class, 'server1Price']);
    Route::match(['get', 'post'], 'server-1/rent', [ResellerApiController::class, 'server1Rent']);

    Route::match(['get', 'post'], 'server-2/services', [ResellerApiController::class, 'server2Services']);
    Route::match(['get', 'post'], 'server-2/price', [ResellerApiController::class, 'server2Price']);
    Route::match(['get', 'post'], 'server-2/rent', [ResellerApiController::class, 'server2Rent']);

    Route::match(['get', 'post'], 'server-3/countries', [ResellerApiController::class, 'server3Countries']);
    Route::match(['get', 'post'], 'server-3/services', [ResellerApiController::class, 'server3Services']);
    Route::match(['get', 'post'], 'server-3/price', [ResellerApiController::class, 'server3Price']);
    Route::match(['get', 'post'], 'server-3/rent', [ResellerApiController::class, 'server3Rent']);

    Route::match(['get', 'post'], 'server-4/countries', [ResellerApiController::class, 'server4Countries']);
    Route::match(['get', 'post'], 'server-4/services', [ResellerApiController::class, 'server4Services']);
    Route::match(['get', 'post'], 'server-4/price', [ResellerApiController::class, 'server4Price']);
    Route::match(['get', 'post'], 'server-4/rent', [ResellerApiController::class, 'server4Rent']);

    // Legacy aliases
    Route::match(['get', 'post'], 'get-world-countries', [ResellerApiController::class, 'getWorldCountries']);
    Route::match(['get', 'post'], 'get-world-services', [ResellerApiController::class, 'getWorldServices']);
    Route::match(['get', 'post'], 'check-world-number-availability', [ResellerApiController::class, 'checkWorldAvailability']);
    Route::match(['get', 'post'], 'rent-world-number', [ResellerApiController::class, 'rentWorldNumber']);
    Route::match(['get', 'post'], 'get-world-sms', [ResellerApiController::class, 'getWorldSms']);
    Route::match(['get', 'post'], 'cancel-world-sms', [ResellerApiController::class, 'cancelWorldSms']);
    Route::match(['get', 'post'], 'get-usa-sms', [ResellerApiController::class, 'getUsaSms']);
    Route::match(['get', 'post'], 'cancel-usa-sms', [ResellerApiController::class, 'cancelUsaSms']);
    Route::match(['get', 'post'], 'usa-services', [ResellerApiController::class, 'usaServicesRetired']);
    Route::match(['get', 'post'], 'rent-usa-number', [ResellerApiController::class, 'rentUsaRetired']);
});
