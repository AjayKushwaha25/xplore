<?php

namespace App\Imports;

use Throwable;
use App\Models\{WD,QRCodeItem, RewardItem, User};
use Illuminate\Support\{Str, Collection};
use Carbon\Carbon;
use App\Notifications\ImportHasFailedNotification;
use Maatwebsite\Excel\Concerns\{ToModel, ToCollection, Importable, SkipsErrors, SkipsFailures, SkipsOnError, SkipsOnFailure, WithStartRow, WithValidation, WithChunkReading, WithEvents};
use Illuminate\Contracts\Queue\ShouldQueue;
// use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Support\Facades\{Auth, File};
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Imagick;

class QRCodeImport implements ToCollection, WithValidation, WithStartRow, WithChunkReading, WithEvents
{
    public $rowCount = 0;
    public $importedBy;

    public function __construct(User $importedBy)
    {
        $this->importedBy = $importedBy;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row)
        {
            // try {
                ++$this->rowCount;
                $newQRFolder = "coupons/{$row[0]}";

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

                $rewardId = RewardItem::whereValue($row[5])->value('id');

                $wd = WD::firstOrCreate([
                    'code' => $row[0],
                ],[
                    'firm_name' => $row[1],
                    'section_code' => $row[2],
                ]);

                // final coupon path
                $qrCodeRewardAmt = "{$newQRFolder}/{$row[5]}";

                if(!storage_disk()->exists($qrCodeRewardAmt)) {
                    storage_disk()->makeDirectory($qrCodeRewardAmt, 0777, true); //creates directory
                }

                $imagePath = "{$qrCodeRewardAmt}/{$row[3]}.png";

                $qrCodeItem = QRCodeItem::firstOrCreate([
                    'serial_number' => $row[3],
                ],[
                    'reward_item_id' => $rewardId,
                    'wd_id' => $wd->id,
                    'path' => $imagePath,
                    'coupon_code' => $row[4]
                ]);

                $url = url('/')."/login/?uid={$qrCodeItem->id}&serial_number={$row[3]}&coupon_code={$row[4]}";

                $qrCodeItem->update([
                    'url' => $url,
                ]);

            /*} catch (ValidationException $e) {
                \Log::info($this->rowCount);
                $this->failed($e);
            } catch (\Exception $e) {
                \Log::info($this->rowCount);
                $this->failed($e);
            }*/
        }
    }
    public function startRow(): int
    {
        return 2;
    }
    public function rules():array   
    {
        return [
            '*.0' => ['required'],
            '*.1' => ['required'],
            '*.2' => ['sometimes'],
            '*.3' => ['required'],
            '*.4' => ['required'],
            '*.5' => ['required','numeric'],
        ];
    }
    public function customValidationAttributes()
    {
        return [
            '0' => 'WD Code',
            '1' => 'WD Firm Name',
            '2' => 'Section Code',
            '3' => 'Serial Number',
            '4' => 'Coupon Code',
            '5' => 'Amount',
        ];
    }

    public function onError(Throwable $error)
    {
        \Log::error('Import failed (onError): '.$exception->getMessage());
    }
    /**
     * Handle a job failure.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed($e)
    {
        if ($e instanceof ValidationException) {
            $this->importedBy->notify(new ImportHasFailedNotification($e->failures()));
        } else {
            // Handle other exceptions here...
            \Log::error('Import failed (failed): '.$e->getMessage());
        }
    }
    public function registerEvents(): array
    {
        $user = Auth::user();
        return [
            ImportFailed::class => function(ImportFailed $event) {
                $user->notify(new ImportHasFailedNotification($event->getException()));
            },
        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
    public function chunkSize(): int
    {
        return 50;
    }

}
