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
                'value' => 20,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => '935d6be2-c448-4c90-aa19-4fbc7188fc09',
                'value' => 30,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 'ce2cfc19-0345-47f4-9f39-8f4fb677d2b2',
                'value' => 100,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => '80fbefe2-755d-4ed9-9dc9-4f1ddeb05e90',
                'value' => 150,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 'bc2ea51f-0792-497c-a365-c4ccee3ad128',
                'value' => 300,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];

        RewardItem::insert($datas);
    }
}
