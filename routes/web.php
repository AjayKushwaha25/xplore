<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PagesController, LoginController,RegisterController, CouponCodeController,LpRetailerController};
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\{UserController, BackendController, AdminController, QRCodeItemController, RetailerController, LoginHistoryController, PayoutController,RazorpayController};

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
Route::get('qrcode-gen', function(){
    $generated = \App\Models\QRCodeItem::where('is_qr_code_generated',1)->count();
    return $generated - 7512;
});
Route::get('qrcode-count', function() {
    $wds = [
        "AH2062",
        "AH3038",
        "AH3132",
        "AH3141",
        "BL5146",
        "BL5323",
        "BL5439",
        "BL5644",
        "CH5068",
        "CH5086",
        "NG3054",
        "NG3104",
        "NG3160",
        "PU2224",
        "PU2500",
        "PU4031",
    ];
    $rewards = [
        50,100,200
    ];
    $data = [];
    $totalCount = 0;
    foreach($wds as $wd){
        $data[$wd]['total'] = 0;
        foreach($rewards as $reward){
            $count = count(\Storage::disk('gcs')->files("coupons/A/{$wd}/{$reward}"));
            $data[$wd][$reward] = $count;
            $data[$wd]['total'] += $count;
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
        Route::get('getLoginHistories', [AdminController::class, 'getLoginHistories'])->name('get_loginHistories');
        Route::get('coupon-count', [AdminController::class, 'getCouponCount'])->name('get_coupon_count');

        Route::post('qr-code-bulk-update', [QRCodeItemController::class, 'bulkUpdate'])->name('qr-codes.bulk-update');
        Route::get('qrCodesList', [QRCodeItemController::class, 'qrCodesList'])->name('qr_code_lists');
        Route::post('generate-qrcode', [QRCodeItemController::class, 'generateBulkQRCode'])->name('qr-codes.generate-qrcode');
        Route::post('bulk-store', [QRCodeItemController::class, 'bulkStore'])->name('qr-codes.bulk_store');
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
    
        // Razorpay
        Route::get('razorpay/index', [RazorpayController::class, 'index'])->name('razorpay.index');
        Route::post('razorpay/exportData', [RazorpayController::class, 'exportData'])->name('razorpay.exportData');
    });
    
});
