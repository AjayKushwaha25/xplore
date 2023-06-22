<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class LoginHistory extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'retailer_id',
        'q_r_code_item_id',
        'ip_address',
        'user_agent'
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function qRCodeItem()
    {
        return $this->belongsTo(QRCodeItem::class);
    }
}
