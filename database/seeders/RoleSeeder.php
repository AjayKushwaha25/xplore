<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->insert([
            [
                'id' => 'b126a2f4-3924-4b12-b64f-ddbe23111675',
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 'b93ccdd6-6e1d-4247-a886-78195aa3cb9b',
                'name' => 'Admin',
                'slug' => 'admin',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]
        ]);
    }
}
