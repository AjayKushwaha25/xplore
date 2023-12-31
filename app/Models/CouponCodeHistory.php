<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class CouponCodeHistory extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'retailer_id',
        'coupon_code_id'
    ];

    public function lpRetailer()
    {
        return $this->belongsTo(LpRetailer::class);
    }

    public function rewardItem() {
        return $this->belongsTo(RewardItem::class);
    }

    public function couponCode() {
        return $this->belongsTo(CouponCode::class);
    }
}
