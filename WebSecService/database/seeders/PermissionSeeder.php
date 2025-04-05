<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'create_products',
            'edit_products',
            'delete_products',
            'view_products',
            'manage_customers',
            'view_customers',
            'add_customer_credit',
            'create_employees',
            'edit_users',
            'delete_users',
            'view_users',
            'show_users',
            'admin_users',
            'make_purchases',
            'view_purchase_history',
            'manage_roles',
            'manage_permissions'
        ];

        // Create each permission
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $employeeRole = Role::where('name', 'employee')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // Give all permissions to admin
        foreach ($permissions as $permission) {
            $adminRole->givePermissionTo($permission);
        }

        // Give specific permissions to employee
        $employeeRole->givePermissionTo([
            'view_products',
            'edit_products',
            'create_products',
            'delete_products',
            'manage_customers',
            'view_customers',
            'add_customer_credit'
        ]);

        // Give specific permissions to customer
        $customerRole->givePermissionTo([
            'view_products',
            'make_purchases',
            'view_purchase_history'
        ]);
    }
} 