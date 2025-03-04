<!-- Points Conversion Modal -->
<div x-data="{ show: @entangle('showConvertModal') }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95">

    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <!-- Modal panel -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <!-- Header -->
            <div class="absolute right-0 top-0 pr-4 pt-4">
                <button type="button"
                        wire:click="$set('showConvertModal', false)"
                        class="rounded-full bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-coins text-blue-600 text-xl"></i>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <h3 class="text-xl font-semibold leading-6 text-gray-900">
                        Convert Points to Cash
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Current conversion rate: <span class="font-medium text-gray-900">{{ $currencySymbol }}{{ number_format($conversionRate, 2) }}</span> per point
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="mt-6 space-y-4">
                <!-- Points   -->
                <div>
                    <label for="points" class="block text-sm font-medium text-gray-700">
                        Points to Convert
                    </label>
                    <div class="relative mt-2 rounded-lg shadow-sm">
                        <input type="number"
                               wire:model.live="customConversionAmount"
                               min="{{ $this->getMinimumConversionAmount() }}"
                               max="{{ $this->getMaximumConversionAmount() }}"
                               class="block w-full rounded-lg border-0 py-3 pl-4 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                               placeholder="Enter amount">
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">points</span>
                        </div>
                    </div>
                    @error('customConversionAmount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Card -->
                <div class="rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">You'll receive:</span>
                        <span class="text-lg font-semibold text-blue-600">
                            {{ $currencySymbol }}{{ number_format($this->convertedAmountPreview, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Info List -->
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Available Points:</span>
                        <span class="font-medium">{{ number_format($availablePoints) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Minimum Conversion:</span>
                        <span class="font-medium">{{ number_format($this->getMinimumConversionAmount()) }} points</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse space-y-3 sm:space-y-0">
                <button type="button"
                        wire:click="convertPoints"
                        wire:loading.attr="disabled"
                        class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 sm:w-auto">
                    <span wire:loading.remove wire:target="convertPoints">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Convert Points
                    </span>
                    <span wire:loading wire:target="convertPoints">
                        <i class="fas fa-circle-notch fa-spin mr-2"></i>
                        Converting...
                    </span>
                </button>
                <button type="button"
                        wire:click="$set('showConvertModal', false)"
                        class="inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div x-data="{ show: false, message: '' }"
     x-on:points-converted.window="
        show = true;
        message = `Successfully converted ${$event.detail.points} points to ${$wire.currencySymbol}${$event.detail.amount}`;
        setTimeout(() => show = false, 3000)
     "
     x-show="show"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed bottom-0 right-0 z-50 m-6 pointer-events-none">
    <div class="pointer-events-auto w-96 overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p x-text="message" class="text-sm font-medium text-gray-900"></p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                    <button @click="show = false"
                            class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
