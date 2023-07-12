<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QRCodeItem;

class QrCodeItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '7b8a7f19-f1f9-484c-9273-a0f89aa5f051',
                'url' => url('/').'/login/?uid=7b8a7f19-f1f9-484c-9273-a0f89aa5f051',
                'path' => 'qr-1.png',
                'serial_number' => 'MU123',
                'coupon_code' => '123',
                'wd_id' => '7b8a7f19-f1f9-484c-9273-a0f89aa5f232',
                'reward_item_id' => '935d6be2-c448-4c90-aa19-4fbc7188fc09',
                'is_redeemed' => '0',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => '7b8a7f19-f1f9-484c-9273-a0f89aa5f05a',
                'url' => url('/').'/login/?uid=7b8a7f19-f1f9-484c-9273-a0f89aa5f05a',
                'path' => 'qr-1.png',
                'serial_number' => 'MU124',
                'coupon_code' => '124',
                'wd_id' => '7b8a7f19-f1f9-484c-9273-a0f89aa5f234',
                'reward_item_id' => 'ce2cfc19-0345-47f4-9f39-8f4fb677d2b2',
                'is_redeemed' => '0',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];
        QRCodeItem::insert($data);
    }
}
