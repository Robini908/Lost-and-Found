<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\RolePermission;
use Illuminate\Support\Facades\Auth;

class CheckRolePermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!RolePermission::hasAnyPermission(Auth()->user(), [$permission])) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
