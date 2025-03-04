<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken()) {
            $token = PersonalAccessToken::findToken($request->bearerToken());

            if ($token) {
                // Check if token has expired
                if ($token->expires_at && now()->gt($token->expires_at)) {
                    return response()->json([
                        'message' => 'API token has expired.',
                        'error' => 'token_expired'
                    ], 401);
                }

                // Update usage statistics
                $token->forceFill([
                    'last_used_at' => now(),
                    'usage_count' => $token->usage_count + 1,
                ])->save();

                // Add token metadata to request for logging
                $request->merge([
                    'token_metadata' => [
                        'type' => $token->token_type,
                        'name' => $token->name,
                        'abilities' => $token->abilities,
                    ]
                ]);
            }
        }

        return $next($request);
    }
}
