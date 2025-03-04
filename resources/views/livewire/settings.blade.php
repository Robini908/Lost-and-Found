<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Settings</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Configure your application's settings and preferences
                    </p>
                </div>
                <!-- Quick Actions -->
                <div class="flex items-center space-x-3">
                    <button wire:click="resetToDefault" class="inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                        <i class="fas fa-undo mr-2"></i>
                        Reset to Default
                    </button>
                    <button wire:click="saveSettings" class="inline-flex items-center px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-12 gap-6">
            <!-- Sidebar Navigation -->
            <div class="col-span-12 lg:col-span-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <nav class="space-y-1 p-4">
                        <a href="#general" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700 bg-blue-50 text-blue-700">
                            <i class="fas fa-cog w-5 h-5 mr-3"></i>
                            General
                        </a>
                        <a href="#rewards" class="flex items-center px-4 py-3 text-sm font-medium text-gray-900 rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fas fa-gift w-5 h-5 mr-3"></i>
                            Rewards
                        </a>
                        <a href="#notifications" class="flex items-center px-4 py-3 text-sm font-medium text-gray-900 rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fas fa-bell w-5 h-5 mr-3"></i>
                            Notifications
                        </a>
                        <a href="#security" class="flex items-center px-4 py-3 text-sm font-medium text-gray-900 rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fas fa-shield-alt w-5 h-5 mr-3"></i>
                            Security
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-span-12 lg:col-span-9 space-y-6">
                <!-- General Settings Card -->
                <div id="general" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">General Settings</h3>
                                <p class="mt-1 text-sm text-gray-500">Basic application configuration</p>
                            </div>
                            <span class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                <i class="fas fa-cog text-xl"></i>
                            </span>
                        </div>

                        <div class="space-y-6">
                            <!-- Site Name -->
                            <div class="grid grid-cols-3 gap-6">
                                <div class="col-span-3 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Site Name</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-globe text-gray-400"></i>
                                        </div>
                                        <x-input
                                            type="text"
                                            wire:model="settings.site_name"
                                            class="pl-10 block w-full"
                                            placeholder="Enter your site name"
                                        />
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">This name will appear throughout the application.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reward Settings Card -->
                <div id="rewards" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Reward System</h3>
                                <p class="mt-1 text-sm text-gray-500">Configure point system and rewards</p>
                            </div>
                            <span class="p-2 bg-yellow-50 rounded-lg text-yellow-600">
                                <i class="fas fa-gift text-xl"></i>
                            </span>
                        </div>

                        <div class="space-y-6">
                            <!-- Points Configuration -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Conversion Rate -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Points Conversion Rate</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-exchange-alt text-gray-400"></i>
                                        </div>
                                        <x-input
                                            type="number"
                                            wire:model="settings.points_conversion_rate"
                                            class="pl-10 block w-full"
                                            min="1"
                                        />
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-sm text-gray-500">points = 1 unit</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Currency Symbol -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-dollar-sign text-gray-400"></i>
                                        </div>
                                        <x-input
                                            type="text"
                                            wire:model="settings.currency_symbol"
                                            class="pl-10 w-32"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Minimum Points -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Minimum Points for Conversion</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-coins text-gray-400"></i>
                                    </div>
                                    <x-input
                                        type="number"
                                        wire:model="settings.min_points_convert"
                                        class="pl-10 block w-full"
                                        min="0"
                                    />
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Minimum points required before users can convert to currency.</p>
                            </div>

                            <!-- Enable/Disable Rewards -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">Enable Reward System</label>
                                        <p class="text-sm text-gray-500">Turn the reward system on or off globally</p>
                                    </div>
                                    <div class="ml-4">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="settings.enable_rewards"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    <div
        x-data="{ show: false, message: '' }"
        x-on:settings-saved.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="fixed bottom-4 right-4 z-50"
    >
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <p x-text="message"></p>
        </div>
    </div>
</div>
