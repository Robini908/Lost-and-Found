<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class ManagePermissions extends Component
{
    public $permissions;
    public $permissionName;
    public $isEditing = false;
    public $isCreating = false;
    public $selectedPermission;

    protected $rules = [
        'permissionName' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->permissions = Permission::all();
    }

    public function createPermission()
    {
        $this->reset(['permissionName']);
        $this->isCreating = true;
        $this->isEditing = false;
    }

    public function editPermission($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);
        $this->selectedPermission = $permission;
        $this->permissionName = $permission->name;
        $this->isEditing = true;
        $this->isCreating = false;
    }

    public function savePermission()
    {
        $this->validate();

        if ($this->isEditing) {
            $permission = $this->selectedPermission;
            $permission->name = $this->permissionName;
            $permission->save();
        } else {
            Permission::create(['name' => $this->permissionName]);
        }

        $this->reset(['permissionName', 'isEditing', 'isCreating']);
        $this->permissions = Permission::all();
    }

    public function deletePermission($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);
        $permission->delete();
        $this->permissions = Permission::all();
    }

    public function cancel()
    {
        $this->reset(['permissionName', 'isEditing', 'isCreating']);
    }

    public function render()
    {
        return view('livewire.manage-permissions');
    }
}