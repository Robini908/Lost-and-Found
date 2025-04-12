<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LostItem;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\RoleService;
use Usernotnull\Toast\Concerns\WireToast;

class DisplayLostItems extends Component
{
    use WithPagination;
    use WireToast;

    // Filters
    public $search = '';
    public $status = '';
    public $category = '';
    public $dateRange = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 12;
    public $view = 'grid';
    public $categories = [];
    public $selectedItem = null;
    public $showModal = false;
    public $showMapView = false;
    public $showAdvancedFilters = false;
    public $activeImageIndex = 0; // Track active image in gallery

    // Advanced Filters
    public $priceRange = '';
    public $condition = '';
    public $brand = '';
    public $color = '';
    public $location = '';
    public $radius = '';
    public $selectedItems = []; // Ensure this is always initialized as an empty array
    public $bulkAction = '';
    public $showBulkActionsDropdown = false;

    // Map View
    public $mapCenter = ['lat' => 0, 'lng' => 0];
    public $mapZoom = 12;
    public $markers = [];

    // Delete functionality
    public $canDelete = false;
    protected $roleService;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'category' => ['except' => ''],
        'dateRange' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 12],
        'view' => ['except' => 'grid'],
        'condition' => ['except' => ''],
        'brand' => ['except' => ''],
        'color' => ['except' => ''],
        'location' => ['except' => ''],
        'radius' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadCategories();
        $this->roleService = app('role-permission');
        $this->canDelete = $this->roleService->canDeleteItems(Auth::user());
    }

    public function loadCategories()
    {
        $this->categories = Cache::remember('categories', 60 * 60, function () {
            return Category::select('id', 'name')->orderBy('name')->get()->toArray();
        });
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'status',
            'category',
            'dateRange',
            'priceRange',
            'condition',
            'brand',
            'color',
            'location',
            'radius'
        ]);
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function toggleView($view)
    {
        $this->view = $view;

        if ($view === 'map' && !$this->showMapView) {
            $this->showMapView = true;
            $this->initializeMap();
        } elseif ($view !== 'map') {
            $this->showMapView = false;
        }
    }

    public function initializeMap()
    {
        // Initialize map with default center if needed
        if ($this->mapCenter['lat'] == 0 && $this->mapCenter['lng'] == 0) {
            $this->mapCenter = [
                'lat' => 0, // Set to your default latitude
                'lng' => 0  // Set to your default longitude
            ];
        }

        // Load markers for current items
        $items = $this->getFilteredQuery()
            ->whereNotNull('location_lat')
            ->whereNotNull('location_lng')
            ->select('id', 'title', 'location_lat', 'location_lng', 'status')
            ->get();

        $this->markers = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'lat' => (float) $item->location_lat,
                'lng' => (float) $item->location_lng,
                'status' => $item->status
            ];
        })->toArray();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
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

    public function viewItem($itemId)
    {
        $this->selectedItem = $itemId;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedItem = null;
        $this->activeImageIndex = 0;
    }

    public function nextImage($total)
    {
        $this->activeImageIndex = ($this->activeImageIndex + 1) % $total;
    }

    public function prevImage($total)
    {
        $this->activeImageIndex = ($this->activeImageIndex - 1 + $total) % $total;
    }

    public function hasActiveFilters()
    {
        return $this->search ||
               $this->status ||
               $this->category ||
               $this->dateRange ||
               $this->condition ||
               $this->brand ||
               $this->color ||
               $this->location;
    }

    public function toggleItemSelection($itemId)
    {
        // Ensure selectedItems is an array
        if (!is_array($this->selectedItems)) {
            $this->selectedItems = [];
        }

        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }
    }

    public function selectAll()
    {
        $this->selectedItems = $this->getFilteredQuery()->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedItems = [];
    }

    public function exportSelected($format = 'pdf')
    {
        if (empty($this->selectedItems)) {
            $this->addError('export', 'Please select items to export');
            toast()
                ->warning('Please select items to export')
                ->push();
            return;
        }

        // Make sure selectedItems is properly formatted
        $itemIds = is_array($this->selectedItems) ? implode(',', $this->selectedItems) : $this->selectedItems;

        // Redirect to the appropriate export controller method
        return redirect()->route('items.export.' . $format, [
            'item_ids' => $itemIds
        ]);
    }

    public function printSelected()
    {
        if (empty($this->selectedItems)) {
            $this->addError('print', 'Please select items to print');
            toast()
                ->warning('Please select items to print')
                ->push();
            return;
        }

        // Make sure selectedItems is properly formatted
        $itemIds = is_array($this->selectedItems) ? implode(',', $this->selectedItems) : $this->selectedItems;

        // Redirect to print view
        return redirect()->route('items.print', [
            'item_ids' => $itemIds
        ]);
    }

    public function exportToPdf($items)
    {
        // This function is replaced by the ItemExportController
        // Keeping as a placeholder for backwards compatibility
        return null;
    }

    public function exportToCsv($items)
    {
        // This function is replaced by the ItemExportController
        // Keeping as a placeholder for backwards compatibility
        return null;
    }

    public function claimItem($itemId)
    {
        $item = LostItem::find($itemId);
        if ($item && $item->status === 'found') {
            $item->update([
                'status' => 'claimed',
                'claimed_by' => Auth::id(),
                'claimed_at' => now(),
            ]);

            $this->dispatch('item-claimed', $itemId);
            $this->closeModal();
        }
    }

    public function markAsFound($itemId)
    {
        $item = LostItem::find($itemId);
        if ($item && $item->status === 'lost') {
            $item->update([
                'status' => 'found',
                'found_by' => Auth::id(),
                'found_at' => now(),
            ]);

            $this->dispatch('item-found', $itemId);
            $this->closeModal();
        }
    }

    public function markAsReturned($itemId)
    {
        $item = LostItem::find($itemId);
        if ($item && $item->status === 'claimed') {
            $item->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);

            $this->dispatch('item-returned', $itemId);
            $this->closeModal();
        }
    }

    public function reportMatch($itemId)
    {
        // Implement potential match reporting logic
        $this->dispatch('show-match-form', $itemId);
    }

    /**
     * Delete a single item
     */
    public function deleteItem($itemId)
    {
        if (!$this->canDelete) {
            $this->addError('permission', 'You do not have permission to delete items.');
            return;
        }

        try {
            DB::beginTransaction();

            $item = LostItem::with('images')->findOrFail($itemId);

            // Delete associated images first
            foreach ($item->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            $item->delete();
            DB::commit();

            $this->dispatch('item-deleted');
            $this->resetPage();
            toast()
                ->success('Item deleted successfully.')
                ->push();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting item: ' . $e->getMessage());
            toast()
                ->danger('Failed to delete item. Please try again.')
                ->push();
        }
    }

    /**
     * Delete multiple selected items
     */
    public function deleteSelected()
    {
        if (!$this->canDelete) {
            $this->addError('permission', 'You do not have permission to delete items.');
            return;
        }

        if (empty($this->selectedItems)) {
            toast()
                ->info('Please select items to delete.')
                ->push();
            return;
        }

        try {
            DB::beginTransaction();

            $items = LostItem::whereIn('id', $this->selectedItems)->with('images')->get();
            $deletedCount = 0;

            foreach ($items as $item) {
                // Delete associated images
                foreach ($item->images as $image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }
                $item->delete();
                $deletedCount++;
            }

            DB::commit();

            $this->selectedItems = [];
            $this->dispatch('items-deleted');
            $this->resetPage();

            toast()
                ->success($deletedCount . ' items deleted successfully.')
                ->push();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting selected items: ' . $e->getMessage());
            toast()
                ->danger('Failed to delete selected items. Please try again.')
                ->push();
        }
    }

    /**
     * Execute bulk action on selected items
     */
    public function executeBulkAction()
    {
        if (empty($this->selectedItems)) {
            toast()
                ->info('Please select items first.')
                ->push();
            return;
        }

        if (empty($this->bulkAction)) {
            toast()
                ->info('Please select an action.')
                ->push();
            return;
        }

        switch ($this->bulkAction) {
            case 'export-pdf':
                return $this->exportSelected('pdf');
                break;
            case 'export-word':
                return $this->exportSelected('word');
                break;
            case 'export-excel':
                return $this->exportSelected('excel');
                break;
            case 'print':
                return $this->printSelected();
                break;
            case 'delete':
                return $this->deleteSelected();
                break;
            default:
                toast()
                    ->info('Invalid action selected.')
                    ->push();
                break;
        }
    }

    protected function getFilteredQuery()
    {
        return LostItem::query()
            ->with(['images', 'category', 'user'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->category, fn($q) => $q->where('category_id', $this->category))
            ->when($this->condition, fn($q) => $q->where('condition', $this->condition))
            ->when($this->brand, fn($q) => $q->where('brand', 'like', '%' . $this->brand . '%'))
            ->when($this->color, fn($q) => $q->where('color', 'like', '%' . $this->color . '%'))
            ->when($this->dateRange, function ($q) {
                $dates = explode(' to ', $this->dateRange);
                if (count($dates) === 2) {
                    $q->whereBetween('created_at', [$dates[0], $dates[1]]);
                }
            })
            ->when($this->location && $this->radius, function ($q) {
                // Location-based filtering logic here
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $query = $this->getFilteredQuery();

        $items = $query->paginate($this->perPage);

        // Ensure selectedItems is always an array
        if (!is_array($this->selectedItems)) {
            $this->selectedItems = [];
        }

        $totalSelected = count($this->selectedItems);

        return view('livewire.display-lost-items', [
            'items' => $items,
            'categories' => $this->categories,
            'totalSelected' => $totalSelected,
            'canDelete' => $this->canDelete
        ]);
    }
}
