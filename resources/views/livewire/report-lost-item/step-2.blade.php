<div class="space-y-6">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Basic Information</h2>
        <p class="mt-2 text-sm text-gray-600">Tell us about the item</p>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column - Title and Suggestions -->
        <div class="space-y-4">
            <!-- Title Input Group -->
            <div class="sticky top-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <div class="mt-1 relative">
                    <input type="text"
                           wire:model.live="title"
                           id="title"
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md pl-4 pr-12"
                           placeholder="e.g. iPhone 13 Pro Max"
                           autocomplete="off">

                    <!-- Loading Indicator -->
                    <div wire:loading wire:target="title" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <div class="flex items-center space-x-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
                        </div>
                    </div>
                </div>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Suggestions Card -->
            <div x-data="{ show: false }"
                 x-show="show"
                 x-init="() => {
                    show = @js(!empty($title) && strlen($title) >= 3);
                    $wire.on('suggestionsUpdated', () => {
                        show = true;
                        $nextTick(() => {
                            if ($refs.suggestionsContainer) {
                                $refs.suggestionsContainer.scrollTop = 0;
                            }
                        });
                    });
                 }"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden lg:h-[calc(100vh-16rem)] flex flex-col divide-y divide-gray-100">
                    <!-- Header -->
                    <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-white flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 rounded-full">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0012 18.75c-.883 0-1.68-.34-2.28-.893l-.549.547z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Suggested Categories</h3>
                                <p class="text-xs text-gray-500">Based on your item title</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ !empty($suggestedCategories) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ !empty($suggestedCategories) ? count($suggestedCategories) . ' found' : 'No matches' }}
                            </span>
                        </div>
                    </div>

                    <!-- Scrollable Suggestions Body -->
                    <div class="flex-1 overflow-y-auto">
                        <div class="p-2 space-y-1">
                            @if(!empty($suggestedCategories))
                                @foreach($categories->whereIn('id', $suggestedCategories) as $category)
                                    <button type="button"
                                            wire:click="$set('category_id', '{{ $category->id }}')"
                                            class="group w-full text-left px-4 py-3 rounded-lg hover:bg-blue-50 transition-all duration-200
                                                   {{ $category_id == $category->id ? 'bg-blue-50 ring-2 ring-blue-200' : '' }}">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg
                                                        {{ $category_id == $category->id ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600' }}">
                                                <i class="fas fa-{{ $category->icon }} text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium {{ $category_id == $category->id ? 'text-blue-700' : 'text-gray-900' }}">
                                                        {{ $category->name }}
                                                    </p>
                                                    @if($category_id == $category->id)
                                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                            Selected
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($category->description)
                                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $category->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            @endif

                            <!-- No matches message -->
                            @if(empty($suggestedCategories))
                                <div class="text-center py-8">
                                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4 bg-gray-50 rounded-full p-2">
                                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">No matching categories</h3>
                                    <p class="mt-1 text-sm text-gray-500">Create a new category for this item</p>
                                    <button type="button"
                                            wire:click="$set('showCategoryModal', true)"
                                            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Create New Category
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if(!empty($suggestedCategories))
                            <div class="p-4 bg-gray-50 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs text-gray-500">Can't find the right category?</p>
                                    <button type="button"
                                            wire:click="$set('showCategoryModal', true)"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Create New
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
            </div>
    </div>

        <!-- Right Column - Other Fields -->
        <div class="space-y-6">
    <!-- Description -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <div class="mt-1 relative">
            <textarea
                wire:model.live.debounce.500ms="description"
                id="description"
                rows="4"
                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                placeholder="Please provide detailed description of the item..."></textarea>
            <div wire:loading wire:target="description" class="absolute right-3 top-3">
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
                </div>
            </div>
        </div>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Category Selection -->
    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
        <div class="mt-1 relative">
            <div class="flex space-x-2">
                <div class="flex-1 relative">
                    <select wire:model.live="category_id" id="category_id"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm
                               {{ empty($title) || strlen($title) < 3 ? 'bg-gray-50' : '' }}">
                        <option value=""></option>

                        @if(!empty($suggestedCategories))
                            <!-- Suggested Categories Group -->
                            <optgroup label="Suggested Categories">
                                @foreach($categories->whereIn('id', $suggestedCategories) as $category)
                                    <option value="{{ $category->id }}" class="font-medium text-blue-600">
                                        {{ $category->name }} ★
                                    </option>
                                @endforeach
                            </optgroup>

                            <!-- Other Categories Group (Disabled) -->
                            <optgroup label="Other Categories">
                                @foreach($categories->whereNotIn('id', $suggestedCategories) as $category)
                                    <option value="{{ $category->id }}" disabled class="text-gray-400">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @else
                            <!-- Show message when no suggestions -->
                            <option value="" disabled class="text-gray-500">
                                {{ strlen($title) >= 3 ? 'No matching categories found' : 'Enter at least 3 characters in title' }}
                            </option>
                        @endif
                    </select>
                    @if(empty($title) || strlen($title) < 3)
                        <div class="absolute inset-0 bg-gray-50/50 flex items-center justify-center pointer-events-none">
                            <span class="text-sm text-gray-500">Enter at least 3 characters in title</span>
                        </div>
                    @endif
                </div>
                <button type="button"
                        wire:click="$set('showCategoryModal', true)"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New
                </button>
            </div>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Anonymous Reporting -->
    <div class="relative flex items-start p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center h-5">
            <input wire:model="is_anonymous" id="is_anonymous" type="checkbox"
                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="is_anonymous" class="font-medium text-gray-700">Report Anonymously</label>
            <p class="text-gray-500">Your identity will not be visible to others</p>
        </div>
    </div>
</div>

<!-- New Category Modal -->
<x-dialog-modal wire:model="showCategoryModal">
    <x-slot name="title">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add New Category
        </div>
    </x-slot>

    <x-slot name="content">
        <!-- Separate form for category creation -->
        <div x-data="{ submitting: false }" class="space-y-4">
                        <!-- Category Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Category Name
                            </label>
                            <input type="text"
                       wire:model.blur="newCategoryName"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter category name">
                            @error('newCategoryName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icon Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Icon
                            </label>
                    <div class="grid grid-cols-6 gap-2 p-3 bg-gray-50 rounded-lg">
                                @foreach(['box', 'book', 'key', 'wallet', 'phone', 'laptop', 'bag', 'camera', 'watch', 'glasses', 'umbrella', 'headphones'] as $icon)
                                    <button type="button"
                                    wire:click="$set('newCategoryIcon', '{{ $icon }}')"
                                        class="aspect-square flex items-center justify-center p-2 rounded-lg transition-all duration-200
                                                       {{ $newCategoryIcon === $icon ? 'bg-blue-100 text-blue-600 ring-2 ring-blue-400 ring-offset-2' : 'hover:bg-blue-50 text-gray-600 hover:text-blue-600' }}">
                                    <i class="fas fa-{{ $icon }} text-lg"></i>
                                        </button>
                                @endforeach
                            </div>
                            @error('newCategoryIcon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                    <textarea wire:model.blur="newCategoryDescription"
                                          rows="3"
                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Brief description of the category"></textarea>
                            @error('newCategoryDescription')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                        </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <x-secondary-button
                    wire:click="$set('showCategoryModal', false)"
                    wire:loading.attr="disabled"
                    type="button">
                                    Cancel
                </x-secondary-button>
                <x-button
                    wire:click.prevent="createCategory"
                    wire:loading.attr="disabled"
                    wire:target="createCategory"
                    type="button"
                    class="bg-blue-600 hover:bg-blue-700">
                    <span wire:loading.remove wire:target="createCategory">
                        <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                                    Save Category
                    </span>
                    <span wire:loading wire:target="createCategory">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <!-- Loading States -->
    <div wire:loading wire:target="createCategory" class="fixed inset-0 bg-black bg-opacity-25 z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white p-4 rounded-lg shadow-xl max-w-sm w-full mx-4">
                <div class="flex items-center space-x-4">
                    <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-700">Creating category...</span>
                </div>
                        </div>
                    </div>
        </div>
    </div>
</div>
