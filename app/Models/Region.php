<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class Region extends Model
{
    use HasFactory, UsesUUID;

    protected $fillable = [
        'region',
        'status',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
