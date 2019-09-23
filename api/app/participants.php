<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class participants extends Pivot
{
    //permissions & roles
    const POST = 1;
    const EDIT = 2;
    const DELETE_USER_MESSAGE = 4;
    const ADD_USER = 8 ;
    const REMOVE_USER = 16;
    const EDIT_PERMISSIONS = 32;
    const ADMIN = 64;
    const ADMIN_PERMISSIONS = 127;
    const EDIT_ADMIN_PERMISSIONS = 128;
    const SUPER_ADMIN = 292;
    const SUPER_ADMIN_PERMISSIONS = 255;
    const CREATOR = 256;
    const DELETE_CHAT = 512;
    const CREATOR_PERMISSIONS = 959;

    //default permissions per chat type
    const P_ONETONE = 959;
    const P_OPEN_G = 3;
    const P_CLOSE_G = 3;
    

    public $timestamps = false;

    public function hasPermission( int $permission ) {
        return $this->permissions & $permission;
    }
}
