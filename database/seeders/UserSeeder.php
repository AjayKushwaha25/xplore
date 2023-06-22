<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            [
                'id' => 'bd1203d4-622f-4ffe-b745-1f29c934bb0d',
                'name' => 'Ottoegde Admin',
                'email' => 'ajay@ottoedge.com',
                'password' => Hash::make('ottoedge@321'),
                'status' => 1,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 'b1ac9931-9129-4afb-8196-2b617940b530',
                'name' => 'Admin',
                'email' => 'admin@player-klov.com',
                'password' => Hash::make('321'),
                'status' => 1,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]
        ]);
    }
}
