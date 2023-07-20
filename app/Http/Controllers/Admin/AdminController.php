<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Validator, Storage};
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Models\{LoginHistory, User, Role, RewardItem};
use DataTables;
use Carbon\Carbon;
use File;
use ZipArchive;
use NavHelper;
use CustomHelper;

class AdminController extends Controller
{
    public function index(){


        $loginHistories = LoginHistory::with([
                'retailer:id,name,mobile_number',
                'qRCodeItem:id,serial_number,reward_item_id',
                'qRCodeItem.rewardItem:id,value'
            ])
            ->select('id','retailer_id','q_r_code_item_id','created_at')
            ->latest()
            ->get();

        $topScannedUsers = $loginHistories
            ->groupBy('retailer_id')
            ->map(function ($histories, $retailerId) {
                return [
                    'retailerId' => $retailerId,
                    'name' => $histories->first()->retailer->name,
                    'mobile_number' => $histories->first()->retailer->mobile_number,
                    'count' => $histories->count()
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        $scannedHistories = $loginHistories->take(10);

        $data = [
            // 'counts' => NavHelper::getCouponCounts(),
            'coupons' => RewardItem::orderBy('value')->get(['id','value']),
            'scannedHistories' => $scannedHistories,
            'topScannedUsers' => $topScannedUsers,
        ];

        return view('admin.index', compact('data'));
    }

    public function getCouponCount(Request $request){
        $counts = NavHelper::getCouponCounts();
        return response()->json(['couponCounts' => $counts]);

    }
    

    public function generateQRCode(){
        $data = [
            // 'counts' => NavHelper::getCouponCounts(),
        ];
        return view('admin.view.generate_qr_code')->with('data',$data);
    }

    public function export(){
        $tables = [
            'payouts',
            'retailers',
            'login_histories',
            'lp_retailers',
            'coupon_code_histories',
            'master_report'
        ];

        $data = [
            'tables' => $tables,
            'ignoreTables' => CustomHelper::ignoredTables(),
        ];

        return view('admin/export')->with('data',$data);
    }
}
