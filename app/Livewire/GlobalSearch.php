<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\Storage;

class GlobalSearch extends Component
{
    public $query = '';
    public $isSearching = false;
    public $showResults = false;

    protected $queryString = ['query'];

    public function getSearchResultsProperty()
    {
        if (strlen($this->query) < 2) {
            return [];
        }

        return LostItem::where(function ($query) {
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
                    'type' => $item->item_type === 'found' ? 'Found' : ($item->item_type === 'searched' ? 'Searched' : 'Lost'),
                    'category' => $item->category ? $item->category->name : 'Uncategorized',
                    'location' => $item->location_type === 'map' ? $item->location_address : $item->area,
                    'location_address' => $item->location_address,
                    'area' => $item->area,
                    'date' => $item->created_at->format('M d, Y'),
                    'image' => $item->images->first() ? $item->images->first()->image_path : null,
                    'url' => route('lost-items.show', $item->id),
                    'highlight' => $this->highlightMatch($item->title, $this->query),
                    'created_at' => $item->created_at,
                    'is_verified' => $item->is_verified ?? false,
                    'value' => $item->estimated_value,
                    'currency' => $item->currency
                ];
            });
    }

    public function updatedQuery()
    {
        $this->showResults = strlen($this->query) >= 2;
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
        $this->showResults = false;
    }

    public function navigateToResult($url)
    {
        $this->clearSearch();
        $this->redirect($url);
    }

    public function render()
    {
        return view('livewire.global-search', [
            'searchResults' => $this->searchResults
        ]);
    }
}
