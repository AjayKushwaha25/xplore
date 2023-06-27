<?php

namespace App\Imports;

use Throwable;
use App\Models\{QRCodeItem, RewardItem, User};
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

class QRCodeImport implements ToCollection, WithValidation, WithStartRow, WithChunkReading, ShouldQueue, WithEvents
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
        $todaysDate = now()->format('d-m-Y');
        // $batch
        $newQRFolder = "coupons/{$todaysDate}";

        if(!storage_disk()->exists($newQRFolder)) {
            storage_disk()->makeDirectory($newQRFolder, 0777, true); //creates directory
        }

        // $finalFront = storage_path("app/public/{$newQRFolder}/front.png");
        $finalFrontFileName = "front.png";
        if(config('app.env')=='local'){
            $finalFront = Storage::disk('public')->path("{$newQRFolder}/{$finalFrontFileName}");
        }else{
            $finalFront = Storage::disk('gcs')->publicUrl($finalFrontFileName);
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
                Storage::disk('gcs')->put("{$finalFrontFileName}", $imageData);

                $image = new Imagick();
                $image->readImageBlob(Storage::disk('gcs')->get("{$finalFrontFileName}"));
                $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                $image->setImageResolution(300, 300);
                $imageData = $image->getImageBlob();

                Storage::disk('gcs')->put("{$finalFrontFileName}", $imageData);
            }
        }

        foreach ($rows as $key => $row)
        {
            try {
                $rewardValue = $row[1];
                $rewardId = RewardItem::whereValue($row[1])->value('id');

                $qrCodeItem = QRCodeItem::updateOrCreate([
                    'serial_number' => $row[0],
                ],[
                    'reward_item_id' => $rewardId,
                ]);

                // final coupon path
                $qrCodeRewardAmt = "{$newQRFolder}/{$row[1]}";

                if(!storage_disk()->exists($qrCodeRewardAmt)) {
                    storage_disk()->makeDirectory($qrCodeRewardAmt, 0777, true); //creates directory
                }

                $imagePath = "{$qrCodeRewardAmt}/{$row[0]}.png";
                if(!storage_disk()->exists($imagePath)){
                    $url = url('/')."/login/?uid={$qrCodeItem->id}";
                    // $couponUniqueURL = url('/')."?k={$qrCodeItem->key}";

                    $qr_code_file = "qr-code/{$row[0]}.png";
                    $qrCodeImage = QrCode::format('png')
                            ->size(550)->errorCorrection('H')
                            ->generate($url);

                    if(config('app.env')=='local'){
                        Storage::disk('public')->put($qr_code_file, $qrCodeImage);
                        $image = new Imagick(Storage::disk('public')->path("{$qr_code_file}"));
                        $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                        $image->setImageResolution(300, 300);
                        $image->writeImage(Storage::disk('public')->path("{$qr_code_file}"));
                    }else{
                        Storage::disk('gcs')->put($qr_code_file, $qrCodeImage);
                        $image = new Imagick();
                        $image->readImageBlob(Storage::disk('gcs')->get("{$qr_code_file}"));
                        $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                        $image->setImageResolution(300, 300);
                        $imageData = $image->getImageBlob();

                        Storage::disk('gcs')->put("{$qr_code_file}", $imageData);
                    }

                    $back = Image::make(public_path('images/coupon_template/back.png'));

                    $back->text($rewardValue, 1340, 600, function($font) {
                        $font->file(public_path('fonts/Poppins-SemiBold.ttf'));
                        $font->size(150);
                        $font->color('#2C3689');
                        $font->align('left');
                        $font->valign('bottom');
                    });

                    $back->text($row[0], 500, 700, function($font) {
                        $font->file(public_path('fonts/Poppins-SemiBold.ttf'));
                        $font->size(26);
                        $font->color('#2C3689');
                        $font->align('left');
                        $font->valign('bottom');
                    });

                    if(config('app.env')=='local'){
                        $qrCode = Image::make(Storage::disk('public')->path("{$qr_code_file}")); //qr-code
                        $back->insert($qrCode, 'top-left', 110, 100);

                        $finalQRCode = Storage::disk('public')->path("{$imagePath}");
                        $imgSave = $back->save($finalQRCode);

                        $image = new Imagick($finalQRCode);
                        $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                        $image->setImageResolution(300, 300);
                        $image->writeImage($finalQRCode);
                    }else{
                        $qrCode = Image::make(Storage::disk('gcs')->publicUrl("{$qr_code_file}")); //qr-code
                        $back->insert($qrCode, 'top-right', 106, 104);

                        $imageData = $back->encode();
                        Storage::disk('gcs')->put("{$imagePath}", $imageData);

                        $image = new Imagick();
                        $image->readImageBlob(Storage::disk('gcs')->get("{$imagePath}"));
                        $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                        $image->setImageResolution(300, 300);
                        $imageData1 = $image->getImageBlob();

                        Storage::disk('gcs')->put("{$imagePath}", $imageData1);
                    }

                    $qrCodeItem->update([
                        'url' => $url,
                        'path' => $imagePath,
                    ]);
                }
            } catch (ValidationException $e) {
                $this->failed($e);
            } catch (\Exception $e) {
                $this->failed($e);
            }
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
            '*.1' => ['required','numeric'],
        ];
    }
    public function customValidationAttributes()
    {
        return [
            '0' => 'Serial Number',
            '1' => 'Amount',
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
        return 200;
    }

}
