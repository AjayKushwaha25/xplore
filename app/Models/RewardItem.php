<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class RewardItem extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'value'
    ];

    public function qRCodeItems() {
        return $this->hasMany(QRCodeItem::class);
    }
}
