<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreLpRetailerRequest;
use App\Http\Requests\UpdateLpRetailerRequest;
use DataTables;

use App\Models\{ CouponCode, Retailer, RewardItem, LpRetailer,CouponCodeHistory };

class LpRetailerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.view.lp_retailer');
    }

    public function lpRetailersList(){
        $lp_retailers = LpRetailer::query();
        return Datatables::of($lp_retailers)->make(true);
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

        $couponCodeValue = CouponCode::with('rewardItem:id,value')->where('is_redeemed', 0)->inRandomOrder()->first(['id','code','reward_item_id']);

        if(!$couponCodeValue){
            return back()->with([
                'status' => 'failed',
                'message' => 'Coupon Code does not exists.'
            ])->withInput();
        }

        $rewardValue = $couponCodeValue->rewardItem->value;

        $retailer->couponCodes()->attach($couponCodeValue->id);

        $couponCodeValue->update([
            'is_redeemed' => 1
        ]);

        return redirect()->route('thank_you')->with([
            'status' => 'success',
            'message' => 'Cashback will be credited withtin 24 Hours.',
            // 'status' => 'success',
            'data' => [
                'img_path' => "coin{$rewardValue}.png",
                'value' => $rewardValue,
            ],
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

    public function viewLoginHistory(){
        return view('admin.view.lp_retailer_histories');
    }

    public function lpRetailerHistoryLists(){
        $loginHistories = CouponCodeHistory::with([
            'lpRetailer:id,name',
            'couponCode:id,code,reward_item_id',
            'couponCode.rewardItem:id,value'
        ])
        ->select('lp_retailer_id','coupon_code_id','coupon_code_histories.created_at');


        // dd($loginHistories->get());
        return Datatables::of($loginHistories)->make(true);

    }
}
