<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    const ONE2ONE = 0;
    const OPEN_G = 1;
    const CLOSE_G = 2;

    //chat status
    const DELETED = 1;

    public $timestamps = false;
    protected $guarded = ["id"];

    public static function search( int $id ) {
        return chat::where([["id", "=", $id], ["status", "<>", chat::DELETED]])->first();
    }
    
    public function messages () {
        return $this->belongsToMany('App\message', 'messaging', 'cid', 'mid')
                        ->withPivot("state", "time", 'uid');
    }

    public function participants () {
        return $this->belongsToMany('App\user', 'participants', 'cid', 'uid')
                        ->using("App\participants")->withPivot("uid", "time", 'cid', 'permissions');
    }

    public function participant ( int $userId ) {
        return $this->belongsToMany('App\user', 'participants', 'cid', 'uid')
                        ->wherePivot("uid", $userId)
                        ->using("App\participants")->withPivot("uid", "time", 'cid', 'permissions');
    }

    public function hasParticipant( int $userId) {
        return !$this->participant($userId)->get()->isEmpty();
    }
}
