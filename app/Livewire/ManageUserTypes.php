<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Facades\RolePermission;

class ManageUserTypes extends Component
{
    public $roles;
    public $permissions;
    public $roleName;
    public $rolePermissions = [];
    public $isEditing = false;
    public $isCreating = false;
    public $selectedRole;

    protected $rules = [
        'roleName' => 'required|string|max:255',
        'rolePermissions' => 'array',
    ];

    public function mount()
    {
        if (!RolePermission::canManageContent(auth()->user(), 'roles', 'view')) {
            abort(403, 'Unauthorized action.');
        }

        $this->roles = Role::all();
        $this->permissions = Permission::all();
    }

    public function createRole()
    {
        $this->reset(['roleName', 'rolePermissions']);
        $this->isCreating = true;
        $this->isEditing = false;
    }

    public function editRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $this->selectedRole = $role;
        $this->roleName = $role->name;
        $this->rolePermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
        $this->isCreating = false;
    }

    public function saveRole()
    {
        $this->validate();

        if ($this->isEditing) {
            $role = $this->selectedRole;
            $role->name = $this->roleName;
            $role->syncPermissions($this->rolePermissions);
        } else {
            $role = Role::create(['name' => $this->roleName]);
            $role->syncPermissions($this->rolePermissions);
        }

        $this->reset(['roleName', 'rolePermissions', 'isEditing', 'isCreating']);
        $this->roles = Role::all();
    }

    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);

        // Prevent deletion of higher priority roles
        if (!RolePermission::hasHigherOrEqualPriority(
            RolePermission::getHighestRole(auth()->user()),
            $role->name
        )) {
            $this->addError('role', 'You cannot delete a higher-ranking role.');
            return;
        }

        $role->delete();
        $this->roles = Role::all();
    }

    public function cancel()
    {
        $this->reset(['roleName', 'rolePermissions', 'isEditing', 'isCreating']);
    }

    public function render()
    {
        return view('livewire.manage-user-types');
    }
}
