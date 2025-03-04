<?php

namespace App\Traits;

use App\Services\HashIdService;

trait HasSecureIds
{
    public function getHashedIdAttribute(): string
    {
        return app(HashIdService::class)->encode($this->id);
    }

    public static function findByHashedId(string $hashedId): ?self
    {
        $id = app(HashIdService::class)->decode($hashedId);
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
