<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{RetailerRequest, RetailerCheckBalanceRequest};
use App\Models\{Retailer, RewardItem, QRCodeItem, LoginHistory, Payout};
use Auth;

class LoginController extends Controller
{
    public function login(RetailerRequest $request)
    {
        $retailerId = Retailer::where('mobile_number', $request->validated('mobile_number'))->value('id');
        if(!$retailerId){
            return back()->with([
                'status' => 'failed',
                'message' => 'User does not exists',
            ]);
        }

        /* Check Daily Limit */
        /*$latestCreatedAtDateForUser = LoginHistory::whereRetailerId($retailerId)->latest()->value('created_at');
        if($latestCreatedAtDateForUser){
            if($latestCreatedAtDateForUser->toDateString() == now()->toDateString()){
                return back()->with([
                    'status' => 'failed',
                    'message' => 'Your daily limit is exceeded.'
                ]);
            }
        }*/

        if (Auth::guard('retailer')->loginUsingId($retailerId)) {
            $qrCodeItem = QRCodeItem::whereId($request->validated('uid'))->whereIsRedeemed(0)->first();
            if(!$qrCodeItem){
                return redirect()->route('login',['uid'=>$request->validated('uid')]);
            }
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
                return back()->with([
                    'status' => 'failed',
                    'message' => 'Invalid Coupon.'
                ]);
            }
        }
    }

    public function checkBalanceLogin(RetailerCheckBalanceRequest $request){
        $retailerId = Retailer::where('mobile_number',  $request->validated('mobile_number') )->value('id');
        if(!$retailerId){
            return back()->with([
                'status' => 'failed',
                'message' => 'User does not exists',
            ]);
        }

        if (Auth::guard('retailer')->loginUsingId($retailerId)) {
            return redirect()->route('check_balance.history');
        }else{
            return back()->with([
                'status' => 'failed',
                'message' => 'Something went wrong. Please try again after sometime.'
            ]);
        }
    }
}
