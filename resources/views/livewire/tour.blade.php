<div>
    @if($currentStep < count($steps))
        <div x-data="{ show: true }" x-show="show" x-cloak class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div x-ref="stepElement" class="mb-4">
                    <p>{{ $steps[$currentStep]['content'] }}</p>
                </div>
                <div class="flex justify-between">
                    <button wire:click="previousStep" class="bg-gray-300 px-4 py-2 rounded text-gray-700">Previous</button>
                    <button wire:click="nextStep" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Next</button>
                </div>
            </div>
        </div>
    @endif
</div>