<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use Illuminate\Support\Facades\Storage;

class DashboardRecentItems extends Component
{
    public $recentItems = [];
    public $limit = 4;

    public function mount()
    {
        $this->loadRecentItems();
    }

    public function loadRecentItems()
    {
        $this->recentItems = LostItem::with(['images', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit($this->limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'item_type' => $item->item_type,
                    'location' => $item->location,
                    'created_at' => $item->created_at,
                    'image' => $item->images->first() ? $item->images->first()->image_path : null,
                    'category' => $item->category->name
                ];
            });
    }

    public function getListeners()
    {
        return [
            'echo:items,ItemCreated' => 'loadRecentItems',
            'echo:items,ItemUpdated' => 'loadRecentItems',
            'refresh-recent-items' => 'loadRecentItems'
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-recent-items');
    }
}
