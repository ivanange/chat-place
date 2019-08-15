<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    
    public function messages () {
        return $this->belongsToMany('App\message', 'messaging', 'cid', 'mid')
                        ->as("messaging")
                        ->withPivot("state", "time");
    }

    public function participants () {
        return $this->belongsToMany('App\user', 'participants', 'cid', 'uid')
                        ->as("participants")
                        ->withPivot("permissions", "time");
    }
}
