<?php

namespace App\Helpers;

use App\Models\CountsModel;

class NavHelper
{
    public static function getCounts(){
        $counts = [
            'count50' => CountsModel::getCouponCount(50),
            'count100' => CountsModel::getCouponCount(100),
            'count200' => CountsModel::getCouponCount(200),
            'totalCouponCount'  => CountsModel::getCouponCount(),

            'countRedeemed50' => CountsModel::getCouponCount(50,1),
            'countRedeemed100' => CountsModel::getCouponCount(100,1),
            'countRedeemed200' => CountsModel::getCouponCount(200,1),
            'totalCouponRedeemedCount'  => CountsModel::getCouponCount(null,1),
        ];
        // dd($counts);
        return $counts;
    }
}
