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
use Illuminate\Support\Facades\Cache;

class ReportLostItem extends Component
{
    use WithFilePond, WireToast;

    protected $itemMatchingService;

    // Form steps
    public $currentStep = 1;
    public $totalSteps = 5;

    // Modal state
    public $showCategoryModal = false;

    // Common fields
    public $reportType = ''; // reported, searched, found
    public $title = '';
    public $description = '';
    public $category_id = '';
    public $suggestedCategories = [];
    public $suggestionSource = 'none'; // To track where suggestions came from: database, semantic, or ai
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
    public $newCategoryName = '';
    public $newCategoryIcon = '';
    public $newCategoryDescription = '';

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
        return array_merge($this->rules, [
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if (!empty($this->suggestedCategories) && !in_array($value, $this->suggestedCategories)) {
                        $fail('Please select one of the suggested categories or create a new one.');
                    }
                },
            ],
        ]);
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
            ],
            3 => [
                'category_id' => [
                    'required',
                    'exists:categories,id',
                    function ($attribute, $value, $fail) {
                        if (!empty($this->title) && !empty($this->suggestedCategories)) {
                            if (!in_array($value, $this->suggestedCategories)) {
                                $suggestedCategoryNames = Category::whereIn('id', $this->suggestedCategories)
                                    ->pluck('name')
                                    ->implode(', ');
                                $fail("Please select one of the suggested categories or create a new one. Suggested: {$suggestedCategoryNames}");
                            }
                        }
                    }
                ],
                'brand',
                'color',
                'condition',
                'date',
                'images.*',
                'notes'
            ],
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

        // Load settings and categories
        $this->currency = Setting::get('currency', 'USD');
        $this->loadCategories();

        // Initialize empty suggestions
        $this->suggestedCategories = [];
    }

    protected function loadCategories()
    {
        try {
            $this->categories = collect(Cache::remember('categories', now()->addHour(), function () {
                return Category::orderBy('name')->get()->keyBy('id');
            }));
        } catch (\Exception $e) {
            $this->categories = collect();
            toast()->danger('Failed to load categories')->push();
        }
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

    /**
     * Handle title updates and trigger category suggestions
     */
    public function updated($propertyName)
    {
        // Handle title updates with semantic category suggestions
        if ($propertyName === 'title') {
            if (empty($this->title) || strlen($this->title) < 3) {
                $this->reset('category_id', 'suggestedCategories');
                return;
            }

            // Only trigger category suggestions in step 2 or 3
            if (in_array($this->currentStep, [2, 3])) {
                $this->updateSuggestedCategories();
            }
            return;
        }

        // Handle other field updates as before
        $this->validateOnly($propertyName);

        switch ($propertyName) {
            case 'reportType':
                $this->date = null;
                break;
            case 'locationType':
                $this->handleLocationTypeChange();
                break;
            case 'date':
                $this->handleDateUpdate();
                break;
            case 'showCategoryModal':
                if ($this->showCategoryModal) {
                    $this->loadCategories();
                }
                break;
        }
    }

    /**
     * Update suggested categories based on the item title
     * Prioritizing database categories first with AI as fallback
     */
    public function updateSuggestedCategories()
    {
        try {
            if (empty($this->title) || strlen($this->title) < 3) {
                $this->reset('category_id', 'suggestedCategories', 'suggestionSource');
                return;
            }

            // Use the updated ItemMatchingService which now prioritizes database categories
            if ($this->itemMatchingService) {
                // First try direct database matches
                $directMatches = $this->itemMatchingService->getDirectCategorySuggestions(
                    $this->title,
                    collect($this->categories)
                );

                if (!empty($directMatches)) {
                    $this->suggestedCategories = $directMatches;
                    $this->suggestionSource = 'database';
                    if (empty($this->category_id)) {
                        $this->category_id = $directMatches[0];
                    }
                    $this->dispatch('suggestionsUpdated');
                    return;
                }

                // Then try word-based matches
                $wordMatches = $this->itemMatchingService->getWordBasedCategorySuggestions(
                    $this->title,
                    collect($this->categories)
                );

                if (!empty($wordMatches)) {
                    $this->suggestedCategories = $wordMatches;
                    $this->suggestionSource = 'semantic';
                    if (empty($this->category_id)) {
                        $this->category_id = $wordMatches[0];
                    }
                    $this->dispatch('suggestionsUpdated');
                    return;
                }

                // Finally try AI-based suggestion as fallback
                $aiMatches = $this->itemMatchingService->getAIBasedCategorySuggestions(
                    $this->title,
                    collect($this->categories)
                );

                if (!empty($aiMatches)) {
                    $this->suggestedCategories = $aiMatches;
                    $this->suggestionSource = 'ai';
                    if (empty($this->category_id)) {
                        $this->category_id = $aiMatches[0];
                    }

                    if (count($aiMatches) < 2) {
                        $this->prepareNewCategoryFromTitle();
                    }

                    $this->dispatch('suggestionsUpdated');
                    return;
                }
            }

            // If no matches at all, prepare a new category
            $this->prepareNewCategoryFromTitle();
            $this->suggestedCategories = [];
            $this->suggestionSource = 'none';
            $this->dispatch('suggestionsUpdated');

        } catch (\Exception $e) {
            Log::error('Failed to update category suggestions', [
                'error' => $e->getMessage(),
                'title' => $this->title
            ]);

            // Ensure we always have suggestions by using direct database fallback
            if (empty($this->suggestedCategories)) {
                $this->handleFallbackCategorySuggestions();
                $this->suggestionSource = 'fallback';
            }
        }
    }

    /**
     * Handle fallback category suggestions when AI matching fails
     */
    public function handleFallbackCategorySuggestions()
    {
        // Get basic keyword matches
        $matches = collect($this->categories)->filter(function ($category) {
            $titleWords = explode(' ', strtolower($this->title));
            $categoryWords = explode(' ', strtolower($category->name));

            // Check for word matches
            $matchingWords = array_intersect($titleWords, $categoryWords);
            if (!empty($matchingWords)) {
                return true;
            }

            // Check description if available
            if ($category->description) {
                foreach ($titleWords as $word) {
                    if (stripos($category->description, $word) !== false) {
                        return true;
                    }
                }
            }

            return false;
        });

        if ($matches->isNotEmpty()) {
            $this->suggestedCategories = $matches->take(3)->keys()->all();
        } else {
            // If no matches, suggest creating a new category
            $this->prepareNewCategoryFromTitle();
            $this->suggestedCategories = [];
        }

        $this->dispatch('suggestionsUpdated');
    }

    /**
     * Prepare new category details based on the title with semantic understanding
     */
    public function prepareNewCategoryFromTitle()
    {
        try {
            // Get item attributes and context
            $attributes = $this->itemMatchingService->extractItemAttributes($this->title);

            // Generate a meaningful category name
            $words = explode(' ', strtolower($this->title));
            $commonWords = ['the', 'a', 'an', 'my', 'our', 'their', 'his', 'her', 'its', 'lost', 'found'];
            $words = array_diff($words, $commonWords);

            // Try to extract a type-based name first
            $categoryName = '';
            foreach ($attributes as $attribute) {
                if (strpos($attribute, 'Type:') === 0) {
                    $type = trim(str_replace('Type:', '', $attribute));
                    $categoryName = ucwords($type . ' Items');
                    break;
                }
            }

            // If no type found, use the first two significant words
            if (empty($categoryName)) {
                $significant = array_slice($words, 0, 2);
                $categoryName = ucwords(implode(' ', $significant) . ' Items');
            }

            $this->newCategoryName = $categoryName;

            // Generate a semantic description
            $this->newCategoryDescription = $this->generateSemanticDescription($attributes);

            // Suggest an appropriate icon
            $this->suggestCategoryIcon();

            Log::info('New category prepared', [
                'name' => $this->newCategoryName,
                'description' => $this->newCategoryDescription,
                'based_on_title' => $this->title
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to prepare new category', [
                'error' => $e->getMessage(),
                'title' => $this->title
            ]);

            // Fallback to basic category name
            $this->newCategoryName = ucwords($this->title) . ' Category';
            $this->newCategoryDescription = "Category for items similar to: {$this->title}";
            $this->newCategoryIcon = 'box';
        }
    }

    /**
     * Generate a semantic description for the new category
     */
    public function generateSemanticDescription($attributes)
    {
        $description = "Category for ";

        if (!empty($attributes)) {
            $description .= implode(', ', array_map(function($attr) {
                return strtolower(str_replace(['Type:', 'Material:'], '', $attr));
            }, $attributes)) . " items. ";
        }

        $description .= "This category is designed for items that share characteristics with {$this->title}. ";
        $description .= "Items in this category typically have significant personal or practical value and require careful handling.";

        return $description;
    }

    /**
     * Suggest an appropriate icon based on semantic understanding
     */
    protected function suggestCategoryIcon()
    {
        $semanticMappings = [
            // Electronics and devices
            'phone|mobile|laptop|computer|tablet|device|electronic' => 'microchip',

            // Personal items and accessories
            'wallet|purse|bag|backpack|accessory' => 'shopping-bag',

            // Documents and identification
            'card|id|passport|document|certificate|license' => 'file-alt',

            // Valuable items
            'jewelry|ring|necklace|watch|gold|silver' => 'gem',

            // Keys and security items
            'key|access|security' => 'key',

            // Clothing and personal items
            'clothing|jacket|shirt|dress|wear' => 'tshirt',

            // Books and educational materials
            'book|notebook|diary|journal' => 'book',

            // Transportation
            'car|vehicle|bicycle|bike' => 'car',

            // Pet-related
            'pet|dog|cat|animal' => 'paw',

            // Technology
            'usb|drive|memory|storage' => 'hdd',

            // Musical instruments
            'guitar|piano|music|instrument' => 'music',

            // Sports equipment
            'sport|ball|equipment|gear' => 'futbol'
        ];

        $titleLower = strtolower($this->title);
        foreach ($semanticMappings as $pattern => $icon) {
            if (preg_match("/($pattern)/i", $titleLower)) {
                $this->newCategoryIcon = $icon;
                return;
            }
        }

        // Default icon if no match found
        $this->newCategoryIcon = 'box';
    }

    /**
     * Create a new category with AI-enhanced context
     *
     * @return \stdClass
     */
    public function createCategory()
    {
        try {
            $validatedData = $this->validate([
                'newCategoryName' => 'required|min:3|unique:categories,name',
                'newCategoryIcon' => 'required',
                'newCategoryDescription' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            $category = Category::create([
                'name' => $this->newCategoryName,
                'icon' => $this->newCategoryIcon,
                'description' => $this->newCategoryDescription,
            ]);

            // Update suggestions to include the new category
            $this->suggestedCategories = !empty($this->suggestedCategories)
                ? array_merge($this->suggestedCategories, [$category->id])
                : [$category->id];

            // Set the new category as selected
            $this->category_id = $category->id;

            // Clear the cache and refresh categories
            Cache::forget('categories');
            $this->loadCategories();

            DB::commit();

            // Close modal and show success message
            $this->showCategoryModal = false;
            toast()
                ->success("Category '{$category->name}' created successfully!")
                ->push();

            // Reset form
            $this->reset(['newCategoryName', 'newCategoryIcon', 'newCategoryDescription']);

            return (object)[
                'success' => true,
                'category' => $category
            ];

        } catch (\Illuminate\Validation\ValidationException $e) {
            toast()->warning('Please check the form for errors.')->push();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create category', [
                'error' => $e->getMessage(),
                'category_name' => $this->newCategoryName
            ]);
            toast()->danger('Failed to create category')->push();
            return (object)['success' => false];
        }
    }

    private function handleLocationTypeChange()
    {
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

    private function handleDateUpdate()
    {
            if ($this->reportType === 'found') {
                $this->date_found = $this->date;
            } else {
                $this->date_lost = $this->date;
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
                ->success('Item reported successfully!' . ($this->reportType === 'found' ? ' Check your rewards for points earned.' : ''))
                ->push();

            return redirect()->route('products.view-items');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Form validation failed', [
                'errors' => $e->errors(),
                'current_step' => $this->currentStep
            ]);

            toast()
                ->warning('Please check the form for errors.')
                ->push();

            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'report_type' => $this->reportType,
                'current_step' => $this->currentStep
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

    /**
     * Get category suggestions using AI embeddings
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
     * @return array<int, mixed>
     */
    protected function getAIBasedCategorySuggestions($title, $categories)
    {
        return $this->itemMatchingService->getAIBasedCategorySuggestions($title, $categories);
    }

    /**
     * Fallback category suggestion method
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
     * @return array<int, mixed>
     */
    protected function getFallbackCategorySuggestions($title, $categories)
    {
        return $this->itemMatchingService->getFallbackCategorySuggestions($title, $categories);
    }
}
