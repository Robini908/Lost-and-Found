<div class="bg-white p-6 rounded-lg shadow-sm">
    <div class="mb-6">
        <div wire:ignore>
            <livewire:livewire-pie-chart
                :pie-chart-model="$pieChartModel"
            />
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-sm font-medium text-gray-500">Total Items</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-sm font-medium text-gray-500">Claimed Items</div>
            <div class="mt-1 text-2xl font-semibold text-green-600">{{ $stats['claimed'] }}</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-sm font-medium text-gray-500">Verified Items</div>
            <div class="mt-1 text-2xl font-semibold text-blue-600">{{ $stats['verified'] }}</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-sm font-medium text-gray-500">Matched Items</div>
            <div class="mt-1 text-2xl font-semibold text-purple-600">{{ $stats['matched'] }}</div>
        </div>
    </div>
</div>
