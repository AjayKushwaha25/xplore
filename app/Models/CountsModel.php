<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountsModel extends Model
{
    public static function getCouponCount($value = null, $isRedeemed = null) {
        $query = QRCodeItem::whereHas('rewardItem', function($query) use ($value) {
                            if($value != null){
                                $query->where('value', $value);
                            }
                            $query->where('status', 1);
                        });
        if ($isRedeemed !== null) {
            $query = $query->whereIsRedeemed($isRedeemed);
        }
        $query->where('status', 1);
        $result = $query->count();
        return $result;
    }

    public static function getTotalCouponCount(){
        return QRCodeItem::whereStatus(1)->count();
    }
}
