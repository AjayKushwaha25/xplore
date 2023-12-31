<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class City extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'name',
        'abbr',
        'status'
    ];

    public function wds()
    {
        return $this->hasMany(WD::class);
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }
}
