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
                'reward_item_id' => '80fbefe2-755d-4ed9-9dc9-4f1ddeb05e90',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => '7b8a7f19-f1f9-484c-9273-a0f89aa5f05a',
                'url' => url('/').'/login/?uid=7b8a7f19-f1f9-484c-9273-a0f89aa5f05a',
                'path' => 'qr-1.png',
                'reward_item_id' => 'fc3cba88-0ced-4ea2-a060-b4df76b01060',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];
        QRCodeItem::insert($data);
    }
}
