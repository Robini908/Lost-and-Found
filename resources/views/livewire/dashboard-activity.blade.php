<div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
            <span class="text-sm text-gray-500">Last 7 days</span>
        </div>
        <div class="space-y-4">
            @forelse($activities as $activity)
                <div wire:key="activity-{{ $activity['id'] }}" class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{
                            $activity['type'] === 'lost' ? 'bg-red-50 text-red-600' :
                            ($activity['type'] === 'found' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600')
                        }}">
                            <i class="fas {{
                                $activity['type'] === 'lost' ? 'fa-search' :
                                ($activity['type'] === 'found' ? 'fa-box' : 'fa-check')
                            }}"></i>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center space-x-2">
                            <img src="{{ $activity['user']['avatar'] }}" alt="{{ $activity['user']['name'] }}" class="h-5 w-5 rounded-full">
                            <span class="text-sm font-medium text-gray-900">{{ $activity['user']['name'] }}</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $activity['description'] }}</p>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                            <i class="fas fa-clock mr-1.5"></i>
                            {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                        </div>
                    </div>
                    @if($activity['link'])
                        <a href="{{ $activity['link'] }}" class="flex-shrink-0 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-clock text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500">No recent activity</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
