<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PagesController, LoginController,RegisterController, CouponCodeController,LpRetailerController};
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\{UserController, BackendController, AdminController, QRCodeItemController, RetailerController, LoginHistoryController, PayoutController};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('loginhistorymodal', [PagesController::class, 'getLoginHistoryModalData'])->name('history_modal');
Route::get('qrcode-count', function() {
    $wds = [
        'NG3160',
        'NG3104',
        'BH3072',
        'BH3041',
        'NG3054',
        'NG2949',
        'PU2224',
        'PU4031',
        'AH3132',
        'AH3123'
    ];
    $rewards = [
        50,100,200
    ];
    $data = [];
    $totalCount = 0;
    foreach($wds as $wd){
        foreach($rewards as $reward){
            $count = count(\Storage::disk('gcs')->files("coupons/{$wd}/{$reward}"));
            $data[$wd][$reward] = $count;
            $totalCount += $count;
            $data['total_count'] = $totalCount;
        }
    }
    // dd($data);
    return $data;
});

Route::get('/coupon/register', [LpRetailerController::class, 'create'])->name('lp_retailer.create');
Route::post('/register/store', [LpRetailerController::class, 'store'])->name('lp_retailer.store');

Route::get('thank-you', [LpRetailerController::class, 'thankYou'])->name('thank_you');

Route::group(['middleware' => ['redirect.url']], function() {
    Route::get('/', [PagesController::class, 'login'])->name('redirect_login');
});
Route::group(['middleware' => ['verify.url','retailer.logout']], function() {
    Route::get('/login', [PagesController::class, 'login'])->name('login');
    Route::get('/register', [PagesController::class, 'register'])->name('sign_up');
});
Route::get('/logout', [PagesController::class, 'logout'])->name('logout');

/* POST */

Route::group(['as' => 'check.'], function() {
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('register', [RegisterController::class, 'register'])->name('register');

});

Route::group(['prefix' => 'check_balance', 'as' => 'check_balance.'], function() {
    Route::get('/login', [PagesController::class, 'checkBalanceLogin'])->name('login');
    Route::post('/check-login',[LoginController::class, 'checkBalanceLogin'])->name('check.login');

    Route::group(['middleware' => ['auth:retailer']], function() {
        Route::get('/history', [PagesController::class, 'history'])->name('history');
    });
});


Route::group(['middleware' => ['auth:retailer']], function() {
    Route::get('/reward', [PagesController::class, 'reward'])->name('reward');
});

/* Admin Routes */
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [AdminLoginController::class, 'login']);
    Route::get('login', [AdminLoginController::class, 'login'])->name('login');
    Route::get('logout', [AdminLoginController::class, 'logout'])->name('logout');
    Route::get('register', [AdminLoginController::class, 'register'])->name('register');
    Route::post('checkAdminLogin', [BackendController::class, 'checkAdminLogin'])->name('check_login');
    Route::post('registerAdmin', [BackendController::class, 'registerAdmin']);
    Route::get('public/downloadZip/{path}', [BackendController::class, 'downloadZip']);
    Route::get('public/downloadZipForPrintableFiles/{path}', [BackendController::class, 'downloadZipForPrintableFiles']);

    /* Following routes require Auth */
    Route::group(['middleware'=>['auth']], function () {
        Route::get('home', [AdminController::class, 'index'])->name('home');

        Route::post('qr-code-bulk-update', [QRCodeItemController::class, 'bulkUpdate'])->name('qr-codes.bulk-update');
        Route::get('qrCodesList', [QRCodeItemController::class, 'qrCodesList'])->name('qr_code_lists');
        Route::post('generate-qrcode', [QRCodeItemController::class, 'generateBulkQRCode'])->name('qr-codes.generate-qrcode');
        Route::resource('qr-codes', QRCodeItemController::class);

        Route::get('generate-qr-code', [AdminController::class, 'generateQRCode']);
        /**/
        Route::post('qrcode', [BackendController::class, 'qrCodeGenerate'])->name('generate_qrcode');
        Route::get('downloadZip/{path}', [BackendController::class, 'downloadZip']);
        Route::get('downloadZipForPrintableFiles/{path}', [BackendController::class, 'downloadZipForPrintableFiles']);
        Route::get('canvas/{folder_name}', [BackendController::class, 'generateImage']);
        /**/

        Route::get('export', [AdminController::class, 'export']);
        Route::post('exportData', [BackendController::class, 'exportData']);

        /* Admin */
        Route::resource('users', UserController::class);

       

        /* Payouts */
        // Route::post('qr-code-bulk-update', [PayoutController::class, 'bulkUpdate'])->name('qr-codes.bulk-update');
        // Route::get('qrCodesList', [PayoutController::class, 'qrCodesList'])->name('qr_code_lists');
        Route::get('payout-upload', [PayoutController::class, 'payoutImport'])->name('payout_upload');
        Route::get('payoutsList', [PayoutController::class, 'payoutsList'])->name('payout_lists');
        Route::post('bulk-payout-upload', [PayoutController::class, 'bulkUpload'])->name('bulk_payout_upload');
        Route::resource('payouts', PayoutController::class);
        Route::get('payouts-count', [PayoutController::class, 'getPayoutCount'])->name('payout_count');



        /* Retailer */
        Route::get('retailer-count', [RetailerController::class, 'getRetailerCount'])->name('retailer_count');
        Route::get('retailersList', [RetailerController::class, 'retailersList'])->name('retailer_lists');
        Route::resource('retailers', RetailerController::class);

        Route::get('loginHistoriesList', [LoginHistoryController::class, 'loginHistoriesList'])->name('login_history_lists');
        Route::get('login-histories', [LoginHistoryController::class, 'index'])->name('login-histories');
        Route::get('lp_retailer', [LpRetailerController::class, 'index'])->name('lpretailer_index');
        Route::get('lp_retailer_lists', [LpRetailerController::class, 'lpRetailersList'])->name('lpretailer_lists');
        Route::get('lpretailer_histories', [LpRetailerController::class, 'viewLoginHistory'])->name('lpretailer_histories_index');
        Route::get('lpretailer_history_lists', [LpRetailerController::class, 'lpRetailerHistoryLists'])->name('lpretailer_history_list');
    });
    
});
