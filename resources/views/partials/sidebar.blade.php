<div wire:ignore x-data="{ open: false, settingsOpen: false, systemSettingsOpen: false }" class="relative">
    <!-- Sidebar Toggle Button -->
    <x-sidebar-toggle />

    <!-- Sidebar -->
    <div x-show="open" @click.away="open = false" class="fixed inset-0 flex z-40 transition-transform transform"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-200 h-100 overflow-y-auto">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="open = false"
                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg  " fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>
                <nav class="mt-5 px-2 space-y-1">
                    <x-sidebar-nav-item route="{{ route('dashboard') }}" icon="fas fa-bars" label="Dashboard" />
                    <x-sidebar-nav-item route="{{ route('products.report-item') }}" icon="fas fa-exclamation-circle"
                        label="Report Item" />
                    <x-sidebar-nav-item route="#" icon="fas fa-search" label="Lost Items" :sublinks="[
                        ['route' => route('products.view-items'), 'icon' => 'fas fa-eye', 'label' => 'View Lost'],
                        ['route' => route('products.my-reported-items'), 'icon' => 'fas fa-eye', 'label' => 'My reported Items'],
                    ]" />
                </nav>
            </div>

            <!-- Settings Section -->
            <div class="mt-auto px-2 pb-4 space-y-1">
                <x-sidebar-nav-item route="#" icon="fas fa-cog" label="Settings" :sublinks="[
                    ['route' => route('users.general-settings'), 'icon' => 'fas fa-cogs', 'label' => 'General Settings'],
                    ['route' => '#', 'icon' => 'fas fa-tools', 'label' => 'System Settings', 'sublinks' => [
                        ['route' => route('admin.manage-usertypes'), 'icon' => 'fas fa-users-cog', 'label' => 'Manage Users'],
                        ['route' => '#', 'icon' => 'fas fa-paint-brush', 'label' => 'Theme'],
                        ['route' => '#', 'icon' => 'fas fa-language', 'label' => 'Languages'],
                    ]],
                    ['route' => '#', 'icon' => 'fas fa-sliders-h', 'label' => 'Other Settings'],
                ]" />
            </div>
        </div>
    </div>
</div>
