<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService
{
    /**
     * Check if a user has a specific role or permission.
     */
    public function userHasRole(User $user, string|array $roles): bool
    {
        return $user->hasRole($roles);
    }

    public function userHasPermission(User $user, string $permission): bool
    {
        return $user->can($permission);
    }

    /**
     * Check if a user has both role and permission.
     */
    public function userHasRoleAndPermission(User $user, string $role, string $permission): bool
    {
        return $user->hasRole($role) && $user->can($permission);
    }

    /**
     * Role-Permission mapping for content access control.
     */
    private function getRolePermissionsMap(): array
    {
        return [
            'lost_items' => [
                'admin' => 'view lost items',
                'moderator' => 'view lost items',
                'user' => 'view lost items',
                'guest' => 'view lost items',
            ],
            'sensitive_data' => [
                'admin' => 'manage sensitive data',
                'superadmin' => 'manage sensitive data',
            ],
            'reports' => [
                'admin' => 'view reports',
                'superadmin' => 'view reports',
                'analyst' => 'view reports',
            ],
            'user_management' => [
                'admin' => 'manage users',
                'superadmin' => 'manage users',
                'hr' => 'manage users',
            ],
            'financial_data' => [
                'admin' => 'view financial data',
                'superadmin' => 'view financial data',
                'accountant' => 'view financial data',
            ],
        ];
    }

    /**
     * Determine if the user can view a specific content type.
     */
    public function canViewContent(User $user, string $contentType): bool
    {
        $rolePermissionsMap = $this->getRolePermissionsMap();

        if (!isset($rolePermissionsMap[$contentType])) {
            return false; // Undefined content type
        }

        foreach ($rolePermissionsMap[$contentType] as $role => $permission) {
            if ($this->userHasRoleAndPermission($user, $role, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter content for the user based on role-permission mapping.
     */
    public function getFilteredContent(User $user, array $content, string $contentType): array
    {
        if (!$this->canViewContent($user, $contentType)) {
            return []; // Restrict content access
        }

        return array_filter($content, function ($item) use ($user, $contentType) {
            $rolePermissionsMap = $this->getRolePermissionsMap();
            foreach ($rolePermissionsMap[$contentType] as $role => $permission) {
                if ($this->userHasRoleAndPermission($user, $role, $permission)) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Check if the user is part of the management team.
     */
    public function teamIsManagement(User $user): bool
    {
        $managementRoles = ['admin', 'superadmin', 'moderator'];
        return $this->userHasRole($user, $managementRoles);
    }
}
