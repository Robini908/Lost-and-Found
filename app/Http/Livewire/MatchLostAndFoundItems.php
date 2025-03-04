<?php

namespace App\Http\Livewire;

use App\Models\LostItem;
use App\Services\ItemMatchingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use WireUi\Traits\Actions;

class MatchLostAndFoundItems extends Component
{
    use Actions;

    public $isLoading = false;
    public $showAnalysisModal = false;
    public $progress = 0;
    public $loadingMessage = '';
    public $matches = [];
    public $showMatches = false;
    public $unmatchedItems;

    protected $itemMatchingService;

    public function mount()
    {
        $this->unmatchedItems = collect();
        $this->fetchUnmatchedItems();
    }

    public function fetchUnmatchedItems()
    {
        $user = Auth::user();
        $this->unmatchedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])
            ->with(['images', 'category'])
            ->get();
    }

    public function findMatches()
    {
        $this->isLoading = true;
        $this->showAnalysisModal = true;
        $this->progress = 0;
        $this->loadingMessage = 'Starting match analysis...';

        try {
            Log::info('Starting match finding process in Livewire component');
            $user = Auth::user();

            if ($this->unmatchedItems->isEmpty()) {
                $this->notification()->error(
                    'No Items Found',
                    'You don\'t have any reported or searched items to match.'
                );
                return;
            }

            // Get found items from other users
            $foundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)
                ->where('user_id', '!=', $user->id)
                ->with(['images', 'category', 'user'])
                ->get();

            if ($foundItems->isEmpty()) {
                $this->notification()->info(
                    'No Found Items',
                    'There are currently no found items to match against. Please check back later.'
                );
                return;
            }

            Log::info('Found potential matching items', [
                'unmatched_items' => $this->unmatchedItems->count(),
                'found_items' => $foundItems->count()
            ]);

            $this->progress = 40;
            $this->loadingMessage = 'Analyzing ' . $foundItems->count() . ' potential matches...';

            // Use the ItemMatchingService to find matches
            $matches = $this->itemMatchingService->findMatches($this->unmatchedItems, $foundItems);
            $this->progress = 80;
            $this->loadingMessage = 'Processing match results...';

            Log::info('Processing matches', ['count' => count($matches)]);

            // Transform matches into the required format
            $this->matches = $matches->map(function ($match) {
                // Save the match to the database
                try {
                    \App\Models\PotentialMatch::updateOrCreate(
                        [
                            'lost_item_id' => $match['reported_item']->id,
                            'found_item_id' => $match['found_item']->id,
                        ],
                        [
                            'similarity_score' => $match['similarity_score'],
                            'match_details' => $match['match_details'],
                            'is_viewed' => false
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Error saving potential match', [
                        'error' => $e->getMessage(),
                        'lost_item_id' => $match['reported_item']->id,
                        'found_item_id' => $match['found_item']->id
                    ]);
                }

                return [
                    'reported_item' => $match['reported_item'],
                    'found_item' => $match['found_item'],
                    'similarity_score' => $match['similarity_score'],
                    'match_details' => array_merge($match['match_details'], [
                        'location_match' => [
                            'reported' => $match['reported_item']->location_type === 'map'
                                ? $match['reported_item']->location_address
                                : $match['reported_item']->area,
                            'found' => $match['found_item']->location_type === 'map'
                                ? $match['found_item']->location_address
                                : $match['found_item']->area,
                        ],
                        'date_match' => [
                            'reported' => $match['reported_item']->date_lost?->format('Y-m-d'),
                            'found' => $match['found_item']->date_found?->format('Y-m-d'),
                        ],
                        'attributes_match' => [
                            'brand' => $match['reported_item']->brand === $match['found_item']->brand,
                            'model' => $match['reported_item']->model === $match['found_item']->model,
                            'color' => $match['reported_item']->color === $match['found_item']->color,
                            'serial_number' => $match['reported_item']->serial_number === $match['found_item']->serial_number,
                        ]
                    ])
                ];
            })->toArray();

            $this->showMatches = true;
            $this->progress = 100;

            if (count($this->matches) > 0) {
                $this->notification()->success(
                    'Matches Found!',
                    'Found ' . count($this->matches) . ' potential matches! We\'ve included matches with 40% or higher similarity.'
                );
            } else {
                $this->notification()->info(
                    'No Matches Found',
                    'No matches found yet. We\'ll keep looking for items with 40% or higher similarity!'
                );
            }

            Log::info('Match finding process completed', [
                'matches_found' => count($this->matches)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in findMatches: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $this->notification()->error(
                'Error',
                'An error occurred while finding matches: ' . $e->getMessage()
            );
        } finally {
            $this->isLoading = false;
            $this->showAnalysisModal = false;
        }
    }

    public function refreshMatches()
    {
        $this->reset(['matches', 'showMatches']);
        $this->fetchUnmatchedItems();
        $this->findMatches();
    }

    public function render()
    {
        return view('livewire.match-lost-and-found-items', [
            'unmatchedItems' => $this->unmatchedItems,
            'matches' => collect($this->matches),
            'hasUnmatchedItems' => $this->unmatchedItems->isNotEmpty(),
        ]);
    }
}
