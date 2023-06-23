<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{RetailerRequest, StoreRetailerRequest};
use App\Models\{CouponCode, Retailer, RewardItem, CouponCodeHistory};

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

        $couponCode = CouponCode::create([
        	'code' => $this->getGenerateNumber(),
        	'reward_item_id' => RewardItem::inRandomOrder()->value('id')
        ]);

        CouponCodeHistory::create([
        	'retailer_id' => $retailer->id,
        	'coupon_code_id' => $couponCode->id,
        ]);

        // return redirect()->route('thank_you');
        return view('thank-you');

    }

    public function getGenerateNumber(){

        $randomNumber = random_int(1, 5500);
        // dd($randomNumber);
        return $randomNumber;
    }
}
