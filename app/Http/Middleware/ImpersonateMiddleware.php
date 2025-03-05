<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lab404\Impersonate\Services\ImpersonateManager;

class ImpersonateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if we're impersonating
        if ($request->session()->has('impersonate')) {
            // Get the impersonator ID
            $impersonatorId = $request->session()->get('impersonate');

            // If we have a valid impersonator ID
            if ($impersonatorId) {
                // Clear any existing authentication
                Auth::logout();

                // Login as the impersonated user
                Auth::loginUsingId($impersonatorId);

                // Set the session to remember we're impersonating
                $request->session()->put('is_impersonating', true);
            }
        }

        return $next($request);
    }
}
