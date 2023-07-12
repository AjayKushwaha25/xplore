<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Retailer, RewardItem, QRCodeItem, LoginHistory};
use App\Http\Requests\{StoreRetailerRequest};
use Auth;

class RegisterController extends Controller
{
    public function register(StoreRetailerRequest $request){
        $qrCodeItem = QRCodeItem::whereId($request->validated('uid'))->whereCouponCode($request->validated('coupon_code'))->first();
        if(!$qrCodeItem){
            return back()->withInput()->with([
                    'status' => 'failed',
                    'message' => 'Enter valid Coupon Code.'
                ]);
        }

        $retailer = Retailer::create($request->validated());
        if(!$retailer){
            return back()->withInput()->with([
                'status' => 'failed',
                'message' => 'Something went wrong, please try again later',
            ]);
        }

        $rewardValue = RewardItem::whereId($qrCodeItem->reward_item_id)->whereStatus(1)->value('value');

        if(!$rewardValue){
            return back()->withInput()->with([
                'status' => 'failed',
                'message' => 'Invalid Coupon.'
            ]);
        }

        if (Auth::guard('retailer')->loginUsingId($retailer->id)) {
            $qrCodeItem->is_redeemed = 1;
            $qrCodeItem->update();
            $data = [
                'retailer_id' => Auth::guard('retailer')->id(),
                'q_r_code_item_id' => $request->validated('uid'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $loginHistory = LoginHistory::create($data);

            return redirect()->route('reward')->with([
                'status' => 'success',
                'data' => [
                    'img_path' => "coin{$rewardValue}.png",
                    'value' => $rewardValue,
                ]
            ]);
        }else{
            return back()->withInput()->with([
                'status' => 'failed',
                'message' => 'Unable to login. Please try again later.'
            ]);
        }
    }
}
