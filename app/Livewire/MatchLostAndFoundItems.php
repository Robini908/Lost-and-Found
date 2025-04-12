<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LostItem;
use App\Models\ItemMatch;
use App\Services\ItemMatchingService;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MatchLostAndFoundItems extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';
    public $selectedCategory = '';
    public $dateRange = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedItem = null;
    public $showMatchDetails = false;
    public $matchDetailsItem = null;
    public $loadingMatches = false;
    public $matchThreshold = 0.3;
    public $perPage = 10;
    public $itemTypeFilter = 'all'; // 'all', 'reported', 'searched'
    public $activeTab = 'searching'; // 'searching', 'found'
    public $showMatchAnalysis = false;
    public $currentAnalysisItem = null;
    public $matchAnalysis = [];

    // Filters
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'dateRange' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'itemTypeFilter' => ['except' => 'all'],
        'activeTab' => ['except' => 'searching'],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->loadingMatches = false;
    }

    #[Computed]
    public function lostItems()
    {
        return LostItem::query()
            ->where('user_id', Auth::id())
            ->whereDoesntHave('matchesAsLost', function($query) {
                $query->where('similarity_score', '>=', $this->matchThreshold);
            })
            ->where(function($query) {
                $query->when($this->itemTypeFilter === 'all', function($q) {
                    $q->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED]);
                })
                ->when($this->itemTypeFilter === 'reported', function($q) {
                    $q->where('item_type', LostItem::TYPE_REPORTED);
                })
                ->when($this->itemTypeFilter === 'searched', function($q) {
                    $q->where('item_type', LostItem::TYPE_SEARCHED);
                });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->dateRange, function ($query) {
                $dates = explode(' - ', $this->dateRange);
                if (count($dates) === 2) {
                    $query->whereBetween('date_lost', $dates);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->with(['category', 'images'])
            ->paginate($this->perPage);
    }

    #[Computed]
    public function foundMatches()
    {
        return ItemMatch::where('similarity_score', '>=', $this->matchThreshold)
            ->whereHas('lostItem', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['lostItem.category', 'lostItem.images', 'foundItem.category', 'foundItem.images', 'foundItem.user'])
            ->orderByDesc('similarity_score')
            ->get()
            ->map(function ($match) {
                return [
                    'lost_item' => $match->lostItem,
                    'found_item' => $match->foundItem,
                    'similarity' => $match->similarity_score,
                    'matched_at' => $match->matched_at,
                    'finder' => $match->foundItem->user->name
                ];
            });
    }

    #[Computed]
    public function matchedItems()
    {
        if (!$this->selectedItem) {
            return collect();
        }

        return ItemMatch::where('lost_item_id', $this->selectedItem)
            ->whereHas('foundItem', function($query) {
                $query->where('user_id', '!=', Auth::id())
                      ->where('item_type', LostItem::TYPE_FOUND);
            })
            ->with(['foundItem.category', 'foundItem.images', 'foundItem.user'])
            ->orderByDesc('similarity_score')
            ->get()
            ->map(function ($match) {
                return [
                    'item' => $match->foundItem,
                    'similarity' => $match->similarity_score,
                    'matched_at' => $match->matched_at,
                    'finder' => $match->foundItem->user->name
                ];
            });
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->selectedItem = null;
        $this->showMatchDetails = false;
    }

    public function setItemTypeFilter($type)
    {
        $this->itemTypeFilter = $type;
        $this->resetPage();
    }

    public function findMatches($itemId)
    {
        $this->loadingMatches = true;
        $this->selectedItem = $itemId;
        $this->showMatchDetails = true;

        try {
            $lostItem = LostItem::where('user_id', Auth::id())->findOrFail($itemId);
            $matchingService = app(ItemMatchingService::class);

            dispatch(function () use ($matchingService, $lostItem) {
                $matches = $matchingService->findMatches($lostItem);
            })->afterResponse();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error finding matches: ' . $e->getMessage()
            ]);
        }

        $this->loadingMatches = false;
    }

    #[On('echo:items.matches,item.matched')]
    public function handleNewMatch($event)
    {
        if ($this->selectedItem == $event['matchData']['lost_item_id']) {
            $this->dispatch('$refresh');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'New potential match found!'
            ]);
        }
    }

    public function showMatchDetails($itemId)
    {
        try {
            $this->matchDetailsItem = LostItem::with(['category', 'images', 'user'])
                ->where('item_type', LostItem::TYPE_FOUND)
                ->where('user_id', '!=', Auth::id())
                ->find($itemId);

            if ($this->matchDetailsItem) {
                $this->showMatchDetails = true;
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Loading item details...'
                ]);
                $this->dispatch('open-modal', 'match-details');
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Item not found'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error loading item details: ' . $e->getMessage()
            ]);
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function refreshMatches()
    {
        if ($this->selectedItem) {
            $this->findMatches($this->selectedItem);
        }
    }

    public function closeMatchDetails()
    {
        $this->showMatchDetails = false;
        $this->matchDetailsItem = null;
        $this->selectedItem = null;
    }

    public function viewMatchAnalysis($itemId)
    {
        $this->currentAnalysisItem = LostItem::with(['category', 'images'])
            ->where('user_id', Auth::id())
            ->findOrFail($itemId);

        $this->matchAnalysis = ItemMatch::where('lost_item_id', $itemId)
            ->where('similarity_score', '>=', $this->matchThreshold)
            ->with(['foundItem.category', 'foundItem.images', 'foundItem.user'])
            ->orderByDesc('similarity_score')
            ->get()
            ->map(function ($match) {
                return [
                    'found_item' => $match->foundItem,
                    'similarity' => $match->similarity_score,
                    'matched_at' => $match->matched_at,
                    'finder' => $match->foundItem->user->name,
                    'matching_attributes' => [
                        'title' => similar_text($this->currentAnalysisItem->title, $match->foundItem->title, $titlePercentage) ?
                            round($titlePercentage, 2) : 0,
                        'description' => similar_text($this->currentAnalysisItem->description, $match->foundItem->description, $descPercentage) ?
                            round($descPercentage, 2) : 0,
                        'category' => $this->currentAnalysisItem->category_id === $match->foundItem->category_id ? 100 : 0,
                        'location' => similar_text($this->currentAnalysisItem->location, $match->foundItem->location, $locationPercentage) ?
                            round($locationPercentage, 2) : 0,
                    ]
                ];
            });

        $this->dispatch('open-modal', 'match-analysis');
    }

    public function closeMatchAnalysis()
    {
        $this->showMatchAnalysis = false;
        $this->currentAnalysisItem = null;
        $this->matchAnalysis = [];
    }

    public function viewAllMatchesAnalysis()
    {
        $firstMatch = $this->foundMatches->first();
        if ($firstMatch) {
            $this->viewMatchAnalysis($firstMatch['lost_item']->id);
        }
    }

    public function render()
    {
        return view('livewire.match-lost-and-found-items', [
            'lostItems' => $this->lostItems,
            'matchedItems' => $this->matchedItems,
            'foundMatches' => $this->foundMatches,
            'itemTypes' => [
                'all' => 'All Items',
                'reported' => 'Reported Items',
                'searched' => 'Searched Items'
            ]
        ]);
    }
}
