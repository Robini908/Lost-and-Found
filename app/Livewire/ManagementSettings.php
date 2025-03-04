<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Usernotnull\Toast\Concerns\WireToast;

class ManagementSettings extends Component
{
    use WireToast;

    public $activeTab = 'roles';
    public $totalUsers = 0;
    public $totalRoles = 0;
    public $totalPermissions = 0;

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'Unauthorized action.');
        }

        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        // Cache statistics for 5 minutes to improve performance
        $this->totalUsers = Cache::remember('total_users', now()->addMinutes(5), function () {
            return User::count();
        });

        $this->totalRoles = Cache::remember('total_roles', now()->addMinutes(5), function () {
            return Role::count();
        });

        $this->totalPermissions = Cache::remember('total_permissions', now()->addMinutes(5), function () {
            return Permission::count();
        });
    }

    public function refreshStatistics()
    {
        Cache::forget('total_users');
        Cache::forget('total_roles');
        Cache::forget('total_permissions');
        $this->loadStatistics();
    }

    // Listen for events that might change the statistics
    protected $listeners = [
        'userCreated' => 'refreshStatistics',
        'userDeleted' => 'refreshStatistics',
        'roleCreated' => 'refreshStatistics',
        'roleDeleted' => 'refreshStatistics',
        'permissionCreated' => 'refreshStatistics',
        'permissionDeleted' => 'refreshStatistics',
    ];

    public function render()
    {
        return view('livewire.management-settings');
    }
}
