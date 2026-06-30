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

// Legacy wallet API
Route::any('e_fund', [ProductController::class, 'e_fund']);
Route::any('e_check', [HomeController::class, 'e_check']);
Route::any('verify', [ProductController::class, 'verify_username']);

// Reseller API v1
Route::prefix('v1')->middleware('throttle:120,1')->group(function () {
    Route::match(['get', 'post'], 'balance', [ResellerApiController::class, 'balance']);
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
