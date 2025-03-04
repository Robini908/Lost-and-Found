<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Manage Users</h2>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->roles->first()?->name ?? 'No Role' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button wire:click="editUser({{ $user->id }})" class="bg-blue-500 hover:bg-blue-600">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit User Modal -->
    <x-dialog-modal wire:model="isEditing">
        <x-slot name="title">Edit User Role</x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="role" value="Role" />
                    <select wire:model="selectedRole" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('isEditing', false)" class="mr-2">
                Cancel
            </x-secondary-button>
            <x-button wire:click="updateUser">
                Update
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
