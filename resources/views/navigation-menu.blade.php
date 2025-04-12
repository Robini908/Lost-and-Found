<!-- filepath: /c:/my-projects/lost-found/resources/views/navigation-menu.blade.php -->
<nav x-data="{ open: false, searchOpen: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" data-tippy-content="Dashboard">
                        <i class="fas fa-home"></i>
                    </x-nav-link>

                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out" data-tippy-content="Items">
                                    <i class="fas fa-box-open"></i>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('products.report-item', ['mode' => 'lost']) }}">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ __('Report Lost Item') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('products.report-item', ['mode' => 'found']) }}">
                                    <i class="fas fa-search mr-2"></i>
                                    {{ __('Report Found Item') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('products.view-items') }}">
                                    <i class="fas fa-eye mr-2"></i>
                                    {{ __('View All Items') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('products.my-items') }}">
                                    <i class="fas fa-list mr-2"></i>
                                    {{ __('My Reported Items') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('match-items') }}">
                                    <i class="fas fa-exchange-alt mr-2"></i>
                                    {{ __('Match Items') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <x-nav-link href="{{ route('rewards') }}" :active="request()->routeIs('rewards')" data-tippy-content="Rewards">
                        <i class="fas fa-gift"></i>
                    </x-nav-link>

                    @if(app('role-permission')->isAtLeastAdmin(auth()->user()))
                        <x-nav-link href="{{ route('admin.manage-users') }}" :active="request()->routeIs('admin.manage-users')" data-tippy-content="Manage Users">
                            <i class="fas fa-users"></i>
                        </x-nav-link>

                        <x-nav-link href="{{ route('admin.items') }}" :active="request()->routeIs('admin.items')" data-tippy-content="Manage Items">
                            <i class="fas fa-box"></i>
                        </x-nav-link>

                        <x-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')" data-tippy-content="Settings">
                            <i class="fas fa-cog"></i>
                        </x-nav-link>

                        <x-nav-link href="{{ route('analytics') }}" :active="request()->routeIs('analytics')" data-tippy-content="Analytics">
                            <i class="fas fa-chart-bar"></i>
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                <!-- Language Selector -->
                <livewire:language-switcher />

                <!-- Search Icon -->
                <button @click="searchOpen = true" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-search text-lg"></i>
                </button>

                {{-- @if(app('role-permission')->isSuperAdmin(auth()->user()))
                    <livewire:impersonate-user />
                @endif --}}

                <!-- Settings Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-center text-white font-medium">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                    </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                <i class="fas fa-user mr-2"></i>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if(app('role-permission')->isAtLeastAdmin(auth()->user()))
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    <i class="fas fa-key mr-2"></i>
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    <div x-show="searchOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="searchOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block w-full max-w-2xl my-8 align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Search</h3>
                        <button @click="searchOpen = false" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <livewire:global-search />
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <i class="fas fa-home mr-2"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Items Dropdown -->
            <x-responsive-nav-link href="{{ route('products.report-item', ['mode' => 'lost']) }}" :active="request()->routeIs('products.report-item') && request()->mode === 'lost'">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ __('Report Lost Item') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('products.report-item', ['mode' => 'found']) }}" :active="request()->routeIs('products.report-item') && request()->mode === 'found'">
                <i class="fas fa-search mr-2"></i>
                {{ __('Report Found Item') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('products.view-items') }}" :active="request()->routeIs('products.view-items')">
                <i class="fas fa-eye mr-2"></i>
                {{ __('View All Items') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('products.my-items') }}" :active="request()->routeIs('products.my-items')">
                <i class="fas fa-list mr-2"></i>
                {{ __('My Reported Items') }}
            </x-responsive-nav-link>

            @if(app('role-permission')->isAtLeastAdmin(auth()->user()))
                <x-responsive-nav-link href="{{ route('admin.manage-users') }}" :active="request()->routeIs('admin.manage-users')">
                    <i class="fas fa-users mr-2"></i>
                    {{ __('Manage Users') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('admin.items') }}" :active="request()->routeIs('admin.items')">
                    <i class="fas fa-box mr-2"></i>
                    {{ __('Manage Items') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                    <i class="fas fa-cog mr-2"></i>
                    {{ __('Settings') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('analytics') }}" :active="request()->routeIs('analytics')">
                    <i class="fas fa-chart-bar mr-2"></i>
                    {{ __('Analytics') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @else
                    <div class="shrink-0 mr-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-center text-white font-medium">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}">
                    <i class="fas fa-user mr-2"></i>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if(app('role-permission')->isAtLeastAdmin(auth()->user()))
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}">
                        <i class="fas fa-key mr-2"></i>
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
