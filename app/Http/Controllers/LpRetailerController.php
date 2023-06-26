<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreLpRetailerRequest;
use App\Http\Requests\UpdateLpRetailerRequest;

use App\Models\{ CouponCode, Retailer, RewardItem, LpRetailer };

class LpRetailerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('lp_registration');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLpRetailerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLpRetailerRequest $request)
    {
        $retailer = LpRetailer::updateOrCreate([
            'mobile_number'=>$request->validated('mobile_number')
        ],[
            'name'  => $request->validated('name'),
            'whatsapp_number'   => $request->validated('whatsapp_number'),
            'upi_id'    => $request->validated('upi_id')
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

   

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LpRetailer  $lpRetailer
     * @return \Illuminate\Http\Response
     */
    public function show(LpRetailer $lpRetailer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LpRetailer  $lpRetailer
     * @return \Illuminate\Http\Response
     */
    public function edit(LpRetailer $lpRetailer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLpRetailerRequest  $request
     * @param  \App\Models\LpRetailer  $lpRetailer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLpRetailerRequest $request, LpRetailer $lpRetailer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LpRetailer  $lpRetailer
     * @return \Illuminate\Http\Response
     */
    public function destroy(LpRetailer $lpRetailer)
    {
        //
    }

    public function getGenerateNumber(){
        $randomNumber = random_int(1, 5500);
        return $randomNumber;
    }

    public function thankYou(){
        return view('thank-you');
    }
}
