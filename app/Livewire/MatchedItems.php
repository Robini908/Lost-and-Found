<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use Illuminate\Support\Facades\Auth;

class MatchedItems extends Component
{
    public function render()
    {
        // Fetch matched items for the authenticated user
        $user = Auth::user();
        $matchedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', ['reported', 'searched'])
            ->whereNotNull('matched_found_item_id')
            ->with('matchedFoundItem')
            ->get();

        return view('livewire.matched-items', [
            'matchedItems' => $matchedItems,
        ]);
    }
}
