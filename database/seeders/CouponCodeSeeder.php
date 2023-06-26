<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{CouponCode, RewardItem};

class CouponCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 50rs coupon
        for($j=1; $j<=3850;$j++){
            CouponCode::create([
                'code' => $j,
                'reward_item_id' => RewardItem::whereValue(50)->value('id'),
            ]);
        }
        // 100rs coupon
        for($k=3851; $k<=5225;$k++){
            CouponCode::create([
                'code' => $k,
                'reward_item_id' => RewardItem::whereValue(100)->value('id'),
            ]);
        }
        // 200rs coupon
        for($i=5226; $i<=5500;$i++){
            CouponCode::create([
                'code' => $i,
                'reward_item_id' => RewardItem::whereValue(200)->value('id'),
            ]);
        }
    }
}
