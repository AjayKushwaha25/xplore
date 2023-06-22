<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
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
                            // 'loginHistory.qRCodeItem.wd:id,code'
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
}
