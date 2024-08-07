<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::any('webhook',  [HomeController::class,'smspool_webhook']);

Route::any('webhook2',  [HomeController::class,'diasy_webhook']);




Route::any('e_fund',  [ProductController::class,'e_fund']);
Route::any('e_check',  [HomeController::class,'e_check']);

Route::any('verify',  [ProductController::class,'verify_username']);




