<?php

namespace App\Services;

use Hashids\Hashids;

class HashIdService
{
    private Hashids $hashids;

    public function __construct()
    {
        // Use a long, random salt and minimum length for security
        $this->hashids = new Hashids(
            config('app.key'),
            16,
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        );
    }

    public function encode($id): string
    {
        return $this->hashids->encode($id);
    }

    public function decode($hash): ?int
    {
        $decoded = $this->hashids->decode($hash);
        return $decoded[0] ?? null;
    }
}
