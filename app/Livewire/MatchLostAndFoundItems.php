<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Services\ItemMatchingService;
use Illuminate\Support\Facades\Log;
use App\Models\ItemMatch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessItemMatching;
use Livewire\Attributes\On;

class MatchLostAndFoundItems extends Component
{
    use WithPagination;

    // Add protected $paginationTheme
    protected $paginationTheme = 'tailwind';

    public $userId;
    public $isLoading = false;
    public $progress = 0;
    public $processingItemId = null;
    public $selectedTab = 'unmatched';
    public $searchQuery = '';
    public $selectedCategory = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $itemsPerPage = 9;
    public $processingStage = '';
    public $showFilters = false;
    public $batchSize = 5; // Number of items to process in each batch
    public $autoMatchEnabled = true;

    // Add page property for pagination
    #[Computed]
    public function page()
    {
        return $this->getPage();
    }

    protected $queryString = [
        'searchQuery' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedTab' => ['except' => 'unmatched'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    protected ItemMatchingService $itemMatchingService;

    // Cache TTL in seconds (1 hour)
    protected const CACHE_TTL = 3600;
    protected const POLLING_INTERVAL = 30000; // 30 seconds

    public function boot(ItemMatchingService $itemMatchingService)
    {
        $this->itemMatchingService = $itemMatchingService;
    }

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->startAutoMatching();
    }

    #[On('echo:items,ItemCreated')]
    public function handleNewItem($event)
    {
        if ($this->autoMatchEnabled) {
            $this->dispatchMatchingJob($event['itemId']);
        }
    }

    #[On('echo:items,ItemUpdated')]
    public function handleItemUpdate($event)
    {
        $this->invalidateCache();
        if ($this->autoMatchEnabled) {
            $this->dispatchMatchingJob($event['itemId']);
        }
    }

    protected function dispatchMatchingJob($itemId)
    {
        ProcessItemMatching::dispatch($itemId)
            ->onQueue('matching')
            ->delay(now()->addSeconds(5));
    }

    #[On('pollMatches')]
    public function processUnmatchedItems()
    {
        if (!$this->autoMatchEnabled) {
            return;
        }

        $unmatchedItems = LostItem::query()
            ->where('user_id', $this->userId)
            ->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])
            ->doesntHave('matches')
            ->limit(5)
            ->get();

        foreach ($unmatchedItems as $item) {
            $this->dispatchMatchingJob($item->id);
        }

        // Schedule the next poll using dispatch and JavaScript setTimeout
        $this->dispatch('scheduleNextPoll');
    }

    public function startAutoMatching()
    {
        if ($this->autoMatchEnabled) {
            $this->processUnmatchedItems();
        }
    }

    #[On('matchProcessed')]
    public function handleMatchProcessed($data)
    {
        $this->invalidateCache();

        if ($data['matches_found'] > 0) {
            $this->notify('success', "Found {$data['matches_found']} new matches for item #{$data['item_id']}");
        }
    }

    #[Computed]
    public function unmatchedItems()
    {
        $cacheKey = $this->getUnmatchedItemsCacheKey();
        return Cache::remember($cacheKey, self::CACHE_TTL, fn() => $this->getFilteredItems(false));
    }

    #[Computed]
    public function matchedItems()
    {
        $cacheKey = $this->getMatchedItemsCacheKey();
        return Cache::remember($cacheKey, self::CACHE_TTL, fn() => $this->getFilteredItems(true));
    }

    protected function getFilteredItems($matched)
    {
        $query = LostItem::query()
            ->where('user_id', $this->userId)
            ->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED]);

        $query->when($matched,
            fn(Builder $q) => $q->has('matches'),
            fn(Builder $q) => $q->doesntHave('matches')
        );

        $query->when($this->searchQuery, function (Builder $q) {
            $q->where(function (Builder $sq) {
                $sq->where('title', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $this->searchQuery . '%');
            });
        });

        $query->when($this->selectedCategory,
            fn(Builder $q) => $q->where('category_id', $this->selectedCategory)
        );

        return $query->with([
            'images',
            'category',
            'matches' => fn($q) => $q->orderBy('similarity_score', 'desc')
                ->with(['foundItem.images', 'foundItem.category'])
        ])
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->itemsPerPage);
    }

    public function toggleAutoMatch()
    {
        $this->autoMatchEnabled = !$this->autoMatchEnabled;
        if ($this->autoMatchEnabled) {
            $this->startAutoMatching();
            $this->notify('info', 'Auto-matching enabled');
        } else {
            $this->notify('info', 'Auto-matching disabled');
        }
    }

    public function findMatchesForItem($itemId)
    {
        $this->isLoading = true;
        $this->processingItemId = $itemId;
        $this->dispatchMatchingJob($itemId);
        $this->notify('info', 'Match processing started in background');
            $this->isLoading = false;
    }

    // Cache key methods remain the same
    protected function getUnmatchedItemsCacheKey(): string
    {
        return sprintf(
            'unmatched_items:%s:%s:%s:%s:%s:%d:%s',
            $this->userId,
            $this->searchQuery,
            $this->selectedCategory,
            $this->sortField,
            $this->sortDirection,
            $this->page(),
            $this->itemsPerPage
        );
    }

    protected function getMatchedItemsCacheKey(): string
    {
        return sprintf(
            'matched_items:%s:%s:%s:%s:%s:%d:%s',
            $this->userId,
            $this->searchQuery,
            $this->selectedCategory,
            $this->sortField,
            $this->sortDirection,
            $this->page(),
            $this->itemsPerPage
        );
    }

    // Add method to get current page
    protected function getPage()
    {
        return request()->query('page', 1);
    }

    // Update resetPage method
    public function resetPage()
    {
        $this->setPage(1);
    }

    public function invalidateCache()
    {
        Cache::forget($this->getUnmatchedItemsCacheKey());
        Cache::forget($this->getMatchedItemsCacheKey());
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->invalidateCache();
    }

    public function getListeners()
    {
        return [
            'echo:items,ItemDeleted' => 'handleItemDelete',
            'refreshMatches' => '$refresh'
        ];
    }

    public function handleItemDelete($event)
    {
        $this->invalidateCache();
        $this->dispatch('refreshMatches');
    }

    /**
     * Display a notification message
     *
     * @param string $type success|info|error
     * @param string $message
     * @return void
     */
    protected function notify($type, $message)
    {
        $this->dispatch('notify', [
            'type' => $type,
            'message' => $message
        ]);
    }

    public function render()
    {
        return view('livewire.match-lost-and-found-items', [
            'categories' => Cache::remember('all_categories', self::CACHE_TTL,
                fn() => \App\Models\Category::all()
            ),
            'unmatchedItems' => $this->unmatchedItems(),
            'matchedItems' => $this->matchedItems()
        ]);
    }
}
