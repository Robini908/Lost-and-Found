<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class CustomRateLimiter extends BaseSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->ip() . ':' . $request->route()->getName();

        $limit = RateLimiter::attempt(
            $key,
            $perMinute = 60,
            function() {
                return true;
            },
            $decaySeconds = 60
        );

        if (!$limit) {
            \Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'route' => $request->route()->getName(),
                'user_id' => auth()->id()
            ]);

            abort(429, 'Too Many Requests');
        }

        return $next($request);
    }
}
