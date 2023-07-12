<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WD;

class WdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('wd')->insert([
            [
                'id' => '7b8a7f19-f1f9-484c-9273-a0f89aa5f232',
                'code' => '1234',
                'firm_name' => 'wsq',
                'section_code' => 'MU123',
                'status' => '2',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => '7b8a7f19-f1f9-484c-9273-a0f89aa5f234',
                'code' => '1235',
                'firm_name' => 'acg',
                'section_code' => 'MU123',
                'status' => '1',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        
    ]);
    }
}
