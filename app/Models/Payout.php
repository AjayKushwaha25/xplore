<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class Payout extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'login_history_id',
        'utr',
        'status',
        'reason',
        'processed_at'
    ];

    protected $casts =[
        'processed_at' => 'datetime'
    ];

    const UPDATED_AT = null;

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function qRCodeItem()
    {
        return $this->belongsTo(QRCodeItem::class);
    }

    public function loginHistory()
    {
        return $this->belongsTo(LoginHistory::class);
    }
}
