<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Facades\RolePermission;
use Spatie\Permission\Models\Role;

class ManageUsers extends Component
{
    public $users;
    public $roles;
    public $selectedUser;
    public $selectedRole;
    public $isEditing = false;

    public function mount()
    {
        if (!RolePermission::canManageUsers(auth()->user())) {
            abort(403, 'Unauthorized action.');
        }

        $this->loadUsers();
        $this->roles = Role::all();
    }

    public function loadUsers()
    {
        $this->users = User::with('roles')->get();
    }

    public function editUser($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->selectedRole = $this->selectedUser->roles->first()?->name;
        $this->isEditing = true;
    }

    public function updateUser()
    {
        $this->validate([
            'selectedRole' => 'required|exists:roles,name'
        ]);

        $this->selectedUser->syncRoles([$this->selectedRole]);
        $this->isEditing = false;
        $this->loadUsers();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'User role updated successfully!'
        ]);
    }

    public function render()
    {
        return view('livewire.manage-users');
    }
}
