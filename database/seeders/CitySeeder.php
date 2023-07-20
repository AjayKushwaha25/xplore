<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{City, WD};
use Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'NG3160' => 'AMARAWATI',
            'NG3104' => 'AURANGABAD',
            'BH3072' => 'GWALIOR',
            'BH3041' => 'INDORE',
            'NG3054' => 'NAGPUR',
            'NG2949' => 'NANDED WAGHAL',
            'PU2224' => 'Kolhapur',
            'PU4031' => 'PUNE',
            'AH3132' => 'AHMADABAD',
            'AH3123' => 'SURAT'
        ];

        foreach($data as $wd => $city){
            $city = City::create([
                'name' => Str::title($city),
                'abbr' => Str::upper(substr($city, 0, 3)),
                'status' => 1
            ]);

            $wd = WD::whereCode($wd)->update(['city_id' => $city->id]);
        }
    }
}










