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
                    ->orWhere('location', 'like', '%' . $this->query . '%');
            })
            ->with(['category', 'images'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->item_type,
                    'category' => $item->category->name,
                    'location' => $item->location,
                    'date' => $item->created_at->format('M d, Y'),
                    'image' => $item->images->first() ? $item->images->first()->image_path : null,
                    'url' => route('lost-items.show', $item->id),
                    'highlight' => $this->highlightMatch($item->title, $this->query)
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
