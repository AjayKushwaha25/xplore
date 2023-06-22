<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Retailer, RewardItem, QRCodeItem, LoginHistory};
use App\Http\Requests\{StoreRetailerRequest};
use Auth;

class RegisterController extends Controller
{
    public function register(StoreRetailerRequest $request){
        $qrCodeItem = QRCodeItem::whereId($request->validated('uid'))->whereIsRedeemed(0)->first();
        if(!$qrCodeItem){
            return redirect()->route('sign_up',['uid'=>$request->validated('uid')]);
        }
        $retailer = Retailer::create($request->validated());
        if(!$retailer){
            return back()->with([
                'status' => 'failed',
                'message' => 'Something went wrong, please try again later',
            ]);
        }

        if (Auth::guard('retailer')->loginUsingId($retailer->id)) {
            $rewardValue = RewardItem::whereId($qrCodeItem->reward_item_id)->whereStatus(1)->value('value');
            if($rewardValue){
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
                $retailer->delete();
                return back()->with([
                    'status' => 'failed',
                    'message' => 'Invalid Coupon.'
                ]);
            }
        }
    }
}
