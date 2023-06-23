<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{RetailerRequest, StoreRetailerRequest};
use App\Models\{CouponCode, Retailer, RewardItem, QRCodeItem, LoginHistory, Payout};

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

        $code = new CouponCode;

        $code->code = $this->getGenerateNumber();
        $code->save();
    }

    public function getGenerateNumber(){

        $randomNumber = random_int(1, 5500);
        // dd($randomNumber);
        return $randomNumber;
    }
}
