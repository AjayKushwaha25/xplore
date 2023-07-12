<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{QRCodeItem, LoginHistory, Payout};
use Auth;
use App\Actions\CalculatePendingPayoutAction;

class PagesController extends Controller
{
    public function login(Request $request){
        // dd($request->k);
        $data = [
            'status' => $request->status ?? '',
            'message' => $request->message ?? ''
        ];
        $qrcodeId = $request->get('uid');
        $serialNumber = $request->get('serial_number');
        $couponCode = $request->get('coupon_code');

        return view('login', compact('qrcodeId', 'data', 'serialNumber', 'couponCode'));
    }

    public function register(Request $request){
        $data = [
            'status' => $request->status ?? '',
            'message' => $request->message ?? ''
        ];
        $qrcodeId = $request->get('uid');
        $serialNumber = $request->get('serial_number');
        $couponCode = $request->get('coupon_code');

        return view('signup', compact('qrcodeId', 'data', 'serialNumber', 'couponCode'));
    }

    public function reward(){
        return view('reward');
    }

    public function checkBalanceLogin(){
        return view('check_balance.login');
    }

    public function history(CalculatePendingPayoutAction $calculatePendingPayoutAction){
        $retailerId = \Auth::guard('retailer')->id();

        $data = $calculatePendingPayoutAction->handle($retailerId);

        return view('check_balance.home', compact('data'));
    }

    public function getLoginHistoryModalData(Request $request){
        // $loginHistoryid = $request->all();
        $loginHistoryid = $request->get('lH_id');
        // dd( $loginHistoryid);
               // $loginHistoryid =$this->input->post('lH_id');

        // $info = $request->get('lH_id');
        // return $info;
        // dd($loginHistoryid);
        $loginHistory = LoginHistory::with([
            'qRCodeItem:id,reward_item_id,serial_number',
            'qRCodeItem.rewardItem:id,value',
            'retailer:id,upi_id',
        ])
        ->where('id',$loginHistoryid)
        ->latest()
        ->select('id', 'q_r_code_item_id', 'retailer_id', 'created_at')
        ->first();
        // dd($loginHistory->qRcodeItem->serial_number);
        // exit;

        $payouts = Payout::with('loginHistory.qRCodeItem.rewardItem:id,value')
        ->where('login_history_id', $loginHistoryid)
        ->select(['id', 'login_history_id', 'utr', 'status', 'reason', 'processed_at', 'created_at'])
        ->first();
        
        // dd( $payouts);
        // exit;

        return response()->json([
            'status' => 'success',
            'message' => 'data get',
            'data' => $loginHistory,
            'payouts' => $payouts,
            
        ]);
    } 


    public function logout(){
        Auth::guard('retailer')->logout();
        return redirect()->route('login')->with([
            'status' => 'failed',
            'message' => 'User logged out!'
        ]);
    }


}
