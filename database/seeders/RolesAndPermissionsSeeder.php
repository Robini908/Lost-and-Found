<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view lost items',
            'create lost items',
            'edit lost items',
            'delete lost items',
            'manage users', // Additional permission for admin
            'manage roles', // Additional permission for admin
        ];

        // Create or update permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Define roles and their associated permissions
        $roles = [
            'user' => [
                'view lost items',
            ],
            'admin' => [
                'view lost items',
                'create lost items',
                'edit lost items',
                'delete lost items',
                'manage users',
                'manage roles',
            ],
            'moderator' => [ // Additional role for moderation
                'view lost items',
                'create lost items',
                'edit lost items',
            ],
            'superadmin' => Permission::all()->pluck('name')->toArray(), // Superadmin has all permissions
        ];

        // Create or update roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Sync permissions to the role
            $role->syncPermissions($rolePermissions);
        }

        // Create a superadmin user
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'], // Unique identifier
            [
                'name' => 'Super Admin',
                'password' => Hash::make(value: 'superadminpassword'), // Default password
            ]
        );

        // Assign the superadmin role to the user
        $superadmin->assignRole('superadmin');
    }
}
