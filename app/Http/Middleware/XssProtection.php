<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XssProtection extends BaseSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(strtoupper($request->method()), ['GET', 'HEAD', 'OPTIONS'])) {
            $input = $request->all();
            $sanitized = $this->sanitizeInput($input);
            $request->merge($sanitized);
        }

        $response = $next($request);

        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
