<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BypassEmailVerification
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is impersonating
        if (Session::has('impersonator_id')) {
            // Bypass email verification for impersonated users
            return $next($request);
        }

        // For non-impersonated users, enforce email verification
        if (Auth::check() && !Auth::user()->hasVerifiedEmail()) {
            // Redirect to the email verification notice page
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
