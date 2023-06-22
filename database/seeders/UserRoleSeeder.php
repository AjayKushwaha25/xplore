<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users_roles')->insert([
            [
                'user_id' => 'bd1203d4-622f-4ffe-b745-1f29c934bb0d',
                'role_id' => 'b126a2f4-3924-4b12-b64f-ddbe23111675',
            ],
            [
                'user_id' => 'b1ac9931-9129-4afb-8196-2b617940b530',
                'role_id' => 'b93ccdd6-6e1d-4247-a886-78195aa3cb9b',
            ]
        ]);
    }
}
