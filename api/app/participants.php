<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class participants extends Pivot implements Constants
{

    public $timestamps = false;

    public function hasPermission( int $permission ) {
        return $this->permissions & $permission;
    }
}
