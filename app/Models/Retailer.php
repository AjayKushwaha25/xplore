<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class Retailer extends Authenticatable
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'name',
        'mobile_number',
        'whatsapp_number',
        'upi_id',
        'coupon_code',
    ];

    public function couponCodes()
    {
        return $this->belongsToMany(CouponCode::class, 'coupon_code_histories', 'retailer_id', 'coupon_code_id');
    }
}
