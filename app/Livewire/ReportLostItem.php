<?php

namespace App\Livewire;

use Route;
use Livewire\Component;
use App\Models\Category;
use App\Models\LostItem;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\LostItemImage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\LivewireFilepond\WithFilePond;
use Usernotnull\Toast\Concerns\WireToast;
use Illuminate\Support\Facades\Storage;
use App\Models\RewardHistory;

class ReportLostItem extends Component
{
    use WithFileUploads, WithPagination, WireToast, WithFilePond;

    public $currentStep = 1;
    public $title, $description, $category, $location, $date_lost, $date_found;
    public $condition, $value;
    public $category_id;
    public $category_name;
    public $type = 'reported';
    public $is_anonymous = false;
    public $categories;
    public $mode = 'reporting-lost';
    public $geolocation;
    public $successMessage = '';
    public $showCategoryModal = false;
    public $newCategoryName;

    // FilePond
    public $images = [];

    protected $listeners = [
        'categoryAdded' => 'refreshCategories'
    ];

    protected $messages = [
        'title.required' => 'Please provide a title for the item.',
        'description.required' => 'Please provide a description of the item.',
        'category.required' => 'Please select a category for the item.',
        'location.required' => 'Please specify the location.',
        'date_lost.required' => 'Please provide the date when the item was lost.',
        'date_lost.before_or_equal' => 'The date lost cannot be in the future.',
        'date_found.required' => 'Please provide the date when the item was found.',
        'date_found.before_or_equal' => 'The date found cannot be in the future.',
        'condition.required' => 'Please describe the condition of the item.',
        'images.*.image' => 'Each file must be a valid image.',
        'images.*.max' => 'Each image must not be larger than 2MB.',
        'newCategoryName' => 'Please provide a name for the new category.',
        'newCategoryName.unique' => 'The category name already exists.',
    ];

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'value' => 'nullable|numeric',
            'images.*' => 'nullable|image|max:2048',
        ];

        if ($this->mode === 'searching') {
            $rules['date_lost'] = 'required|date|before_or_equal:today';
        }

        if ($this->mode === 'reporting-found') {
            $rules['date_found'] = 'required|date|before_or_equal:today';
        }

        if ($this->mode === 'reporting-lost') {
            $rules['is_anonymous'] = 'boolean';
        }

        return $rules;
    }

    public function mount($mode = null)
    {
        $this->categories = Category::all();
        if ($mode) {
            $this->mode = $mode;
        }
        Log::info('ReportLostItem mounted with mode: ' . $this->mode);
    }

    public function openCategoryModal()
    {
        $this->showCategoryModal = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:categories,name',
        ]);

        $newCategory = Category::create(['name' => $this->newCategoryName]);

        $this->categories = Category::all();

        $this->category_id = $newCategory->id;

        $this->reset('newCategoryName');
        $this->showCategoryModal = false;

        session()->flash('message', 'Category added successfully!');
    }

    public function updatedCategoryId($value)
    {
        $category = Category::find($value);
        $this->category_name = $category ? $category->name : null;
    }

    public function step1()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
        ]);

        if ($this->mode === 'searching') {
            $this->validate(['date_lost' => 'required|date|before_or_equal:today']);
        }

        if ($this->mode === 'reporting-found') {
            $this->validate(['date_found' => 'required|date|before_or_equal:today']);
        }

        $this->currentStep = 2;
    }

    public function step2()
    {
        $this->validate([
            'condition' => 'required|string|max:255',
            'images.*' => 'nullable|image|max:2048',
        ]);

        if ($this->mode === 'searching') {
            $this->validate(['value' => 'nullable|numeric']);
        }

        $this->currentStep = 3;
    }

    public function step3()
    {
        if ($this->mode === 'reporting-lost') {
            $this->validate(['is_anonymous' => 'boolean']);
        }

        $this->currentStep = 4;
    }

    public function updatedImages()
    {
        Log::info('Images updated', ['images' => $this->images]);
    }

    public function submit()
    {
        $this->validate();

        $itemType = match($this->mode) {
            'reporting-found' => 'found',
            'searching' => 'searched',
            default => 'reported'
        };

        try {
            DB::beginTransaction();

            $lostItem = LostItem::create([
                'user_id' => Auth::id(),
                'title' => $this->title,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'item_type' => $itemType,
                'location' => $this->location,
                'date_lost' => $this->mode === 'searching' ? $this->date_lost : null,
                'date_found' => $this->mode === 'reporting-found' ? $this->date_found : null,
                'condition' => $this->condition,
                'value' => $this->value,
                'is_anonymous' => $this->mode === 'reporting-lost' ? $this->is_anonymous : false,
            ]);

            if ($this->images) {
                foreach ($this->images as $image) {
                    if (is_string($image)) {
                        // Handle temporary FilePond file
                        $path = str_replace('livewire-tmp/', '', $image);
                        $finalPath = 'lost-items/' . $path;

                        // Move the file from temporary to final location
                        if (Storage::disk('public')->exists('livewire-tmp/' . $path)) {
                            Storage::disk('public')->move('livewire-tmp/' . $path, $finalPath);
                        }

                        LostItemImage::create([
                            'lost_item_id' => $lostItem->id,
                            'image_path' => $finalPath,
                        ]);
                    } else {
                        // Handle regular file upload
                        $imagePath = $image->store('lost-items', 'public');
                        LostItemImage::create([
                            'lost_item_id' => $lostItem->id,
                            'image_path' => $imagePath,
                        ]);
                    }
                }
            }

            // Handle reward points for found items
            if ($this->mode === 'reporting-found') {
                $pointsPerFoundItem = 100;
                $userId = Auth::id();

                // Add points to user
                DB::table('users')
                    ->where('id', $userId)
                    ->increment('reward_points', $pointsPerFoundItem);

                // Create reward history
                RewardHistory::create([
                    'user_id' => $userId,
                    'type' => 'earned',
                    'points' => $pointsPerFoundItem,
                    'description' => 'Reported found item: ' . $lostItem->title,
                    'lost_item_id' => $lostItem->id,
                ]);

                // Dispatch event to update rewards component
                $this->dispatch('foundItemReported')->to('rewards');
            }

            DB::commit();

            $this->resetForm();
            $this->currentStep = 1;

            if ($this->mode === 'reporting-found') {
                toast()->success('Found item reported successfully! You earned 100 points!')->push();
            } else {
                toast()->success($this->mode === 'reporting-lost' ? 'Lost item reported successfully!' : 'Search request submitted successfully!')->push();
            }

            $this->redirect(route('products.view-items'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving lost item: ' . $e->getMessage(), [
                'exception' => $e,
                'images' => $this->images
            ]);
            toast()->danger('An error occurred while saving the item.')->push();
        }
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'description',
            'category_id',
            'location',
            'date_lost',
            'date_found',
            'condition',
            'value',
            'images',
            'is_anonymous',
        ]);
    }

    public function back($step)
    {
        $this->currentStep = $step;
    }

    public function render()
    {
        return view('livewire.report-lost-item', [
            'lostItems' => LostItem::latest()->paginate(6),
            'categories' => $this->categories,
        ]);
    }
}
