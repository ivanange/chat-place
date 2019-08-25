<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    const READ = 1;
    const EDITED = 2;
    const FORWARDED = 4;
    const DELETED = 8;


    public $timestamps = false;

    public function owners () {
        return $this->belongsToMany('App\user', 'messaging', 'mid', 'uid')
                        ->withPivot("state", "time", 'cid');
    }

    public function owner (int $chatId ) {
        return $this->belongsToMany('App\user', 'messaging', 'mid', 'uid')
                        ->wherePivot("cid", $chatId)
                        ->withPivot("state", "time", 'cid');
    }

    public function chats () {
        return $this->belongsToMany('App\chat', 'messaging', 'mid', 'cid')
                        ->withPivot("state", "time", 'cid');
    }
}
