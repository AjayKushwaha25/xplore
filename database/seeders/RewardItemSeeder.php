<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RewardItem;

class RewardItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'id' => 'fc3cba88-0ced-4ea2-a060-b4df76b01060',
                'value' => 50,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => '935d6be2-c448-4c90-aa19-4fbc7188fc09',
                'value' => 100,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 'ce2cfc19-0345-47f4-9f39-8f4fb677d2b2',
                'value' => 200,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];

        RewardItem::insert($datas);
    }
}
