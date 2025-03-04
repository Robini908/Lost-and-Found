<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LostItem;
use App\Models\Category;
use App\Models\LostItemImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Usernotnull\Toast\Concerns\WireToast;

class EditLostItem extends Component
{
    use WithFileUploads;
    use WireToast;

    public $item;
    public $title;
    public $description;
    public $category_id;
    public $status;
    public $condition;
    public $brand;
    public $color;
    public $date;
    public $notes;
    public $is_anonymous;
    public $images = [];
    public $existingImages = [];
    public $categories;
    public $locationType;
    public $location_address;
    public $location_lat;
    public $location_lng;
    public $area;
    public $landmarks;

    protected $rules = [
        'title' => 'required|min:5',
        'description' => 'required|min:10',
        'category_id' => 'required|exists:categories,id',
        'condition' => 'required|in:new,like_new,excellent,good,fair,poor,damaged',
        'brand' => 'nullable|string|max:100',
        'color' => 'nullable|string|max:50',
        'date' => 'required|date|before_or_equal:today',
        'notes' => 'nullable|string',
        'is_anonymous' => 'boolean',
        'images.*' => 'nullable|image|max:5120', // 5MB max
        'locationType' => 'required|in:specific,area',
        'location_address' => 'required_if:locationType,specific',
        'location_lat' => 'required_if:locationType,specific|nullable|numeric',
        'location_lng' => 'required_if:locationType,specific|nullable|numeric',
        'area' => 'required_if:locationType,area|nullable|string',
        'landmarks' => 'nullable|string',
    ];

    public function mount($itemId)
    {
        $this->item = LostItem::with('images')->findOrFail($itemId);

        // Check if user owns the item
        if ($this->item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load categories
        $this->categories = Category::all();

        // Populate fields
        $this->title = $this->item->title;
        $this->description = $this->item->description;
        $this->category_id = $this->item->category_id;
        $this->status = $this->item->status;
        $this->condition = $this->item->condition;
        $this->brand = $this->item->brand;
        $this->color = $this->item->color;
        $this->date = $this->item->date_lost ?? $this->item->date_found;
        $this->notes = $this->item->notes;
        $this->is_anonymous = $this->item->is_anonymous;
        $this->locationType = $this->item->location_type;
        $this->location_address = $this->item->location_address;
        $this->location_lat = $this->item->location_lat;
        $this->location_lng = $this->item->location_lng;
        $this->area = $this->item->area;
        $this->landmarks = $this->item->landmarks;

        // Load existing images
        $this->existingImages = $this->item->images->toArray();
    }

    public function removeExistingImage($imageId)
    {
        $image = LostItemImage::find($imageId);
        if ($image && $image->lost_item_id === $this->item->id) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
            $this->existingImages = array_filter($this->existingImages, fn($img) => $img['id'] !== $imageId);
            toast()->success('Image removed successfully')->push();
        }
    }

    protected function handleImageUploads()
    {
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                $path = $image->store('lost-items', 'public');

                LostItemImage::create([
                    'lost_item_id' => $this->item->id,
                    'image_path' => $path
                ]);
            }
        }
    }

    public function deleteItem()
    {
        try {
            DB::beginTransaction();

            // Delete associated images
            foreach ($this->item->images as $image) {
                Storage::delete('public/' . $image->image_path);
                $image->delete();
            }

            // Delete the item
            $this->item->delete();

            DB::commit();

            toast()->success('Item deleted successfully')->push();
            $this->dispatch('itemDeleted')->to('my-reported-items');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting item', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            toast()->danger('Failed to delete item: ' . $e->getMessage())->push();
        }
    }

    public function cancel()
    {
        $this->dispatch('closeEdit')->to('my-reported-items');
    }

    public function submit()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Update the item with the new values
            $this->item->update([
                'title' => $this->title,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'brand' => $this->brand,
                'color' => $this->color,
                'condition' => $this->condition,
                'date' => $this->date,
                'notes' => $this->notes,
                'is_anonymous' => $this->is_anonymous,
                'location_type' => $this->locationType,
                'location_address' => $this->location_address,
                'location_lat' => $this->location_lat,
                'location_lng' => $this->location_lng,
                'area' => $this->area,
                'landmarks' => $this->landmarks,
            ]);

            // Handle image uploads
            if ($this->images) {
                $this->handleImageUploads();
            }

            DB::commit();

            toast()->success('Item updated successfully')->push();
            $this->dispatch('itemUpdated')->to('my-reported-items');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating item', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            toast()->danger('Failed to update item: ' . $e->getMessage())->push();
        }
    }

    public function render()
    {
        return view('livewire.edit-lost-item', [
            'categories' => $this->categories
        ]);
    }
}
