<div class="min-h-screen bg-gray-50">
    <!-- Top App Bar -->
    <div class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-medium text-gray-800">
                        <i class="fas fa-coins text-blue-600 mr-2"></i>
                        Rewards Dashboard
                    </h1>
                    <div class="hidden md:flex items-center bg-gray-50 rounded-full px-4 py-1.5">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">Last updated: {{ now()->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Real-time Status -->
                    <button wire:click="togglePolling"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg {{ $isPolling ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-600' }} text-sm font-medium transition-colors">
                        <i class="fas {{ $isPolling ? 'fa-signal' : 'fa-signal-slash' }} mr-2"></i>
                        {{ $isPolling ? 'Real-time On' : 'Real-time Off' }}
                    </button>
                    <button class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <i class="fas fa-bell text-gray-600"></i>
                    </button>
                    </div>
                </div>
            </div>
        </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Points Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Available Points Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-coins text-2xl"></i>
                    </div>
                    <button wire:click="$set('showConvertModal', true)"
                            class="bg-white/20 hover:bg-white/30 rounded-lg px-4 py-2 text-sm font-medium transition-colors">
                        Convert to Cash
                    </button>
                </div>
                <h3 class="text-lg font-medium opacity-90">Available Points</h3>
                <div class="mt-2 flex items-baseline">
                    <span class="text-4xl font-bold">{{ number_format($availablePoints) }}</span>
                    <span class="ml-2 text-sm opacity-75">points</span>
                </div>
                <div class="mt-4 text-sm opacity-75">
                    Worth {{ $currencySymbol }}{{ number_format($dollarValue, 2) }}
                </div>
            </div>

            <!-- Cash Value Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-medium opacity-90">Conversion Rate</h3>
                <div class="mt-2 flex items-baseline">
                    <span class="text-4xl font-bold">{{ $currencySymbol }}{{ number_format($conversionRate, 2) }}</span>
                    <span class="ml-2 text-sm opacity-75">per point</span>
                </div>
                <div class="mt-4 text-sm opacity-75">
                    Current exchange rate
                </div>
            </div>

            <!-- Points Expiring Card -->
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-medium opacity-90">Expiring Soon</h3>
                <div class="mt-2 flex items-baseline">
                    <span class="text-4xl font-bold">{{ number_format($pointsExpiringSoon) }}</span>
                    <span class="ml-2 text-sm opacity-75">points</span>
                </div>
                <div class="mt-4 text-sm opacity-75">
                    Expires in 30 days
                </div>
            </div>
            </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach([
                ['title' => 'Total Earned', 'value' => $stats['total_earned'], 'icon' => 'fa-star', 'color' => 'text-green-600', 'bg' => 'bg-green-50'],
                ['title' => 'Total Converted', 'value' => $stats['total_converted'], 'icon' => 'fa-exchange-alt', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                ['title' => 'Monthly Average', 'value' => $stats['estimated_monthly_earnings'], 'icon' => 'fa-chart-line', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                ['title' => 'Found Items', 'value' => $stats['found_items_points'] ?? 0, 'icon' => 'fa-search-location', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50']
            ] as $stat)
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="{{ $stat['bg'] }} p-3 rounded-full">
                        <i class="fas {{ $stat['icon'] }} {{ $stat['color'] }}"></i>
                    </div>
                    <span class="{{ $stat['color'] }} text-2xl font-semibold">{{ number_format($stat['value']) }}</span>
                </div>
                <h3 class="text-sm font-medium text-gray-900">{{ $stat['title'] }}</h3>
            </div>
            @endforeach
        </div>

        <!-- Activity Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Points Activity</h3>
                <div class="flex items-center space-x-2 bg-gray-50 rounded-lg p-1">
                    @foreach(['7days' => '7D', '30days' => '30D', '90days' => '90D', 'year' => '1Y'] as $value => $label)
                        <button wire:click="updateChartPeriod('{{ $value }}')"
                            class="px-3 py-1 rounded-md text-sm {{ $selectedPeriod === $value ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:bg-gray-100' }} transition-colors">
                            {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="h-80">
                @if($showLineChart)
                    <livewire:livewire-line-chart
                        key="{{ $selectedPeriod }}"
                        :line-chart-model="$this->getPointsChartModel()"
                    />
                @endif
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Transaction History</h3>
                    <div class="flex items-center space-x-3">
                        <button wire:click="$set('showFilterModal', true)"
                                class="inline-flex items-center px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                        <select wire:model="typeFilter"
                                class="form-select rounded-lg text-sm border-gray-200 bg-gray-50 text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Types</option>
                            <option value="earned">Earned</option>
                            <option value="converted">Converted</option>
                            <option value="bonus">Bonus</option>
                            <option value="referral">Referral</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($rewardHistory as $history)
                        <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($history['date'])->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $history['metadata']['type'] === 'earned' ? 'bg-green-50 text-green-700' : '' }}
                                    {{ $history['metadata']['type'] === 'converted' ? 'bg-blue-50 text-blue-700' : '' }}
                                    {{ $history['metadata']['type'] === 'bonus' ? 'bg-purple-50 text-purple-700' : '' }}
                                    {{ $history['metadata']['type'] === 'referral' ? 'bg-yellow-50 text-yellow-700' : '' }}">
                                    {{ ucfirst($history['metadata']['type']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $history['description'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $history['metadata']['category'] === 'found_item' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-50 text-gray-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $history['metadata']['category'])) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium
                                    {{ $history['points'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $history['points'] > 0 ? '+' : '' }}{{ number_format($history['points']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    @if($history['converted_amount'])
                                        {{ $history['currency'] }}{{ number_format($history['converted_amount'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 rounded-full p-4 mb-4">
                                        <i class="fas fa-history text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">No transaction history found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6">
        <button wire:click="refreshData"
                class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <!-- Include the conversion modal -->
    @include('livewire.partials.convert-points-modal')
</div>
