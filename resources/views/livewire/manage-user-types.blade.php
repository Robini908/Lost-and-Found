<!-- filepath: /c:/my-projects/lost-found/resources/views/livewire/manage-user-types.blade.php -->
<div class="container mx-auto px-2 py-2" x-data="{ isCreating: @entangle('isCreating'), isEditing: @entangle('isEditing') }">
    <div class="flex justify-between items-center mb-4">
        <!-- New Role Button (Visible when neither creating nor editing) -->
        <template x-if="!isCreating && !isEditing">
            <x-button wire:click="createRole">
                <i class="fas fa-plus"></i> New Role
            </x-button>
        </template>
    </div>

    <!-- Role Creation or Editing Form (Visible when creating or editing) -->
    <template x-if="isCreating || isEditing">
        <div>
            <h2 class="text-xl font-semibold mb-4" x-text="isEditing ? 'Edit Role' : 'Create Role'"></h2>
            <form wire:submit.prevent="saveRole">
                <div class="mb-4">
                    <x-label for="roleName" class="block text-sm font-medium text-gray-700">Role Name</x-label>
                    <x-input type="text" id="roleName" wire:model="roleName" class="mt-1 block w-full" />
                    <x-input-error for="roleName" />
                </div>
                <div class="mb-4">
                    <x-label for="rolePermissions" class="block text-sm font-medium text-gray-700">Permissions</x-label>
                    <div class="mt-1 grid grid-cols-2 gap-4">
                        @foreach ($permissions as $permission)
                            <div class="flex items-center">
                                <x-checkbox id="permission-{{ $permission->id }}" wire:model="rolePermissions" value="{{ $permission->name }}" />
                                <x-label for="permission-{{ $permission->id }}">{{ $permission->name }}</x-label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <!-- Cancel Button (Closes the form without saving) -->
                    <x-button type="button" wire:click="cancel" >Cancel</x-button>
                    <!-- Save/Update Button -->
                    <x-button type="submit">{{ $isEditing ? 'Update' : 'Create' }}</x-button>
                </div>
            </form>
        </div>
    </template>

    <!-- Roles List (Visible when not creating or editing) -->
    <template x-if="!isCreating && !isEditing">
        <div>
            <h2 class="text-xl font-semibold mb-4">Roles</h2>
            <x-table>
                <x-slot name="head">
                    <x-table.heading>Role</x-table.heading>
                    <x-table.heading>Permissions</x-table.heading>
                    <x-table.heading class="text-right">Actions</x-table.heading>
                </x-slot>
                <x-slot name="body">
                    @foreach ($roles as $role)
                        <x-table.row>
                            <x-table.cell>{{ $role->name }}</x-table.cell>
                            <x-table.cell>{{ implode(', ', $role->permissions->pluck('name')->toArray()) }}</x-table.cell>
                            <x-table.cell class="text-right">
                                @if(RolePermission::canManageContent(auth()->user(), 'roles', 'edit'))
                                    <x-button wire:click="editRole({{ $role->id }})" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </x-button>
                                @endif
                                @if(RolePermission::canManageContent(auth()->user(), 'roles', 'delete'))
                                    <x-button wire:click="deleteRole({{ $role->id }})" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-trash"></i>
                                    </x-button>
                                @endif
                            </x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-slot>
            </x-table>
        </div>
    </template>
</div>
