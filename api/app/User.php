<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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


    public function chats () {
        return $this->belongsToMany('App\chat', 'participants', 'uid', 'cid')
                        ->as("participants")
                        ->withPivot("permissions", "time");
    }

    public function messages () {
        return $this->belongsToMany('App\message', 'messaging', 'uid', 'mid')
                        ->as("messaging")
                        ->withPivot("state", "time");
    }

    public function blockedUsers () {
        return $this->belongsToMany('App\user', 'block', 'blockerid', 'blockedid')
                        ->as("blocks");
    }


}
