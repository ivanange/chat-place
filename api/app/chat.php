<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chat extends Model implements Constants
{
    const ONE2ONE = 0;
    const OPEN_G = 1;
    const CLOSE_G = 2;

    //chat status
    const ACTIVE = 0;
    const DELETED = 1;

    public $timestamps = false;
    protected $fillable = ["type", "title", "desc"];


    public static function seek( int $id ) {
        return chat::where([["id", "=", $id], ["status", "<>", chat::DELETED]]);
    }

    public static function search( int $id ) {
        return chat::seek($id)->first();
    }

    public static function searchOrFail( int $id ) {
        return chat::search($id) ?? abort(404, "chat:$id not found");
    }

    public function getDefaultPermission() {
        return $this->type == chat::ONE2ONE ? 
                              chat::P_ONE2ONE :
                              ( $this->type == chat::OPEN_G ? 
                                chat::P_OPEN_G :
                                chat::P_CLOSE_G 
                              );
    }
    
    public function messages () {
        return $this->belongsToMany('App\message', 'messaging', 'cid', 'mid')
                        ->withPivot("state", "time", 'uid');
    }

    public function getMessages () {
        return  $this
                ->messages()
                ->get();
    }

    public function participants () {
        return $this->belongsToMany('App\user', 'participants', 'cid', 'uid')
                        ->using("App\participants")->withPivot("uid", "time", 'cid', 'permissions');
    }

    public function getParticipants () {
        return  $this
                ->participants() 
                ->get();
    }

    public function participant ( int $userId ) {
        return $this->belongsToMany('App\user', 'participants', 'cid', 'uid')
                        ->wherePivot("uid", $userId)
                        ->using("App\participants")->withPivot("uid", "time", 'cid', 'permissions');
    }

    public function getParticipant ( int $userId ) {
        return  $this
                ->participant ( $userId )
                ->first();
    }

    public function hasParticipant( int $userId) {
        return !$this
                ->participant($userId)
                ->where("state", "=", user::AUTHENTICATED )
                ->get()->isEmpty();
    }

    public function checkParticipant( int $userId) {
        return $this
                ->participant($userId)
                ->where("state", "=", user::AUTHENTICATED )
                ->first();
    }
}
