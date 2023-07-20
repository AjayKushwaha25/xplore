<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class WD extends Model
{
    use HasFactory, UsesUUID;

    protected $table = 'wd';

    protected $fillable = [
    	'code',
        'firm_name',
        'section_code',
        'status',
    ];
    
    public function qRCodeItems() {
        return $this->hasMany(QRCodeItem::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }
}
