<!-- filepath: /c:/my-projects/lost-found/resources/views/users/general-settings.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('General Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="overflow-hidden shadow-xl sm:rounded-lg">
            <livewire:manage-profile-image />
        </div>
    </div>
</x-app-layout>