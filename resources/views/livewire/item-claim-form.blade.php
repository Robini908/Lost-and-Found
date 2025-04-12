<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Submit Item Claim</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Lost Item Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Your Lost Item</h3>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600"><span class="font-medium">Title:</span> {{ $lostItem->title }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Category:</span> {{ $lostItem->category->name }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Date Lost:</span> {{ $lostItem->date_lost->format('M d, Y') }}</p>
                            @if($lostItem->images->isNotEmpty())
                                <img src="{{ $lostItem->images->first()->url }}" alt="{{ $lostItem->title }}" class="h-32 w-full object-cover rounded-lg mt-2">
                            @endif
                        </div>
                    </div>

                    <!-- Found Item Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Found Item</h3>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600"><span class="font-medium">Title:</span> {{ $foundItem->title }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Category:</span> {{ $foundItem->category->name }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Date Found:</span> {{ $foundItem->date_found->format('M d, Y') }}</p>
                            @if($foundItem->images->isNotEmpty())
                                <img src="{{ $foundItem->images->first()->url }}" alt="{{ $foundItem->title }}" class="h-32 w-full object-cover rounded-lg mt-2">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Claim Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form wire:submit.prevent="submitClaim" class="space-y-6 px-4 py-5 sm:p-6">
                <!-- Claim Details -->
                <div>
                    <label for="claimDetails" class="block text-sm font-medium text-gray-700">
                        Claim Details
                    </label>
                    <div class="mt-1">
                        <textarea
                            id="claimDetails"
                            wire:model="claimDetails"
                            rows="4"
                            class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md"
                            placeholder="Please provide detailed information about your claim..."
                        ></textarea>
                        @error('claimDetails')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Provide specific details that can help verify your ownership of the item.
                    </p>
                </div>

                <!-- Verification Method -->
                <div>
                    <label for="verificationMethod" class="block text-sm font-medium text-gray-700">
                        Preferred Verification Method
                    </label>
                    <select
                        id="verificationMethod"
                        wire:model="verificationMethod"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option value="document">Document Verification</option>
                        <option value="in_person">In-Person Verification</option>
                        <option value="other">Other</option>
                    </select>
                    @error('verificationMethod')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Notes -->
                <div>
                    <label for="additionalNotes" class="block text-sm font-medium text-gray-700">
                        Additional Notes
                    </label>
                    <div class="mt-1">
                        <textarea
                            id="additionalNotes"
                            wire:model="additionalNotes"
                            rows="3"
                            class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md"
                            placeholder="Any additional information..."
                        ></textarea>
                        @error('additionalNotes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Terms Acceptance -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="termsAccepted"
                            wire:model="termsAccepted"
                            type="checkbox"
                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                        >
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="termsAccepted" class="font-medium text-gray-700">
                            I confirm that all provided information is accurate
                        </label>
                        <p class="text-gray-500">
                            False claims may result in account suspension.
                        </p>
                        @error('termsAccepted')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Submit Claim</span>
                        <span wire:loading>
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
