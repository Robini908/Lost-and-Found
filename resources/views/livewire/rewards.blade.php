<div>
    <!-- Add ItemDetailsModal component inside the main div -->
    <livewire:item-details-modal />

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rewards') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Main Container -->
                <div class="flex h-[calc(100vh-4rem)]">
                    <!-- Left Sidebar - Stats -->
                    <div class="w-80 bg-white shadow-md overflow-y-auto sticky top-0 h-full">
                        <div class="p-6 space-y-4">
                            <!-- Available Points Card -->
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium opacity-90">Available Points</p>
                                        <h3 class="text-3xl font-bold mt-1">{{ number_format($availablePoints) }}</h3>
                                    </div>
                                    <div class="bg-white/20 rounded-full p-3">
                                        <i class="fas fa-coins text-2xl"></i>
                                    </div>
                                </div>
                                <div class="mt-4 text-sm opacity-90">
                                    Worth: {{ $currencySymbol }}{{ number_format($dollarValue, 2) }}
                                </div>
                            </div>

                            <!-- Monthly Earnings Card -->
                            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium opacity-90">This Month</p>
                                        <h3 class="text-3xl font-bold mt-1">{{ number_format($monthlyEarnings) }}</h3>
                                    </div>
                                    <div class="bg-white/20 rounded-full p-3">
                                        <i class="fas fa-chart-line text-2xl"></i>
                                    </div>
                                </div>
                                <div class="mt-4 text-sm opacity-90">
                                    From {{ $monthlyReportedItems }} reported items
                                </div>
                            </div>

                            <!-- Quick Actions Card -->
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-sm font-medium text-gray-900">Quick Actions</h3>
                                        <div class="bg-gray-100 rounded-full p-2">
                                            <i class="fas fa-bolt text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <button wire:click="$set('showConvertModal', true)"
                                                @class([
                                                    'w-full bg-gray-100 transition rounded-lg px-4 py-2.5 text-sm font-medium text-gray-900 flex items-center justify-between group',
                                                    'hover:bg-gray-200' => $availablePoints >= $conversionRate,
                                                    'opacity-50 cursor-not-allowed' => $availablePoints < $conversionRate,
                                                ])
                                                @if($availablePoints < $conversionRate) disabled @endif>
                                            <span class="flex items-center">
                                                <i class="fas fa-exchange-alt mr-2 text-gray-600 group-hover:text-gray-900"></i>
                                                Convert to {{ $currencySymbol }}
                                            </span>
                                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600"></i>
                                        </button>
                                        <button wire:click="$set('showHistoryModal', true)"
                                                class="w-full bg-gray-100 hover:bg-gray-200 transition rounded-lg px-4 py-2.5 text-sm font-medium text-gray-900 flex items-center justify-between group">
                                            <span class="flex items-center">
                                                <i class="fas fa-history mr-2 text-gray-600 group-hover:text-gray-900"></i>
                                                View Full History
                                            </span>
                                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Area -->
                    <div class="flex-1 bg-gray-50 p-6 overflow-y-auto">
                        <div class="max-w-4xl mx-auto">
                            <!-- Recent Activity -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                                <div class="p-6 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false"
                                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <button wire:click="exportHistory"
                                                            class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                                                        <i class="fas fa-file-export mr-2"></i>
                                                        Export History
                                                    </button>
                                                    <button wire:click="$set('showFilterModal', true)"
                                                            class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                                                        <i class="fas fa-filter mr-2"></i>
                                                        Filter Activities
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="divide-y divide-gray-200">
                                    @forelse($rewardHistory->take(5) as $history)
                                        <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="{{ $history['type'] === 'earned' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full p-2">
                                                        <i class="fas {{ $history['type'] === 'earned' ? 'fa-plus' : 'fa-minus' }} text-lg"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $history['description'] }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $history['date'] }}
                                                        </p>
                                                        @if($history['item'])
                                                            <a href="{{ route('lost-items.details', $history['item']->id) }}"
                                                               class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                                                View Item Details
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $history['type'] === 'earned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $history['type'] === 'earned' ? '+' : '' }}{{ number_format($history['points']) }}
                                                </span>
                                                @if($history['type'] === 'converted')
                                                    <button wire:click="showRedoConversion({{ $history['id'] }})"
                                                            class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-flex items-center">
                                                        <i class="fas fa-redo mr-1"></i>
                                                        Redo Conversion
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-6 text-center">
                                            <div class="text-gray-500">
                                                <i class="fas fa-history text-4xl mb-2"></i>
                                                <p>No reward activity yet.</p>
                                                <p class="text-sm mt-1">Start earning points by reporting found items!</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                                @if($rewardHistory->count() > 5)
                                    <div class="p-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                                        <button wire:click="$set('showHistoryModal', true)"
                                                class="w-full text-center text-sm text-gray-600 hover:text-gray-900">
                                            View All Activity
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Convert Points Modal -->
    <div x-data="{ show: @entangle('showConvertModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

            <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl" x-trap.noscroll="show">
                <div class="absolute top-4 right-4">
                    <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                        <i class="fas fa-exchange-alt text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Convert Points to {{ $currencySymbol }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Current rate: {{ $conversionRate }} points = {{ $currencySymbol }}1
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Available Points:</span>
                        <span class="font-semibold">{{ number_format($availablePoints) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-600">Worth:</span>
                        <span class="font-semibold">{{ $currencySymbol }}{{ number_format($dollarValue, 2) }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <button wire:click="convertPoints"
                            @class([
                                'w-full flex justify-center items-center px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                                'bg-indigo-600 text-white hover:bg-indigo-700' => $availablePoints >= $conversionRate,
                                'bg-gray-300 text-gray-500 cursor-not-allowed' => $availablePoints < $conversionRate,
                            ])
                            @if($availablePoints < $conversionRate) disabled @endif>
                        Convert All Points
                    </button>
                    <button @click="show = false"
                            class="w-full flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full History Modal -->
    <div x-data="{ show: @entangle('showHistoryModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

            <div class="relative bg-white rounded-lg max-w-4xl w-full shadow-xl" x-trap.noscroll="show">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Complete Reward History</h3>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <div class="space-y-6">
                        @forelse($rewardHistory as $history)
                            <div class="flex items-center justify-between hover:bg-gray-50 p-4 rounded-lg transition-colors duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="{{ $history['type'] === 'earned' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full p-3">
                                        <i class="fas {{ $history['type'] === 'earned' ? 'fa-plus' : 'fa-minus' }} text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $history['description'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $history['date'] }}</p>
                                        @if($history['item'])
                                            <a href="{{ route('lost-items.details', $history['item']->id) }}"
                                               class="text-xs text-indigo-600 hover:text-indigo-800 mt-1 inline-block">
                                                View Item Details
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $history['type'] === 'earned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $history['type'] === 'earned' ? '+' : '' }}{{ number_format($history['points']) }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="text-gray-500">
                                    <i class="fas fa-history text-4xl mb-3"></i>
                                    <p class="text-lg">No reward history yet</p>
                                    <p class="text-sm mt-2">Start earning points by reporting found items!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Points Earned:</span>
                        <span class="font-semibold text-green-600">+{{ number_format($rewardHistory->where('type', 'earned')->sum('points')) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div x-data="{ show: @entangle('showFilterModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

            <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl" x-trap.noscroll="show">
                <div class="absolute top-4 right-4">
                    <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Filter Activities</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Customize your reward history view
                    </p>
                </div>

                <form wire:submit.prevent="applyFilters">
                    <div class="space-y-4">
                        <!-- Date Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">From Date</label>
                                <input type="date" wire:model="dateFrom"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">To Date</label>
                                <input type="date" wire:model="dateTo"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Activity Type</label>
                            <select wire:model="typeFilter"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="all">All Activities</option>
                                <option value="earned">Earned Points</option>
                                <option value="converted">Converted Points</option>
                            </select>
                        </div>

                        <!-- Points Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Min Points</label>
                                <input type="number" wire:model="minPoints"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Max Points</label>
                                <input type="number" wire:model="maxPoints"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Sort Options -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sort By</label>
                                <select wire:model="sortBy"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="date">Date</option>
                                    <option value="points">Points</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Direction</label>
                                <select wire:model="sortDirection"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="desc">Descending</option>
                                    <option value="asc">Ascending</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button type="button"
                                wire:click="resetFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Reset
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Redo Conversion Modal -->
    <div x-data="{ show: @entangle('showRedoConversionModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

            <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl" x-trap.noscroll="show">
                <div class="absolute top-4 right-4">
                    <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                        <i class="fas fa-redo text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Redo Points Conversion</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Convert {{ number_format($pointsToRedo ?? 0) }} points to {{ $currencySymbol }}
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Points to Convert:</span>
                        <span class="font-semibold">{{ number_format($pointsToRedo ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-600">Worth:</span>
                        <span class="font-semibold">{{ $currencySymbol }}{{ number_format(($pointsToRedo ?? 0) / $conversionRate, 2) }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <button wire:click="redoConversion"
                            @class([
                                'w-full flex justify-center items-center px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
                                'bg-blue-600 text-white hover:bg-blue-700' => $availablePoints >= ($pointsToRedo ?? 0),
                                'bg-gray-300 text-gray-500 cursor-not-allowed' => $availablePoints < ($pointsToRedo ?? 0),
                            ])
                            @if($availablePoints < ($pointsToRedo ?? 0)) disabled @endif>
                        Confirm Conversion
                    </button>
                    <button @click="show = false"
                            class="w-full flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
