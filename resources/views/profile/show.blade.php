<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-700">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="relative z-10">
                    <div class="flex items-center space-x-8">
                        <!-- Profile Photo/Avatar -->
                        <div class="shrink-0">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-xl"
                                     src="{{ Auth::user()->profile_photo_url }}"
                                     alt="{{ Auth::user()->name }}" />
                            @else
                                <div class="h-32 w-32 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500
                                            border-4 border-white shadow-xl flex items-center justify-center">
                                    <span class="text-4xl font-bold text-white">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- User Info -->
                        <div class="text-white">
                            <h2 class="text-3xl font-bold">{{ Auth::user()->name }}</h2>
                            <p class="text-blue-100 mt-1">{{ Auth::user()->email }}</p>
                            <div class="flex items-center mt-4 space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    {{ Auth::user()->roles->first()?->name ?? 'User' }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-clock mr-2"></i>
                                    Joined {{ Auth::user()->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Decorative pattern -->
            <div class="absolute inset-y-0 right-0 w-1/2 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 0L100 100V0H0Z" fill="currentColor"/>
                </svg>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Profile Information Section -->
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8">
                        @livewire('profile.update-profile-information-form')
                    </div>
                </div>

                <!-- Extended Profile Information -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mt-8">
                    <div class="p-8">
                        @livewire('profile.update-extended-profile-information')
                    </div>
                </div>
            @endif

            <!-- Password Update Section -->
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8">
                    @livewire('profile.update-password-form')
                    </div>
                </div>
            @endif

            <!-- Two Factor Authentication -->
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8">
                    @livewire('profile.two-factor-authentication-form')
                    </div>
                </div>
            @endif

            <!-- Browser Sessions -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8">
                @livewire('profile.logout-other-browser-sessions-form')
                </div>
            </div>

            <!-- Delete Account -->
            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8">
                    @livewire('profile.delete-user-form')
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Activity Log -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        <i class="fas fa-clock text-gray-400"></i>
                    </div>
                    <div class="space-y-4">
                        <!-- Add activity items here -->
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Statistics</h3>
                        <i class="fas fa-chart-bar text-gray-400"></i>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <span class="block text-2xl font-bold text-blue-600">0</span>
                            <span class="text-sm text-gray-500">Items Found</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-2xl font-bold text-blue-600">0</span>
                            <span class="text-sm text-gray-500">Items Reported</span>
                        </div>
                    </div>
                </div>

                <!-- Rewards -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Rewards</h3>
                        <i class="fas fa-gift text-gray-400"></i>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-blue-600">0</span>
                        <span class="text-sm text-gray-500">Points Earned</span>
                        <a href="{{ route('rewards') }}"
                           class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            View Rewards
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
