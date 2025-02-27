<!-- Modern Collapsible Sidebar -->
<div x-data="{
    open: false,
    activeMenu: null,
    init() {
        this.$watch('open', value => {
            if (value) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        });
    }
}" class="relative" @keydown.escape="open = false">

    <!-- Floating Toggle Button -->
    <button @click="open = !open"
        class="fixed top-20 left-4 z-50 p-3 rounded-full bg-white/90 shadow-lg hover:shadow-xl hover:bg-white transition-all duration-300">
        <i class="fas" :class="open ? 'fa-times' : 'fa-bars'"></i>
    </button>

    <!-- Sidebar -->
    <nav x-show="open"
        x-transition:enter="transition-transform duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        @click.away="open = false"
        class="fixed top-0 left-0 z-40 h-screen w-72 bg-gradient-to-br from-blue-100 to-gray-100 shadow-2xl flex flex-col">

        <!-- Header -->
        <div class="flex-none">
            <!-- Logo Section -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200/50">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <x-application-mark class="block h-9 w-auto" />
                    <span class="text-xl font-bold text-gray-700">Lost & Found</span>
                </a>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- User Profile Section -->
            <div class="p-4 border-b border-gray-200/50 bg-white/50">
                <div class="flex items-center space-x-3">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="h-10 w-10 rounded-full object-cover border-2 border-blue-200"
                             src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ Auth::user()->name }}" />
                    @else
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center border-2 border-blue-200">
                            <span class="text-lg text-blue-600">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</h3>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable Navigation -->
        <div class="flex-1 overflow-y-auto py-4 px-3">
            <!-- Main Navigation -->
            <div class="space-y-2">
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                    <i class="fas fa-home text-lg text-blue-200"></i>
                    <span>Dashboard</span>
                </x-nav-link>

                <!-- Report Items Section -->
                <div class="space-y-1">
                    <button @click="activeMenu = (activeMenu === 'report') ? null : 'report'"
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-600 hover:bg-white/60 hover:text-gray-700 rounded-lg transition-colors duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-plus-circle text-lg text-green-200"></i>
                            <span>Report Items</span>
                        </div>
                        <i class="fas" :class="activeMenu === 'report' ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                    </button>
                    <div x-show="activeMenu === 'report'"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="pl-11 space-y-1">
                        <x-nav-link href="{{ route('products.report-item') }}" :active="request()->routeIs('products.report-item')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('products.report-item') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-exclamation-circle text-red-200"></i>
                            <span>Report Lost</span>
                        </x-nav-link>
                        <x-nav-link href="{{ route('products.report-found-item') }}" :active="request()->routeIs('products.report-found-item')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('products.report-found-item') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-search text-green-200"></i>
                            <span>Report Found</span>
                        </x-nav-link>
                    </div>
                </div>

                <!-- View Items Section -->
                <div class="space-y-1">
                    <button @click="activeMenu = (activeMenu === 'view') ? null : 'view'"
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-600 hover:bg-white/60 hover:text-gray-700 rounded-lg transition-colors duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-list text-lg text-purple-200"></i>
                            <span>View Items</span>
                            <livewire:notification-counter />
                        </div>
                        <i class="fas" :class="activeMenu === 'view' ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                    </button>
                    <div x-show="activeMenu === 'view'"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="pl-11 space-y-1">
                        <x-nav-link href="{{ route('products.view-items') }}" :active="request()->routeIs('products.view-items')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('products.view-items') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-eye text-blue-200"></i>
                            <span>All Items</span>
                        </x-nav-link>
                        <x-nav-link href="{{ route('products.my-reported-items') }}" :active="request()->routeIs('products.my-reported-items')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('products.my-reported-items') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-user-tag text-yellow-200"></i>
                            <span>My Items</span>
                        </x-nav-link>
                        <x-nav-link href="{{ route('match-items') }}" :active="request()->routeIs('match-items')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('match-items') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-exchange-alt text-green-200"></i>
                            <span>Match Items</span>
                        </x-nav-link>
                        <x-nav-link href="{{ route('matched-items') }}" :active="request()->routeIs('matched-items')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('matched-items') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-check-circle text-green-200"></i>
                            <span>Matched Items</span>
                        </x-nav-link>
                    </div>
                </div>

                <!-- Rewards Section -->
                <x-nav-link href="{{ route('rewards.index') }}" :active="request()->routeIs('rewards.index')"
                    class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('rewards.index') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-gift text-lg text-yellow-200"></i>
                        <span>Rewards</span>
                    </div>
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-600 rounded-full">New</span>
                </x-nav-link>
            </div>
        </div>

        <!-- Settings Section (Fixed at bottom) -->
        <div class="flex-none border-t border-gray-200/50">
            <div class="p-4 space-y-1">
                <!-- Settings Button -->
                <button @click="activeMenu = (activeMenu === 'settings') ? null : 'settings'"
                    class="w-full flex items-center justify-between px-4 py-3 text-gray-600 hover:bg-white/60 hover:text-gray-700 rounded-lg transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-cog text-lg text-blue-200"></i>
                        <span>Settings</span>
                    </div>
                    <i class="fas" :class="activeMenu === 'settings' ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>
                <!-- Settings Submenu -->
                <div x-show="activeMenu === 'settings'"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    class="pl-11 space-y-1">
                    <x-nav-link href="{{ route('users.general-settings') }}" :active="request()->routeIs('users.general-settings')"
                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('users.general-settings') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                        <i class="fas fa-user-cog text-blue-200"></i>
                        <span>Account Settings</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')"
                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.show') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                        <i class="fas fa-user-circle text-blue-200"></i>
                        <span>Profile</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('users.general-settings') }}#notifications" :active="request()->is('*/notifications')"
                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->is('*/notifications') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                        <i class="fas fa-bell text-blue-200"></i>
                        <span>Notifications</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('users.general-settings') }}#security" :active="request()->is('*/security')"
                        class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->is('*/security') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                        <i class="fas fa-shield-alt text-blue-200"></i>
                        <span>Security</span>
                    </x-nav-link>
                    @if(Auth::user()->hasRole('admin'))
                        <x-nav-link href="{{ route('admin.manage-usertypes') }}" :active="request()->routeIs('admin.manage-usertypes')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.manage-usertypes') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-users-cog text-purple-200"></i>
                            <span>User Management</span>
                        </x-nav-link>
                        <x-nav-link href="{{ route('users.general-settings') }}#system" :active="request()->is('*/system')"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->is('*/system') ? 'bg-white/60 text-gray-700' : 'text-gray-600 hover:bg-white/60 hover:text-gray-700' }}">
                            <i class="fas fa-server text-purple-200"></i>
                            <span>System Settings</span>
                        </x-nav-link>
                    @endif
            </div>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors duration-200">
                        <i class="fas fa-sign-out-alt text-lg text-red-200"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Backdrop -->
    <div x-show="open"
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-black/20 z-30">
    </div>
</div>
