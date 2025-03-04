<!-- Step 5: Review -->
<div class="space-y-8">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">Review Your Report</h2>
        <p class="mt-2 text-sm text-gray-600">Please verify all information before submitting</p>
    </div>

    <!-- Material Design inspired review card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header with report type -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 rounded-full p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-white">
                            @switch($reportType)
                                @case('reported')
                                    Lost Item Report
                                    @break
                                @case('searched')
                                    Searching for Item
                                    @break
                                @case('found')
                                    Found Item Report
                                    @break
                            @endswitch
                        </h3>
                        <p class="text-blue-100 text-sm">{{ $is_anonymous ? 'Anonymous Report' : 'Public Report' }}</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                        {{ date('F j, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Content sections -->
        <div class="p-6">
            <!-- Basic Information Section -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">Basic Information</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pl-12">
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Title</p>
                        <p class="mt-1 text-base text-gray-900">{{ $title }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Category</p>
                        <p class="mt-1 text-base text-gray-900">{{ $categories->firstWhere('id', $category_id)->name ?? 'Not selected' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Date</p>
                        <p class="mt-1 text-base text-gray-900">{{ $date ? date('F j, Y', strtotime($date)) : 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Item Details Section -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">Item Details</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pl-12">
                    @if($brand)
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Brand</p>
                        <p class="mt-1 text-base text-gray-900">{{ $brand }}</p>
                    </div>
                    @endif
                    @if($color)
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Color</p>
                        <p class="mt-1 text-base text-gray-900">{{ $color }}</p>
                    </div>
                    @endif
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Condition</p>
                        <p class="mt-1 text-base text-gray-900">
                            @switch($condition)
                                @case('new')
                                    New
                                    @break
                                @case('like_new')
                                    Like New
                                    @break
                                @case('excellent')
                                    Excellent
                                    @break
                                @case('good')
                                    Good
                                    @break
                                @case('fair')
                                    Fair
                                    @break
                                @case('poor')
                                    Poor
                                    @break
                                @case('damaged')
                                    Damaged
                                    @break
                                @default
                                    Not specified
                            @endswitch
                        </p>
                    </div>
                </div>

                @if($description)
                <div class="mt-4 pl-12">
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="mt-1 text-base text-gray-900 whitespace-pre-line">{{ $description }}</p>
                    </div>
                </div>
                @endif

                @if($notes)
                <div class="mt-4 pl-12">
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <p class="text-sm font-medium text-gray-500">Additional Notes</p>
                        <p class="mt-1 text-base text-gray-900">{{ $notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Location Information Section -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">Location Information</h4>
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $locationType === 'specific' ? 'Specific Location' : 'General Area' }}
                    </span>
                </div>

                <div class="pl-12">
                    @if($locationType === 'specific')
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                            <p class="text-sm font-medium text-gray-500">Address</p>
                            <p class="mt-1 text-base text-gray-900">{{ $location_address }}</p>

                            @if($location_lat && $location_lng)
                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                Coordinates: {{ $location_lat }}, {{ $location_lng }}
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                                <p class="text-sm font-medium text-gray-500">Area</p>
                                <p class="mt-1 text-base text-gray-900">{{ $area }}</p>
                            </div>

                            @if($landmarks)
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                                <p class="text-sm font-medium text-gray-500">Landmarks</p>
                                <p class="mt-1 text-base text-gray-900">{{ $landmarks }}</p>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Images Section -->
            @if(count($images) > 0)
            <div>
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">Uploaded Images</h4>
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        {{ count($images) }} {{ count($images) === 1 ? 'image' : 'images' }}
                    </span>
                </div>

                <div class="pl-12">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($images as $index => $image)
                            <div class="relative group">
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 shadow-sm border border-gray-200 transition-all duration-200 group-hover:shadow-md group-hover:border-blue-300">
                                    @if($image->temporaryUrl())
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview {{ $index + 1 }}"
                                             class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <span class="text-white text-xs font-medium px-2 py-1 bg-black bg-opacity-50 rounded-full">Image {{ $index + 1 }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Submit Notice Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900">Ready to Submit?</h4>
                <p class="mt-2 text-sm text-gray-600">
                    Please review all information carefully before submitting your report.
                </p>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h5 class="text-sm font-medium text-gray-900">Report Review</h5>
                            <p class="mt-1 text-xs text-gray-500">Your report will be reviewed by our team</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h5 class="text-sm font-medium text-gray-900">Status Tracking</h5>
                            <p class="mt-1 text-xs text-gray-500">You can track the status in your dashboard</p>
                        </div>
                    </div>
                    @if($reportType === 'found')
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h5 class="text-sm font-medium text-gray-900">Reward Points</h5>
                            <p class="mt-1 text-xs text-gray-500">You'll receive reward points for reporting a found item</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h5 class="text-sm font-medium text-gray-900">Notifications</h5>
                            <p class="mt-1 text-xs text-gray-500">You'll receive updates about your report</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Add smooth transitions for hover effects */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Add responsive adjustments */
@media (max-width: 640px) {
    .pl-12 {
        padding-left: 0.5rem;
    }
}
</style>
