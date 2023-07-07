<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\QRCodeItem;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;
use Imagick, Storage;
use Illuminate\Support\Collection;

class GenerateQRCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $qrCodeItems;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $qrCodeItems)
    {
        $this->qrCodeItems = $qrCodeItems;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->qrCodeItems as $qrCodeItem){
            if(!storage_disk()->exists($qrCodeItem->path)){
                $rewardValue = $qrCodeItem->rewardItem->value;
                $qr_code_file = "qr-code/{$qrCodeItem->path}";
                $qrCodeImage = QrCode::format('png')
                        ->size(550)->errorCorrection('H')
                        ->generate($qrCodeItem->url);

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

                $back->text($qrCodeItem->coupon_code, 520, 700, function($font) {
                    $font->file(public_path('fonts/Poppins-SemiBold.ttf'));
                    $font->size(26);
                    $font->color('#2C3689');
                    $font->align('left');
                    $font->valign('bottom');
                });

                $back->text($qrCodeItem->serial_number, 109, 90, function($font) {
                    $font->file(public_path('fonts/Poppins-SemiBold.ttf'));
                    $font->size(26);
                    $font->color('#2C3689');
                    $font->align('left');
                    $font->valign('bottom');
                });

                if(config('app.env')=='local'){
                    $qrCode = Image::make(Storage::disk('public')->path("{$qr_code_file}")); //qr-code
                    $back->insert($qrCode, 'top-left', 110, 100);

                    $finalQRCode = Storage::disk('public')->path("{$qrCodeItem->path}");
                    $imgSave = $back->save($finalQRCode);

                    $image = new Imagick($finalQRCode);
                    $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageResolution(300, 300);
                    $image->writeImage($finalQRCode);
                }else{
                    $qrCode = Image::make(Storage::disk('gcs')->publicUrl("{$qr_code_file}")); //qr-code
                    $back->insert($qrCode, 'top-left', 110, 100);

                    $imageData = $back->encode();
                    Storage::disk('gcs')->put("{$qrCodeItem->path}", $imageData);

                    $image = new Imagick();
                    $image->readImageBlob(Storage::disk('gcs')->get("{$qrCodeItem->path}"));
                    $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageResolution(300, 300);
                    $imageData1 = $image->getImageBlob();

                    Storage::disk('gcs')->put("{$qrCodeItem->path}", $imageData1);
                }

                \Log::info("qrcodelog: ". $qrCodeItem->serial_number . "_" . $rewardValue);
            }
        }
    }
}
