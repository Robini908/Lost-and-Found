<!-- filepath: /c:/my-projects/lost-found/resources/views/livewire/manage-permissions.blade.php -->
<div class="container mx-auto px-2 py-2">
    <div class="flex justify-between items-center mb-4">
        @if (!$isCreating && !$isEditing)
            <x-button wire:click="createPermission">
                <i class="fas fa-plus"></i> New Permission
            </x-button>
        @endif
    </div>

    @if ($isCreating || $isEditing)
        <div>
            <h2 class="text-xl font-semibold mb-4">{{ $isEditing ? 'Edit Permission' : 'Create Permission' }}</h2>
            <form wire:submit.prevent="savePermission">
                <div class="mb-4">
                    <x-label for="permissionName" class="block text-sm font-medium text-gray-700">Permission Name</x-label>
                    <x-input type="text" id="permissionName" wire:model="permissionName" class="mt-1 block w-full" />
                    <x-input-error for="permissionName" />
                </div>
                <div class="flex justify-end space-x-2">
                    <x-button type="button" wire:click="cancel">Cancel</x-button>
                    <x-button type="submit">{{ $isEditing ? 'Update' : 'Create' }}</x-button>
                </div>
            </form>
        </div>
    @endif

    @if (!$isCreating && !$isEditing)
        <div>
            <h2 class="text-xl font-semibold mb-4">Permissions</h2>
            <x-table>
                <x-slot name="head">
                    <x-table.heading>Permission</x-table.heading>
                    <x-table.heading class="text-right">Actions</x-table.heading>
                </x-slot>
                <x-slot name="body">
                    @foreach ($permissions as $permission)
                        <x-table.row>
                            <x-table.cell>{{ $permission->name }}</x-table.cell>
                            <x-table.cell class="text-right">
                                <x-button wire:click="editPermission({{ $permission->id }})" class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-edit"></i></x-button>
                                <x-button wire:click="deletePermission({{ $permission->id }})" class="text-red-600 hover:text-red-900 ml-2"><i class="fas fa-trash"></i></x-button>
                            </x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-slot>
            </x-table>
        </div>
    @endif
</div>