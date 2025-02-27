<!-- Claim Modal -->
<x-dialog-modal wire:model="showClaimModal">
    <x-slot name="title">
        <div class="flex items-center">
            <i class="fas fa-hand-holding text-blue-500 mr-2"></i>
            Claim Item
        </div>
    </x-slot>

    <x-slot name="content">
        @if($claimDetails)
            <div class="space-y-6">
                <!-- Match Summary -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">Match Summary</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-blue-700">Match Score</span>
                        <span class="text-lg font-bold text-blue-800">{{ number_format($claimDetails['similarity_score'] * 100, 0) }}%</span>
                    </div>
                </div>

                <!-- Item Details -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Your Item</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Title:</span> {{ $claimDetails['reported_item']->title }}</p>
                            <p><span class="font-medium">Category:</span> {{ $claimDetails['reported_item']->category->name }}</p>
                            <p><span class="font-medium">Date Lost:</span> {{ optional($claimDetails['reported_item']->date_lost)->format('M d, Y') ?? 'Not specified' }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Found Item</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Found By:</span> {{ $claimDetails['found_item']->user->name }}</p>
                            <p><span class="font-medium">Location:</span> {{ $claimDetails['found_item']->location }}</p>
                            <p><span class="font-medium">Date Found:</span> {{ optional($claimDetails['found_item']->date_found)->format('M d, Y') ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Message -->
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Important Notice</h4>
                            <p class="mt-1 text-sm text-yellow-700">
                                By claiming this item, you confirm that this is your lost item. The founder will be notified of your claim.
                                Please be prepared to verify your ownership when meeting with the founder.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-slot>

    <x-slot name="footer">
        <div class="flex justify-between w-full">
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </x-secondary-button>

            <x-button wire:click="claimItem" wire:loading.attr="disabled" class="ml-3">
                <i class="fas fa-check mr-2"></i>
                Confirm Claim
            </x-button>
        </div>
    </x-slot>
</x-dialog-modal>
