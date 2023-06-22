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
            'counts' => NavHelper::getCounts(),
            'coupons' => RewardItem::orderBy('value')->get(['id','value']),
            'scannedHistories' => $scannedHistories,
            'topScannedUsers' => $topScannedUsers,
        ];

        return view('admin.index', compact('data'));
    }


    public function generateQRCode(){
        $data = [
            // 'counts' => NavHelper::getCounts(),
        ];
        return view('admin.view.generate_qr_code')->with('data',$data);
    }
    public static function directoryLists($qrCodePublicPath){
        // return DIRECTORY_SEPARATOR;
        $result = [];

        $currentDirectory = scandir($qrCodePublicPath);
        // return $currentDirectory;
        $id = 1;
        foreach ($currentDirectory as $key => $value)
        {
            if(!in_array($value, array(".","..")))
            {
                $fullPath = $qrCodePublicPath . DIRECTORY_SEPARATOR . $value;
                // $id = $key;
                // dd($fullPath, Carbon::parse(filemtime($fullPath))->format('d-m-Y h:i A'));
                /*
                $fullPath = $qrCodePublicPath . DIRECTORY_SEPARATOR . $value;
                if (is_dir($fullPath))
                {
                     $result[$fullPath] = self::directoryLists($fullPath);
                }
                else
                {
                    $result[] = $fullPath;
                }*/
                if(is_dir($fullPath)){
                    $result2['sr_no'] = $id;
                    $result2['folder_name'] = $value;
                    $result2['created_at'] = Carbon::parse(filemtime($fullPath))->format('d-m-Y h:i A');
                    $result[$id++] = $result2;
                }
            }
       }

       return $result;
    }

    public function export(){
        $tables = [
            'payouts',
            'retailers',
            'login_histories',
        ];
        // dd($tables);
        $data = [
            // 'counts' => NavHelper::getCounts(),
            // 'tables' => CustomHelper::getAllTables(),
            'tables' => $tables,
            'ignoreTables' => CustomHelper::ignoredTables(),
        ];
        // dd($data['tables']);
        return view('admin/export')->with('data',$data);
    }

    /* View */

    /* Add */

    /* Edit View */

}
