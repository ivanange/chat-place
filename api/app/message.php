<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    //

    public function owner () {
        return $this->belongsToMany('App\user', 'messaging', 'mid', 'uid')
                        ->as("messaging")
                        ->withPivot("state", "time");
    }

    public function chats () {
        return $this->belongsToMany('App\chat', 'messaging', 'mid', 'cid')
                        ->as("messaging")
                        ->withPivot("state", "time");
    }
}
