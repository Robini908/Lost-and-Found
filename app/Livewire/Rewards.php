<?php

namespace App\Livewire;

use Carbon\Carbon;
use Mpdf\Mpdf;
use App\Models\Setting;
use Livewire\Component;
use App\Models\RewardHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Usernotnull\Toast\Concerns\WireToast;
use App\Models\User;
use App\Models\LostItem;
use Asantibanez\LivewireCharts\Models\LineChartModel;

class Rewards extends Component
{
    use WireToast;

    public ?User $user = null;
    public int $availablePoints = 0;
    public float $dollarValue = 0;
    public float $conversionRate;
    public string $currency;
    public string $currencySymbol;
    public ?float $customConversionAmount = null;
    public array $rewardHistory = [];
    public array $chartData = [];
    public int $totalEarnedPoints = 0;
    public int $totalConvertedPoints = 0;
    public int $pointsExpiringSoon = 0;
    public string $selectedPeriod = '30days';
    public ?string $selectedType = null;
    public ?string $selectedCategory = null;
    public bool $showLineChart = true;
    public bool $firstRun = true;
    public bool $showConvertModal = false;
    public bool $showHistoryModal = false;
    public $showFilterModal = false;
    public $showRedoConversionModal = false;
    public $showStatsModal = false;
    public int $minPointsConvert = 1000;
    public int $rewardPointsExpiryDays = 365;
    public int $foundItemRewardPoints = 100;
    public bool $isPolling = true;
    public int $pollInterval = 10000; // 10 seconds
    public $lastCheckedItemId = null;

    // Filter properties
    public $dateFrom = null;
    public $dateTo = null;
    public $typeFilter = 'all';
    public $minPoints = null;
    public $maxPoints = null;
    public $sortBy = 'date';
    public $sortDirection = 'desc';
    public $categoryFilter = 'all';

    // Redo conversion properties
    public $selectedConversion;
    public $pointsToRedo;

    // Stats properties
    public $stats = [];

    // Add rules for filter validation
    protected $rules = [
        'dateFrom' => 'nullable|date',
        'dateTo' => 'nullable|date|after_or_equal:dateFrom',
        'minPoints' => 'nullable|numeric|min:0',
        'maxPoints' => 'nullable|numeric|min:0|gte:minPoints',
        'typeFilter' => 'in:all,earned,converted,bonus,referral',
        'categoryFilter' => 'string',
        'sortBy' => 'in:date,points',
        'sortDirection' => 'in:asc,desc'
    ];

    protected $listeners = [
        'foundItemReported' => 'refreshData',
        'echo:lost-items,ItemFound' => 'handleNewFoundItem',
        'poll.tick' => 'checkForNewFoundItems'
    ];

    public function mount()
    {
        $this->user = Auth::user();
        if (!$this->user) {
            return redirect()->route('login');
        }

        // Load settings
        $this->loadSettings();

        // Check for unrewarded found items
        $this->checkAndAwardFoundItemPoints();

        // Refresh user data to get updated points
        $this->user = User::find(Auth::id());
        $this->availablePoints = $this->user->reward_points ?? 0;
        $this->dollarValue = $this->availablePoints * $this->conversionRate;

        Log::info('Rewards component mounted', [
            'user_id' => Auth::id(),
            'available_points' => $this->availablePoints,
            'dollar_value' => $this->dollarValue
        ]);

        $this->refreshData();
    }

    protected function loadSettings()
    {
        // Load global settings with defaults
        $this->conversionRate = (float) Setting::get('points_conversion_rate', 0.01);
        $this->currency = Setting::get('currency', 'USD');
        $this->currencySymbol = Setting::get('currency_symbol', '$');
        $this->minPointsConvert = (int) Setting::get('min_points_convert', 1000);
        $this->rewardPointsExpiryDays = (int) Setting::get('reward_points_expiry_days', 365);
        $this->foundItemRewardPoints = (int) Setting::get('found_item_reward_points', 100);

        // Log settings for debugging
        Log::info('Rewards settings loaded', [
            'conversion_rate' => $this->conversionRate,
            'currency' => $this->currency,
            'currency_symbol' => $this->currencySymbol,
            'min_points_convert' => $this->minPointsConvert,
            'reward_points_expiry_days' => $this->rewardPointsExpiryDays,
            'found_item_reward_points' => $this->foundItemRewardPoints
        ]);
    }

    // Add polling configuration
    public function getPollingConfiguration()
    {
        return [
            'enabled' => $this->isPolling,
            'method' => 'checkForNewFoundItems',
            'interval' => $this->pollInterval
        ];
    }

    /**
     * Handle new found item event from broadcast
     */
    public function handleNewFoundItem($event)
    {
        $this->checkAndAwardFoundItemPoints();
        $this->refreshData();
    }

    /**
     * Check for new found items in real-time
     */
    public function checkForNewFoundItems()
    {
        try {
            $latestFoundItem = DB::table('lost_items')
                ->where('user_id', Auth::id())
                ->where('item_type', 'found')
                ->latest('id')
                ->first();

            if ($latestFoundItem && ($this->lastCheckedItemId === null || $latestFoundItem->id > $this->lastCheckedItemId)) {
                $this->lastCheckedItemId = $latestFoundItem->id;
                $this->checkAndAwardFoundItemPoints();
                $this->refreshData();

                Log::info('New found item detected and processed', [
                    'user_id' => Auth::id(),
                    'item_id' => $latestFoundItem->id,
                    'last_checked_id' => $this->lastCheckedItemId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to check for new found items: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle polling state
     */
    public function togglePolling()
    {
        $this->isPolling = !$this->isPolling;
        $this->dispatch('polling-toggled', ['isPolling' => $this->isPolling]);
    }

    /**
     * Check for unrewarded found items and award points
     */
    protected function checkAndAwardFoundItemPoints()
    {
        try {
            // Get found items by the user that haven't been rewarded yet - Improved query
            $unrewardedItems = DB::table('lost_items AS li')
                ->select('li.*')
                ->leftJoin('reward_histories AS rh', function($join) {
                    $join->on('li.id', '=', 'rh.lost_item_id')
                         ->where('rh.type', '=', 'earned')
                         ->where('rh.category', '=', 'found_item');
                })
                ->where('li.user_id', Auth::id())
                ->where('li.item_type', 'found')
                ->whereNull('rh.id')  // Ensures no reward exists
                ->where('li.created_at', '>=', now()->subDays(30))  // Only items from last 30 days
                ->get();

            if ($unrewardedItems->isEmpty()) {
                Log::info('No unrewarded found items to process', [
                'user_id' => Auth::id(),
                    'timestamp' => now()
                ]);
                return;
            }

            DB::beginTransaction();

            $totalPointsAwarded = 0;
            $itemsProcessed = 0;

            foreach ($unrewardedItems as $item) {
                try {
                    // Create reward history entry with explicit string values
                    $rewardHistory = RewardHistory::create([
                    'user_id' => Auth::id(),
                        'type' => 'earned',  // String value for type
                    'points' => $this->foundItemRewardPoints,
                    'conversion_rate' => $this->conversionRate,
                    'converted_amount' => 0,
                    'currency' => $this->currency,
                    'description' => 'Reward for reporting found item: ' . $item->title,
                        'category' => 'found_item',  // String value for category
                    'lost_item_id' => $item->id,
                    'metadata' => [
                        'item_type' => $item->item_type,
                            'location' => $item->location_address ?? '',
                        'currency_symbol' => $this->currencySymbol,
                            'points_awarded' => $this->foundItemRewardPoints,
                            'awarded_at' => now()->toDateTimeString(),
                            'item_title' => $item->title,
                            'item_category' => $item->category_id
                    ],
                    'expires_at' => now()->addDays($this->rewardPointsExpiryDays),
                    'is_expired' => false
                ]);

                    if ($rewardHistory) {
                        // Update user's total points
                        $pointsUpdated = DB::table('users')
                    ->where('id', Auth::id())
                    ->increment('reward_points', $this->foundItemRewardPoints);

                        if ($pointsUpdated) {
                            $totalPointsAwarded += $this->foundItemRewardPoints;
                            $itemsProcessed++;

                Log::info('Reward points awarded for found item', [
                    'user_id' => Auth::id(),
                    'points' => $this->foundItemRewardPoints,
                                'item_id' => $item->id,
                                'reward_history_id' => $rewardHistory->id,
                                'timestamp' => now()
                            ]);
                        }
                    }
                } catch (\Exception $itemError) {
                    Log::error('Error processing individual found item: ' . $itemError->getMessage(), [
                        'item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'error' => $itemError->getMessage(),
                        'trace' => $itemError->getTraceAsString()
                    ]);
                    continue;
                }
            }

            DB::commit();

            // Show notification only if points were actually awarded
            if ($itemsProcessed > 0) {
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => "You've earned {$totalPointsAwarded} points for reporting " .
                                ($itemsProcessed === 1 ? 'a found item!' : $itemsProcessed . ' found items!')
                ]);

                // Refresh the data immediately
                $this->refreshData();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award found item points: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to process reward points. Please try again.'
            ]);
        }
    }

    public function refreshData()
    {
        // Check for unrewarded found items
        $this->checkAndAwardFoundItemPoints();

        // Refresh user data to get updated points
        $this->user = User::find(Auth::id());
        $this->availablePoints = $this->user->reward_points ?? 0;
        $this->loadSettings(); // Refresh settings
        $this->loadRewardHistory();
        $this->calculateStats();

        Log::info('Rewards data refreshed', [
            'user_id' => Auth::id(),
            'available_points' => $this->availablePoints
        ]);
    }

    protected function loadRewardHistory()
    {
        try {
            $query = RewardHistory::where('user_id', Auth::id())
                ->with('lostItem')
                ->where('is_expired', false);

            // Apply filters
            if ($this->dateFrom) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->dateFrom));
            }
            if ($this->dateTo) {
                $query->whereDate('created_at', '<=', Carbon::parse($this->dateTo));
            }
            if ($this->typeFilter !== 'all') {
                $query->whereJsonContains('metadata->type', $this->typeFilter);
            }
            if ($this->categoryFilter !== 'all') {
                $query->whereJsonContains('metadata->category', $this->categoryFilter);
            }
            if ($this->minPoints) {
                $query->where('points', '>=', $this->minPoints);
            }
            if ($this->maxPoints) {
                $query->where('points', '<=', $this->maxPoints);
            }

            // Apply sorting
            $query->orderBy(
                $this->sortBy === 'date' ? 'created_at' : 'points',
                $this->sortDirection
            );

            $this->rewardHistory = $query->get()
                ->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'type' => $history->metadata['type'] ?? 'unknown',
                        'points' => $history->points,
                        'description' => $history->description,
                        'date' => $history->created_at->format('Y-m-d'),
                        'category' => $history->metadata['category'] ?? 'unknown',
                        'conversion_rate' => $history->conversion_rate,
                        'converted_amount' => $history->converted_amount,
                        'currency' => $history->currency,
                        'expires_at' => $history->expires_at ? Carbon::parse($history->expires_at)->format('Y-m-d') : null,
                        'metadata' => $history->metadata,
                        'item' => $history->lostItem,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load reward history: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to load reward history. Please try again.'
            ]);
            $this->rewardHistory = [];
        }
    }

    protected function calculateStats()
    {
        // Convert array to collection for proper method access
        $rewardCollection = collect($this->rewardHistory);

        // Calculate basic stats
        $this->stats = [
            'total_earned' => $rewardCollection->where('metadata.type', 'earned')->sum('points'),
            'total_converted' => abs($rewardCollection->where('metadata.type', 'converted')->sum('points')),
            'total_bonus' => $rewardCollection->where('metadata.type', 'bonus')->sum('points'),
            'total_referral' => $rewardCollection->where('metadata.type', 'referral')->sum('points'),
            'conversion_count' => $rewardCollection->where('metadata.type', 'converted')->count(),
            'average_points_per_conversion' => $this->calculateAveragePointsPerConversion(),
            'points_growth_rate' => $this->calculatePointsGrowthRate(),
            'estimated_monthly_earnings' => $this->calculateEstimatedMonthlyEarnings(),
            'points_expiring_soon' => $this->calculateExpiringPoints(),
            'category_distribution' => $this->calculateCategoryDistribution(),
        ];

        // Prepare chart data based on selected period
        $this->prepareChartData();
    }

    protected function calculateAveragePointsPerConversion()
    {
        $rewardCollection = collect($this->rewardHistory);
        $conversions = $rewardCollection->where('metadata.type', 'converted');
        return $conversions->count() > 0 ? abs($conversions->sum('points')) / $conversions->count() : 0;
    }

    protected function calculatePointsGrowthRate()
    {
        $rewardCollection = collect($this->rewardHistory);
        $currentMonth = $rewardCollection
            ->where('metadata.type', 'earned')
            ->where('date', '>=', Carbon::now()->startOfMonth()->format('Y-m-d'))
            ->sum('points');

        $lastMonth = $rewardCollection
            ->where('metadata.type', 'earned')
            ->where('date', '>=', Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'))
            ->where('date', '<', Carbon::now()->startOfMonth()->format('Y-m-d'))
            ->sum('points');

        return $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    protected function calculateEstimatedMonthlyEarnings()
    {
        $rewardCollection = collect($this->rewardHistory);
        $last3Months = $rewardCollection
            ->where('metadata.type', 'earned')
            ->where('date', '>=', Carbon::now()->subMonths(3)->format('Y-m-d'))
            ->sum('points');

        return $last3Months / 3;
    }

    protected function calculateExpiringPoints()
    {
        $rewardCollection = collect($this->rewardHistory);
        return $rewardCollection
            ->where('expires_at', '!=', null)
            ->where('expires_at', '>=', Carbon::now()->format('Y-m-d'))
            ->where('expires_at', '<=', Carbon::now()->addDays(30)->format('Y-m-d'))
            ->sum('points');
    }

    protected function calculateCategoryDistribution()
    {
        $rewardCollection = collect($this->rewardHistory);
        return $rewardCollection
            ->where('metadata.type', 'earned')
            ->groupBy('metadata.category')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'points' => $items->sum('points')
                ];
            });
    }

    protected function prepareChartData()
    {
        $data = collect();
        $dates = [];
        $earnedPoints = [];
        $convertedPoints = [];

        switch ($this->selectedPeriod) {
            case '7days':
                $startDate = now()->subDays(7);
                $groupFormat = 'Y-m-d';
                $displayFormat = 'M d';
                break;
            case '30days':
                $startDate = now()->subDays(30);
                $groupFormat = 'Y-m-d';
                $displayFormat = 'M d';
                break;
            case '90days':
                $startDate = now()->subDays(90);
                $groupFormat = 'Y-m';
                $displayFormat = 'M Y';
                break;
            case 'year':
                $startDate = now()->subYear();
                $groupFormat = 'Y-m';
                $displayFormat = 'M Y';
                break;
            default:
                $startDate = now()->subDays(30);
                $groupFormat = 'Y-m-d';
                $displayFormat = 'M d';
        }

        // Get earned points
        $earned = RewardHistory::where('user_id', Auth::id())
            ->where('created_at', '>=', $startDate)
            ->whereJsonContains('metadata->type', 'earned')
            ->selectRaw('DATE_FORMAT(created_at, ?) as date, SUM(points) as total', [$groupFormat])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get converted points
        $converted = RewardHistory::where('user_id', Auth::id())
            ->where('created_at', '>=', $startDate)
            ->whereJsonContains('metadata->type', 'converted')
            ->selectRaw('DATE_FORMAT(created_at, ?) as date, SUM(points) as total', [$groupFormat])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare dates array
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            now()->addDay()
        );

        foreach ($period as $date) {
            $formattedDate = $date->format($groupFormat);
            $displayDate = $date->format($displayFormat);

            $earnedValue = $earned->firstWhere('date', $formattedDate)?->total ?? 0;
            $convertedValue = $converted->firstWhere('date', $formattedDate)?->total ?? 0;

            // Add data points for earned points
            $data->push([
                'seriesName' => 'Earned Points',
                'date' => $displayDate,
                'value' => $earnedValue
            ]);

            // Add data points for converted points
            $data->push([
                'seriesName' => 'Converted Points',
                'date' => $displayDate,
                'value' => $convertedValue
            ]);
        }

        return $data;
    }

    public function getPointsChartModel()
    {
        $data = $this->prepareChartData();

        $chart = new LineChartModel();
        $chart->setTitle('Points Activity')
            ->setAnimated($this->firstRun)
            ->withOnPointClickEvent('onPointClick')
            ->multiLine()
            ->setXAxisVisible(true)
            ->setYAxisVisible(true);

        // Add earned points series
        $earnedData = $data->where('seriesName', 'Earned Points');
        $chart->addPoint(
            'Earned Points',
            $earnedData->pluck('value')->toArray(),
            '#10B981'
        );

        // Add converted points series
        $convertedData = $data->where('seriesName', 'Converted Points');
        $chart->addPoint(
            'Converted Points',
            $convertedData->pluck('value')->toArray(),
            '#EF4444'
        );

        $chart->setColors(['#10B981', '#EF4444'])
            ->withGrid();

        return $chart;
    }

    public function onPointClick($point)
    {
        $this->emit('pointClicked', $point);
    }

    public function convertPoints()
    {
        try {
            // Validate the conversion amount
            $this->validate([
                'customConversionAmount' => 'required|numeric|min:1|max:' . $this->availablePoints
            ], [
                'customConversionAmount.max' => 'You cannot convert more points than you have available.'
            ]);

            $points = (int)$this->customConversionAmount;
            $conversionRate = (float)Setting::get('points_conversion_rate', 0.01);
            $currencySymbol = Setting::get('currency_symbol', '$');
            $convertedAmount = $points * $conversionRate;

            DB::beginTransaction();

            try {
                // Create conversion record
            $rewardHistory = RewardHistory::create([
                    'user_id' => Auth::id(),
                    'points' => -$points, // Negative points for conversion
                    'conversion_rate' => $conversionRate,
                    'converted_amount' => $convertedAmount,
                    'currency' => $this->currency ?? 'USD',
                    'description' => "Converted {$points} points to {$currencySymbol}{$convertedAmount}",
                    'metadata' => [
                'type' => 'converted',
                        'category' => 'points_conversion',
                        'points_converted' => $points,
                        'conversion_rate' => $conversionRate,
                        'currency_symbol' => $currencySymbol,
                        'converted_at' => now()->toDateTimeString(),
                        'converted_amount' => $convertedAmount
                    ],
                    'expires_at' => null // Conversions don't expire
                ]);

                // Update user's total points
                DB::table('users')
                    ->where('id', Auth::id())
                    ->decrement('reward_points', $points);

            DB::commit();

                // Reset the conversion amount
            $this->customConversionAmount = null;
                $this->showConvertModal = false;

                // Refresh the data
            $this->refreshData();

                toast()
                    ->success("Successfully converted {$points} points to {$currencySymbol}{$convertedAmount}")
                    ->push();

                // Emit event for real-time updates
                $this->dispatch('pointsConverted', [
                    'points' => $points,
                    'amount' => $convertedAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Points conversion failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'points' => $this->customConversionAmount ?? null
            ]);

            toast()
                ->danger('Failed to convert points: ' . $e->getMessage())
                ->push();
        }
    }

    // Add helper method for minimum conversion amount
    public function getMinimumConversionAmount()
    {
        return (int)Setting::get('minimum_conversion_amount', 100);
    }

    // Add helper method for maximum conversion amount
    public function getMaximumConversionAmount()
    {
        return min(
            (int)Setting::get('maximum_conversion_amount', 10000),
            $this->availablePoints
        );
    }

    // Add computed property for converted amount preview
    public function getConvertedAmountPreviewProperty()
    {
        if (!is_numeric($this->customConversionAmount)) {
            return 0;
        }

        $conversionRate = (float)Setting::get('points_conversion_rate', 0.01);
        return number_format($this->customConversionAmount * $conversionRate, 2);
    }

    public function exportHistory()
    {
        try {
            $history = $this->user->rewardHistories()
                ->when($this->selectedPeriod, function ($query) {
                    return match($this->selectedPeriod) {
                        'today' => $query->whereDate('created_at', today()),
                        'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                        'month' => $query->whereMonth('created_at', now()->month),
                        'year' => $query->whereYear('created_at', now()->year),
                        default => $query
                    };
                })
                ->latest()
                ->get();

            $mpdf = new \Mpdf\Mpdf([
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 20,
                'margin_bottom' => 20,
            ]);

            $html = view('pdf.reward-history', [
                'history' => $history,
                'user' => $this->user,
                'period' => ucfirst($this->selectedPeriod ?: 'all time'),
                'generated_at' => now()->format('Y-m-d H:i:s')
            ])->render();

            $mpdf->WriteHTML($html);

            $filename = 'reward-history-' . now()->format('Y-m-d') . '.pdf';
            $mpdf->Output($filename, 'D');

        } catch (\Exception $e) {
            Log::error('Failed to export reward history: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to export reward history. Please try again later.'
            ]);
        }
    }

    public function updateChartPeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->prepareChartData();
    }

    public function updatedDateFrom()
    {
        $this->validateOnly('dateFrom');
        $this->refreshData();
    }

    public function updatedDateTo()
    {
        $this->validateOnly('dateTo');
        $this->refreshData();
    }

    public function updatedTypeFilter()
    {
        $this->validateOnly('typeFilter');
        $this->refreshData();
    }

    public function updatedCategoryFilter()
    {
        $this->validateOnly('categoryFilter');
        $this->refreshData();
    }

    public function updatedMinPoints()
    {
        $this->validateOnly('minPoints');
        $this->refreshData();
    }

    public function updatedMaxPoints()
    {
        $this->validateOnly('maxPoints');
        $this->refreshData();
    }

    public function updatedSortBy()
    {
        $this->validateOnly('sortBy');
        $this->refreshData();
    }

    public function updatedSortDirection()
    {
        $this->validateOnly('sortDirection');
        $this->refreshData();
    }

    public function resetFilters()
    {
        $this->reset([
            'dateFrom',
            'dateTo',
            'typeFilter',
            'minPoints',
            'maxPoints',
            'categoryFilter',
            'sortBy',
            'sortDirection'
        ]);
        $this->refreshData();
    }

    public function toggleFilterModal()
    {
        $this->showFilterModal = !$this->showFilterModal;
    }

    public function applyFilters()
    {
        $this->validate();
        $this->showFilterModal = false;
        $this->refreshData();
    }

    /**
     * Manually check and award points for a specific found item
     */
    public function manuallyCheckAndAwardPoints($itemId)
    {
        try {
            DB::beginTransaction();

            $item = DB::table('lost_items')
                ->where('id', $itemId)
                ->where('item_type', 'found')
                ->first();

            if (!$item) {
                Log::error('Item not found or not eligible for points', [
                    'item_id' => $itemId,
                    'user_id' => Auth::id()
                ]);
                return false;
            }

            // Check if points were already awarded
            $existingReward = RewardHistory::where('lost_item_id', $itemId)
                ->where('type', '=', 'earned')
                ->where('category', '=', 'found_item')
                ->first();

            if ($existingReward) {
                Log::info('Points already awarded for this item', [
                    'item_id' => $itemId,
                    'reward_id' => $existingReward->id
                ]);
                return false;
            }

            // Create reward history entry with explicit string values
            $rewardHistory = RewardHistory::create([
                'user_id' => $item->user_id,
                'type' => 'earned',  // String value for type
                'points' => $this->foundItemRewardPoints,
                'conversion_rate' => $this->conversionRate,
                'converted_amount' => 0,
                'currency' => $this->currency,
                'description' => 'Reward for reporting found item: ' . $item->title,
                'category' => 'found_item',  // String value for category
                'lost_item_id' => $item->id,
                'metadata' => [
                    'item_type' => $item->item_type,
                    'location' => $item->location_address ?? '',
                    'currency_symbol' => $this->currencySymbol,
                    'points_awarded' => $this->foundItemRewardPoints,
                    'awarded_at' => now()->toDateTimeString(),
                    'item_title' => $item->title,
                    'item_category' => $item->category_id,
                    'manual_check' => true
                ],
                'expires_at' => now()->addDays($this->rewardPointsExpiryDays),
                'is_expired' => false
            ]);

            // Update user's total points
            $pointsUpdated = DB::table('users')
                ->where('id', $item->user_id)
                ->increment('reward_points', $this->foundItemRewardPoints);

            if ($pointsUpdated) {
                Log::info('Points manually awarded for found item', [
                    'item_id' => $itemId,
                    'user_id' => $item->user_id,
                    'points' => $this->foundItemRewardPoints,
                    'reward_history_id' => $rewardHistory->id
                ]);

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to manually award points: ' . $e->getMessage(), [
                'item_id' => $itemId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Check points status for debugging
     */
    public function checkPointsStatus($userId = null)
    {
        try {
            $userId = $userId ?? Auth::id();

            // Get user's current points
            $user = User::find($userId);
            $currentPoints = $user->reward_points ?? 0;

            // Get total earned points from history
            $earnedPoints = RewardHistory::where('user_id', $userId)
                ->where('type', 'earned')
                ->where('is_expired', false)
                ->sum('points');

            // Get total converted points
            $convertedPoints = RewardHistory::where('user_id', $userId)
                ->where('type', 'converted')
                ->where('is_expired', false)
                ->sum('points');

            // Get found items count
            $foundItemsCount = DB::table('lost_items')
                ->where('user_id', $userId)
                ->where('item_type', 'found')
                ->count();

            // Get rewarded found items count
            $rewardedItemsCount = RewardHistory::where('user_id', $userId)
                ->where('type', 'earned')
                ->where('category', 'found_item')
                ->where('is_expired', false)
                ->count();

            $status = [
                'user_id' => $userId,
                'current_points' => $currentPoints,
                'total_earned' => $earnedPoints,
                'total_converted' => $convertedPoints,
                'found_items_count' => $foundItemsCount,
                'rewarded_items_count' => $rewardedItemsCount,
                'points_per_found_item' => $this->foundItemRewardPoints,
                'expected_points' => $foundItemsCount * $this->foundItemRewardPoints,
                'missing_rewards' => $foundItemsCount - $rewardedItemsCount,
                'timestamp' => now()->toDateTimeString()
            ];

            Log::info('Points status check', $status);
            return $status;

        } catch (\Exception $e) {
            Log::error('Failed to check points status: ' . $e->getMessage(), [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function render()
    {
        $this->loadRewardHistory();
        $this->calculateStats();

        // Get available categories for filter
        $categories = collect($this->rewardHistory)
            ->pluck('category')
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        return view('livewire.rewards', [
            'availablePoints' => $this->availablePoints,
            'dollarValue' => sprintf('%s%.2f', $this->currencySymbol, $this->dollarValue),
            'history' => $this->rewardHistory,
            'chartData' => $this->chartData,
            'totalEarnedPoints' => $this->stats['total_earned'],
            'totalConvertedPoints' => $this->stats['total_converted'],
            'pointsExpiringSoon' => $this->stats['points_expiring_soon'],
            'currency' => $this->currency,
            'currencySymbol' => $this->currencySymbol,
            'conversionRate' => $this->conversionRate,
            'categories' => $categories,
            'types' => ['all', 'earned', 'converted', 'bonus', 'referral'],
            'minPointsConvert' => $this->minPointsConvert,
            'rewardPointsExpiryDays' => $this->rewardPointsExpiryDays,
            'foundItemRewardPoints' => $this->foundItemRewardPoints
        ]);
    }
}
