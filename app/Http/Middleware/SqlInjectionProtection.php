<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SqlInjectionProtection extends BaseSecurityMiddleware
{
    protected $sqlPatterns = [
        '/\bSELECT\b\s+.*?\bFROM\b/i',
        '/\bINSERT\b\s+\bINTO\b/i',
        '/\bUPDATE\b\s+.*?\bSET\b/i',
        '/\bDELETE\b\s+\bFROM\b/i',
        '/\bDROP\b\s+\bTABLE\b/i',
        '/\bUNION\b\s+\bSELECT\b/i',
        '/\bOR\b\s+\d+\s*=\s*\d+/i',
        '/\bAND\b\s+\d+\s*=\s*\d+/i',
        '/\bEXEC\b\s+/i',
        '/\bALTER\b\s+\bTABLE\b/i',
    ];

    // Components that should be excluded from SQL injection checks
    protected $excludedComponents = [
        'profile.update-extended-profile-information',
    ];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        // Skip check for Livewire components that are known to be safe
        if (isset($input['components']) && is_array($input['components'])) {
            foreach ($input['components'] as $component) {
                if (isset($component['snapshot'])) {
                    $snapshot = json_decode($component['snapshot'], true);
                    if (isset($snapshot['memo']['name']) && in_array($snapshot['memo']['name'], $this->excludedComponents)) {
                        return $next($request);
                    }
                }
            }
        }

        if ($this->containsSqlInjection($input)) {
            Log::channel('security')->warning('Potential SQL injection attempt', [
                'ip' => $request->ip(),
                'input' => $input,
                'user_id' => auth()->id()
            ]);

            // For development environment, just log without blocking
            if (app()->environment('local', 'development')) {
                return $next($request);
            }

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

        // Skip JSON strings as they might contain valid SQL-like patterns
        if ($this->isValidJson($input)) {
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
