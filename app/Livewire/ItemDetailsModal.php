<?php

namespace App\Livewire;

use App\Models\LostItem;
use Livewire\Component;

class ItemDetailsModal extends Component
{
    public $showModal = false;
    public $item = null;

    protected $listeners = ['showItemDetails' => 'showItem'];

    public function showItem($itemId)
    {
        $this->item = LostItem::with(['images', 'user'])->find($itemId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->item = null;
    }

    public function render()
    {
        return view('livewire.item-details-modal');
    }
}
