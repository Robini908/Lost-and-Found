<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\User;
use App\Models\ItemMessage;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Services\ItemMatchingService;
use Usernotnull\Toast\Concerns\WireToast;
use Illuminate\Support\Str;
use App\Mail\ContactItemFounder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\ItemClaim;

class MatchLostAndFoundItems extends Component
{
    use WithPagination;
    use WireToast;

    /**
     * @var array<int, object{
     *   reported_item: array{id: int},
     *   found_item: array{id: int},
     *   similarity_score: float
     * }>
     */
    public array $matches = [];

    /** @var \Illuminate\Database\Eloquent\Collection */
    public $unmatchedItems;

    public $showBanner = false;
    public $bannerMessage = '';
    public $showMatches = false;
    public $isLoading = false;
    public $loadingMessage = '';
    public $progress = 0;
    public $showAnalysisModal = false;
    public $showContactModal = false;
    public $showClaimModal = false;
    public $showItemMatchingPage = false;
    public $lastUpdateTimestamp;
    public $contactMessage = '';
    public $selectedMatchIndex = null;
    public $selectedMatch = null;
    public $matchAnalysis = null;
    public $founderContact = null;
    public $claimDetails = null;
    public $lastItemsHash;
    public $isPolling = true;
    public $pollInterval = 60000; // 1 minute in milliseconds

    public $messages = [
        "Gathering requirements...",
        "Calculating similarity scores...",
        "Analyzing images...",
        "Matching locations...",
        "Finalizing results...",
        "Hold on a moment, this will not take long..."
    ];

    protected $itemMatchingService;

    protected $listeners = [
        'echo:lost-found,ItemUpdated' => '$refresh',
        'refreshMatches' => 'refreshMatches'
    ];

    public function boot()
    {
        $this->itemMatchingService = new ItemMatchingService();
    }

    public function mount()
    {
        $this->fetchUnmatchedItems();
        $this->lastUpdateTimestamp = now();
        $this->lastItemsHash = $this->calculateItemsHash();
        $this->loadMatches();
    }

    public function getListeners()
    {
        return [
            'echo:lost-found,ItemUpdated' => '$refresh',
            'refreshMatches' => 'refreshMatches',
            'poll.state' => 'handlePollState'
        ];
    }

    public function handlePollState($state)
    {
        if (isset($state['lastUpdateTimestamp']) && $state['lastUpdateTimestamp'] !== $this->lastUpdateTimestamp) {
            $this->refreshMatches();
        }
    }

    public function fetchUnmatchedItems()
    {
        $user = Auth::user();
        $this->unmatchedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])
            ->whereNull('matched_found_item_id')  // Only get items that haven't been matched
            ->with(['images', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function loadMatches()
    {
        $user = Auth::user();
        $this->matches = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])
            ->whereNotNull('matched_found_item_id')  // Only get matched items
            ->with(['images', 'category', 'matchedFoundItem.user', 'matchedFoundItem.images'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'reported_item' => $item,
                    'found_item' => $item->matchedFoundItem,
                    'similarity_score' => $this->calculateSimilarityScore($item, $item->matchedFoundItem),
                    'match_details' => [
                        'location_match' => [
                            'reported' => $item->location_type === 'map' ? $item->location_address : $item->area,
                            'found' => $item->matchedFoundItem->location_type === 'map' ?
                                $item->matchedFoundItem->location_address : $item->matchedFoundItem->area
                        ],
                        'date_match' => [
                            'reported' => $item->date_lost?->format('Y-m-d'),
                            'found' => $item->matchedFoundItem->date_found?->format('Y-m-d')
                        ],
                        'attributes_match' => [
                            'brand' => $item->brand === $item->matchedFoundItem->brand,
                            'model' => $item->model === $item->matchedFoundItem->model,
                            'color' => $item->color === $item->matchedFoundItem->color,
                            'serial_number' => $item->serial_number === $item->matchedFoundItem->serial_number
                        ]
                    ]
                ];
            })->toArray();
    }

    protected function calculateSimilarityScore($reportedItem, $foundItem)
    {
        // Simple similarity calculation for display purposes
        $textSimilarity = similar_text(
            $reportedItem->title . ' ' . $reportedItem->description,
            $foundItem->title . ' ' . $foundItem->description,
            $percent
        );
        return $percent / 100;
    }

    public function toggleItemMatchingPage()
    {
        $this->showItemMatchingPage = !$this->showItemMatchingPage;
        $this->showMatches = false;
    }

    public function refreshMatches()
    {
        try {
            if ($this->checkForUpdates()) {
                $this->findMatches();
                toast()->success('New matches found!');
            } else {
                toast()->info('No new matches available.');
            }
        } catch (\Exception $e) {
            toast()->danger('Error refreshing matches: ' . $e->getMessage());
            logger()->error('Match refresh error: ' . $e->getMessage());
        }
    }

    public function findMatches()
    {
        $this->isLoading = true;
        $this->showAnalysisModal = true;
        $this->progress = 0;
        $this->showMatches = false;
        $this->loadingMessage = $this->messages[0];

        try {
            Log::info('Starting match finding process in Livewire component');
            $user = Auth::user();

            // Get reported/searched items for the current user that haven't been matched yet
            $reportedItems = LostItem::where('user_id', $user->id)
                ->whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])
                ->whereNull('matched_found_item_id')
                ->with(['images', 'category'])
                ->get();

            Log::info('Found reported items', ['count' => $reportedItems->count()]);
            $this->progress = 20;
            $this->loadingMessage = $this->messages[1];

            // Get found items from other users that haven't been claimed
            $foundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)
                ->where('user_id', '!=', $user->id)
                ->whereNull('claimed_by')
                ->whereNull('matched_found_item_id')
                ->with(['images', 'category', 'user'])
                ->get();

            Log::info('Found potential matching items', ['count' => $foundItems->count()]);
            $this->progress = 40;
            $this->loadingMessage = $this->messages[2];

            // Use the ItemMatchingService to find matches
            $matches = $this->itemMatchingService->findMatches($reportedItems, $foundItems);
            $this->progress = 80;
            $this->loadingMessage = $this->messages[4];

            Log::info('Processing matches', ['count' => $matches->count()]);

            // Transform matches into the required format with detailed information
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

            // Update timestamp and hash
            $this->lastUpdateTimestamp = now();
            $this->lastItemsHash = $this->calculateItemsHash();
            $this->showMatches = true;
            $this->progress = 100;

            if (count($this->matches) > 0) {
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Found ' . count($this->matches) . ' potential matches!'
                ]);
                toast()
                    ->success('Found ' . count($this->matches) . ' potential matches!')
                    ->push();
            } else {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'No matches found at this time. We\'ll keep looking!'
                ]);
                toast()
                    ->info('No matches found at this time. We\'ll keep looking!')
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

    /**
     * Show analysis modal for a specific match
     *
     * @param int $matchIndex
     * @return void
     */
    public function showAnalysis(int $matchIndex): void
    {
        /** @var object{reported_item: array{id: int}, found_item: array{id: int}, similarity_score: float} $match */
        $match = (object) $this->matches[$matchIndex];
        $reportedItem = LostItem::with(['images', 'category'])->find($match->reported_item['id']);
        $foundItem = LostItem::with(['images', 'category'])->find($match->found_item['id']);

        $this->matchAnalysis = [
            'reported_item' => $reportedItem,
            'found_item' => $foundItem,
            'similarity_score' => $match->similarity_score,
            'text_similarity' => $this->calculateTextSimilarity($reportedItem, $foundItem),
            'image_similarity' => $this->calculateBestImageSimilarity($reportedItem, $foundItem),
            'location_similarity' => $this->calculateLocationSimilarity($reportedItem, $foundItem),
            'time_similarity' => $this->calculateTimeSimilarity($reportedItem, $foundItem)
        ];

        $this->showAnalysisModal = true;
    }

    protected function calculateTextSimilarity($item1, $item2)
    {
        similar_text(
            $item1->title . ' ' . $item1->description,
            $item2->title . ' ' . $item2->description,
            $percent
        );
        return $percent / 100;
    }

    protected function calculateBestImageSimilarity($item1, $item2)
    {
        // Simplified image similarity for display
        return 0.75; // Placeholder value
    }

    protected function calculateLocationSimilarity($item1, $item2)
    {
        if (!$item1->geolocation || !$item2->geolocation) {
            return 0;
        }

        // Simple distance-based similarity
        $lat1 = $item1->geolocation['lat'];
        $lon1 = $item1->geolocation['lng'];
        $lat2 = $item2->geolocation['lat'];
        $lon2 = $item2->geolocation['lng'];

        $distance = $this->calculateDistance($lat1, $lon1, $lat2, $lon2);
        return max(0, 1 - ($distance / 10000)); // Normalize distance to similarity
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344; // Convert to kilometers
    }

    protected function calculateTimeSimilarity($item1, $item2)
    {
        if (!$item1->date_lost || !$item2->date_found) {
            return 0;
        }

        $days = abs($item1->date_lost->diffInDays($item2->date_found));
        return max(0, 1 - ($days / 30)); // Normalize days to similarity (max 30 days difference)
    }

    /**
     * Show contact modal for a specific match
     *
     * @param int $matchIndex
     * @return void
     */
    public function showContact($index)
    {
        try {
            $this->selectedMatchIndex = $index;
            $match = $this->matches[$index];

            // Load the found item with its relationships
            $foundItem = LostItem::with(['user'])->findOrFail($match['found_item']['id']);

            // Update the match with the loaded found item
            $match['found_item'] = $foundItem;
            $this->selectedMatch = $match;

            $this->showContactModal = true;
        } catch (\Exception $e) {
            Log::error('Error showing contact modal: ' . $e->getMessage());
            toast()->danger('Unable to load contact information. Please try again.');
        }
    }

    /**
     * Show claim modal for a specific match
     *
     * @param int $matchIndex
     * @return void
     */
    public function showClaim(int $matchIndex): void
    {
        /** @var object{reported_item: array{id: int}, found_item: array{id: int}, similarity_score: float} $match */
        $match = (object) $this->matches[$matchIndex];
        $reportedItem = LostItem::with(['category'])->find($match->reported_item['id']);
        $foundItem = LostItem::with(['user', 'category'])->find($match->found_item['id']);

        $this->claimDetails = [
            'reported_item' => $reportedItem,
            'found_item' => $foundItem,
            'similarity_score' => $match->similarity_score
        ];

        $this->showClaimModal = true;
    }

    public function contactFounder($selectedMatchIndex)
    {
        try {
            $this->selectedMatchIndex = $selectedMatchIndex;
            $match = $this->matches[$selectedMatchIndex];
            $foundItem = $match['found_item'];
            $founder = User::findOrFail($foundItem['user_id']);

            // Prepare email data
            $emailData = [
                'founderName' => $founder->name,
                'requesterName' => Auth::user()->name,
                'requesterEmail' => Auth::user()->email,
                'itemTitle' => $foundItem['title'],
                'location' => $foundItem['location_address'] ?? $foundItem['area'] ?? 'Location not specified',
                'dateFound' => isset($foundItem['date_found']) ? Carbon::parse($foundItem['date_found'])->format('F j, Y') : 'Not specified',
                'similarityScore' => $match['similarity_score'],
            ];

            // Send confirmation email to founder
            Mail::to($founder->email)->queue(new ContactItemFounder($emailData));

            toast()->success('Confirmation email sent to the finder.');
            $this->showContactModal = false;

        } catch (\Exception $e) {
            logger()->error('Error sending confirmation email: ' . $e->getMessage());
            toast()->danger('Unable to send confirmation email. Please try again later.');
        }
    }

    public function claimItem()
    {
        if (!$this->claimDetails) {
            toast()->danger('No item selected for claiming.');
            return;
        }

        try {
            $reportedItem = $this->claimDetails['reported_item'];
            $foundItem = $this->claimDetails['found_item'];

            // Create a new claim
            $claim = ItemClaim::create([
                'user_id' => Auth::id(),
                'lost_item_id' => $reportedItem->id,
                'found_item_id' => $foundItem->id,
                'status' => 'pending',
                'claim_details' => [
                    'similarity_score' => $this->claimDetails['similarity_score'],
                    'reported_date' => $reportedItem->date_lost,
                    'found_date' => $foundItem->date_found
                ]
            ]);

            $this->showClaimModal = false;
            toast()->success('Claim submitted successfully!');

            // Redirect to the claim verification page
            return redirect()->route('claims.verify', ['claimId' => $claim->id]);

        } catch (\Exception $e) {
            toast()->danger('Failed to submit claim: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showAnalysisModal = false;
        $this->showContactModal = false;
        $this->showClaimModal = false;
        $this->matchAnalysis = null;
        $this->founderContact = null;
        $this->claimDetails = null;
        $this->contactMessage = '';
    }

    protected function calculateItemsHash()
    {
        $user = Auth::user();

        // Get all relevant items
        $reportedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', ['reported', 'searched'])
            ->get();

        $foundItems = LostItem::where('item_type', 'found')
            ->where('user_id', '!=', $user->id)
            ->get();

        // Create a string containing all relevant item data
        $itemsData = $reportedItems->concat($foundItems)->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'updated_at' => $item->updated_at->timestamp,
                'status' => $item->status,
                'matched_found_item_id' => $item->matched_found_item_id,
            ];
        })->toJson();

        return md5($itemsData);
    }

    public function togglePolling()
    {
        $this->isPolling = !$this->isPolling;
        $this->dispatch('polling-toggled', isPolling: $this->isPolling);
    }

    public function checkForUpdates()
    {
        $currentHash = $this->calculateItemsHash();

        if ($currentHash !== $this->lastItemsHash) {
            $this->refreshMatches();
            $this->lastItemsHash = $currentHash;
            return true;
        }

        return false;
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
