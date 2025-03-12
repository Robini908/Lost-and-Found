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
                    <button 
                        x-data=""
                        x-on:click="if (confirm('Are you sure you want to reset all settings to their default values? This action cannot be undone.')) { $wire.resetToDefault() }"
                        class="inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                    >
                        <i class="fas fa-undo mr-2"></i>
                        Reset to Default
                    </button>
                    <button 
                        wire:click="saveSettings" 
                        class="inline-flex items-center px-4 py-2 text-sm text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                    >
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
                        <a href="#contact" class="flex items-center px-4 py-3 text-sm font-medium text-gray-900 rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fas fa-address-card w-5 h-5 mr-3"></i>
                            Contact & Support
                        </a>
                        <a href="#security" class="flex items-center px-4 py-3 text-sm font-medium text-gray-900 rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fas fa-shield-alt w-5 h-5 mr-3"></i>
                            Security
                        </a>
                        <a href="#notifications" class="flex items-center px-4 py-3 text-sm font-medium text-gray-900 rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fas fa-bell w-5 h-5 mr-3"></i>
                            Notifications
                        </a>
                        <a href="#rewards" class="flex items-center px-4 py-3 text-sm font-medium text-gray-900 rounded-lg transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fas fa-gift w-5 h-5 mr-3"></i>
                            Rewards
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

                <!-- Contact & Support Settings -->
                <div id="contact" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Contact & Support</h3>
                                <p class="mt-1 text-sm text-gray-500">Configure contact information and support details</p>
                            </div>
                            <span class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                <i class="fas fa-address-card text-xl"></i>
                            </span>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                                    <div class="mt-1">
                                        <x-input type="email" wire:model="settings.contact_email" class="w-full" placeholder="support@example.com" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Support Phone</label>
                                    <div class="mt-1">
                                        <x-input type="tel" wire:model="settings.support_phone" class="w-full" placeholder="+1 (555) 123-4567" />
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Office Address</label>
                                    <div class="mt-1">
                                        <x-textarea wire:model="settings.office_address" class="w-full" rows="3" placeholder="Enter your office address" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Office Hours</label>
                                    <div class="mt-1">
                                        <x-input type="text" wire:model="settings.office_hours" class="w-full" placeholder="9:00 AM - 5:00 PM" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div id="security" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Security Settings</h3>
                                <p class="mt-1 text-sm text-gray-500">Configure security and authentication settings</p>
                            </div>
                            <span class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </span>
                        </div>

                        <div class="space-y-6">
                            <!-- Authentication Settings -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Max Login Attempts</label>
                                    <div class="mt-1">
                                        <x-input type="number" wire:model="settings.max_login_attempts" class="w-full" min="1" max="10" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lockout Duration (minutes)</label>
                                    <div class="mt-1">
                                        <x-input type="number" wire:model="settings.lockout_duration" class="w-full" min="1" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Password Expiry (days)</label>
                                    <div class="mt-1">
                                        <x-input type="number" wire:model="settings.password_expires_days" class="w-full" min="0" />
                                        <p class="mt-1 text-sm text-gray-500">Set to 0 to disable password expiry</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Session Lifetime (minutes)</label>
                                    <div class="mt-1">
                                        <x-input type="number" wire:model="settings.session_lifetime" class="w-full" min="1" />
                                    </div>
                                </div>
                            </div>

                            <!-- Security Features -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">Require Two-Factor Authentication</label>
                                        <p class="text-sm text-gray-500">Enforce 2FA for all users</p>
                                    </div>
                                    <div class="ml-4">
                                        <x-toggle wire:model="settings.require_2fa" />
                                    </div>
                                </div>

                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">Enable reCAPTCHA</label>
                                        <p class="text-sm text-gray-500">Add reCAPTCHA protection to forms</p>
                                    </div>
                                    <div class="ml-4">
                                        <x-toggle wire:model="settings.enable_recaptcha" />
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload Security -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Allowed File Types</label>
                                    <div class="mt-1">
                                        <x-input type="text" wire:model="settings.allowed_file_types" class="w-full" placeholder="jpg,jpeg,png,pdf" />
                                        <p class="mt-1 text-sm text-gray-500">Comma-separated list of allowed file extensions</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Max File Size (MB)</label>
                                    <div class="mt-1">
                                        <x-input type="number" wire:model="settings.max_file_size" class="w-full" min="1" max="50" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div id="notifications" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Notification Settings</h3>
                                <p class="mt-1 text-sm text-gray-500">Configure how and when notifications are sent</p>
                            </div>
                            <span class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                <i class="fas fa-bell text-xl"></i>
                            </span>
                        </div>

                        <div class="space-y-6">
                            <!-- Notification Channels -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">Email Notifications</label>
                                        <p class="text-sm text-gray-500">Send notifications via email</p>
                                    </div>
                                    <div class="ml-4">
                                        <x-toggle wire:model="settings.enable_email_notifications" />
                                    </div>
                                </div>

                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">SMS Notifications</label>
                                        <p class="text-sm text-gray-500">Send notifications via SMS</p>
                                    </div>
                                    <div class="ml-4">
                                        <x-toggle wire:model="settings.enable_sms_notifications" />
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Events -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">Item Match Notifications</label>
                                        <p class="text-sm text-gray-500">Notify users when a potential match is found</p>
                                    </div>
                                    <div class="ml-4">
                                        <x-toggle wire:model="settings.notify_on_item_match" />
                                    </div>
                                </div>

                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">New Item Notifications</label>
                                        <p class="text-sm text-gray-500">Notify admins when new items are added</p>
                                    </div>
                                    <div class="ml-4">
                                        <x-toggle wire:model="settings.notify_admins_on_new_item" />
                                    </div>
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

    <!-- Add reset feedback handler -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('settings-reset', () => {
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Add visual feedback
                const content = document.querySelector('.settings-content');
                if (content) {
                    content.classList.add('animate-pulse');
                    setTimeout(() => {
                        content.classList.remove('animate-pulse');
                    }, 1000);
                }
            });
        });
    </script>
</div>
