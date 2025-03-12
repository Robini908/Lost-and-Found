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
    public $selectedItems = [];
    public $bulkAction = '';

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
        $this->categories = Category::all();
        $this->initializeMapCenter();
        $this->roleService = new RoleService();

        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $this->canDelete = $this->roleService->userHasRole($user, ['admin', 'superadmin']);
        } else {
            $this->canDelete = false;
        }
    }

    protected function initializeMapCenter()
    {
        $this->mapCenter = [
            'lat' => config('services.google.maps_default_lat', 0),
            'lng' => config('services.google.maps_default_lng', 0)
        ];
        $this->mapZoom = config('services.google.maps_default_zoom', 12);
    }

    public function toggleView($view)
    {
        $this->view = $view;
        if ($view === 'map') {
            $this->showMapView = true;
            $this->loadMapMarkers();
        } else {
            $this->showMapView = false;
        }
    }

    public function loadMapMarkers()
    {
        $items = $this->getFilteredQuery()
            ->with('images')
            ->get();

        $this->markers = $items->map(function ($item) {
            $firstImage = $item->images->first();
            return [
                'id' => $item->id,
                'lat' => (float) $item->location_lat,
                'lng' => (float) $item->location_lng,
                'title' => $item->title,
                'status' => $item->status,
                'image' => $firstImage ? $firstImage->url : null,
                'imageCount' => $item->images->count()
            ];
        })->toArray();
    }

    public function viewDetails($itemId)
    {
        try {
            $this->selectedItem = LostItem::with(['images', 'category', 'user'])
                ->findOrFail($itemId);
            $this->activeImageIndex = 0;
            $this->showModal = true;
        } catch (\Exception $e) {
            Log::error('Error viewing item details: ' . $e->getMessage());
            toast()
                ->danger('Unable to load item details. Please try again.')
                ->push();
        }
    }

    public function nextImage()
    {
        if ($this->selectedItem && $this->selectedItem->images->count() > 0) {
            $this->activeImageIndex = ($this->activeImageIndex + 1) % $this->selectedItem->images->count();
        }
    }

    public function previousImage()
    {
        if ($this->selectedItem && $this->selectedItem->images->count() > 0) {
            $this->activeImageIndex = ($this->activeImageIndex - 1 + $this->selectedItem->images->count()) % $this->selectedItem->images->count();
        }
    }

    public function setActiveImage($index)
    {
        if ($this->selectedItem && $index >= 0 && $index < $this->selectedItem->images->count()) {
            $this->activeImageIndex = $index;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedItem = null;
        $this->activeImageIndex = 0;
        $this->resetErrorBag();
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function resetFilters()
    {
        $this->reset([
            'search', 'status', 'category', 'dateRange',
            'condition', 'brand', 'color', 'location',
            'radius'
        ]);
    }

    /**
     * Check if any filters are currently active
     */
    public function hasActiveFilters(): bool
    {
        return !empty($this->search) ||
               !empty($this->status) ||
               !empty($this->category) ||
               !empty($this->dateRange) ||
               !empty($this->condition) ||
               !empty($this->brand) ||
               !empty($this->color) ||
               !empty($this->location) ||
               !empty($this->radius);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getMapMarkers()
    {
        return $this->getFilteredQuery()
            ->with('images')
            ->whereNotNull(['location_lat', 'location_lng'])
            ->get()
            ->map(function ($item) {
                $firstImage = $item->images->first();
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'lat' => $item->location_lat,
                    'lng' => $item->location_lng,
                    'status' => $item->status,
                    'image' => $firstImage ? $firstImage->url : null,
                    'imageCount' => $item->images->count()
                ];
            });
    }

    public function toggleItemSelection($itemId)
    {
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

    public function exportSelected($format = 'csv')
    {
        if (empty($this->selectedItems)) {
            $this->addError('export', 'Please select items to export');
            return;
        }

        return response()->streamDownload(function () use ($format) {
            $items = LostItem::whereIn('id', $this->selectedItems)
                ->with(['category', 'user'])
                ->get();

            if ($format === 'csv') {
                $this->exportToCsv($items);
            } else {
                $this->exportToPdf($items);
            }
        }, 'lost-items.' . $format);
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

        return view('livewire.display-lost-items', [
            'items' => $items,
            'categories' => $this->categories,
            'totalSelected' => count($this->selectedItems),
            'canDelete' => $this->canDelete
        ]);
    }
}
