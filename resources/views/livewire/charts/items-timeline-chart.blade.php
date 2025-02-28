<div class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h3 class="text-lg font-medium text-gray-900">{{ $lineChartModel->getTitle() }}</h3>
        <div class="flex items-center space-x-4">
            <select wire:model="chartType" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="status">Status Distribution</option>
                <option value="verification">Verification Status</option>
                <option value="claims">Claims & Matches</option>
            </select>
            <select wire:model="days" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>
    </div>

    <div class="mt-4">
        <div wire:ignore>
            <livewire:livewire-line-chart
                :line-chart-model="$lineChartModel"
            />
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @if($chartType === 'status')
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-sm text-gray-600">Reported Items</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-sm text-gray-600">Searched Items</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-sm text-gray-600">Found Items</span>
            </div>
        @elseif($chartType === 'verification')
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-sm text-gray-600">Verified Items</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-sm text-gray-600">Unverified Items</span>
            </div>
        @else
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                <span class="text-sm text-gray-600">Claimed Items</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-sm text-gray-600">Matched Items</span>
            </div>
        @endif
    </div>
</div>
