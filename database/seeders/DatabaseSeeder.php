<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            UserRoleSeeder::class,
            RewardItemSeeder::class,
            WdSeeder::class,
            QrCodeItemSeeder::class,
            RegionSeeder::class,

            // CouponCodeSeeder::class,
        ]);
    }
}
