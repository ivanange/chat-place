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
    const EDIT_ADMIN_PERMISSIONS = 128;
    const SUPER_ADMIN = 292;
    const CREATOR = 256;

    //default permissions per chat type
    const P_ONETONE = 512;
    const P_OPEN_G = 7;
    const P_CLOSE_G = 7;
    

    public $timestamps = false;

    public function hasPermission( string $permission ) {
        if ( !$this->{$permission}) {
            throw new App\Exceptions("Undefined Permission");
        } 
        return $this->permission & constant('self::'. $permission);
    }
}
