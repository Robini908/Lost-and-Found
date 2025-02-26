<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\LostItem;
use App\Models\LostItemImage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\LivewireFilepond\WithFilePond;
use Usernotnull\Toast\Concerns\WireToast;

class EditLostItem extends Component
{
    use WithFileUploads, WithFilePond, WireToast;


    public $editingItem = false; // Controls whether the edit form is visible
    public $item; // The item being edited

    // Item Properties
    public $title, $description, $category_id, $condition, $value, $is_anonymous, $location, $date_lost;

    // Image Upload
    public $images = []; // For new image uploads
    public $existingImages = []; // For existing images
    public $userItems;
    public $confirmingDelete = false;
    public $itemIdToDelete;

   
    public function mount()
    {
        $this->userItems = LostItem::where('user_id', Auth::id())->get();
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')) {
            $this->userItems = LostItem::all();
        }
    }

    public function updatedImages($value)
    {
        // Ensure new images are unique
        foreach ($value as $file) {
            if (!in_array($file, $this->images)) {
                $this->images[] = $file;
            }
        }
    }
    public function loadItem($itemId)
    {
        $this->item = LostItem::find($itemId);

        if ($this->item) {
            $this->title = $this->item->title;
            $this->description = $this->item->description;
            $this->category_id = $this->item->category_id;
            $this->condition = $this->item->condition;
            $this->value = $this->item->value;
            $this->is_anonymous = $this->item->is_anonymous;
            $this->location = $this->item->location;
            $this->date_lost = $this->item->date_lost;
            $this->existingImages = $this->item->images; // Load existing images
            $this->editingItem = true; // Show the edit form
        }
    }

    public function saveItem()
    {
        // Validate the input fields
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'condition' => 'required|string',
            'date_lost' => 'nullable|date',
            'value' => 'nullable|numeric',
            'is_anonymous' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|max:2048', // Validate uploaded images
            'images' => 'max:5', // Ensure no more than 5 images are uploaded
        ]);

        try {
            if ($this->item) {
                // Update item details
                $this->item->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'location' => $this->location,
                    'condition' => $this->condition,
                    'date_lost' => $this->date_lost,
                    'value' => $this->value,
                    'is_anonymous' => $this->is_anonymous,
                    'category_id' => $this->category_id,
                ]);

                // Save new images
                foreach ($this->images as $image) {
                    // Store the image and get the path
                    $path = $image->store('lost-items', 'public');

                    // Create a new LostItemImage record
                    LostItemImage::create([
                        'lost_item_id' => $this->item->id,
                        'image_path' => $path,
                    ]);
                }

                // Refresh the existing images
                $this->existingImages = $this->item->fresh()->images;

                // Reset the images array
                $this->images = [];
                $this->cancelEdit();

                // Show success toast
                toast()
                    ->success('Item updated successfully.')
                    ->push();
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error saving item: ' . $e->getMessage());

            // Show error toast
            toast()
                ->danger('An error occurred while saving the item. Please try again.')
                ->push();
        }
    }

    public function confirmDelete($itemId)
    {
        $this->itemIdToDelete = $itemId;
        $this->confirmingDelete = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->itemIdToDelete = null;
    }

    public function deleteItem()
    {
        $item = LostItem::find($this->itemIdToDelete);
        if ($item) {
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
            $item->delete();
            $this->cancelDelete();
            toast()->success('Item deleted successfully.')->push();
        }
    }


    public function deleteImage($imageId)
    {
        $image = LostItemImage::find($imageId);
        if ($image) {
            // Delete the image file from storage
            Storage::disk('public')->delete($image->image_path);
            // Delete the image record from the database
            $image->delete();
            // Refresh the existing images
            $this->existingImages = $this->item->fresh()->images;

            // Show success toast
            toast()
                ->success('Image deleted successfully.')
                ->push();
        }
    }


    public function cancelEdit()
    {
        $this->editingItem = false; // Hide the edit form
        $this->reset(['title', 'description', 'location', 'condition', 'date_lost', 'value', 'is_anonymous', 'category_id', 'images']);
    }

    public function render()
    {
        $categories = Category::all(); // Fetch all categories for the dropdown
        return view('livewire.edit-lost-item', [
            'categories' => $categories,
        ]);
    }
}
