<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class RolePermissionService
{
    protected $cacheDuration = 3600; // 1 hour

    /**
     * Get all roles with their permissions
     */
    public function getAllRoles(): Collection
    {
        return Cache::remember('all_roles_with_permissions', $this->cacheDuration, function () {
            return Role::with('permissions')->get();
        });
    }

    /**
     * Get role hierarchy (ordered by priority)
     */
    public function getRoleHierarchy(): array
    {
        return [
            'superadmin' => 1,
            'admin' => 2,
            'moderator' => 3,
            'user' => 4,
        ];
    }

    /**
     * Check if a role has higher or equal priority
     */
    public function hasHigherOrEqualPriority(string $roleA, string $roleB): bool
    {
        $hierarchy = $this->getRoleHierarchy();
        return ($hierarchy[$roleA] ?? 999) <= ($hierarchy[$roleB] ?? 999);
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions(): Collection
    {
        return Cache::remember('all_permissions', $this->cacheDuration, function () {
            return Permission::all();
        });
    }

    /**
     * Get permissions by role
     */
    public function getPermissionsByRole(string $roleName): Collection
    {
        return Cache::remember("role_permissions_{$roleName}", $this->cacheDuration, function () use ($roleName) {
            return Role::findByName($roleName)->permissions;
        });
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($user, array $permissions): bool
    {
        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions($user, array $permissions): bool
    {
        return $user->hasAllPermissions($permissions);
    }

    /**
     * Get highest role for a user
     */
    public function getHighestRole($user): ?string
    {
        $hierarchy = $this->getRoleHierarchy();
        $userRoles = $user->roles->pluck('name');

        return $userRoles->sort(function ($a, $b) use ($hierarchy) {
            return ($hierarchy[$a] ?? 999) - ($hierarchy[$b] ?? 999);
        })->first();
    }

    /**
     * Check if user can manage specific content
     */
    public function canManageContent($user, $content, string $action = 'view'): bool
    {
        $highestRole = $this->getHighestRole($user);

        // Superadmin can do everything
        if ($highestRole === 'superadmin') {
            return true;
        }

        // Check specific permissions based on content type and action
        $permissionMap = [
            'lost_items' => [
                'view' => 'view lost items',
                'create' => 'create lost items',
                'edit' => 'edit lost items',
                'delete' => 'delete lost items',
            ],
            'users' => [
                'view' => 'manage users',
                'edit' => 'manage users',
            ],
            'roles' => [
                'view' => 'manage roles',
                'edit' => 'manage roles',
            ],
        ];

        $contentType = $content instanceof \App\Models\LostItem ? 'lost_items' :
                      (is_string($content) ? $content : get_class($content));

        $requiredPermission = $permissionMap[$contentType][$action] ?? null;

        return $requiredPermission ? $user->hasPermissionTo($requiredPermission) : false;
    }

    /**
     * Clear role and permission cache
     */
    public function clearCache(): void
    {
        Cache::forget('all_roles_with_permissions');
        Cache::forget('all_permissions');

        foreach (array_keys($this->getRoleHierarchy()) as $role) {
            Cache::forget("role_permissions_{$role}");
        }
    }

    /**
     * Role checks
     */
    public function isSuperAdmin($user): bool
    {
        return $user && $user->hasRole('superadmin');
    }

    public function isAdmin($user): bool
    {
        return $user && $user->hasRole('admin');
    }

    public function isModerator($user): bool
    {
        return $user && $user->hasRole('moderator');
    }

    public function isUser($user): bool
    {
        return $user && $user->hasRole('user');
    }

    public function isAtLeastModerator($user): bool
    {
        return $user && ($this->isSuperAdmin($user) || $this->isAdmin($user) || $this->isModerator($user));
    }

    public function isAtLeastAdmin($user): bool
    {
        return $user && ($this->isSuperAdmin($user) || $this->isAdmin($user));
    }

    /**
     * Feature access control
     */
    public function canAccessAdminPanel($user): bool
    {
        return $this->isAtLeastModerator($user);
    }

    public function canManageUsers($user): bool
    {
        return $this->isAtLeastAdmin($user);
    }

    public function canManageRoles($user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function canViewAnalytics($user): bool
    {
        return $this->isAtLeastModerator($user);
    }

    public function canDeleteItems($user): bool
    {
        return $this->isAtLeastModerator($user);
    }

    public function canVerifyClaims($user): bool
    {
        return $this->isAtLeastModerator($user);
    }

    public function canManageSettings($user): bool
    {
        return $this->isAtLeastAdmin($user);
    }

    /**
     * Content visibility filters
     */
    public function getVisibleMenuItems($user): array
    {
        $items = [
            'dashboard' => [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'fas fa-home',
                'visible' => true
            ],
            'report_lost' => [
                'name' => 'Report Lost Item',
                'route' => 'products.report-item',
                'icon' => 'fas fa-exclamation-circle',
                'visible' => true
            ],
            'report_found' => [
                'name' => 'Report Found Item',
                'route' => 'products.report-found-item',
                'icon' => 'fas fa-search',
                'visible' => true
            ],
            'my_items' => [
                'name' => 'My Items',
                'route' => 'products.my-reported-items',
                'icon' => 'fas fa-list',
                'visible' => true
            ],
            'all_items' => [
                'name' => 'All Items',
                'route' => 'products.view-items',
                'icon' => 'fas fa-box',
                'visible' => $this->isAtLeastModerator($user)
            ],
            'claims' => [
                'name' => 'Verify Claims',
                'route' => 'claims.verify',
                'icon' => 'fas fa-check-circle',
                'visible' => $this->canVerifyClaims($user)
            ],
            'users' => [
                'name' => 'Manage Users',
                'route' => 'admin.manage-users',
                'icon' => 'fas fa-users',
                'visible' => $this->canManageUsers($user)
            ],
            'roles' => [
                'name' => 'Manage Roles',
                'route' => 'roles.index',
                'icon' => 'fas fa-user-shield',
                'visible' => $this->canManageRoles($user)
            ],
            'analytics' => [
                'name' => 'Analytics',
                'route' => 'analytics',
                'icon' => 'fas fa-chart-bar',
                'visible' => $this->canViewAnalytics($user)
            ],
            'settings' => [
                'name' => 'Settings',
                'route' => 'settings',
                'icon' => 'fas fa-cog',
                'visible' => $this->canManageSettings($user)
            ]
        ];

        return array_filter($items, fn($item) => $item['visible']);
    }

    /**
     * Item visibility and actions
     */
    public function canViewItemDetails($user, $item): bool
    {
        // Allow all authenticated users to view items
        return true;
    }

    public function canEditItem($user, $item): bool
    {
        return $this->isAtLeastModerator($user) || $item->user_id === $user->id;
    }

    public function canDeleteItem($user, $item): bool
    {
        return $this->isAtLeastAdmin($user) || $item->user_id === $user->id;
    }
}
