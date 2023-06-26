<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{StoreCCRetailerRequest};
use App\Models\{CouponCode, Retailer, RewardItem};

class CouponCodeController extends Controller
{
    public function index(){
        return view('lp_registration');
    }

    public function store(StoreCCRetailerRequest $request)
    {
        // $retailer = Retailer::firstOrCreate($request->validated());
        $retailer = Retailer::updateOrCreate([
            'mobile_number' => $request->validated('mobile_number')
        ],[
            'name' => $request->validated('name'),
            'whatsapp_number' => $request->validated('whatsapp_number'),
            'upi_id' => $request->validated('upi_id'),
        ]);
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

        $retailer->couponCodes()->attach($couponCode->id);

        return redirect()->route('thank_you')->with([
            'status' => 'success',
            'message' => 'Cashback will be credited withtin 24 Hours.'
        ]);
    }

    public function getGenerateNumber(){
        $randomNumber = random_int(1, 5500);
        return $randomNumber;
    }

    public function thankYou(){
        return view('thank-you');
    }
}
