<?php

namespace App\Livewire;

use Livewire\Component;
use App\Facades\RolePermission;
use App\Models\LostItem;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{
    public $stats;

    public function mount()
    {
        if (!RolePermission::canViewAnalytics(auth()->user())) {
            abort(403, 'Unauthorized action.');
        }

        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_items' => LostItem::count(),
            'found_items' => LostItem::where('status', 'found')->count(),
            'pending_claims' => Claim::where('status', 'pending')->count(),
            // Add more statistics as needed
        ];
    }

    public function render()
    {
        return view('livewire.analytics');
    }
}
