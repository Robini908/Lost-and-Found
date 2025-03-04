<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SqlInjectionProtection extends BaseSecurityMiddleware
{
    protected $sqlPatterns = [
        '/\bSELECT\b/i',
        '/\bINSERT\b/i',
        '/\bUPDATE\b/i',
        '/\bDELETE\b/i',
        '/\bDROP\b/i',
        '/\bUNION\b/i',
        '/\bOR\b.*?=.*?\b/i',
        '/\bAND\b.*?=.*?\b/i',
    ];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        if ($this->containsSqlInjection($input)) {
            \Log::warning('Potential SQL injection attempt', [
                'ip' => $request->ip(),
                'input' => $input,
                'user_id' => auth()->id()
            ]);

            abort(403, 'Invalid input detected');
        }

        return $next($request);
    }

    protected function containsSqlInjection($input)
    {
        if (is_array($input)) {
            return collect($input)->contains(function ($value) {
                return $this->containsSqlInjection($value);
            });
        }

        if (!is_string($input)) {
            return false;
        }

        foreach ($this->sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
