<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\NewAccessToken;

class ApiTokenService
{
    /**
     * Create a new API token.
     *
     * @param User $user
     * @param array $data
     * @return NewAccessToken
     */
    public function createToken(User $user, array $data): NewAccessToken
    {
        $expiresAt = $this->calculateExpirationDate($data['expiration'] ?? null);

        $abilities = $this->getTokenAbilities($data['type'] ?? 'read', $data['permissions'] ?? []);

        return $user->createToken(
            $data['name'],
            $abilities,
            $expiresAt
        );
    }

    /**
     * Calculate token expiration date based on selected option.
     *
     * @param string|null $expiration
     * @return Carbon|null
     */
    protected function calculateExpirationDate(?string $expiration): ?Carbon
    {
        return match ($expiration) {
            '24 hours' => Carbon::now()->addHours(24),
            '7 days' => Carbon::now()->addDays(7),
            '30 days' => Carbon::now()->addDays(30),
            default => null
        };
    }

    /**
     * Get token abilities based on type and permissions.
     *
     * @param string $type
     * @param array $permissions
     * @return array
     */
    protected function getTokenAbilities(string $type, array $permissions): array
    {
        $baseAbilities = match ($type) {
            'read' => ['read'],
            'write' => ['read', 'write'],
            'full' => ['*'],
            default => ['read']
        };

        // If full access, return all abilities
        if (in_array('*', $baseAbilities)) {
            return ['*'];
        }

        // Merge base abilities with specific permissions
        return array_unique(array_merge($baseAbilities, $permissions));
    }

    /**
     * Update token permissions.
     *
     * @param User $user
     * @param int $tokenId
     * @param array $permissions
     * @return bool
     */
    public function updateTokenPermissions(User $user, int $tokenId, array $permissions): bool
    {
        $token = $user->tokens()->findOrFail($tokenId);

        // Keep the base type abilities and merge with new permissions
        $currentAbilities = $token->abilities;
        $baseAbilities = array_intersect(['read', 'write'], $currentAbilities);

        $newAbilities = array_unique(array_merge($baseAbilities, $permissions));

        return $token->update(['abilities' => $newAbilities]);
    }

    /**
     * Delete an API token.
     *
     * @param User $user
     * @param int $tokenId
     * @return bool
     */
    public function deleteToken(User $user, int $tokenId): bool
    {
        return $user->tokens()->where('id', $tokenId)->delete();
    }

    /**
     * Get token usage statistics.
     *
     * @param User $user
     * @param int $tokenId
     * @return array
     */
    public function getTokenStats(User $user, int $tokenId): array
    {
        $token = $user->tokens()->findOrFail($tokenId);

        return [
            'last_used' => $token->last_used_at,
            'created_at' => $token->created_at,
            'expires_at' => $token->expires_at,
            'total_uses' => $token->tokenable->authentications()->where('token_id', $tokenId)->count(),
        ];
    }
}
