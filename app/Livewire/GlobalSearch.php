<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use Illuminate\Support\Str;
use Livewire\Attributes\Debounce;
use Illuminate\Support\Facades\Storage;

class GlobalSearch extends Component
{
    public $query = '';
    public $searchResults = [];
    public $isSearching = false;
    public $showResults = false;


    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->searchResults = [];
            $this->showResults = false;
            return;
        }

        $this->isSearching = true;

        $this->searchResults = LostItem::where(function ($query) {
                $query->where('title', 'like', '%' . $this->query . '%')
                    ->orWhere('description', 'like', '%' . $this->query . '%')
                    ->orWhere('location_address', 'like', '%' . $this->query . '%')
                    ->orWhere('area', 'like', '%' . $this->query . '%')
                    ->orWhere('landmarks', 'like', '%' . $this->query . '%')
                    ->orWhere('location_lost', 'like', '%' . $this->query . '%')
                    ->orWhere('location_found', 'like', '%' . $this->query . '%');
            })
            ->with(['category', 'images'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'type' => 'Lost Item',
                    'category' => $item->category ? $item->category->name : 'Uncategorized',
                    'location' => $item->location_type === 'map' ? $item->location_address : $item->area,
                    'location_address' => $item->location_address,
                    'area' => $item->area,
                    'date' => $item->created_at->format('M d, Y'),
                    'image' => $item->images->first() ? $item->images->first()->image_path : null,
                    'url' => route('lost-items.show', $item->hashed_id),
                    'highlight' => $this->highlightMatch($item->title, $this->query),
                    'created_at' => $item->created_at,
                    'is_verified' => $item->is_verified ?? false,
                    'value' => $item->estimated_value,
                    'currency' => $item->currency
                ];
            });

        $this->showResults = true;
        $this->isSearching = false;
    }

    protected function highlightMatch($text, $query)
    {
        if (!$query) return $text;

        $pattern = '/' . preg_quote($query, '/') . '/i';
        return preg_replace($pattern, '<mark class="bg-yellow-200">$0</mark>', $text);
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->searchResults = [];
        $this->showResults = false;
    }

    public function navigateToResult($url)
    {
        $this->clearSearch();
        $this->redirect($url);
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
