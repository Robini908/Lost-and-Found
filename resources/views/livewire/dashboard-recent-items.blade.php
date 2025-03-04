<div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-gray-900">Recent Items</h3>
            <a href="{{ route('products.view-items') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                View All
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($recentItems as $item)
                <div wire:key="item-{{ $item['id'] }}" class="group bg-gray-50 rounded-lg p-4 hover:bg-white hover:shadow-md transition-all duration-200 border border-gray-100">
                    <div class="flex items-center space-x-4">
                        @if($item['image'])
                            <img src="{{ Storage::url($item['image']) }}"
                                 alt="{{ $item['title'] }}"
                                 class="w-16 h-16 rounded-lg object-cover">
                        @else
                            <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                {{ $item['title'] }}
                            </h4>
                            <p class="mt-1 text-sm text-gray-500 truncate">{{ $item['description'] }}</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @switch($item['item_type'])
                                        @case('reported')
                                            bg-red-50 text-red-700
                                            @break
                                        @case('searched')
                                            bg-blue-50 text-blue-700
                                            @break
                                        @case('found')
                                            bg-green-50 text-green-700
                                            @break
                                        @default
                                            bg-gray-50 text-gray-700
                                    @endswitch">
                                    {{ ucfirst($item['item_type']) }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $item['category'] }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-12 bg-gray-50 rounded-lg">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">No items yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Start by reporting a lost or found item</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
