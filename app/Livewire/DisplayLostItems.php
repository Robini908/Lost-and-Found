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
use Illuminate\Support\Facades\Crypt;
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

    public function __construct()
    {
        $this->itemMatchingService = new ItemMatchingService();
    }


    protected $listeners = ['itemDeleted' => 'refreshList']; // Define listeners here

    public function mount()
    {
        // No encryption/decryption needed for date_lost
    }

    public function confirmClaim($itemId)
    {
        $this->itemToClaim = LostItem::find($itemId);

        // Get the authenticated user's reported items
        $user = Auth::user();
        $reportedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', ['reported', 'searched'])
            ->with(['images' => function ($query) {
                $query->select('id', 'lost_item_id', 'image_path');
            }])
            ->select('id', 'title', 'description', 'geolocation', 'date_lost', 'updated_at')
            ->get();

        // Ensure the user has reported items
        if ($reportedItems->isEmpty()) {
            toast()->danger('No reported items found for comparison.')
                ->push();
            return;
        }

        // Calculate similarity scores using the ItemMatchingService
        $this->imageSimilarityScore = $this->itemMatchingService->calculateImageSimilarity(
            $reportedItems->first()->images,
            $this->itemToClaim->images
        );

        $this->textSimilarityScore = $this->itemMatchingService->calculateTextSimilarity(
            $reportedItems->first()->title . ' ' . $reportedItems->first()->description,
            $this->itemToClaim->title . ' ' . $this->itemToClaim->description
        );

        // Calculate location similarity score
        $this->locationSimilarityScore = $this->itemMatchingService->calculateLocationSimilarity(
            $reportedItems->first()->geolocation,
            $this->itemToClaim->geolocation
        );

        // Perform checks based on similarity scores
        $this->checks = [
            'description_matches' => $this->textSimilarityScore > 0.5, // Adjust threshold as needed
            'images_match' => $this->imageSimilarityScore > 0.5, // Image similarity above 50%
            'location_matches' => $this->locationSimilarityScore > 0.1, // Location similarity above 10%
        ];

        // Automatically set matched_found_item_id if image similarity > 70%
        if ($this->imageSimilarityScore > 0.7) {
            $reportedItems->first()->update(['matched_found_item_id' => $this->itemToClaim->id]);

            // Invalidate the cache
            Cache::forget('matched_items'); // Clear the cache for matched items

            // Emit an event to refresh the UI
            $this->dispatch('itemMatched');

            toast()->success('Item matched successfully!')
                ->push();
        }

        // Open the confirmation modal
        $this->confirmingClaim = true;
    }

    public function confirmResetClaim($itemId)
    {
        $this->itemToReset = LostItem::find($itemId);
        $this->confirmingResetClaim = true; // Open the reset confirmation modal
    }

    public function resetClaim()
    {
        if ($this->itemToReset) {
            $this->itemToReset->update(['claimed_by' => null]); // Reset the claim
            toast()->success('Claim has been reset successfully.')
            ->push();
        }

        $this->closeResetClaimModal(); // Close the modal
        $this->render(); // Refresh the list
    }

    public function closeResetClaimModal()
    {
        $this->confirmingResetClaim = false;
        $this->itemToReset = null;
    }
    public function processClaim()
    {
        // Process the claim (e.g., update the item's claimed_by field)
        $this->itemToClaim->update(['claimed_by' => Auth::id()]);

        // Close the modal
        $this->closeClaimModal();

        // Refresh the list of lost items
        $this->render();
    }


    public function closeClaimModal()
    {
        $this->confirmingClaim = false;
        $this->itemToClaim = null;
        $this->checks = [];
        $this->similarityScore = 0;
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
        $this->selectedItem = LostItem::with('images', 'user')->find($itemId);
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
                $item->delete();

                // Show a success toast message
                toast()
                    ->success("Item deleted successfully.")
                    ->push();

                // Refresh the list of items
                return redirect()->to(route('products.view-items'));
            }
        }

        // Reset the delete confirmation modal
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
        $categories = Category::all();

        // Fetch all items based on search criteria
        $lostItems = LostItem::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
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
            })
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('livewire.display-lost-items', [
            'lostItems' => $lostItems,
            'categories' => $categories,
        ]);
    }
}
