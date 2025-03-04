<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ItemClaim;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemClaimPolicy
{
    use HandlesAuthorization;

    public function verifyClaims(User $user)
    {
        // Modify this according to your authorization logic
        return $user->hasRole('admin') || $user->hasPermission('verify-claims');
    }

    public function verify(User $user, ItemClaim $claim)
    {
        // Add any specific claim verification logic here
        return $this->verifyClaims($user) && $claim->status === 'pending';
    }
}
