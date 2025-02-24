<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ManageUserRoles extends Component
{
    public $users;
    public $roles;
    public $selectedUser;
    public $userRoles = [];
    public $isEditing = false;

    public function mount()
    {
        // Fetch roles from the database dynamically and map them to their priority values
        $roles = Role::all();
        $rolePriority = $roles->mapWithKeys(function ($role, $index) {
            return [$role->name => $index];
        });

        // Retrieve all users with their roles
        $this->users = User::with('roles')
            ->get()
            ->sortByDesc(function ($user) use ($rolePriority) {
                // Get the highest role's priority for the user
                $highestRolePriority = $user->roles->pluck('name')
                    ->map(function ($role) use ($rolePriority) {
                        return $rolePriority[$role] ?? PHP_INT_MAX; // If no priority, give a very high value
                    })
                    ->min(); // Get the minimum priority for the user (higher role = smaller number)

                return $highestRolePriority;
            });

        // Retrieve all roles
        $this->roles = $roles;
    }

    public function editUserRoles($userId)
    {
        $user = User::findOrFail($userId);
        $this->selectedUser = $user;
        $this->userRoles = $user->roles->pluck('name')->first(); // Use first role for radio button
        $this->isEditing = true;
    }

    public function saveUserRoles()
    {
        $user = $this->selectedUser;
        $user->syncRoles([$this->userRoles]); // Sync with a single role

        $this->reset(['selectedUser', 'userRoles', 'isEditing']);
        $this->users = User::all();
    }

    public function cancel()
    {
        $this->reset(['selectedUser', 'userRoles', 'isEditing']);
    }

    public function render()
    {
        return view('livewire.manage-user-roles');
    }
}
