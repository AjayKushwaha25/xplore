<?php

namespace App\Helpers;

use App\Models\CountsModel;

class NavHelper
{
    public static function getCouponCounts(){
        $counts = [
            'countCoupon50' => CountsModel::getCouponCount(50),
            'countCoupon100' => CountsModel::getCouponCount(100),
            'countCoupon200' => CountsModel::getCouponCount(200),
            'totalCouponCount'  => CountsModel::getCouponCount(),

            'countRedeemedCoupon50' => CountsModel::getCouponCount(50,1),
            'countRedeemedCoupon100' => CountsModel::getCouponCount(100,1),
            'countRedeemedCoupon200' => CountsModel::getCouponCount(200,1),
            'totalCouponRedeemedCount'  => CountsModel::getCouponCount(null,1),
        ];
        // dd($counts);
        return $counts;
    }
}
