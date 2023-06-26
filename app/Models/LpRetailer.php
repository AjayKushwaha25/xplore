<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class LpRetailer extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'name',
        'mobile_number',
        'whatsapp_number',
        'upi_id',
    ];

    public function couponCodes()
    {
        return $this->belongsToMany(CouponCode::class, 'coupon_code_histories', 'lp_retailer_id', 'coupon_code_id');
    }
}
