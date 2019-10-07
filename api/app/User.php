<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\chat;

class User extends Authenticatable implements JWTSubject , Constants
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

    protected $fillable = ["name", "email", "password", "phone", "desc", "visibility", "settings"];
    public $timestamps = false;



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

    public static function seek( int $id ) {
        return User::where([["id", "=", $id],["state", "<>", User::DELETED]]);
    }

    public static function search( int $id ) {
        return User::seek($id)->first();
    }

    public static function searchOrFail( int $id ) {
        return User::search($id) ?? abort(404, "User:$id not found");
    }

    public function isAdmin( $chat) {
        return $this->getChat(is_object($chat) ? $chat->id : $chat)->pivot->permissions & user::ADMIN;
    }

    public function isSuperAdmin( chat $chat) {
        return $this->getChat(is_object($chat) ? $chat->id : $chat)->pivot->permissions & user::SUPER_ADMIN;
    }

    public function chats () {
        return $this->belongsToMany('App\chat', 'participants', 'uid', 'cid')
                        ->using("App\participants")->withPivot("uid", "time", 'cid', 'permissions');
    }

    public function getChats () {
        return $this->chats()->get();
    }

    public function chat(int $chatId ) {
        return $this->belongsToMany('App\chat', 'participants', 'uid', 'cid', 'permissions')
                        ->wherePivot("cid", $chatId)
                        ->using("App\participants")->withPivot("uid", "time", 'cid', 'permissions');
    }

    public function getChat(int $chatId ) {
        return $this->chat($chatId)->first();
    }

    public function messages () {
        return $this->belongsToMany('App\message', 'messaging', 'uid', 'mid')
                        ->withPivot("state", "time", 'cid');
    }

    public function getMessages () {
        return $this->messages()->get();
    }

    public function blockedUsers() {
        return $this->belongsToMany('App\user', 'block', 'blockerid', 'blockedid')
                        ->as("blocks");
    }

    public function getBlockedUsers() {
        return $this->blockedUsers()->get();
    }

    // cannot implement this here, have to create a participants model and a messaging model 



}
