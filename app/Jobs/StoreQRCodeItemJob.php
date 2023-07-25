<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Imagick;
use Illuminate\Support\{Str};
use App\Models\{QRCodeItem,WD, RewardItem, City};
use Illuminate\Support\Facades\Storage;

class StoreQRCodeItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = null; // Set the timeout to unlimited
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            \DB::transaction(function () {
                $code = $this->data['code'];
                $firm_name = $this->data['firm_name'] ?? 'NA';
                $cityName = $this->data['city'];
                $serial_number = $this->data['serial_number'];

                $mainFolder = "coupons/A";
                $newQRFolder = "{$mainFolder}/{$code}";

                if(!storage_disk()->exists($newQRFolder)) {
                    storage_disk()->makeDirectory($newQRFolder, 0777, true);
                }

                $finalFrontFileName = "front.png";
                if(config('app.env')=='local'){
                    $finalFront = Storage::disk('public')->path("{$mainFolder}/{$finalFrontFileName}");
                }else{
                    $finalFront = Storage::disk('gcs')->publicUrl("{$mainFolder}/{$finalFrontFileName}");
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
                        Storage::disk('gcs')->put("{$mainFolder}/{$finalFrontFileName}", $imageData);

                        $image = new Imagick();
                        $image->readImageBlob(Storage::disk('gcs')->get("{$mainFolder}/{$finalFrontFileName}"));
                        $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                        $image->setImageResolution(300, 300);
                        $imageData = $image->getImageBlob();

                        Storage::disk('gcs')->put("{$mainFolder}/{$finalFrontFileName}", $imageData);
                    }
                }


                $wd = WD::firstOrCreate([
                    'code' => $code,
                ],[
                    'firm_name' => $firm_name,
                ]);

                $city = City::where('name', Str::title($cityName))->first();
                $abbr = Str::upper(substr($cityName, 0, 3));

                // Check if the abbreviation already exists in the database
                /*$exists = City::where('abbr', $abbr)->exists();

                if ($exists) {
                    // If the abbreviation already exists, try alternative sets of characters
                    $altAbbr = [
                        Str::upper(substr($cityName, 0, 2)) . Str::upper(substr($cityName, 3, 1)),
                        Str::upper(substr($cityName, 0, 1)) . Str::upper(substr($cityName, 2, 2)),
                        Str::upper(substr($cityName, 0, 1)) . Str::upper(substr($cityName, 1, 1)) . Str::upper(substr($cityName, 3, 1)),
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
                }*/

                if (!$city) {
                    $city = City::create([
                        'name' => Str::title($cityName),
                        'abbr' => $abbr,
                        'status' => 1
                    ]);

                    WD::whereCode($wd->code)->update(['city_id' => $city->id]);
                }

                /* Coupon Code is in serial thats why below code */
                $_50 = (int) ceil($serial_number * 0.9);
                $_100 = (int) ceil($serial_number * 0.05);
                $_200 = (int) floor($serial_number * 0.05);

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
            });
        } catch (\Exception $e) {
            \Log::error('Error processing StoreQRCodeItemJob: ' . $e->getMessage());
        }
    }
}
