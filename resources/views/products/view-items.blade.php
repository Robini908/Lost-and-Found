<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lost Items') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="overflow-hidden shadow-xl sm:rounded-lg">
            <livewire:display-lost-items lazy />
        </div>
    </div>

    {{-- @foreach($items as $item)
        <div class="item-card">
            <h3>{{ $item->name }}</h3>
            <p>{{ $item->description }}</p>

            <div class="actions">
                @if(app('role-permission')->canEditItem(auth()->user(), $item))
                    <x-button wire:click="editItem({{ $item->id }})">
                        <i class="fas fa-edit"></i> Edit
                    </x-button>
                @endif

                @if(app('role-permission')->canDeleteItem(auth()->user(), $item))
                    <x-button wire:click="deleteItem({{ $item->id }})" class="text-red-600">
                        <i class="fas fa-trash"></i> Delete
                    </x-button>
                @endif
            </div>
        </div>
    @endforeach --}}

</x-app-layout>
