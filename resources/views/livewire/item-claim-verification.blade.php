<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
     x-data="{ claimId: '{{ $claim?->hashed_id }}' }"
     x-init="$watch('claimId', value => {
         if (!value) window.location.href = '/404';
     })">
    @if($claim)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Progress Steps -->
            <div class="border-b border-gray-200 p-4">
                <div class="flex justify-between">
            @foreach($verificationSteps as $index => $step)
                        <div class="flex items-center">
                            <div class="@if($step['completed']) bg-green-500 @elseif($currentStep === $index + 1) bg-blue-500 @else bg-gray-300 @endif rounded-full h-8 w-8 flex items-center justify-center text-white">
                                {{ $index + 1 }}
                            </div>
                            <div class="ml-2">
                                <p class="text-sm font-medium">{{ $step['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $step['description'] }}</p>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 border-t-2 border-gray-200 my-auto mx-4"></div>
                        @endif
            @endforeach
                </div>
    </div>

    <!-- Step Content -->
    <div class="p-6">
                @switch($currentStep)
                    @case(1)
                        <div>
                            <h3 class="text-lg font-medium">Basic Information</h3>
                            <div class="mt-4 space-y-4">
                            <div>
                                    <p class="font-medium">Claimant:</p>
                                    <p>{{ $claim->user->name }}</p>
                            </div>
                            <div>
                                    <p class="font-medium">Lost Item:</p>
                                    <p>{{ $claim->lostItem->title }}</p>
                            </div>
                            <div>
                                    <p class="font-medium">Claim Date:</p>
                                    <p>{{ $claim->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            </div>
                        </div>
                        @break

                    @case(2)
                            <div>
                            <h3 class="text-lg font-medium">Identity Verification</h3>
                            <div class="mt-4">
                                <label class="block">
                                    <span class="text-gray-700">Verification Method</span>
                                    <select wire:model="verificationMethod" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Select a method</option>
                                        <option value="id">Government ID</option>
                                        <option value="email">Email Verification</option>
                                        <option value="phone">Phone Verification</option>
                                </select>
                                </label>
                            </div>
                        </div>
                        @break

                    @case(3)
                                <div>
                            <h3 class="text-lg font-medium">Item Details Verification</h3>
                            <div class="mt-4">
                                <label class="block">
                                    <span class="text-gray-700">Verification Notes</span>
                                    <textarea wire:model="verificationNotes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4"></textarea>
                                    </label>
                                </div>
                        </div>
                        @break

                    @case(4)
                            <div>
                            <h3 class="text-lg font-medium">Final Decision</h3>
                            <div class="mt-4 space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="requiresInPerson" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <span class="ml-2">Requires In-Person Verification</span>
                                    </div>

                                @if($requiresInPerson)
                                    <div>
                                        <label class="block">
                                            <span class="text-gray-700">Expiration Days</span>
                                            <input type="number" wire:model="expirationDays" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        </label>
                </div>
            @endif

                                <div class="flex space-x-4">
                                    <button wire:click="$set('showVerificationModal', true)" class="bg-green-500 text-white px-4 py-2 rounded-md">
                                    Verify Claim
                                </button>
                                    <button wire:click="$set('showRejectionModal', true)" class="bg-red-500 text-white px-4 py-2 rounded-md">
                                    Reject Claim
                                </button>
                            </div>
                        </div>
                    </div>
                        @break
                @endswitch
    </div>

    <!-- Navigation Buttons -->
            <div class="px-6 py-4 bg-gray-50 flex justify-between">
        <button
            wire:click="previousStep"
                    @if($currentStep === 1) disabled @endif
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md @if($currentStep === 1) opacity-50 cursor-not-allowed @endif"
        >
            Previous
        </button>
        <button
            wire:click="nextStep"
                    @if($currentStep === count($verificationSteps)) disabled @endif
                    class="bg-blue-500 text-white px-4 py-2 rounded-md @if($currentStep === count($verificationSteps)) opacity-50 cursor-not-allowed @endif"
        >
            Next
        </button>
            </div>
    </div>

    <!-- Verification Modal -->
        <x-modal wire:model="showVerificationModal">
            <div class="p-6">
                <h3 class="text-lg font-medium">Verify Claim</h3>
                <div class="mt-4">
                    <p>Are you sure you want to verify this claim?</p>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button wire:click="$set('showVerificationModal', false)" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
                        Cancel
                    </button>
                    <button wire:click="verifyClaim" class="bg-green-500 text-white px-4 py-2 rounded-md">
                        Confirm Verification
                    </button>
                </div>
            </div>
        </x-modal>

    <!-- Rejection Modal -->
        <x-modal wire:model="showRejectionModal">
            <div class="p-6">
                <h3 class="text-lg font-medium">Reject Claim</h3>
                <div class="mt-4">
                    <label class="block">
                        <span class="text-gray-700">Rejection Notes</span>
                        <textarea wire:model="rejectionNotes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4"></textarea>
                    </label>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button wire:click="$set('showRejectionModal', false)" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
                        Cancel
                    </button>
                    <button wire:click="rejectClaim" class="bg-red-500 text-white px-4 py-2 rounded-md">
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </x-modal>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500">No claim selected.</p>
        </div>
    @endif
</div>
