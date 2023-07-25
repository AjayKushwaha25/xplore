<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Payout,LoginHistory};
use App\Imports\PayoutImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ImportRequest;
use DataTables;
// use Illuminate\Contracts\Database\Eloquent\Builder;

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

        $status = $request->get('status');
        $wd_code = $request->get('wd_code');

        if($wd_code == "all" || $wd_code == null){
            $payout = Payout::query()
            ->with([
                'loginHistory:id,q_r_code_item_id,retailer_id',
                'loginHistory.retailer:id,name,mobile_number',
                'loginHistory.qRCodeItem:id,serial_number,reward_item_id,wd_id',
                'loginHistory.qRCodeItem.rewardItem:id,value',
                'loginHistory.qRCodeItem.wd:id,code'
            ])
            ->select('id','login_history_id','utr','status','reason','processed_at');
        }
        else{
            $payout = Payout::query()
                        ->with([
                            'loginHistory:id,q_r_code_item_id,retailer_id',
                            'loginHistory.retailer:id,name,mobile_number',
                            'loginHistory.qRCodeItem:id,serial_number,reward_item_id,wd_id',
                            'loginHistory.qRCodeItem.rewardItem:id,value',
                            'loginHistory.qRCodeItem.wd:id,code'
                        ])
                        ->whereHas('loginHistory.qRCodeItem.wd',function ($query) use ($wd_code){
                            $query->where('code', $wd_code);
                        })
                        ->select('id','login_history_id','utr','status','reason','processed_at');
                    }    

        switch ($status) {
            case 'success':
                $payouts =  $payout->where('status',1)->get();
                   
                break;
            case 'failed':
                $payouts =  $payout->where('status',0)->get();
                   
                break;
            case 'pending':
                $payouts =  $payout->where('status',2)->get();
                   
                break;
            default:
                $payouts = $payout->get();
                break;
        }
        // dd($payouts);

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

    public function getPayoutCount(Request $request){
        $status = $request->get('status');
        $wd_code = $request->get('wd_code');
        // dd($wd_id);
        $payoutAmount=0;
        if($wd_code == "all" || $wd_code == null){
        $payout = Payout::with([
                'loginHistory:id,q_r_code_item_id',
                'loginHistory.qRCodeItem:id,reward_item_id,wd_id',
                'loginHistory.qRCodeItem.rewardItem:id,value',
                'loginHistory.qRCodeItem.wd:id'
            ])
            ->select('login_history_id');
            // dd($payout);
            // $value= $payout->qRCodeItem->rewardItem->value;
        }
        else{
            $payout = Payout::with([
                'loginHistory:id,q_r_code_item_id',
                'loginHistory.qRCodeItem:id,reward_item_id,wd_id',
                'loginHistory.qRCodeItem.rewardItem:id,value',
                
            ])
            ->whereHas('loginHistory.qRCodeItem.wd',function ($query) use ($wd_code){
                $query->where('code', $wd_code);
            })
            ->select('login_history_id');
            // $value= $payout->loginHistory->qRCodeItem->rewardItem->value;
            // dd($value);
            
        }  
        // dd($payout);  


        switch ($status) {
            case 'total':
                // dd($wd_code);
                if($wd_code == "all" || $wd_code == null){
                    $totalPayout = LoginHistory::with([
                        'qRCodeItem:id,reward_item_id,wd_id',
                        'qRCodeItem.rewardItem:id,value',
            ])
            ->select('q_r_code_item_id')->get();
                }
                else{
                $totalPayout = LoginHistory::with([
                                'qRCodeItem:id,reward_item_id,wd_id',
                                'qRCodeItem.rewardItem:id,value',
                    ])
                    ->whereHas('qRCodeItem.wd',function ($query) use ($wd_code){
                        $query->where('code', $wd_code);
                    })
                    ->select('q_r_code_item_id')->get();
                }
                    // dd($totalPayout);

                    foreach ($totalPayout as $totalPayout) {
                        $payoutAmount += $totalPayout->qRCodeItem->rewardItem->value;
                        }
                        // dd($payoutAmount);  
                    break;
            case 'success': 
                        $successPayout =  $payout->where('status',1)->get();
                        foreach ($successPayout as $successPayout) {
                            $payoutAmount += $successPayout->loginHistory->qRCodeItem->rewardItem->value;
                        }
                        //  dd($payoutAmount);  
                    break;
            case 'failed':
                        $failedPayout =  $payout->where('status',0)->get();
                        foreach ($failedPayout as $failedPayout) {
                            $payoutAmount += $failedPayout->loginHistory->qRCodeItem->rewardItem->value;
                            }
                            // dd($payoutAmount);  
                break;
            case 'pending':
                $pendingPayout =  $payout->where('status',2)->get();
                        foreach ($pendingPayout as $pendingPayout) {
                            $payoutAmount += $pendingPayout->loginHistory->qRCodeItem->rewardItem->value;
                            }
                            // dd($payoutAmount);  
                break;
        }
   
       
        return response()->json(['payoutamount' => $payoutAmount]);
 
    }

    
}
