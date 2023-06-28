<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class QRCodeItem extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'url',
        'path',
        'serial_number',
        'reward_item_id',
        'is_redeemed',
        'status',
    ];


    
    public function rewardItem() {
        return $this->belongsTo(RewardItem::class);
    }
}
