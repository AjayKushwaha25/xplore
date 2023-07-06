<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Payout,LoginHistory};
use App\Imports\PayoutImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ImportRequest;
use DataTables;

class PayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $payouts = Payout::get();
        return view('admin.view.payouts');
    }

    public function payoutsList(Request $request){
        $payouts = Payout::query()
                        ->with([
                            'loginHistory:id,q_r_code_item_id,retailer_id',
                            'loginHistory.retailer:id,name,mobile_number',
                            'loginHistory.qRCodeItem:id,serial_number,reward_item_id',
                            'loginHistory.qRCodeItem.rewardItem:id,value',
                            'loginHistory.qRCodeItem.wd:id,code'
                        ])
                        ->select('id','login_history_id','utr','status','reason','processed_at')
                        ->get();

        return Datatables::of($payouts)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function bulkUpload(ImportRequest $request){
        // dd($request->validated());
        try {
            $payoutImport = new PayoutImport;
            $importDataArr = Excel::import($payoutImport,$request->validated('file'));
            $totalDataCount = $payoutImport->getRowCount();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $dataError['rows'] = $failure->row();
                $dataError['attribute'] = $failure->attribute();
                $dataError['errors'] = $failure->errors();
                $dataError['values'] = $failure->values();
            }
            return back()->with('upload-failed',$dataError);
        }
        return back()->with('upload-success','File uploaded successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function show(Payout $payout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function edit(Payout $payout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payout $payout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payout  $payout
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payout $payout)
    {
        //
    }

    public function payoutImport()
    {
    }

    public function getPayoutCount(){
   
        $totalPayout = LoginHistory::with([
            'qRCodeItem:id,reward_item_id',
            'qRCodeItem.rewardItem:id,value'
        ])->select('q_r_code_item_id')->get();
        $totalPayoutAmount=0;
        foreach ($totalPayout as $totalPayout) {
            $totalPayoutAmount += $totalPayout->qRCodeItem->rewardItem->value;
            }
        
        $status1 = 1;
        $successPayout = Payout::with([
        'loginHistory:id,q_r_code_item_id',
        'qRCodeItem:id,reward_item_id',
        'qRCodeItem.rewardItem:id,value'
        ])->select('login_history_id')->where('status',$status1)->get();
        
        $successPayoutAmount = 0;
        foreach ($successPayout as $successPayout) {
            $successPayoutAmount += $successPayout->loginHistory->qRCodeItem->rewardItem->value;
            }
           
        $status2 = 0;
        $failedPayout = Payout::with([
            'loginHistory:id,q_r_code_item_id',
            'qRCodeItem:id,reward_item_id',
            'qRCodeItem.rewardItem:id,value'
        ])->select('login_history_id')->where('status',$status2)->get();
    
        $failedPayoutAmount = 0;
        foreach ($failedPayout as $failedPayout) {
            $failedPayoutAmount += $failedPayout->loginHistory->qRCodeItem->rewardItem->value;
            }
    
        $status3 = 2;
      
        $pendingPayout = Payout::with([
            'loginHistory:id,q_r_code_item_id',
            'qRCodeItem:id,reward_item_id',
            'qRCodeItem.rewardItem:id,value'
        ])->select('login_history_id')->where('status',$status3)->get();

        $pendingPayoutAmount = 0;
        foreach ($pendingPayout as $pendingPayout) {
            $pendingPayoutAmount += $pendingPayout->loginHistory->qRCodeItem->rewardItem->value;
            }

        $data = [
            'totalPayoutAmount' => $totalPayoutAmount,
            'successPayoutAmount' => $successPayoutAmount,
            'failedPayoutAmount' => $failedPayoutAmount,
            'pendingPayoutAmount' => $pendingPayoutAmount,
        ];

        return $data;
 
    }

    public function viewSuccessPayout()
    {
        // $payouts = Payout::get();
        return view('admin.view.success-payout');
    }

    public function getSuccessPayout(){
        $status=1;
        $success_payouts = Payout::query()
        ->with([
            'loginHistory:id,q_r_code_item_id,retailer_id',
            'loginHistory.retailer:id,name,mobile_number',
            'loginHistory.qRCodeItem:id,serial_number,reward_item_id',
            'loginHistory.qRCodeItem.rewardItem:id,value',
            'loginHistory.qRCodeItem.wd:id,code'
        ])
        ->select('id','login_history_id','utr','status','reason','processed_at')
        ->where('status',$status)
        ->get();
            // dd($success_payouts);
        return Datatables::of($success_payouts)->make(true);
    }


    public function viewFailedPayout()
    {
        return view('admin.view.failed-payout');
    }
    public function getFailedPayout(){
        $status=0;
        $failed_payouts = Payout::query()
        ->with([
            'loginHistory:id,q_r_code_item_id,retailer_id',
            'loginHistory.retailer:id,name,mobile_number',
            'loginHistory.qRCodeItem:id,serial_number,reward_item_id',
            'loginHistory.qRCodeItem.rewardItem:id,value',
            'loginHistory.qRCodeItem.wd:id,code'
        ])
        ->select('id','login_history_id','utr','status','reason','processed_at')
        ->where('status',$status)
        ->get();
            // dd($success_payouts);
        return Datatables::of($failed_payouts)->make(true);
    }

    public function viewPendingPayout()
    {
        return view('admin.view.pending-payout');
    }
    public function getPendingPayout(){
        $status=2;
        $pending_payouts = Payout::query()
        ->with([
            'loginHistory:id,q_r_code_item_id,retailer_id',
            'loginHistory.retailer:id,name,mobile_number',
            'loginHistory.qRCodeItem:id,serial_number,reward_item_id',
            'loginHistory.qRCodeItem.rewardItem:id,value',
            'loginHistory.qRCodeItem.wd:id,code'
        ])
        ->select('id','login_history_id','utr','status','reason','processed_at')
        ->where('status',$status)
        ->get();
            // dd($pending_payouts);
        return Datatables::of($pending_payouts)->make(true);
    }
}
