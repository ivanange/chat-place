<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    const ONE2ONE = 0;
    const OPEN_G = 1;
    const CLOSE_G = 2;

    public $timestamps = false;
    
    public function messages () {
        return $this->belongsToMany('App\message', 'messaging', 'cid', 'mid')
                        ->withPivot("state", "time", 'uid');
    }

    public function participants () {
        return $this->belongsToMany('App\user', 'participants', 'cid', 'uid')
                        ->using("App\participants");
    }

    public function participant ( int $userId ) {
        return $this->belongsToMany('App\user', 'participants', 'cid', 'uid')
                        ->wherePivot("uid", $userId)
                        ->using("App\participants");
    }
}
