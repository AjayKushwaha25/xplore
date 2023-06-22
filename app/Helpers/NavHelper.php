<?php

namespace App\Helpers;

use App\Models\CountsModel;

class NavHelper
{
    public static function getCounts(){
        $counts = [
            'count20' => CountsModel::getCouponCount(20),
            'count30' => CountsModel::getCouponCount(30),
            'count100' => CountsModel::getCouponCount(100),
            'count150' => CountsModel::getCouponCount(150),
            'count300' => CountsModel::getCouponCount(300),
            'totalCouponCount'  => CountsModel::getCouponCount(),

            'countRedeemed20' => CountsModel::getCouponCount(20,1),
            'countRedeemed30' => CountsModel::getCouponCount(30,1),
            'countRedeemed100' => CountsModel::getCouponCount(100,1),
            'countRedeemed150' => CountsModel::getCouponCount(150,1),
            'countRedeemed300' => CountsModel::getCouponCount(300,1),
            'totalCouponRedeemedCount'  => CountsModel::getCouponCount(null,1),
        ];
        // dd($counts);
        return $counts;
    }
}
