<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class Role extends Model
{
    use HasFactory, UsesUUID;

    public function users() {
        return $this->belongsToMany(User::class);
    }
}
