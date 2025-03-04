<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnhancedCsrfProtection extends BaseSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$this->shouldSkip($request)) {
            // Validate token presence and format
            $token = $request->header('X-CSRF-TOKEN') ?? $request->input('_token');

            if (!$token || !is_string($token) || strlen($token) !== 40) {
                abort(403, 'Invalid CSRF token');
            }

            // Add additional CSRF headers
            $response = $next($request);
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            return $response;
        }

        return $next($request);
    }

    protected function shouldSkip(Request $request)
    {
        return $request->isMethod('GET') ||
               $request->is('api/*') ||
               $request->is('webhook/*');
    }
}
