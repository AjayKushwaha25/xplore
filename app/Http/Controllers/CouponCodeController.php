<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{RetailerRequest, StoreRetailerRequest};
use App\Models\{Retailer, RewardItem, QRCodeItem, LoginHistory, Payout};

class CouponCodeController extends Controller
{
    public function coupon(StoreRetailerRequest $request){
    
        $retailer = Retailer::create($request->validated());
        if(!$retailer){
            return back()->with([
                'status' => 'failed',
                'message' => 'Something went wrong, please try again later',
            ]);
        }
    }
}
