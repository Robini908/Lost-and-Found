<div class="container mx-auto px-2 py-2">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Manage User Roles</h2>
    </div>

    @if ($isEditing)
        <div>
            <h2 class="text-xl font-semibold mb-4">Edit User Roles</h2>
            <form wire:submit.prevent="saveUserRoles">
                <div class="mb-4">
                    <x-label for="userName" value="User Name" class="block text-sm font-medium text-gray-700" />
                    <x-input type="text" id="userName" value="{{ $selectedUser->name }}" class="mt-1 block w-full"
                        disabled />
                </div>
                <div class="mb-4">
                    <x-label for="userRoles" value="Roles" class="block text-sm font-medium text-gray-700" />
                    <div class="mt-1 grid grid-cols-2 gap-4">
                        @foreach ($roles as $role)
                            <div class="flex items-center">
                                <x-radio id="role-{{ $role->id }}" name="userRoles" value="{{ $role->name }}"
                                    label="{{ $role->name }}" :checked="$userRoles == $role->name" wire:model="userRoles" />
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <x-button type="button" wire:click="cancel">Cancel</x-button>
                    <x-button type="submit">Save</x-button>
                </div>
            </form>
        </div>
    @else
        <div>
            <h2 class="text-xl font-semibold mb-4">Users</h2>
            <x-table  id="usersTable" class="display" style="width:100%">
                <x-slot name="head">
                    <x-table.heading>User</x-table.heading>
                    <x-table.heading>Roles</x-table.heading>
                    <x-table.heading class="text-right">Actions</x-table.heading>
                </x-slot>
                <x-slot name="body">
                    @foreach ($users as $user)
                        <x-table.row>
                            <x-table.cell>{{ $user->name }}</x-table.cell>
                            <x-table.cell>{{ implode(', ', $user->roles->pluck('name')->toArray()) }}</x-table.cell>
                            <x-table.cell class="text-right">
                                <x-button wire:click="editUserRoles({{ $user->id }})"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    data-tippy-content="Edit User">
                                    <i class="fas fa-edit"></i>
                                </x-button>
                                <x-button wire:click="deleteUser({{ $user->id }})"
                                    class="text-red-600 hover:text-red-900 ml-2"
                                    data-tippy-content="Delete User">
                                    <i class="fas fa-trash"></i>
                                </x-button>
                            </x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-slot>
            </x-table>
        </div>
    @endif
</div>
