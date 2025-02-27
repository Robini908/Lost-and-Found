<!-- Analysis Modal -->
<x-dialog-modal wire:model="showAnalysisModal">
    <x-slot name="title">
        <div class="flex items-center">
            <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
            Match Analysis Details
        </div>
    </x-slot>

    <x-slot name="content">
        @if($isLoading)
            <div class="space-y-4">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16">
                        <div class="w-16 h-16 rounded-full border-4 border-blue-500 border-t-transparent animate-spin"></div>
                    </div>
                </div>
                <p class="text-xl font-semibold text-gray-800 text-center">Analyzing Items</p>
                <p class="text-gray-600 text-center">{{ $loadingMessage }}</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-in-out"
                        style="width: {{ $progress }}%">
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">{{ $progress }}%</div>
            </div>
        @else
            @if($matchAnalysis)
                <div class="space-y-6">
                    <!-- Similarity Score -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-semibold text-blue-800">Overall Match Score</span>
                            <span class="text-2xl font-bold text-blue-600">{{ number_format($matchAnalysis['similarity_score'] * 100, 2) }}%</span>
                        </div>
                    </div>

                    <!-- Items Comparison -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Reported Item -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-3">Reported Item</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Title:</span> {{ $matchAnalysis['reported_item']->title }}</p>
                                <p><span class="font-medium">Category:</span> {{ is_string($matchAnalysis['reported_item']->category) ? $matchAnalysis['reported_item']->category : $matchAnalysis['reported_item']->category['name'] }}</p>
                                <p><span class="font-medium">Location:</span> {{ $matchAnalysis['reported_item']->location }}</p>
                                <p><span class="font-medium">Date Lost:</span> {{ $matchAnalysis['reported_item']->date_lost ? $matchAnalysis['reported_item']->date_lost->format('M d, Y') : 'Not specified' }}</p>
                            </div>
                        </div>

                        <!-- Found Item -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-3">Found Item</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Title:</span> {{ $matchAnalysis['found_item']->title }}</p>
                                <p><span class="font-medium">Category:</span> {{ is_string($matchAnalysis['found_item']->category) ? $matchAnalysis['found_item']->category : $matchAnalysis['found_item']->category['name'] }}</p>
                                <p><span class="font-medium">Location:</span> {{ $matchAnalysis['found_item']->location }}</p>
                                <p><span class="font-medium">Date Found:</span> {{ $matchAnalysis['found_item']->date_found ? $matchAnalysis['found_item']->date_found->format('M d, Y') : 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Match Factors -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-3">Match Factors</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span>Title & Description Similarity</span>
                                <div class="w-48 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ number_format($matchAnalysis['text_similarity'] * 100, 0) }}%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">{{ number_format($matchAnalysis['text_similarity'] * 100, 0) }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Image Similarity</span>
                                <div class="w-48 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ number_format($matchAnalysis['image_similarity'] * 100, 0) }}%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">{{ number_format($matchAnalysis['image_similarity'] * 100, 0) }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Location Proximity</span>
                                <div class="w-48 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ number_format($matchAnalysis['location_similarity'] * 100, 0) }}%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">{{ number_format($matchAnalysis['location_similarity'] * 100, 0) }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Time Correlation</span>
                                <div class="w-48 bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ number_format($matchAnalysis['time_similarity'] * 100, 0) }}%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">{{ number_format($matchAnalysis['time_similarity'] * 100, 0) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center text-gray-600">
                    No match selected for analysis.
                </div>
            @endif
        @endif
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
            <i class="fas fa-times"></i>
            <span>Close</span>
        </x-secondary-button>
    </x-slot>
</x-dialog-modal>
