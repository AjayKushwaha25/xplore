<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Region, City};

class RegionSeeder extends Seeder
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
                'name' => 'South',
                'status' => 1,
            ],
            [
                'name' => 'North',
                'status' => 1,
            ],
            [
                'name' => 'West',
                'status' => 1,
            ],
        ];

        $south = [
            'Manglore',
            'Udipi',
            'Hubli',
            'Belgaum',
        ];

        $north = [
            'Chandigarh',
            'Gwalior',
            'Indore',
        ];

        $west = [
            'Nagpur',
            'Pune',
            'Nanded Waghal',
            'Ahmadabad',
            'Aurangabad',
            'Amravati',
            'Amarawati',
            'Kolhapur',
            'Rajkot',
            'Bhavnagar',
            'Baroda',
            'Surat'
        ];

        foreach($datas as $data){
            $region = Region::create($data);
            if($region->name == "West"){
                foreach($west as $name){
                    $city = City::whereName($name)->first();
                    // dd($city, $name, $region->name);
                    if($city != null){
                        $city->update(['region_id' => $region->id]);
                    }
                }
            }
            if($region->name == "South"){
                foreach($south as $name){
                    $city = City::whereName($name)->first();
                    if($city != null){
                        $city->update(['region_id' => $region->id]);
                    }
                }
            }
            if($region->name == "North"){
                foreach($north as $name){
                    $city = City::whereName($name)->first();
                    if($city != null){
                        $city->update(['region_id' => $region->id]);
                    }
                }
            }
        }
    }
}
