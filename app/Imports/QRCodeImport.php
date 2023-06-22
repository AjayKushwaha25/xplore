<?php

namespace App\Imports;

use Throwable;
use App\Models\{WD, QRCodeItem, RewardItem, User};
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
                $rewardId = RewardItem::whereValue($row[4])->value('id');
                $wd = WD::updateOrCreate([
                    'code' => $row[0],
                ],[
                    'firm_name' => $row[1],
                    'section_code' => $row[2],
                ]);

                $uniqueKey = Str::random(5);

                while (QRCodeItem::where('key', $uniqueKey)->exists()) {
                    $uniqueKey = Str::random(5);
                }

                $qrCodeItem = QRCodeItem::updateOrCreate([
                    'serial_number' => $row[3],
                ],[
                    'key' => $uniqueKey,
                    'wd_id' => $wd->id,
                    'reward_item_id' => $rewardId,
                ]);
                // final coupon path
                $qrCodeRewardAmt = "{$newQRFolder}/{$row[4]}";
                if(!Storage::disk('public')->exists($qrCodeRewardAmt)) {
                    Storage::disk('public')->makeDirectory($qrCodeRewardAmt, 0777, true); //creates directory
                }
                $imagePath = "{$qrCodeRewardAmt}/{$row[3]}.png";
                if(!Storage::disk('public')->exists($imagePath)){
                    $url = url('/')."/login/?uid={$qrCodeItem->id}";
                    $couponUniqueURL = url('/')."?k={$qrCodeItem->key}";

                    $qr_code_file = "qr-code/{$row[3]}.png";
                    $image = QrCode::format('png')
                            ->size(520)->errorCorrection('H')
                            ->generate($url);

                    $storeQrCode = Storage::disk('public')->put($qr_code_file, $image);
                    $image = new Imagick(storage_path("app/public/{$qr_code_file}"));
                    $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageResolution(300, 300);
                    $image->writeImage(storage_path("app/public/{$qr_code_file}"));

                    $back = Image::make(public_path('images/coupon_template/back.png'));

                    $back->text($row[3], 145, 713, function($font) {
                        $font->file(public_path('fonts/Lato-Bold.ttf'));
                        $font->size(26);
                        $font->color('#000');
                        $font->align('left');
                        $font->valign('bottom');
                    });
                    $qrCode = Image::make(storage_path("app/public/{$qr_code_file}"));
                    $back->insert($qrCode, 'top-right', 143, 101);

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
            '*.1' => ['required'],
            '*.2' => ['sometimes'],
            '*.3' => ['required'],
            '*.4' => ['required','numeric'],
        ];
    }
    public function customValidationAttributes()
    {
        return [
            '0' => 'WD Code',
            '1' => 'WD Firm Name',
            '2' => 'Section Code',
            '3' => 'Serial Number',
            '4' => 'Amount',
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
