<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    const UNREAD = 0;
    const READ = 1;
    const EDITED = 2;
    const FORWARDED = 4;
    const DELETED = 8;

    // message types 
    const NOTIFICATION = 0;
    const TEXT = 1;
    const IMAGE = 2;
    const AUDIO = 3;
    const VIDEO = 4;
    const FILE = 5;
    const DIFFUSION = 6;




    public $timestamps = false;
    protected $fillable = ["type", "text"];


    public static function seek(  $cid  , int $mid ) {
        $chat = !is_object($cid) ? chat::find($cid) : $cid;
        return  $chat
                ->messages()
                ->where("id", "=", $mid)
                ->whereRAW("state & ".message::DELETED." = 0 ");
    }

    public static function search(  $cid  , int $mid ) {
        return message::seek($cid, $mid)->first();
    }

    public static function searchOrFail( $cid  , int $mid  ) {
        return message::search($cid, $mid) ?? abort(404, "message:$id not found");
    }

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
