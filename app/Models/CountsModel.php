<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountsModel extends Model
{
    public static function getCouponCount($value = null, $isRedeemed = null, $wdCode = null) {
        $query = QRCodeItem::whereHas('rewardItem', function($query) use ($value) {
                            if($value != null){
                                $query->where('value', $value);
                            }
                            $query->where('status', 1);
                        });
        if ($isRedeemed !== null) {
            $query = $query->whereIsRedeemed($isRedeemed);
        }
        if ($wdCode == "all" || $wdCode == null) {
            $query->where('status', 1);
            $result = $query->count();
        }
        else{
            $query->whereHas('wd',function ($query) use ($wdCode){
                $query->where('code', $wdCode);
            });
            $query->where('status', 1);
            $result = $query->count();
        }
        // $query->where('status', 1);
        // $result = $query->count();
        return $result;
    }

    public static function getTotalCouponCount(){
        return QRCodeItem::whereStatus(1)->count();
    }
}
