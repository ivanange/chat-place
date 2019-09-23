<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    use Notifiable;

    // states
    const UNAUTHENTICATED = 0;
    const AUTHENTICATED = 1;
    const BANNED = 2;
    const DELETED = 3;
    
    //visibility
    const INVISIBLE = 0;
    const VISIBLE = 1;
    const PARTIALLY_VISIBLE = 2;

    protected $guarded = ["id"];
    public $timestamps = false;

    public static function search( int $id ) {
        return User::where([["id", "=", $id],["state", "<>", User::DELETED]])->first();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function setPasswordAttribute($password)
    {
        if ( !empty($password) ) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function chats () {
        return $this->belongsToMany('App\chat', 'participants', 'uid', 'cid')
                        ->using("App\participants")->withPivot("uid", "time", 'cid');
    }

    public function chat (int $chatId ) {
        return $this->belongsToMany('App\chat', 'participants', 'uid', 'cid', 'permissions')
                        ->wherePivot("cid", $chatId)
                        ->using("App\participants")->withPivot("uid", "time", 'cid', 'permissions');
    }

    public function messages () {
        return $this->belongsToMany('App\message', 'messaging', 'uid', 'mid')
                        ->withPivot("state", "time", 'cid');
    }

    public function blockedUsers () {
        return $this->belongsToMany('App\user', 'block', 'blockerid', 'blockedid')
                        ->as("blocks");
    }

    // cannot implement this here, have to create a participants model and a messaging model 



}
