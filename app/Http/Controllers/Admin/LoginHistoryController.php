<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{LoginHistory,DummyLoginHistory};
use Illuminate\Http\Request;
use DataTables;

class LoginHistoryController extends Controller
{
    public function index()
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
            // 'users' => Retailer::get(),
        ];
        return view('admin.view.login-histories')->with('data',$data);
    }

    public function loginHistoriesList(Request $request){
        $wd_code = $request->get('wd_code');
        if($wd_code == "all" || $wd_code == null){
            $loginHistories = LoginHistory::with([
                'retailer:id,name',
                'qRCodeItem:id,serial_number,reward_item_id,wd_id',
                'qRCodeItem.rewardItem:id,value'
            ])
            ->select('login_histories.id','ip_address','retailer_id','q_r_code_item_id','login_histories.created_at');
        }
        else{
            $loginHistories = LoginHistory::with([
                                            'retailer:id,name',
                                            'qRCodeItem:id,serial_number,reward_item_id,wd_id',
                                            'qRCodeItem.rewardItem:id,value'
                                        ])
                                        ->whereHas('qRCodeItem.wd',function ($query) use ($wd_code){
                                            $query->where('code', $wd_code);
                                        })
                                        ->select('login_histories.id','ip_address','retailer_id','q_r_code_item_id','login_histories.created_at');

        }
        // dd($loginHistories->get());
        return Datatables::of($loginHistories)->make(true);
    }

    public function dummy_login_histories()
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
            // 'users' => Retailer::get(),
            'loginHistory' => DummyLoginHistory::get(),
        ];
        return view('admin.view.dummy-login-histories')->with('data',$data);
    }
}
