<?php

namespace App\Services;

use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class HashIdService
{
    private Hashids $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(
            config('hashids.salt'),
            config('hashids.min_length'),
            config('hashids.alphabet')
        );
    }

    public function encode($id): string
    {
        if (!$id) {
            Log::error('Attempting to encode null or empty ID');
            return '';
        }

        $encoded = $this->hashids->encode($id);
        Log::info("HashIdService: Encoding ID {$id} to {$encoded}");
        return $encoded;
    }

    public function decode($hash): ?int
    {
        if (!$hash) {
            Log::error('Attempting to decode null or empty hash');
            return null;
        }

        $decoded = $this->hashids->decode($hash);
        Log::info("HashIdService: Decoding hash {$hash} to " . json_encode($decoded));

        if (empty($decoded)) {
            Log::warning("HashIdService: Failed to decode hash {$hash}");
            return null;
        }

        return $decoded[0];
    }
}
