<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;
use App\Models\Category;
use App\Models\LostItem;
use App\Models\LostItemImage;
use App\Models\RewardHistory;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\LivewireFilepond\WithFilePond;
use Usernotnull\Toast\Concerns\WireToast;
use App\Services\ItemMatchingService;

class ReportLostItem extends Component
{
    use WithFilePond, WireToast;

    protected $itemMatchingService;

    // Form steps
    public $currentStep = 1;
    public $totalSteps = 5;

    // Common fields
    public $reportType = ''; // reported, searched, found
    public $title = '';
    public $description = '';
    public $category_id = '';
    public $suggestedCategories = [];
    public $condition;
    public $brand = '';
    public $model;
    public $color = '';
    public $serial_number;
    public $estimated_value;
    public $currency;
    public $additional_details = [];
    public $is_anonymous = false;
    public $date = '';

    // Location fields
    public $useMap = false;
    public $location_address = '';
    public $location_lat = null;
    public $location_lng = null;
    public $locationType = 'specific';
    public $area = '';
    public $landmarks = '';
    public $notes = '';

    // Date fields
    public $date_lost;
    public $date_found;

    // Images
    public $images = [];

    // Categories
    public $categories = [];

    // Add this property for Livewire file upload validation
    protected $rules = [
        'reportType' => 'required|in:reported,searched,found',
        'title' => 'required|min:5',
        'description' => 'required|min:10',
        'category_id' => 'required|exists:categories,id',
        'condition' => 'required',
        'brand' => 'nullable|string',
        'model' => 'nullable|string',
        'color' => 'nullable|string',
        'serial_number' => 'nullable|string',
        'estimated_value' => 'nullable|numeric',
        'currency' => 'required',
        'location_address' => 'required_if:locationType,specific',
        'location_lat' => 'required_if:locationType,specific|nullable|numeric',
        'location_lng' => 'required_if:locationType,specific|nullable|numeric',
        'date_lost' => 'required_if:reportType,reported,searched|nullable|date',
        'date_found' => 'required_if:reportType,found|nullable|date',
        'images.*' => 'mimetypes:image/jpeg,image/jpg,image/png,image/gif,image/webp,image/bmp,image/tiff,image/svg+xml|max:5120',
        'is_anonymous' => 'boolean',
        'locationType' => 'required|in:specific,area',
        'area' => 'required_if:locationType,area|nullable|string',
        'landmarks' => 'nullable|string',
        'notes' => 'nullable|string',
        'date' => 'required|date',
    ];

    // Add validation messages
    protected $messages = [
        'images.*.image' => 'Each file must be an image.',
        'images.*.max' => 'Each image must not be larger than 5MB.',
    ];

    // Keep the existing rules() method for step validation
    protected function rules()
    {
        return $this->rules;
    }

    public function boot(ItemMatchingService $itemMatchingService)
    {
        $this->itemMatchingService = $itemMatchingService;
    }

    protected function getStepValidationRules()
    {
        $rules = [
            1 => ['reportType'],
            2 => [
                'title' => 'required|min:5',
                'description' => 'required|min:10',
                'is_anonymous' => 'boolean',
                'category_id' => [
                    'required',
                    'exists:categories,id',
                    function ($attribute, $value, $fail) {
                        if (!empty($this->title) && !empty($this->suggestedCategories)) {
                            if (!in_array($value, $this->suggestedCategories)) {
                                $suggestedCategoryNames = Category::whereIn('id', $this->suggestedCategories)
                                    ->pluck('name')
                                    ->implode(', ');
                                $fail("The selected category doesn't seem to match the item title. Suggested categories: {$suggestedCategoryNames}");
                            }
                        }
                    }
                ]
            ],
            3 => ['brand', 'color', 'condition', 'date', 'images.*', 'notes'],
            4 => [
                'locationType',
                'location_address' => $this->locationType === 'specific' ? 'required' : 'nullable',
                'location_lat' => $this->locationType === 'specific' ? 'required' : 'nullable',
                'location_lng' => $this->locationType === 'specific' ? 'required' : 'nullable',
                'area' => $this->locationType === 'area' ? 'required' : 'nullable',
                'landmarks' => $this->locationType === 'area' ? 'nullable' : 'nullable',
            ],
            5 => [], // Review step doesn't need validation
        ];

        return $rules;
    }

    public function mount($mode = null)
    {
        // Set the report type based on the mode
        if ($mode === 'lost') {
            $this->reportType = 'reported';
        } elseif ($mode === 'found') {
            $this->reportType = 'found';
        }

        // Load settings
        $this->currency = Setting::get('currency', 'USD');
        $this->loadCategories();
    }

    protected function loadCategories()
    {
        $this->categories = Category::select('id', 'name')->orderBy('name')->get();
    }

    public function nextStep()
    {
        $rules = $this->getStepValidationRules()[$this->currentStep];

        // Convert array rules to key-value pairs
        if (is_array($rules)) {
            $validationRules = [];
            foreach ($rules as $key => $rule) {
                if (is_numeric($key)) {
                    $validationRules[$rule] = $this->rules[$rule] ?? 'required';
                } else {
                    $validationRules[$key] = $rule;
                }
            }
            $this->validate($validationRules);
        } else {
            $this->validate([$rules => $this->rules[$rules] ?? 'required']);
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    protected function collectRules($keys)
    {
        return collect($this->rules())
            ->filter(function ($value, $key) use ($keys) {
                return in_array($key, $keys);
            })
            ->toArray();
    }

    public function previousStep()
    {
        $this->currentStep = max($this->currentStep - 1, 1);
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
            $this->validateOnly('images.*');
        }
    }

    public function updated($propertyName)
    {
        // Validate images immediately when they're uploaded
        if (str_starts_with($propertyName, 'images.')) {
            $this->validateOnly($propertyName);
            return;
        }

        // When title is updated, suggest categories
        if ($propertyName === 'title' && !empty($this->title)) {
            $this->updateSuggestedCategories();
        }

        // Clear validation errors when a field is updated
        $this->validateOnly($propertyName);

        // Special handling for reportType changes
        if ($propertyName === 'reportType') {
            $this->date = null;
        }

        // Special handling for locationType changes
        if ($propertyName === 'locationType') {
            if ($this->locationType === 'specific') {
                $this->area = '';
                $this->landmarks = '';
            } else {
                $this->location_address = '';
                $this->location_lat = null;
                $this->location_lng = null;
            }
            $this->validateOnly('locationType');
        }

        // Update date_found or date_lost when date is updated
        if ($propertyName === 'date') {
            if ($this->reportType === 'found') {
                $this->date_found = $this->date;
            } else {
                $this->date_lost = $this->date;
            }
        }
    }

    protected function updateSuggestedCategories()
    {
        try {
            // Get text embedding for the title
            $titleEmbedding = $this->itemMatchingService->getTextEmbedding((object)[
                'title' => $this->title,
                'description' => '',
            ]);

            // Get all categories
            $allCategories = Category::all();
            $suggestedCategories = collect();

            foreach ($allCategories as $category) {
                // Calculate similarity between title and category
                $similarity = $this->itemMatchingService->calculateTextSimilarityWithContext(
                    $this->title,
                    $category->name . ' ' . ($category->description ?? '')
                );

                if ($similarity >= 0.3) { // 30% similarity threshold
                    $suggestedCategories->push([
                        'id' => $category->id,
                        'name' => $category->name,
                        'similarity' => $similarity
                    ]);
                }
            }

            // Sort by similarity and get top matches
            $this->suggestedCategories = $suggestedCategories
                ->sortByDesc('similarity')
                ->take(5)
                ->pluck('id')
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Error updating suggested categories: ' . $e->getMessage());
            // Don't throw the error to avoid breaking the UI
            $this->suggestedCategories = [];
        }
    }

    protected function createRewardHistory($lostItem)
    {
        try {
            // Only create reward history for found items
            if ($lostItem->item_type !== 'found') {
                Log::info('Skipping reward points - item type is not found', [
                    'item_id' => $lostItem->id,
                    'item_type' => $lostItem->item_type
                ]);
                return;
            }

            Log::info('Creating reward history for found item', [
                'item_id' => $lostItem->id,
                'user_id' => Auth::id()
            ]);

            // Get reward settings from global settings
            $rewardPoints = (int)Setting::get('found_item_reward_points', 100);
            $expiryDays = (int)Setting::get('reward_points_expiry_days', 365);
            $conversionRate = (float)Setting::get('points_conversion_rate', 0.01);
            $currencySymbol = Setting::get('currency_symbol', '$');

            // Create a basic reward record with type and category in metadata
            $rewardHistory = RewardHistory::create([
                'user_id' => Auth::id(),
                'points' => $rewardPoints,
                'conversion_rate' => $conversionRate,
                'converted_amount' => 0,
                'currency' => $this->currency ?? 'USD',
                'description' => 'Reward for reporting found item: ' . $lostItem->title,
                'lost_item_id' => $lostItem->id,
                'metadata' => [
                    'type' => 'earned',
                    'category' => 'found_item',
                    'item_type' => $lostItem->item_type,
                    'location' => $lostItem->location_address ?? '',
                    'currency_symbol' => $currencySymbol,
                    'points_awarded' => $rewardPoints,
                    'awarded_at' => now()->toDateTimeString()
                ],
                'expires_at' => now()->addDays($expiryDays),
                'is_expired' => false
            ]);

            // Update user's total points directly in the database
            DB::table('users')
                ->where('id', Auth::id())
                ->increment('reward_points', $rewardPoints);

            Log::info('Reward points awarded successfully', [
                'user_id' => Auth::id(),
                'points' => $rewardPoints,
                'item_id' => $lostItem->id,
                'reward_history_id' => $rewardHistory->id
            ]);

            // Dispatch event for Rewards component to handle
            $this->dispatch('foundItemReported');

        } catch (\Exception $e) {
            Log::error('Failed to create reward history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'item_id' => $lostItem->id ?? null
            ]);
            throw $e; // Re-throw to handle in the calling method
        }
    }

    protected function handleImageUploads($lostItem)
    {
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                $path = $image->store('lost-items', 'public');

                // Create image record using LostItemImage model
                LostItemImage::create([
                    'lost_item_id' => $lostItem->id,
                    'image_path' => $path
                ]);
            }
        }
    }

    public function submit()
    {
        try {
            Log::info('Starting form submission', [
                'user_id' => Auth::id(),
                'report_type' => $this->reportType,
                'current_step' => $this->currentStep
            ]);

            // Ensure date_found or date_lost is set based on reportType
            if ($this->reportType === 'found') {
                $this->date_found = $this->date;
            } else {
                $this->date_lost = $this->date;
            }

            $this->validate();
            Log::info('Form validation passed');

            DB::beginTransaction();

            $expiryDays = (int)Setting::get('item_expiry_days', 30);
            Log::info('Retrieved expiry days setting', ['days' => $expiryDays]);

            // Add a small delay to show loading state
            sleep(2);

            // Set the status based on the report type
            $status = $this->reportType === 'found' ? 'found' : 'lost';

            // Create the lost item with the appropriate item_type
            $lostItem = new LostItem([
                'user_id' => Auth::id(),
                'title' => $this->title,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'status' => $status,
                'condition' => $this->condition,
                'brand' => $this->brand,
                'model' => $this->model,
                'color' => $this->color,
                'serial_number' => $this->serial_number,
                'estimated_value' => $this->estimated_value,
                'currency' => $this->currency,
                'location_type' => $this->locationType,
                'location_lat' => $this->location_lat,
                'location_lng' => $this->location_lng,
                'location_address' => $this->location_address,
                'area' => $this->area,
                'landmarks' => $this->landmarks,
                'location_' . ($this->reportType === 'found' ? 'found' : 'lost') => $this->location_address,
                'date_' . ($this->reportType === 'found' ? 'found' : 'lost') => $this->reportType === 'found' ? $this->date_found : $this->date_lost,
                'is_anonymous' => $this->is_anonymous,
                'item_type' => $this->reportType,
                'expires_at' => now()->addDays($expiryDays),
                'additional_details' => $this->additional_details,
            ]);

            Log::info('Created LostItem model instance', [
                'title' => $this->title,
                'location_type' => $this->locationType,
                'item_type' => $this->reportType
            ]);

            $lostItem->save();
            Log::info('Saved lost item', ['item_id' => $lostItem->id]);

            // Handle image uploads using LostItemImage model
            if (!empty($this->images)) {
                Log::info('Processing image uploads', ['count' => count($this->images)]);
                $this->handleImageUploads($lostItem);
            }

            // Create reward history for found items
            if ($this->reportType === 'found') {
                Log::info('Creating reward history for found item', [
                    'item_id' => $lostItem->id,
                    'item_type' => $lostItem->item_type
                ]);
                $this->createRewardHistory($lostItem);
            }

            DB::commit();
            Log::info('Transaction committed successfully');

            toast()
                ->success('Item reported successfully! ' . ($this->reportType === 'found' ? 'Check your rewards for points earned.' : ''))
                ->push();

            return redirect()->route('products.view-items');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'report_type' => $this->reportType
            ]);

            toast()
                ->danger('Something went wrong: ' . $e->getMessage())
                ->push();
        }
    }

    public function render()
    {
        return view('livewire.report-lost-item', [
            'categories' => $this->categories
        ]);
    }
}
