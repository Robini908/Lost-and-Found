<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DataEncryption extends BaseSecurityMiddleware
{
    protected $sensitivePaths = [
        'api/user',
        'claims/*',
        'verification/*'
    ];

    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldEncrypt($request)) {
            $response = $next($request);

            if ($response->getContent()) {
                $content = $response->getContent();
                if ($this->isValidJson($content)) {
                    $encrypted = Crypt::encryptString($content);
                    $response->setContent(['encrypted_data' => $encrypted]);
                }
            }

            return $response;
        }

        return $next($request);
    }

    protected function shouldEncrypt(Request $request)
    {
        return collect($this->sensitivePaths)->contains(function ($path) use ($request) {
            return $request->is($path);
        });
    }
}
