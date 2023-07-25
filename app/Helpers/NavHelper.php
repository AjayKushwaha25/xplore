<?php

namespace App\Helpers;

use App\Models\CountsModel;

class NavHelper
{
    public static function getCouponCounts($wd_code){
        $counts = [
            'countCoupon50' => CountsModel::getCouponCount(value: 50, wdCode: $wd_code),
            'countCoupon100' => CountsModel::getCouponCount(value: 100, wdCode: $wd_code),
            'countCoupon200' => CountsModel::getCouponCount(value: 200, wdCode: $wd_code),
            'totalCouponCount'  => CountsModel::getCouponCount(wdCode: $wd_code),

            'countRedeemedCoupon50' => CountsModel::getCouponCount(50,1,$wd_code),
            'countRedeemedCoupon100' => CountsModel::getCouponCount(100,1,$wd_code),
            'countRedeemedCoupon200' => CountsModel::getCouponCount(200,1,$wd_code),
            'totalCouponRedeemedCount'  => CountsModel::getCouponCount(null,1,$wd_code),
        ];
        // dd($counts);
        return $counts;
    }
}
