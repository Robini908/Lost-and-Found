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

class Rewards extends Component
{
    use WireToast;

    public $availablePoints;
    public $rewardHistory;
    public $conversionRate;
    public $currencySymbol;
    public $showConvertModal = false;
    public $showHistoryModal = false;
    public $showFilterModal = false;
    public $showRedoConversionModal = false;

    // Filter properties
    public $dateFrom;
    public $dateTo;
    public $typeFilter = 'all';
    public $minPoints;
    public $maxPoints;
    public $sortBy = 'date';
    public $sortDirection = 'desc';

    // Redo conversion properties
    public $selectedConversion;
    public $pointsToRedo;

    protected $listeners = ['foundItemReported' => 'refreshData'];

    public function mount()
    {
        $this->refreshData();
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function refreshData()
    {
        Log::info('Rewards component refreshData called', [
            'user_id' => Auth::id(),
            'previous_points' => $this->availablePoints
        ]);

        $this->availablePoints = Auth::user()->reward_points ?? 0;
        $this->conversionRate = (int) Setting::get('points_to_currency_rate', 100);
        $this->currencySymbol = Setting::get('currency_symbol', 'KSh');

        Log::info('Rewards data refreshed', [
            'user_id' => Auth::id(),
            'new_points' => $this->availablePoints,
            'conversion_rate' => $this->conversionRate
        ]);

        $this->loadRewardHistory();
    }

    protected function loadRewardHistory()
    {
        Log::info('Loading reward history', [
            'user_id' => Auth::id()
        ]);

        $query = RewardHistory::where('user_id', Auth::id())
            ->with('lostItem');

        // Apply filters
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }
        if ($this->minPoints) {
            $query->where('points', '>=', $this->minPoints);
        }
        if ($this->maxPoints) {
            $query->where('points', '<=', $this->maxPoints);
        }

        // Apply sorting
        $query->orderBy($this->sortBy === 'date' ? 'created_at' : 'points', $this->sortDirection);

        $this->rewardHistory = $query->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'type' => $history->type,
                    'points' => $history->points,
                    'description' => $history->description,
                    'date' => $history->created_at->format('Y-m-d'),
                    'item' => $history->lostItem,
                ];
            });

        Log::info('Reward history loaded', [
            'user_id' => Auth::id(),
            'history_count' => $this->rewardHistory->count()
        ]);
    }

    public function applyFilters()
    {
        $this->loadRewardHistory();
        $this->showFilterModal = false;
        toast()->success('Filters applied successfully')->push();
    }

    public function resetFilters()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->typeFilter = 'all';
        $this->minPoints = null;
        $this->maxPoints = null;
        $this->sortBy = 'date';
        $this->sortDirection = 'desc';
        $this->loadRewardHistory();
        toast()->success('Filters reset successfully')->push();
    }

    public function exportHistory()
    {
        try {
            $mpdf = new Mpdf([
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
            ]);

            // Add PDF metadata
            $mpdf->SetTitle('Reward History - ' . Auth::user()->name);
            $mpdf->SetAuthor(config('app.name'));

            // Generate HTML content
            $html = view('pdf.reward-history', [
                'history' => $this->rewardHistory,
                'user' => Auth::user(),
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
                'totalEarned' => $this->rewardHistory->where('type', 'earned')->sum('points'),
                'totalConverted' => abs($this->rewardHistory->where('type', 'converted')->sum('points')),
                'currencySymbol' => $this->currencySymbol,
                'conversionRate' => $this->conversionRate,
            ])->render();

            $mpdf->WriteHTML($html);

            // Generate unique filename
            $filename = 'reward-history-' . Auth::id() . '-' . time() . '.pdf';
            $filepath = storage_path('app/public/exports/' . $filename);

            // Ensure directory exists
            if (!file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            $mpdf->Output($filepath, 'F');

            // Return download response
            return response()->download($filepath, $filename, [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            Log::error('Failed to export reward history: ' . $e->getMessage());
            toast()->danger('Failed to export history. Please try again.')->push();
        }
    }

    public function showRedoConversion($conversionId)
    {
        $conversion = $this->rewardHistory->firstWhere(function ($history) use ($conversionId) {
            return $history['type'] === 'converted' && $history['id'] === $conversionId;
        });

        if ($conversion) {
            $this->selectedConversion = $conversion;
            $this->pointsToRedo = abs($conversion['points']);
            $this->showRedoConversionModal = true;
        }
    }

    public function redoConversion()
    {
        if (!$this->selectedConversion || $this->availablePoints < $this->pointsToRedo) {
            toast()->danger('Insufficient points for conversion')->push();
            return;
        }

        try {
            DB::beginTransaction();

            // Create new conversion history record
            RewardHistory::create([
                'user_id' => Auth::id(),
                'type' => 'converted',
                'points' => -$this->pointsToRedo,
                'description' => 'Converted points to ' . $this->currencySymbol . ' (Redo)',
            ]);

            // Update user's points
            DB::table('users')
                ->where('id', Auth::id())
                ->decrement('reward_points', $this->pointsToRedo);

            DB::commit();
            $this->refreshData();
            $this->showRedoConversionModal = false;
            toast()->success('Points conversion successful!')->push();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to redo conversion: ' . $e->getMessage());
            toast()->danger('Failed to convert points. Please try again.')->push();
        }
    }

    public function convertPoints()
    {
        if ($this->availablePoints < $this->conversionRate) {
            toast()->danger('You need at least ' . $this->conversionRate . ' points to convert to money.')->push();
            return;
        }

        try {
            DB::beginTransaction();

            // Create conversion history record
            RewardHistory::create([
                'user_id' => Auth::id(),
                'type' => 'converted',
                'points' => -$this->availablePoints,
                'description' => 'Converted points to ' . $this->currencySymbol,
            ]);

            // Update user's points
            DB::table('users')
                ->where('id', Auth::id())
                ->update(['reward_points' => 0]);
            $this->availablePoints = 0;

            DB::commit();
            $this->refreshData();
            $this->showConvertModal = false;
            toast()->success('Points successfully converted! You will receive your money soon.')->push();
        } catch (\Exception $e) {
            DB::rollBack();
            toast()->danger('Failed to convert points. Please try again.')->push();
            Log::error('Failed to convert points: ' . $e->getMessage());
        }
    }

    public function getMonthlyEarnings()
    {
        return $this->rewardHistory
            ->where('type', 'earned')
            ->where('date', '>=', Carbon::now()->startOfMonth()->format('Y-m-d'))
            ->sum('points');
    }

    public function getMonthlyReportedItems()
    {
        return $this->rewardHistory
            ->where('type', 'earned')
            ->where('date', '>=', Carbon::now()->startOfMonth()->format('Y-m-d'))
            ->count();
    }

    public function viewItemDetails($itemId)
    {
        $this->showHistoryModal = false; // Close the history modal
        $this->dispatch('showItemDetails', ['itemId' => $itemId]); // Show item details modal
    }

    public function render()
    {
        $dollarValue = $this->availablePoints / $this->conversionRate;

        return view('livewire.rewards', [
            'dollarValue' => $dollarValue,
            'monthlyEarnings' => $this->getMonthlyEarnings(),
            'monthlyReportedItems' => $this->getMonthlyReportedItems(),
        ]);
    }
}
