<?php

namespace App\Http\Livewire;

use App\Models\LostItem;
use App\Services\ItemMatchingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class MatchLostAndFoundItems extends Component
{
    public $isLoading = false;
    public $showAnalysisModal = false;
    public $progress = 0;
    public $loadingMessage = '';
    public $matches = [];
    public $showMatches = false;

    protected $itemMatchingService;

    public function __construct(ItemMatchingService $itemMatchingService)
    {
        $this->itemMatchingService = $itemMatchingService;
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

            // Get reported/searched items for the current user
            $reportedItems = LostItem::where('user_id', $user->id)
                ->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])
                ->with(['images', 'category'])
                ->get();

            Log::info('Found reported items', ['count' => $reportedItems->count()]);
            $this->progress = 20;
            $this->loadingMessage = 'Found ' . $reportedItems->count() . ' of your reported items...';

            // Get found items from other users
            $foundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)
                ->where('user_id', '!=', $user->id)
                ->with(['images', 'category', 'user'])
                ->get();

            Log::info('Found potential matching items', ['count' => $foundItems->count()]);
            $this->progress = 40;
            $this->loadingMessage = 'Analyzing ' . $foundItems->count() . ' potential matches...';

            // Use the ItemMatchingService to find matches
            $matches = $this->itemMatchingService->findMatches($reportedItems, $foundItems);
            $this->progress = 80;
            $this->loadingMessage = 'Processing match results...';

            Log::info('Processing matches', ['count' => $matches->count()]);

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
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Found ' . count($this->matches) . ' potential matches! We\'ve included matches with 40% or higher similarity.'
                ]);
                toast()
                    ->success('Found ' . count($this->matches) . ' potential matches!')
                    ->push();
            } else {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'No matches found yet. We\'ll keep looking for items with 40% or higher similarity!'
                ]);
                toast()
                    ->info('No matches found yet. Keep checking back!')
                    ->push();
            }

            Log::info('Match finding process completed', [
                'matches_found' => count($this->matches)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in findMatches: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred while finding matches.'
            ]);
            toast()
                ->danger('An error occurred while finding matches: ' . $e->getMessage())
                ->push();
        } finally {
            $this->isLoading = false;
            $this->showAnalysisModal = false;
        }
    }
}
