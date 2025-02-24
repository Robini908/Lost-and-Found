<!-- filepath: /c:/my-projects/lost-found/resources/views/admin/manage-usertypes.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage User Types, Roles, and Permissions') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeComponent: 'manage-user-roles' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
                <div class="absolute top-0 right-0 mt-4 mr-4">
                    <x-dropdown-menu>
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="menu-button" aria-expanded="true" aria-haspopup="true" data-tippy-content="More Actions">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="items">
                            <a href="#" @click="activeComponent = 'manage-user-types'; open = false" class="text-gray-700 block px-4 py-2 text-sm flex items-center" role="menuitem" tabindex="-1" id="menu-item-0">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 3a1 1 0 011 1v1h2a1 1 0 110 2h-2v2a1 1 0 11-2 0V7H7a1 1 0 110-2h2V4a1 1 0 011-1z" />
                                </svg>
                                Manage User Types
                            </a>
                            <a href="#" @click="activeComponent = 'manage-permissions'; open = false" class="text-gray-700 block px-4 py-2 text-sm flex items-center" role="menuitem" tabindex="-1" id="menu-item-1">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 3a1 1 0 011 1v1h2a1 1 0 110 2h-2v2a1 1 0 11-2 0V7H7a1 1 0 110-2h2V4a1 1 0 011-1z" />
                                </svg>
                                Manage Permissions
                            </a>
                        </x-slot>
                    </x-dropdown-menu>
                </div>
                <div x-show="activeComponent === 'manage-user-roles'" id="manage-user-roles" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:manage-user-roles lazy/>
                </div>
                <div x-show="activeComponent === 'manage-user-types'" id="manage-user-types" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:manage-user-types lazy/>
                    <button @click="activeComponent = 'manage-user-roles'" class="mt-4 ml-4 px-4 py-2 bg-gray-300 text-gray-700 rounded-md">Back</button>
                </div>
                <div x-show="activeComponent === 'manage-permissions'" id="manage-permissions" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:manage-permissions lazy/>
                    <button @click="activeComponent = 'manage-user-roles'" class="mt-4 ml-4 px-4 py-2 bg-gray-300 text-gray-700 rounded-md">Back</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
