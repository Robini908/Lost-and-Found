<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ItemClaim;
use App\Facades\RolePermission;

class VerifyClaim extends Component
{
    public $claim;

    public function mount($claimId)
    {
        if (!RolePermission::canVerifyClaims(auth()->user())) {
            abort(403, 'Unauthorized action.');
        }

        $this->claim = ItemClaim::findOrFail($claimId);
    }

    public function render()
    {
        return view('livewire.verify-claim');
    }
}
