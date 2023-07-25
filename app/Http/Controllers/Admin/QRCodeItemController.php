<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreQRCodeItemRequest, UpdateQRCodeItemRequest, ImportRequest};
use App\Models\{QRCodeItem,WD, RewardItem, City};
use App\Imports\{QRCodeImport,BulkQRCodeImport};
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use Illuminate\Support\Facades\{Auth, Storage};
use App\Jobs\GenerateQRCodeJob;
use Intervention\Image\Facades\Image;
use Imagick;
use Illuminate\Support\{Str};

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
            $newQRFolder = "coupons/A/{$request->code}";

            if(!storage_disk()->exists($newQRFolder)) {
                storage_disk()->makeDirectory($newQRFolder, 0777, true); //creates directory
            }

            $finalFrontFileName = "front.png";
            if(config('app.env')=='local'){
                $finalFront = Storage::disk('public')->path("{$newQRFolder}/{$finalFrontFileName}");
            }else{
                $finalFront = Storage::disk('gcs')->publicUrl("{$newQRFolder}/{$finalFrontFileName}");
            }

            if(!storage_disk()->exists($finalFront)){
                $front = Image::make(public_path('images/coupon_template/front.png'));

                if(config('app.env')=='local'){
                    $front->save($finalFront);
                    $image = new Imagick($finalFront);
                    $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageResolution(300, 300);
                    $image->writeImage($finalFront);
                }else{
                    $imageData = $front->encode();
                    Storage::disk('gcs')->put("{$newQRFolder}/{$finalFrontFileName}", $imageData);

                    $image = new Imagick();
                    $image->readImageBlob(Storage::disk('gcs')->get("{$newQRFolder}/{$finalFrontFileName}"));
                    $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageResolution(300, 300);
                    $imageData = $image->getImageBlob();

                    Storage::disk('gcs')->put("{$newQRFolder}/{$finalFrontFileName}", $imageData);
                }
            }


            $wd = WD::firstOrCreate([
                'code' => $request->code,
            ],[
                'firm_name' => $request->firm_name,
            ]);

            $city = City::where('name', Str::title($request->city))->first();
            $abbr = Str::upper(substr($request->city, 0, 3));

            // Check if the abbreviation already exists in the database
            $exists = City::where('abbr', $abbr)->exists();

            if ($exists) {
                // If the abbreviation already exists, try alternative sets of characters
                $altAbbr = [
                    Str::upper(substr($request->city, 0, 2)) . Str::upper(substr($request->city, 3, 1)),
                    Str::upper(substr($request->city, 0, 1)) . Str::upper(substr($request->city, 2, 2)),
                    Str::upper(substr($request->city, 0, 1)) . Str::upper(substr($request->city, 1, 1)) . Str::upper(substr($request->city, 3, 1)),
                    // You can add more alternative combinations if needed
                ];

                // Find the first alternative abbreviation that is not in use
                foreach ($altAbbr as $alt) {
                    $exists = City::where('abbr', $alt)->exists();
                    if (!$exists) {
                        $abbr = $alt;
                        break;
                    }
                }
            }

            if (!$city) {
                $city = City::create([
                    'name' => Str::title($request->city),
                    'abbr' => $abbr,
                    'status' => 1
                ]);

                WD::whereCode($wd->code)->update(['city_id' => $city->id]);
            }

            /* Coupon Code is in serial thats why below code */
            $_50 = (int) ceil($request->serial_number * 0.9);
            $_100 = (int) ceil($request->serial_number * 0.05);
            $_200 = (int) floor($request->serial_number * 0.05);

            // Insert 90% of entries with value 50
            for ($i = 0; $i < $_50; $i++) {
                $rewardId = RewardItem::whereValue(50)->value('id');
                $qrCodeRewardAmt = "{$newQRFolder}/50";

                if(!storage_disk()->exists($qrCodeRewardAmt)) {
                    storage_disk()->makeDirectory($qrCodeRewardAmt, 0777, true); //creates directory
                }
                $highestCouponCodeValue = QRCodeItem::max(\DB::raw('CAST(coupon_code AS UNSIGNED)'));
                $nextCouponCodeValue = str_pad($highestCouponCodeValue+1, 5, '0', STR_PAD_LEFT);
                // $nextCouponCodeValue = $highestCouponCodeValue+1;

                $highestSerialNumberValue = QRCodeItem::where('serial_number', 'like', "{$abbr}-%")->max(\DB::raw('CAST(SUBSTRING_INDEX(serial_number, "-", -1) AS UNSIGNED)'));

                $serialNumber = $highestSerialNumberValue ? $highestSerialNumberValue+1 : 1;
                $serial_number = "{$abbr}-{$serialNumber}";
                $imagePath = "{$qrCodeRewardAmt}/{$serial_number}.png";

                $qrCodeItem = QRCodeItem::firstOrCreate([
                    'serial_number' => $serial_number,
                ],[
                    'reward_item_id' => $rewardId,
                    'wd_id' => $wd->id,
                    'path' => $imagePath,
                    'coupon_code' => $nextCouponCodeValue
                ]);

                $url = url('/')."/login/?uid={$qrCodeItem->id}&serial_number={$serial_number}&coupon_code={$nextCouponCodeValue}";

                $qrCodeItem->update([
                    'url' => $url,
                ]);
            }

            // Insert 5% of entries with value 100
            for ($i = 0; $i < $_100; $i++) {
                $rewardId = RewardItem::whereValue(100)->value('id');
                $qrCodeRewardAmt = "{$newQRFolder}/100";

                if(!storage_disk()->exists($qrCodeRewardAmt)) {
                    storage_disk()->makeDirectory($qrCodeRewardAmt, 0777, true); //creates directory
                }
                $highestCouponCodeValue = QRCodeItem::max(\DB::raw('CAST(coupon_code AS UNSIGNED)'));
                $nextCouponCodeValue = str_pad($highestCouponCodeValue+1, 5, '0', STR_PAD_LEFT);
                // $nextCouponCodeValue = $highestCouponCodeValue+1;

                $highestSerialNumberValue = QRCodeItem::where('serial_number', 'like', "{$abbr}-%")->max(\DB::raw('CAST(SUBSTRING_INDEX(serial_number, "-", -1) AS UNSIGNED)'));

                $serialNumber = $highestSerialNumberValue ? $highestSerialNumberValue+1 : 1;
                $serial_number = "{$abbr}-{$serialNumber}";
                $imagePath = "{$qrCodeRewardAmt}/{$serial_number}.png";

                $qrCodeItem = QRCodeItem::firstOrCreate([
                    'serial_number' => $serial_number,
                ],[
                    'reward_item_id' => $rewardId,
                    'wd_id' => $wd->id,
                    'path' => $imagePath,
                    'coupon_code' => $nextCouponCodeValue
                ]);

                $url = url('/')."/login/?uid={$qrCodeItem->id}&serial_number={$serial_number}&coupon_code={$nextCouponCodeValue}";

                $qrCodeItem->update([
                    'url' => $url,
                ]);
            }

            // Insert the last 5% of entries with value 200
            for ($i = 0; $i < $_200; $i++) {
                $rewardId = RewardItem::whereValue(200)->value('id');
                $qrCodeRewardAmt = "{$newQRFolder}/200";

                if(!storage_disk()->exists($qrCodeRewardAmt)) {
                    storage_disk()->makeDirectory($qrCodeRewardAmt, 0777, true); //creates directory
                }
                $highestCouponCodeValue = QRCodeItem::max(\DB::raw('CAST(coupon_code AS UNSIGNED)'));
                $nextCouponCodeValue = str_pad($highestCouponCodeValue+1, 5, '0', STR_PAD_LEFT);
                // $nextCouponCodeValue = $highestCouponCodeValue+1;

                $highestSerialNumberValue = QRCodeItem::where('serial_number', 'like', "{$abbr}-%")->max(\DB::raw('CAST(SUBSTRING_INDEX(serial_number, "-", -1) AS UNSIGNED)'));

                $serialNumber = $highestSerialNumberValue ? $highestSerialNumberValue+1 : 1;
                $serial_number = "{$abbr}-{$serialNumber}";
                $imagePath = "{$qrCodeRewardAmt}/{$serial_number}.png";

                $qrCodeItem = QRCodeItem::firstOrCreate([
                    'serial_number' => $serial_number,
                ],[
                    'reward_item_id' => $rewardId,
                    'wd_id' => $wd->id,
                    'path' => $imagePath,
                    'coupon_code' => $nextCouponCodeValue
                ]);

                $url = url('/')."/login/?uid={$qrCodeItem->id}&serial_number={$serial_number}&coupon_code={$nextCouponCodeValue}";

                $qrCodeItem->update([
                    'url' => $url,
                ]);
            }

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
        $qrCodeItems = QRCodeItem::with('rewardItem:id,value')
                    ->where('is_qr_code_generated',0)
                    ->select('id','url','path','serial_number','reward_item_id','coupon_code','is_qr_code_generated')
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
