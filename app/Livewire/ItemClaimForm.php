<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\ItemClaim;
use Illuminate\Support\Facades\Auth;
use Usernotnull\Toast\Concerns\WireToast;

class ItemClaimForm extends Component
{
    use WireToast;

    public $foundItemId;
    public $lostItemId;
    public $foundItem;
    public $lostItem;
    public $claimDetails = '';
    public $verificationMethod = 'document';
    public $additionalNotes = '';
    public $termsAccepted = false;

    public function mount($foundItemId, $lostItemId)
    {
        $this->foundItemId = $foundItemId;
        $this->lostItemId = $lostItemId;

        // Load the items
        $this->foundItem = LostItem::findOrFail($foundItemId);
        $this->lostItem = LostItem::findOrFail($lostItemId);

        // Check if user already has a pending claim
        $existingClaim = ItemClaim::where('user_id', Auth::id())
            ->where('found_item_id', $this->foundItemId)
            ->where('lost_item_id', $this->lostItemId)
            ->whereIn('status', ['pending', 'verified'])
            ->first();

        if ($existingClaim) {
            return redirect()->route('items.view-claims')->with('message', 'You already have a pending claim for this item.');
        }
    }

    public function rules()
    {
        return [
            'claimDetails' => 'required|min:50',
            'verificationMethod' => 'required|in:document,in_person,other',
            'additionalNotes' => 'nullable|max:500',
            'termsAccepted' => 'accepted'
        ];
    }

    public function submitClaim()
    {
        $this->validate();

        try {
            $claim = ItemClaim::create([
                'user_id' => Auth::id(),
                'found_item_id' => $this->foundItemId,
                'lost_item_id' => $this->lostItemId,
                'status' => 'pending',
                'claim_details' => [
                    'description' => $this->claimDetails,
                    'verification_method' => $this->verificationMethod,
                    'additional_notes' => $this->additionalNotes
                ],
                'verification_method' => $this->verificationMethod
            ]);

            toast()
                ->success('Your claim has been submitted successfully!')
                ->push();

            return redirect()->route('items.view-claims');

        } catch (\Exception $e) {
            toast()
                ->danger('Failed to submit claim. Please try again.')
                ->push();
        }
    }

    public function render()
    {
        return view('livewire.item-claim-form');
    }
}
