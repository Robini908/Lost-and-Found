<!-- Match Analysis Modal -->
<div x-data="{ showDetails: false }"
     x-show="$wire.showAnalysisModal"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-6xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-network text-blue-600 mr-3"></i>
            Match Analysis Details
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
        </div>

            <!-- Modal Body -->
            <div class="p-6">
                @if($matchAnalysis)
                    <!-- Items Comparison -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Reported/Searched Item -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-search text-blue-600 mr-2"></i>
                                {{ $matchAnalysis['reported_item']->item_type === 'reported' ? 'Reported Item' : 'Searched Item' }}
                            </h4>
            <div class="space-y-4">
                                <!-- Image Gallery -->
                                @if($matchAnalysis['reported_item']->images->isNotEmpty())
                                    <div class="relative aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                        <img src="{{ Storage::url($matchAnalysis['reported_item']->images->first()->image_path) }}"
                                             alt="{{ $matchAnalysis['reported_item']->title }}"
                                             class="object-cover">
                    </div>
                                @endif

                                <!-- Item Details -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Title</span>
                                        <span class="text-sm text-gray-900">{{ $matchAnalysis['reported_item']->title }}</span>
                </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Category</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $matchAnalysis['reported_item']->category->name }}
                                        </span>
                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Date</span>
                                        <span class="text-sm text-gray-900">
                                            {{ $matchAnalysis['reported_item']->date_lost ? $matchAnalysis['reported_item']->date_lost->format('M d, Y') : 'Not specified' }}
                                        </span>
                </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Location</span>
                                        <div class="text-right">
                                            @if($matchAnalysis['reported_item']->location_type === 'specific')
                                                <span class="text-sm text-gray-900">{{ $matchAnalysis['reported_item']->location_address }}</span>
                                                @if($matchAnalysis['reported_item']->location_lat && $matchAnalysis['reported_item']->location_lng)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Coordinates: {{ number_format($matchAnalysis['reported_item']->location_lat, 6) }},
                                                        {{ number_format($matchAnalysis['reported_item']->location_lng, 6) }}
            </div>
                                                @endif
        @else
                                                <span class="text-sm text-gray-900">{{ $matchAnalysis['reported_item']->area }}</span>
                                                @if($matchAnalysis['reported_item']->landmarks)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Near: {{ $matchAnalysis['reported_item']->landmarks }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                        </div>
                    </div>
                            </div>
                        </div>

                        <!-- Found Item -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-hand-holding text-emerald-600 mr-2"></i>
                                Found Item
                            </h4>
                            <div class="space-y-4">
                                <!-- Image Gallery -->
                                @if($matchAnalysis['found_item']->images->isNotEmpty())
                                    <div class="relative aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                        <img src="{{ Storage::url($matchAnalysis['found_item']->images->first()->image_path) }}"
                                             alt="{{ $matchAnalysis['found_item']->title }}"
                                             class="object-cover">
                                    </div>
                                @endif

                                <!-- Item Details -->
                            <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Title</span>
                                        <span class="text-sm text-gray-900">{{ $matchAnalysis['found_item']->title }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Category</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ $matchAnalysis['found_item']->category->name }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Date Found</span>
                                        <span class="text-sm text-gray-900">
                                            {{ $matchAnalysis['found_item']->date_found ? $matchAnalysis['found_item']->date_found->format('M d, Y') : 'Not specified' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Location</span>
                                        <div class="text-right">
                                            @if($matchAnalysis['found_item']->location_type === 'specific')
                                                <span class="text-sm text-gray-900">{{ $matchAnalysis['found_item']->location_address }}</span>
                                                @if($matchAnalysis['found_item']->location_lat && $matchAnalysis['found_item']->location_lng)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Coordinates: {{ number_format($matchAnalysis['found_item']->location_lat, 6) }},
                                                        {{ number_format($matchAnalysis['found_item']->location_lng, 6) }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-900">{{ $matchAnalysis['found_item']->area }}</span>
                                                @if($matchAnalysis['found_item']->landmarks)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Near: {{ $matchAnalysis['found_item']->landmarks }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Similarity Analysis -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h4 class="text-lg font-medium text-gray-900 flex items-center">
                                <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
                                Similarity Analysis
                            </h4>
                        </div>

                        <div class="p-4">
                            <!-- Overall Score -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900">Overall Match Score</span>
                                    <span class="text-sm font-semibold {{ $matchAnalysis['similarity_score'] >= 0.8 ? 'text-emerald-600' : 'text-blue-600' }}">
                                        {{ number_format($matchAnalysis['similarity_score'] * 100, 1) }}%
                                    </span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $matchAnalysis['similarity_score'] >= 0.8 ? 'bg-emerald-500' : 'bg-blue-500' }} transition-all duration-500"
                                         style="width: {{ $matchAnalysis['similarity_score'] * 100 }}%">
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Scores -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Text Similarity -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">
                                            <i class="fas fa-align-left text-blue-500 mr-1.5"></i>
                                            Text Similarity
                                        </span>
                                        <span class="text-sm font-medium">{{ number_format($matchAnalysis['text_similarity'] * 100, 1) }}%</span>
                                    </div>
                                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500 transition-all duration-500"
                                             style="width: {{ $matchAnalysis['text_similarity'] * 100 }}%">
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Similarity -->
                                <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">
                                            <i class="fas fa-image text-indigo-500 mr-1.5"></i>
                                            Image Similarity
                                        </span>
                                        <span class="text-sm font-medium">{{ number_format($matchAnalysis['image_similarity'] * 100, 1) }}%</span>
                                    </div>
                                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 transition-all duration-500"
                                             style="width: {{ $matchAnalysis['image_similarity'] * 100 }}%">
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Similarity -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1.5"></i>
                                            Location Similarity
                                        </span>
                                        <span class="text-sm font-medium">{{ number_format($matchAnalysis['location_similarity'] * 100, 1) }}%</span>
                                    </div>
                                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500 transition-all duration-500"
                                             style="width: {{ $matchAnalysis['location_similarity'] * 100 }}%">
                                        </div>
                                    </div>
                            </div>

                                <!-- Time Similarity -->
                                <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">
                                            <i class="fas fa-clock text-amber-500 mr-1.5"></i>
                                            Time Similarity
                                        </span>
                                        <span class="text-sm font-medium">{{ number_format($matchAnalysis['time_similarity'] * 100, 1) }}%</span>
                                    </div>
                                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-amber-500 transition-all duration-500"
                                             style="width: {{ $matchAnalysis['time_similarity'] * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Match Insights -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <h5 class="text-sm font-medium text-gray-900 mb-3">Match Insights</h5>
                                <div class="space-y-4">
                                    <!-- Similarity Breakdown -->
                                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                                        <h6 class="text-sm font-medium text-gray-900 mb-3">Similarity Breakdown</h6>
                                        <div class="space-y-3">
                                            <!-- Text Similarity Details -->
                                            <div>
                                                <div class="flex items-center justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Text Match</span>
                                                    <span class="font-medium">{{ number_format($matchAnalysis['match_details']['similarity_breakdown']['text'] * 100, 1) }}%</span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Common words: {{ implode(', ', array_slice($matchAnalysis['match_details']['text_match_highlights']['common_words'], 0, 5)) }}
                                                    @if(count($matchAnalysis['match_details']['text_match_highlights']['common_words']) > 5)
                                                        <span class="text-gray-400">and {{ count($matchAnalysis['match_details']['text_match_highlights']['common_words']) - 5 }} more...</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Location Match Details -->
                                            <div>
                                                <div class="flex items-center justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Location Match</span>
                                                    <span class="font-medium">{{ number_format($matchAnalysis['match_details']['similarity_breakdown']['location'] * 100, 1) }}%</span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Distance: {{ $matchAnalysis['match_details']['location_distance_formatted'] }}
                                                    @if($matchAnalysis['match_details']['location_insights']['same_area'])
                                                        <span class="text-green-600 ml-2">(Same Area)</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Time Match Details -->
                                            <div>
                                                <div class="flex items-center justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Time Match</span>
                                                    <span class="font-medium">{{ number_format($matchAnalysis['match_details']['similarity_breakdown']['time'] * 100, 1) }}%</span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    @if($matchAnalysis['match_details']['time_insights']['has_dates'])
                                                        Time difference: {{ $matchAnalysis['match_details']['time_difference_formatted'] }}
                                                        @if($matchAnalysis['match_details']['time_insights']['found_after_lost'])
                                                            <span class="text-green-600 ml-2">(Found after reported)</span>
                                                        @endif
                                                    @else
                                                        {{ $matchAnalysis['match_details']['time_insights']['message'] }}
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Category Match Details -->
                                            <div>
                                                <div class="flex items-center justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Category Match</span>
                                                    <span class="font-medium">{{ number_format($matchAnalysis['match_details']['similarity_breakdown']['category'] * 100, 1) }}%</span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    @if($matchAnalysis['match_details']['category_match'])
                                                        <span class="text-green-600">Exact category match</span>
                                                    @else
                                                        <span class="text-yellow-600">Different categories</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Debug Information (for admins) -->
                                    @if(auth()->user()->isAdmin())
                                    <div class="mt-4 bg-gray-900 rounded-lg p-4 text-white font-mono text-xs">
                                        <div class="mb-2 text-gray-400">Debug Information:</div>
                                        <pre class="whitespace-pre-wrap">{{ json_encode([
                                            'match_id' => $matchAnalysis['reported_item']->id . '-' . $matchAnalysis['found_item']->id,
                                            'similarity_score' => $matchAnalysis['similarity_score'],
                                            'text_similarity' => $matchAnalysis['match_details']['similarity_breakdown']['text'],
                                            'location_similarity' => $matchAnalysis['match_details']['similarity_breakdown']['location'],
                                            'time_similarity' => $matchAnalysis['match_details']['similarity_breakdown']['time'],
                                            'category_similarity' => $matchAnalysis['match_details']['similarity_breakdown']['category'],
                                            'location_distance' => $matchAnalysis['match_details']['location_distance'],
                                            'time_difference' => $matchAnalysis['match_details']['time_difference'],
                                            'text_match_highlights' => $matchAnalysis['match_details']['text_match_highlights'],
                                            'location_insights' => $matchAnalysis['match_details']['location_insights'],
                                            'time_insights' => $matchAnalysis['match_details']['time_insights'],
                                        ], JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <button wire:click="closeModal"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-times mr-2"></i>
                            Close
                        </button>
                        <button wire:click="showContact({{ $matchAnalysis['reported_item']->id }})"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-envelope mr-2"></i>
                            Contact Finder
                        </button>
                </div>
            @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Loading Analysis...</h3>
                        <p class="mt-2 text-sm text-gray-500">Please wait while we analyze the match.</p>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>
