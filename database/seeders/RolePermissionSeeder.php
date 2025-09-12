<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',
            
            // Order management
            'view orders',
            'edit orders',
            'manage orders',
            'assign designers',
            'view order notes',
            'add order notes',
            
            // Service management
            'view services',
            'create services',
            'edit services',
            'delete services',
            'manage services',
            
            // Quote management
            'view quotes',
            'create quotes',
            'edit quotes',
            'delete quotes',
            'manage quotes',
            'approve quotes',
            'reject quotes',
            
            // Invoice management
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'manage invoices',
            'send invoices',
            
            // Reporting
            'view reports',
            'export reports',
            
            // System
            'access admin panel',
            'impersonate users',
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $designerRole = Role::firstOrCreate(['name' => 'designer']);
        $designerRole->syncPermissions([
            'view orders',
            'edit orders',
            'view order notes',
            'add order notes',
            'view quotes',
            'edit quotes',
            'view services',
            'view reports',
        ]);

        $supportRole = Role::firstOrCreate(['name' => 'support']);
        $supportRole->syncPermissions([
            'view users',
            'view orders',
            'edit orders',
            'view order notes',
            'add order notes',
            'view quotes',
            'edit quotes',
            'view invoices',
            'view reports',
            'access admin panel',
        ]);
    }
}
