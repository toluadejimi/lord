<?php

use App\Http\Controllers\Admin\AdminMaintenanceController;
use App\Http\Controllers\Admin\AdminServicesController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminVtuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\Usa2Controller;
use App\Http\Controllers\VerificationOrderController;
use App\Http\Controllers\VtuController;
use App\Http\Controllers\WalletTransactionController;
use App\Http\Controllers\WorldHeroController;
use App\Http\Controllers\WorldNumberController;
use App\Http\Controllers\WorldSv3Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('logs', [LogViewerController::class, 'index']);


Route::get('/hello-test', function () {
    return 'Hello from web.php';
});


//Clear Config cache:
Route::get('/clear1', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cache cleared</h1>';
});


Route::get('/clear2', function() {
    $exitCode = Artisan::call('config:clear');
    return '<h1>Clear config cleared</h1>';
});


Route::get('/proxy/prices', function (Illuminate\Http\Request $request) {
    // Get the 'country' query parameter
    $country = $request->query('country');

    // Make the request to the 5sim API from the Laravel server
    $response = Http::get('https://5sim.net/v1/guest/prices', [
        'country' => $country,
    ]);

    return $response->json();
});






//auth

Route::get('/',  [HomeController::class,'index']);


Route::post('login_now',  [HomeController::class,'login']);
// Route::get('login',  [HomeController::class,'login_index']);
Route::get('login',  [HomeController::class,'login_index'])->name('login');
Route::post('register_now',  [HomeController::class,'register']);
Route::get('register',  [HomeController::class,'register_index']);
Route::get('ban',  [HomeController::class,'user_ban']);
Route::any('ban-user',  [HomeController::class,'ban_users']);
Route::any('unban-users',  [HomeController::class,'unban_users']);






Route::get('log-out',  [HomeController::class,'logout']);
Route::post('reset-password-now',  [HomeController::class,'reset_password_now']);
Route::post('reset-password',  [HomeController::class,'reset_password']);
Route::get('expired',  [HomeController::class,'expired']);
Route::get('verify-password',  [HomeController::class,'verify_password']);
Route::get('forgot-password',  [HomeController::class,'forget_password']);
Route::get('faq',  [HomeController::class,'faq']);
Route::get('terms',  [HomeController::class,'terms']);
Route::get('policy',  [HomeController::class,'policy']);
Route::get('rules',  [HomeController::class,'rules']);
Route::post('update-password-now',  [HomeController::class,'update_password_now']);

Route::any('get-smscode',  [HomeController::class,'get_smscode']);










Route::group(['middleware' => ['auth', 'user']], function () {



    Route::get('cworld',  [SimController::class,'index']);
    Route::get('cworld/countries',  [SimController::class,'countriesJson']);
    Route::post('buy-csms',  [SimController::class,'order_csms']);
    Route::get('c-sms',  [SimController::class,'delete_sms']);
    Route::get('admin-c-sms',  [SimController::class,'admin_delete_sms']);
    Route::get('get-csms',  [SimController::class,'get_c_sms']);






    Route::get('home',  [HomeController::class,'home']);



    Route::redirect('world', 'cworld', 302);
    Route::any('check-av',  [WorldNumberController::class,'check_av']);
    Route::post('order_now_world',  [WorldNumberController::class,'order_now']);
    Route::any('get-smscodeworld',  [WorldNumberController::class,'get_smscode']);



    Route::get('usno',  [HomeController::class,'home']);
    Route::any('orders',  [HomeController::class,'orders']);
    Route::get('verification/{id}/poll', [VerificationOrderController::class, 'poll'])->whereNumber('id');
    Route::post('verification/{id}/cancel', [VerificationOrderController::class, 'cancel'])->whereNumber('id');
    Route::any('receive-sms',  [HomeController::class,'receive_sms']);
    Route::any('delete-order',  [HomeController::class,'delete_order']);
    Route::post('order-usanumber-now',  [HomeController::class,'order_now']);
    Route::any('cancle-sms',  [HomeController::class,'cancle_sms']);
    Route::any('admin-cancle-sms',  [HomeController::class,'admin_cancle_sms']);
    Route::any('check-sms',  [HomeController::class,'check_sms']);


Route::get('welcome',  [HomeController::class,'welcome_index']);




Route::get('fund-wallet',  [HomeController::class,'fund_wallet']);
Route::get('wallet-transactions', [WalletTransactionController::class, 'index'])->name('wallet.transactions');
Route::get('profile',  [HomeController::class,'profile']);
Route::post('fund-now',  [HomeController::class,'fund_now']);
Route::get('verify',  [HomeController::class,'verify_payment']);
Route::get('verifypay',  [HomeController::class,'verifypay_payment']);

Route::get('resolve-page',  [HomeController::class,'resloveDeposit']);
Route::any('resolve-now',  [HomeController::class,'resolveNow']);
Route::get('change-password',  [HomeController::class,'change_password']);

    Route::get('usa2', [Usa2Controller::class, 'index']);
    Route::post('order-usa2', [Usa2Controller::class, 'order']);
    Route::get('usa2/catalog/price', [Usa2Controller::class, 'catalogPrice']);
    Route::any('get-smscode-usa2', [Usa2Controller::class, 'pollSms']);
    Route::get('world-sv2', [WorldHeroController::class, 'index']);
    Route::get('world-sv2/catalog/countries', [WorldHeroController::class, 'catalogCountries']);
    Route::get('world-sv2/catalog/services', [WorldHeroController::class, 'catalogServices']);
    Route::get('world-sv2/catalog/price', [WorldHeroController::class, 'catalogPrice']);
    Route::post('order-world-hero', [WorldHeroController::class, 'order']);
    Route::any('get-smscode-hero', [WorldHeroController::class, 'pollSms']);
    Route::get('world-sv3', [WorldSv3Controller::class, 'index']);
    Route::get('world-sv3/catalog/countries', [WorldSv3Controller::class, 'catalogCountries']);
    Route::get('world-sv3/catalog/services', [WorldSv3Controller::class, 'catalogServices']);
    Route::get('world-sv3/catalog/price', [WorldSv3Controller::class, 'catalogPrice']);
    Route::post('order-world-sv3', [WorldSv3Controller::class, 'order']);
    Route::any('get-smscode-sv3', [WorldSv3Controller::class, 'pollSms']);
    Route::get('verification', [HomeController::class, 'orders']);
    Route::get('api-docs', [ApiDocsController::class, 'index']);
    Route::post('api-docs/webhook', [ApiDocsController::class, 'updateWebhook']);
    Route::post('api-docs/regenerate', [ApiDocsController::class, 'regenerateKey']);
    Route::post('api-docs/reveal-key', [ApiDocsController::class, 'revealKey']);
    Route::get('vas', [VtuController::class, 'index'])->name('vas.index');
    Route::get('vas/airtime', [VtuController::class, 'airtime'])->name('vas.airtime');
    Route::post('vas/airtime', [VtuController::class, 'buyAirtime'])->name('vas.airtime.buy');
    Route::get('vas/data', [VtuController::class, 'data'])->name('vas.data');
    Route::post('vas/data', [VtuController::class, 'buyData'])->name('vas.data.buy');
    Route::get('vas/cable', [VtuController::class, 'cable'])->name('vas.cable');
    Route::post('vas/cable/validate', [VtuController::class, 'validateCable'])->name('vas.cable.validate');
    Route::post('vas/cable', [VtuController::class, 'buyCable'])->name('vas.cable.buy');
    Route::get('vas/electricity', [VtuController::class, 'electricity'])->name('vas.electricity');
    Route::post('vas/electricity/validate', [VtuController::class, 'validateElectricity'])->name('vas.electricity.validate');
    Route::post('vas/electricity', [VtuController::class, 'buyElectricity'])->name('vas.electricity.buy');
    Route::get('vas/catalog/data-variations', [VtuController::class, 'catalogDataVariations'])->name('vas.catalog.data');
    Route::get('vas/catalog/cable-plans', [VtuController::class, 'catalogCablePlans'])->name('vas.catalog.cable');
    Route::get('vas/catalog/electricity-variations', [VtuController::class, 'catalogElectricityVariations'])->name('vas.catalog.electricity');
    Route::post('assistant/command', [AssistantController::class, 'command']);

});





























// Admin
Route::get('admin',  [AdminController::class,'index']);
Route::post('admin-login',  [AdminController::class,'admin_login']);

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard',  [AdminController::class,'admin_dashboard']);
    Route::get('transactions', [AdminController::class, 'transactions']);
    Route::get('verifications', [AdminController::class, 'verifications']);
    Route::get('services', [AdminServicesController::class, 'index']);
    Route::post('services', [AdminServicesController::class, 'update']);
    Route::get('vtu', [AdminVtuController::class, 'index']);
    Route::post('vtu', [AdminVtuController::class, 'update']);
    Route::post('vtu/fetch-categories', [AdminVtuController::class, 'fetchCategories']);
    Route::get('settings', [AdminSettingsController::class, 'index']);
    Route::post('settings/keys', [AdminSettingsController::class, 'updateKeys']);
    Route::post('settings/notification', [AdminSettingsController::class, 'updateNotification']);
    Route::post('maintenance/migrate', [AdminMaintenanceController::class, 'migrate']);
    Route::post('maintenance/clear-cache', [AdminMaintenanceController::class, 'clearCache']);
    Route::get('users',  [AdminController::class,'index_user']);
    Route::get('view-user',  [AdminController::class,'view_user']);
    Route::any('update-user',  [AdminController::class,'update_user']);
    Route::any('remove-user',  [AdminController::class,'remove_user']);
    Route::any('delete-user',  [AdminController::class,'delete_user']);
    Route::get('search-user',  [AdminController::class,'search_user']);
    Route::any('search-username',  [AdminController::class,'search_username']);
    Route::get('manual-payment',  [AdminController::class,'manual_payment_view']);
    Route::any('verify-payment',  [AdminController::class,'approve_payment']);
    Route::any('update-acct-name',  [AdminController::class,'update_acct_name']);
    Route::any('delete-payment',  [AdminController::class,'delete_payment']);
    Route::any('update-smspool-rate',  [AdminController::class,'update_smspool_rate']);
    Route::any('update-smspool-cost',  [AdminController::class,'update_smspool_cost']);
    Route::any('update-sim-rate',  [AdminController::class,'update_sim_rate']);
    Route::any('update-sim-cost',  [AdminController::class,'update_sim_cost']);
});



Route::any('fund-manual-now',  [HomeController::class,'fund_manual_now']);
Route::any('confirm-pay',  [HomeController::class,'confirm_pay']);


Route::any('about-us',  [HomeController::class,'about_us']);
Route::any('policy',  [HomeController::class,'policy']);














Route::post('edit-front-pr',  [AdminController::class,'edit_front_product']);

















//product

Route::post('buy-now',  [ProductController::class,'buy_now']);
Route::post('item-view',  [ProductController::class,'item_view']);

Route::get('item-view',  [ProductController::class,'i_view']);

Route::get('allcatproduct',  [ProductController::class,'view_all_product']);

Route::post('add-new-product',  [ProductController::class,'add_new_product']);

Route::post('add-front-product',  [ProductController::class,'add_front_product']);

Route::get('detete-front-product',  [ProductController::class,'delete_front_product']);


Route::post('edit-new-product',  [ProductController::class,'edit_front_product']);


//Route::get('view-all',  [ProductController::class,'view_all_product']);


































