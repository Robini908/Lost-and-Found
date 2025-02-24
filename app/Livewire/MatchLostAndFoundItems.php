<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Services\ItemMatchingService;

class MatchLostAndFoundItems extends Component
{
    use WithPagination;

    public $matches = [];
    public $showBanner = false;
    public $bannerMessage = '';
    public $unmatchedItems = [];
    public $showMatches = false;
    public $isLoading = false;
    public $loadingMessage = '';
    public $progress = 0;
    public $showAnalysisModal = false;
    public $showItemMatchingPage = false;

    public $messages = [
        "Gathering requirements...",
        "Calculating similarity scores...",
        "Analyzing images...",
        "Matching locations...",
        "Finalizing results...",
        "Hold on a moment, this will not take long..."
    ];

    protected $itemMatchingService;

    public function __construct()
    {
        $this->itemMatchingService = new ItemMatchingService();
    }

    public function mount()
    {
        $this->fetchUnmatchedItems();
    }

    public function fetchUnmatchedItems()
    {
        $user = Auth::user();
        $this->unmatchedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', ['reported', 'searched'])
            ->with('images')
            ->get();
    }

    public function toggleItemMatchingPage()
    {
        $this->showItemMatchingPage = !$this->showItemMatchingPage;
        $this->showMatches = false;
    }

    public function findMatches()
    {
        $this->isLoading = true;
        $this->showAnalysisModal = true;
        $this->progress = 0;
        $this->showMatches = false;

        // Simulate a real-time analysis process
        foreach ($this->messages as $index => $message) {
            $this->loadingMessage = $message;
            $this->progress = (int)(($index + 1) / count($this->messages) * 100);
            usleep(500000); // Simulate delay (0.5 seconds)
        }

        // Perform the actual matching logic
        $user = Auth::user();
        $reportedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', ['reported', 'searched'])
            ->with('images')
            ->get();

        $foundItems = LostItem::where('item_type', 'found')
            ->with('images')
            ->get();

        $this->matches = $this->itemMatchingService->findMatches($reportedItems, $foundItems);

        // Finalize loading
        $this->isLoading = false;
        $this->showAnalysisModal = false;
        $this->showMatches = true;
        $this->bannerMessage = count($this->matches) > 0 ? 'We found potential matches!' : 'No matches found.';
        $this->showBanner = true;
    }

    public function render()
    {
        return view('livewire.match-lost-and-found-items', [
            'unmatchedItems' => $this->unmatchedItems,
            'matches' => $this->matches,
        ]);
    }
}
