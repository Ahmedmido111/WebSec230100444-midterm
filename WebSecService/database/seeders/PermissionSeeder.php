<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

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
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
                'display_name' => ucwords(str_replace('_', ' ', $permission))
            ]);
        }

        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Give all permissions to admin
        foreach (Permission::all() as $permission) {
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
            'add_customer_credit',
            'show_users'
        ]);

        // Give specific permissions to customer
        $customerRole->givePermissionTo([
            'view_products',
            'make_purchases',
            'view_purchase_history'
        ]);
    }
} 