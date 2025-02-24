<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;

class LostItemsCarousel extends Component
{
    public $lostItems;

    public function mount()
    {
        $this->fetchLostItems();
    }

    public function fetchLostItems()
    {
        $this->lostItems = LostItem::with('images', 'user')->latest()->take(12)->get();
    }

    protected $listeners = ['refreshLostItems' => 'fetchLostItems'];

    public function render()
    {
        return view('livewire.lost-items-carousel');
    }
}
