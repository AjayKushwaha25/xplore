<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Validator, Storage};
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Models\{LoginHistory, User, Role, RewardItem, WD, City, Region};
use DataTables;
use Carbon\Carbon;
use File;
use ZipArchive;
use NavHelper;
use CustomHelper;

class AdminController extends Controller
{
    public function index(Request $request){
        $cityLists = City::with(
            'wds:id,code,city_id',
            )
            ->orderBy('name','asc')
            ->get(['id','name']);
        $wd_code = $request->get('wd_code');

        $loginHistories = LoginHistory::with([
                'retailer:id,name,mobile_number',
                'qRCodeItem:id,serial_number,reward_item_id,wd_id',
                'qRCodeItem.rewardItem:id,value'
            ])
            ->when($wd_code, function ($query) use ($wd_code) {
                $query->whereHas('qRCodeItem.wd',function ($query) use ($wd_code){
                        $query->where('code', $wd_code);
                    });
            })
            ->select('id','retailer_id','q_r_code_item_id','created_at')
            ->latest()
            ->get();


        // dd($loginHistories->count());
        /*if($wd_code == "all" || $wd_code == null){
            $loginHistories = $loginHistories->get();
        }
        else{
            $loginHistories =
                ->get();
        }*/

        $topScannedUsers = $loginHistories
            ->groupBy('retailer_id')
            ->map(function ($histories, $retailerId) {
                return [
                    'retailerId' => $retailerId,
                    'name' => $histories->first()->retailer->name,
                    'mobile_number' => $histories->first()->retailer->mobile_number,
                    'count' => $histories->count(),
                    
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        $scannedHistories = $loginHistories->take(10);

        $data = [
            'coupons' => RewardItem::orderBy('value')->get(['id','value']),
            'scannedHistories' => $scannedHistories,
            'topScannedUsers' => $topScannedUsers,
            'cityLists' => $cityLists,
        ];

        return view('admin.index', compact('data'));
    }

    public function getCouponCount(Request $request){
        $wd_code = $request->get('wd_code');
        
        $counts = NavHelper::getCouponCounts($wd_code);
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

        $region = Region::all();
        // dd($region);

        $data = [
            'tables' => $tables,
            'ignoreTables' => CustomHelper::ignoredTables(),
            'region' => $region,
        ];

        // dd($data);

        return view('admin/export')->with('data',$data);
    }
}
