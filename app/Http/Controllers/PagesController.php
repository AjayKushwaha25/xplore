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

    public function logout(){
        Auth::guard('retailer')->logout();
        return redirect()->route('login')->with([
            'status' => 'failed',
            'message' => 'User logged out!'
        ]);
    }


}
