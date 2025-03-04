<div class="min-h-screen bg-gray-50 py-8" x-data="{ loading: false }">
    <!-- Loading State -->
    <x-loading-state :message="$reportType === 'found' ? 'Submitting found item report...' : ($reportType === 'reported' ? 'Submitting lost item report...' : 'Submitting searched item report...')" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Report an Item</h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                Help us connect lost items with their owners
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-12">
            <div class="relative">
                <div class="overflow-hidden h-2 mb-8 text-xs flex rounded-full bg-gray-200">
                    <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-500 ease-out" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
                </div>
                <div class="flex justify-between -mt-2">
                    @for ($i = 1; $i <= $totalSteps; $i++)
                        <div class="relative flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center {{ $currentStep >= $i ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 bg-white text-gray-500' }} transition-all duration-200 ease-in-out transform {{ $currentStep === $i ? 'scale-110 ring-4 ring-blue-100' : '' }}">
                                <span class="text-sm font-bold">{{ $i }}</span>
                            </div>
                            <div class="absolute -bottom-8 w-32 text-center">
                                <span class="text-xs font-medium uppercase {{ $currentStep >= $i ? 'text-blue-600' : 'text-gray-500' }}">
                                    @switch($i)
                                        @case(1)
                                            Report Type
                                            @break
                                        @case(2)
                                            Basic Info
                                            @break
                                        @case(3)
                                            Details
                                            @break
                                        @case(4)
                                            Location
                                            @break
                                        @case(5)
                                            Review
                                            @break
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <form wire:submit.prevent="submit" @submit="loading = true">
                @include('livewire.report-lost-item.step-' . $currentStep)

                <!-- Navigation Buttons -->
                <div class="mt-8 flex justify-between items-center">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="previousStep" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Previous
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if ($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Next
                            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Report
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Help Text -->
        <div class="text-center">
            <p class="text-base text-gray-500">
                Need help? <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">Contact support</a>
            </p>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="fixed bottom-0 right-0 m-6 space-y-4">
        @if (session()->has('success'))
            <div class="max-w-sm bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="max-w-sm bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
