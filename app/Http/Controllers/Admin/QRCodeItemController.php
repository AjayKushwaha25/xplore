<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreQRCodeItemRequest, UpdateQRCodeItemRequest, ImportRequest};
use App\Models\QRCodeItem;
use App\Imports\{QRCodeImport,BulkQRCodeImport};
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use Illuminate\Support\Facades\Auth;
use App\Jobs\{GenerateQRCodeJob, StoreQRCodeItemJob};

class QRCodeItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
        ];

        return view('admin.view.qr_codes')->with('data',$data);
    }

    public function qrCodesList(Request $request){
        $wd_code = $request->get('wd_code');
        $rewardId = $request->reward_id;

        if($wd_code == "all" || $wd_code == null){
            $qrCodeItems = QRCodeItem::query()->whereHas('rewardItem',function($query) use ($rewardId){
                $query->when($rewardId, function($query) use ($rewardId){
                    $query->whereId($rewardId);
                    $query->select('id','value');
                });
            })
            ->with(['rewardItem:id,value']);
        }
        else{
        $qrCodeItems = QRCodeItem::query()->whereHas('rewardItem',function($query) use ($rewardId){
                                    $query->when($rewardId, function($query) use ($rewardId){
                                        $query->whereId($rewardId);
                                        $query->select('id','value');
                                    });
                                })
                                ->whereHas('wd',function ($query) use ($wd_code){
                                    $query->where('code', $wd_code);
                                })   
                                ->with(['rewardItem:id,value']);
                            }
        return Datatables::of($qrCodeItems)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
        ];
        return view('admin.add.qr_code')->with('data',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQRCodeItemRequest $request)
    {
        try {
            StoreQRCodeItemJob::dispatch($request->validated());
        } catch (\Exception $e) {
            \Log::info($e);
            \Log::info($e->getMessage());
        }
        return back()->with('success','QR Code Item added successfully.');
    }

    public function bulkStore(ImportRequest $request)
    {
        $user = Auth::user();
        // dd($user);
        try {
            $qrCodeImport = new QRCodeImport($user);
            $importDataArr = Excel::import($qrCodeImport,$request->validated('file'));
            $totalDataCount = $qrCodeImport->getRowCount();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(QRCodeItem $qr_code)
    {
        $qr_code->load('rewardItem:id,value');

        return view('admin.view-by-id.qr_code', compact('qr_code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'qRCodeItems' => QRCodeItem::whereId($id)->first(),
        ];
        // dd($data['qRCodeItems']);
        return view('admin.edits.qr_code')->with('data',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQRCodeItemRequest $request, $id)
    {
        // dd($request->validated(), $id);
        $data = [
            "serial_number" => $request->validated('serial_number'),
            // "key" => $request->validated('key'),
        ];
        $qrCodeUpdate = QRCodeItem::whereId($id)->first();
        $update = $qrCodeUpdate->update($data);
        if(!$update){
            exit(json_encode(array('status'=>'failed', 'message'=>'Something went wrong, please try again after sometime')));
        }
        echo json_encode(array('status'=>'success', 'message'=>'QR Code updated successfully'));
    }
    public function bulkUpdate(QRCodeRequest $request)
    {
        // dd($request->all());
        try {
            $qrCodeImport = new BulkQRCodeImport;
            $importDataArr = Excel::import($qrCodeImport,$request->validated('file'));
            $totalDataCount = $qrCodeImport->getRowCount();
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function generateBulkQRCode()
    {
        $qrCodeItems = QRCodeItem::with(['rewardItem:id,value','wd:id,code'])
                    ->where('is_qr_code_generated',0)
                    ->select('id','url','path','serial_number','reward_item_id','coupon_code','is_qr_code_generated','wd_id')
                    ->get();
        // dd($qrCodeItems->count());
        $chunkSize = 50;

        $chunks = collect($qrCodeItems)->chunk($chunkSize);

        $chunks->each(function ($chunk) use ($chunkSize) {
            GenerateQRCodeJob::dispatch($chunk)
                ->delay(now()->addSeconds(10+$chunkSize));
        });

        return back()->with('qrcode-generation-success', 'Printable file will be generated shortly.');
    }

}
