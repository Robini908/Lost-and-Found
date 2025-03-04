<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RolePermission extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'role-permission';
    }
}
