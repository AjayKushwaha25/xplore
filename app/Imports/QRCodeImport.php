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
        if(!Storage::disk('public')->exists($newQRFolder)) {
            Storage::disk('public')->makeDirectory($newQRFolder, 0777, true); //creates directory
        }
        $finalFront = storage_path("app/public/{$newQRFolder}/front.png");

        if(!Storage::disk('public')->exists($finalFront)){
            $front = Image::make(public_path('images/coupon_template/front.png'));
            $imgSave = $front->save($finalFront);

            $image = new Imagick($finalFront);
            $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
            $image->setImageResolution(300, 300);
            $image->writeImage($finalFront);
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
                if(!Storage::disk('public')->exists($qrCodeRewardAmt)) {
                    Storage::disk('public')->makeDirectory($qrCodeRewardAmt, 0777, true); //creates directory
                }
                $imagePath = "{$qrCodeRewardAmt}/{$row[0]}.png";
                if(!Storage::disk('public')->exists($imagePath)){
                    $url = url('/')."/login/?uid={$qrCodeItem->id}";
                    // $couponUniqueURL = url('/')."?k={$qrCodeItem->key}";

                    $qr_code_file = "qr-code/{$row[0]}.png";
                    $image = QrCode::format('png')
                            ->size(550)->errorCorrection('H')
                            ->generate($url);

                    $storeQrCode = Storage::disk('public')->put($qr_code_file, $image);
                    $image = new Imagick(storage_path("app/public/{$qr_code_file}"));
                    $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageResolution(300, 300);
                    $image->writeImage(storage_path("app/public/{$qr_code_file}"));

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
                    $qrCode = Image::make(storage_path("app/public/{$qr_code_file}"));
                    $back->insert($qrCode, 'top-left', 110, 100);

                    $finalQRCode = storage_path("app/public/{$imagePath}");
                    $imgSave = $back->save($finalQRCode);

                    $image = new Imagick($finalQRCode);
                    $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageResolution(300, 300);
                    $image->writeImage($finalQRCode);
                    // dd($image);

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
