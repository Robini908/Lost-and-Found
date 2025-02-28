<?php

namespace App\Livewire;

use Mpdf\Mpdf;
use Livewire\Component;
use App\Models\Category;
use App\Models\LostItem;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\ItemMatchingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Usernotnull\Toast\Concerns\WireToast;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class DisplayLostItems extends Component
{
    use WithPagination, WireToast;

    public $search = '';
    public $category = '';
    public $location = '';
    public $date_lost = '';
    public $condition = '';
    public $reportedItems = null;
    public $confirmingDelete = false;
    public $itemToDelete = null;
    public $selectedItem = null; // Track the selected item
    public $filter = 'all'; // Track the filter type
    public $confirmingDownload = false; // Track download confirmation
    public $downloadType = ''; // Track the type of download (QR, barcode, PDF)
    public $previewContent = ''; // Track content for preview modal
    public $editingItem = false; // Controls the visibility of the modal
    public $itemToEdit = null; // Stores the item being edited
    public $step = 1; // Tracks the current step in the modal
    public $selectedItemId = null; // Track the selected item ID
    public $isEditing = false; // Track if editing is active

    public $checks = []; // Track the checks to display in the modal


    // Item Properties
    public $title, $description, $category_id, $value, $is_anonymous;
    public $itemToClaim = null; // Tracks the item being claimed
    public $confirmingClaim = false; // Tracks if the claim confirmation modal is open
    public $similarityScore = 0;
    public $imageSimilarityScore = 0; // Tracks the image similarity score
    public $textSimilarityScore = 0; // Tracks the text similarity score
    public $locationSimilarityScore = 0;
    public $timeSimilarityScore = 0;
    public $categorySimilarityScore = 0;
    public $totalSimilarityScore = 0;

    public $itemToReset = null;
    public $confirmingResetClaim = false;


    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'location' => ['except' => ''],
        'date_lost' => ['except' => ''],
        'condition' => ['except' => ''],
        'filter' => ['except' => 'all']
    ];
    protected $itemMatchingService;

    protected $listeners = ['itemDeleted' => 'refreshList']; // Define listeners here

    public function mount()
    {
        $this->itemMatchingService = app(ItemMatchingService::class);
    }

    public function calculateImageSimilarity($userReportedItem, $foundItem)
    {
        if (!$this->itemMatchingService) {
            $this->itemMatchingService = app(ItemMatchingService::class);
        }

        if (!$userReportedItem || !$foundItem ||
            $userReportedItem->images->isEmpty() ||
            $foundItem->images->isEmpty()) {
            return 0;
        }

        $reportedFeatures = $this->itemMatchingService->extractImageFeatures($userReportedItem->images);
        $foundFeatures = $this->itemMatchingService->extractImageFeatures($foundItem->images);

        if (empty($reportedFeatures) || empty($foundFeatures)) {
            return 0;
        }

        return $this->itemMatchingService->calculateBestImageSimilarity($reportedFeatures, $foundFeatures);
    }

    public function confirmClaim($itemId)
    {
        $this->itemToClaim = LostItem::find($itemId);

        // Get the authenticated user's reported items
        $reportedItems = Cache::remember("user_reported_items_" . Auth::id(), now()->addMinutes(5), function () {
            return LostItem::where('user_id', Auth::id())
                ->whereIn('item_type', ['reported', 'searched'])
                ->with('images')
                ->get();
        });

        if ($reportedItems->isEmpty()) {
            toast()->danger('No reported items found for comparison.')->push();
            return;
        }

        // Calculate similarity scores
        $this->calculateSimilarityScores($reportedItems->first(), $this->itemToClaim);

        $this->confirmingClaim = true;
    }

    protected function calculateSimilarityScores($reportedItem, $foundItem)
    {
        // Initialize all scores to 0
        $this->textSimilarityScore = 0;
        $this->imageSimilarityScore = 0;
        $this->locationSimilarityScore = 0;
        $this->timeSimilarityScore = 0;
        $this->categorySimilarityScore = 0;
        $this->totalSimilarityScore = 0;

        if (!$reportedItem || !$foundItem || !$this->itemMatchingService) {
            return;
        }

        try {
            // Calculate text similarity
            $this->textSimilarityScore = $this->itemMatchingService->calculateTextSimilarityWithContext(
                $reportedItem->title . ' ' . $reportedItem->description,
                $foundItem->title . ' ' . $foundItem->description
            );

            // Calculate location similarity
            $this->locationSimilarityScore = $this->itemMatchingService->calculateLocationSimilarity(
                $reportedItem->geolocation,
                $foundItem->geolocation
            );

            // Calculate time similarity
            $this->timeSimilarityScore = $this->itemMatchingService->calculateTimeSimilarity(
                $reportedItem->date_lost,
                $foundItem->date_found
            );

            // Calculate category similarity
            $this->categorySimilarityScore = $reportedItem->category_id === $foundItem->category_id ? 1.0 : 0.0;

            // Calculate image similarity
            $this->imageSimilarityScore = $this->calculateImageSimilarity($reportedItem, $foundItem);

            // Calculate total similarity score with weighted average
            $weights = [
                'text' => 0.35,
                'image' => 0.25,
                'category' => 0.15,
                'location' => 0.15,
                'time' => 0.10
            ];

            $this->totalSimilarityScore = min(1.0,
                ($this->textSimilarityScore * $weights['text']) +
                ($this->imageSimilarityScore * $weights['image']) +
                ($this->categorySimilarityScore * $weights['category']) +
                ($this->locationSimilarityScore * $weights['location']) +
                ($this->timeSimilarityScore * $weights['time'])
            );

            // Update checks based on scores
            $this->checks = [
                'description_matches' => $this->textSimilarityScore > 0.5,
                'images_match' => $this->imageSimilarityScore > 0.5,
                'category_matches' => $this->categorySimilarityScore > 0.9,
                'location_matches' => $this->locationSimilarityScore > 0.1,
                'time_matches' => $this->timeSimilarityScore > 0.1,
            ];

            // Auto-match if similarity is high
            if ($this->totalSimilarityScore > 0.6) {
                $reportedItem->update(['matched_found_item_id' => $foundItem->id]);
                Cache::tags(['matched_items'])->flush();
                $this->dispatch('itemMatched');
            }
        } catch (\Exception $e) {
            \Log::error('Error calculating similarity scores: ' . $e->getMessage());
            toast()->danger('Error calculating similarity scores.')->push();
        }
    }

    public function processClaim()
    {
        if ($this->itemToClaim) {
            $this->itemToClaim->update(['claimed_by' => Auth::id()]);
            Cache::forget("item_{$this->itemToClaim->id}");
            $this->closeClaimModal();
            toast()->success('Item claimed successfully!')->push();
            return redirect()->route('matched-items');
        }
    }

    public function closeClaimModal()
    {
        $this->confirmingClaim = false;
        $this->itemToClaim = null;
        $this->resetSimilarityScores();
    }

    protected function resetSimilarityScores()
    {
        $this->imageSimilarityScore = 0;
        $this->textSimilarityScore = 0;
        $this->locationSimilarityScore = 0;
        $this->timeSimilarityScore = 0;
        $this->totalSimilarityScore = 0;
        $this->checks = [];
    }

    public function confirmResetClaim($itemId)
    {
        $this->itemToReset = LostItem::find($itemId);
        $this->confirmingResetClaim = true;
    }

    public function resetClaim()
    {
        if ($this->itemToReset) {
            $this->itemToReset->update([
                'claimed_by' => null,
                'matched_found_item_id' => null
            ]);

            Cache::forget("item_{$this->itemToReset->id}");
            Cache::tags(['matched_items'])->flush();

            $this->closeResetClaimModal();
            toast()->success('Claim has been reset successfully.')->push();
            return redirect()->route('products.view-items');
        }
    }

    public function closeResetClaimModal()
    {
        $this->confirmingResetClaim = false;
        $this->itemToReset = null;
    }

    public function editItem($itemId)
    {
        try {
            // Validate the item ID
            if (!$itemId) {
                throw new \Exception("Item ID is required.");
            }

            // Set the selected item ID
            $this->selectedItemId = $itemId;

            // Find the item using the ID
            $this->itemToEdit = LostItem::find($this->selectedItemId);

            // Check if the item exists
            if (!$this->itemToEdit) {
                throw new \Exception("Item not found.");
            }

            // Check if the item belongs to the authenticated user
            if ($this->itemToEdit->user_id !== Auth::id()) {
                throw new \Exception("You are not authorized to edit this item.");
            }

            // Open the modal
            $this->isEditing = true;

            // Notify the user that the item is ready for editing
            toast()
                ->success("Item loaded successfully.", "Ready to Edit")
                ->push();
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Error in editItem: " . $e->getMessage());

            // Notify the user about the error
            toast()
                ->danger($e->getMessage(), "Error")
                ->push();

            // Reset the state
            $this->selectedItemId = null;
            $this->itemToEdit = null;
            $this->isEditing = false;
        }
    }

    public function closeEditModal()
    {
        $this->isEditing = false; // Close the modal
        $this->selectedItemId = null; // Reset the selected item ID
        $this->itemToEdit = null; // Reset the item being edited
    }

    public function updatedDateLost($value)
    {
        // No encryption/decryption needed for date_lost
        $this->date_lost = $value;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilter($filter)
    {
        $this->$filter = '';
        $this->resetPage();
    }

    public function clearAllFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->location = '';
        $this->date_lost = '';
        $this->condition = '';
        $this->resetPage();
    }

    public function showItemDetails($itemId)
    {
        $this->selectedItem = Cache::remember("item_{$itemId}", now()->addMinutes(5), function () use ($itemId) {
            return LostItem::with(['images', 'user', 'category'])->find($itemId);
        });
    }

    public function closeItemDetails()
    {
        $this->selectedItem = null;
    }

    public function confirmDownload($type)
    {
        $this->downloadType = $type;
        $this->confirmingDownload = true;
    }

    public function downloadItem()
    {
        if ($this->selectedItem) {
            $item = $this->selectedItem;

            switch ($this->downloadType) {
                case 'pdf':
                    // Generate QR Code and Barcode
                    $qrCode = QrCode::size(150)->generate(route('lost-items.show', $item->id));
                    $barcode = DNS1D::getBarcodeSVG($item->id, 'C39');

                    // Generate PDF
                    $mpdf = new Mpdf();
                    $html = view('pdf.lost-item', [
                        'item' => $item,
                        'qrCode' => $qrCode,
                        'barcode' => $barcode,
                    ])->render();
                    $mpdf->WriteHTML($html);
                    return response()->streamDownload(function () use ($mpdf) {
                        echo $mpdf->Output('', 'S');
                    }, 'lost-item-' . $item->id . '.pdf');
                    break;

                case 'qr':
                    // Generate QR Code with additional information
                    $qrContent = "Item: {$item->title}\n";
                    $qrContent .= "Location: {$item->location}\n";
                    $qrContent .= "Date Lost: {$item->date_lost->format('F j, Y')}\n";
                    $qrContent .= "Condition: {$item->condition}\n";
                    $qrContent .= "Reported By: " . ($item->is_anonymous ? 'Anonymous' : $item->user->name) . "\n";
                    $qrContent .= "Login/Register: " . route('login'); // Add login/register link

                    // Generate QR Code as JPG
                    $qrCode = QrCode::format('png')
                        ->size(500)
                        ->backgroundColor(255, 255, 255) // White background
                        ->color(0, 0, 128) // Navy blue color
                        ->generate($qrContent);

                    // Save QR Code to a temporary file
                    $tempFile = tempnam(sys_get_temp_dir(), 'qr') . '.png';
                    file_put_contents($tempFile, $qrCode);

                    // Download the QR Code as JPG
                    return response()->download($tempFile, 'qr-code-' . $item->id . '.png')->deleteFileAfterSend(true);
                    break;
            }
        }

        $this->confirmingDownload = false;
    }

    public function previewContent($type)
    {
        if ($this->selectedItem) {
            $item = $this->selectedItem;

            switch ($type) {
                case 'qr':
                    $this->previewContent = QrCode::size(200)->generate(route('lost-items.show', $item->id));
                    break;
                case 'barcode':
                    $this->previewContent = DNS1D::getBarcodeSVG($item->id, 'C39');
                    break;
            }
        }
    }

    public function confirmDelete($itemId)
    {
        $this->itemToDelete = $itemId;
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if ($this->itemToDelete) {
            $item = LostItem::find($this->itemToDelete);
            if ($item && $item->user_id === Auth::id()) {
                // Delete associated images
                foreach ($item->images as $image) {
                    Storage::delete($image->image_path);
                }

                $item->delete();
                Cache::forget("item_{$this->itemToDelete}");

                toast()->success('Item deleted successfully.')->push();
                return redirect()->route('products.view-items');
            }
        }

        $this->confirmingDelete = false;
        $this->itemToDelete = null;
    }
    public function refreshList()
    {
        // Fetch the latest list of reported items for the authenticated user
        $this->reportedItems = LostItem::where('user_id', Auth::id())
            ->whereIn('item_type', ['reported', 'searched'])
            ->with('images')
            ->get();
    }

    public function render()
    {
        $categories = Cache::remember('categories', now()->addDay(), function () {
            return Category::all();
        });

        $query = LostItem::query()
            ->with(['images', 'category', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, function ($query) {
                $query->where('category_id', $this->category);
            })
            ->when($this->location, function ($query) {
                $query->where('location', 'like', '%' . $this->location . '%');
            })
            ->when($this->date_lost, function ($query) {
                $query->whereDate('date_lost', $this->date_lost);
            })
            ->when($this->condition, function ($query) {
                $query->where('condition', 'like', '%' . $this->condition . '%');
            })
            ->when($this->filter === 'mine', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($this->filter === 'others', function ($query) {
                $query->where('user_id', '!=', Auth::id());
            });

        $lostItems = $query->latest()->paginate(12);

        return view('livewire.display-lost-items', [
            'lostItems' => $lostItems,
            'categories' => $categories,
        ]);
    }
}
