<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use AjayKushwaha25\CustomMakeCommand\Traits\UsesUUID;

class User extends Authenticatable
{
    use Notifiable, UsesUUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public static function getUserDetailsForView()
    {
        $user = User::get();
        return $user;
    }
    public static function getSingleUserDetailsForEdit($uuid)
    {
        $user = User::where('uuid',$uuid)
                    ->get();
        return $user;
    }
    public function roles() {
        return $this->belongsToMany(Role::class,'users_roles');
    }
}
