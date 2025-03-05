<?php

namespace App\Services;

class UrlService
{
    public static function getBaseUrl()
    {
        if (app()->environment('local')) {
            // Check if running on a specific port
            $port = request()->getPort();
            $baseUrl = config('app.url');

            // If port is not standard (80 or 443), append it
            if (!in_array($port, [80, 443]) && !str_contains($baseUrl, ':' . $port)) {
                $baseUrl .= ':' . $port;
            }

            return $baseUrl;
        }

        return config('app.url');
    }
}
