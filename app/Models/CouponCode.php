<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class CouponCode extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'code',
        'reward_item_id',
    ];

    public function retailers()
    {
        return $this->belongsToMany(Retailer::class, 'coupon_code_histories', 'coupon_code_id', 'retailer_id');
    }

    public function rewardItem()
    {
        return $this->belongsTo(RewardItem::class);
    }
}
    

