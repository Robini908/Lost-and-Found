<?php

namespace App\Traits;

use App\Services\HashIdService;
use Illuminate\Support\Facades\Log;

trait HasSecureIds
{
    public function getHashedIdAttribute(): string
    {
        $id = $this->id;
        $hashedId = app(HashIdService::class)->encode($id);
        Log::info("Encoding ID: {$id} to hashed ID: {$hashedId}");
        return $hashedId;
    }

    public static function findByHashedId(string $hashedId): ?self
    {
        Log::info("Attempting to decode hashed ID: {$hashedId}");
        $id = app(HashIdService::class)->decode($hashedId);
        Log::info("Decoded ID: " . ($id ?? 'null'));
        return $id ? self::find($id) : null;
    }

    protected static function bootHasSecureIds()
    {
        static::creating(function ($model) {
            // Add additional security checks before creation
            if (method_exists($model, 'validateSecureCreate')) {
                $model->validateSecureCreate();
            }
        });
    }
}
