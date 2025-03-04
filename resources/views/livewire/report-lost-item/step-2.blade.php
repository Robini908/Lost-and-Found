<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">Basic Information</h2>
        <p class="mt-2 text-sm text-gray-600">Tell us about the item</p>
    </div>

    <!-- Title -->
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
        <div class="mt-1">
            <input type="text" wire:model.debounce.500ms="title" id="title" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="e.g. iPhone 13 Pro Max">
        </div>
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <div class="mt-1">
            <textarea wire:model="description" id="description" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Please provide detailed description of the item..."></textarea>
        </div>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Category -->
    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
        <div class="mt-1">
            <select wire:model="category_id" id="category_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            @if(!empty($suggestedCategories) && !in_array($category->id, $suggestedCategories)) class="text-gray-400" @endif>
                        {{ $category->name }}
                        @if(!empty($suggestedCategories) && !in_array($category->id, $suggestedCategories))
                            (may not match)
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
        @if(!empty($suggestedCategories) && !empty($title))
            <p class="mt-2 text-sm text-blue-600">
                Suggested categories based on your title:
                {{ $categories->whereIn('id', $suggestedCategories)->pluck('name')->implode(', ') }}
            </p>
        @endif
        @error('category_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Anonymous Reporting -->
    <div class="relative flex items-start">
        <div class="flex items-center h-5">
            <input wire:model="is_anonymous" id="is_anonymous" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="is_anonymous" class="font-medium text-gray-700">Report Anonymously</label>
            <p class="text-gray-500">Your identity will not be visible to others</p>
        </div>
    </div>
    @error('is_anonymous')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
