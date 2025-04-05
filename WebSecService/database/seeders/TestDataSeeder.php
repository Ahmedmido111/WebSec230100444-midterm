<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'credit' => 0
        ]);
        $admin->assignRole('admin');

        // Create employee user
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'credit' => 0
        ]);
        $employee->assignRole('employee');

        // Create customer users
        for ($i = 1; $i <= 3; $i++) {
            $customer = User::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'credit' => 1000
            ]);
            $customer->assignRole('customer');
        }

        // Create products
        $products = [
            [
                'code' => 'LAP001',
                'name' => 'Laptop Pro',
                'price' => 999.99,
                'model' => '2024',
                'description' => 'High-performance laptop with latest specs',
                'stock' => 10
            ],
            [
                'code' => 'PHN001',
                'name' => 'SmartPhone X',
                'price' => 699.99,
                'model' => '2024',
                'description' => 'Latest smartphone with advanced features',
                'stock' => 15
            ],
            [
                'code' => 'TAB001',
                'name' => 'Tablet Ultra',
                'price' => 499.99,
                'model' => '2024',
                'description' => 'Premium tablet for professionals',
                'stock' => 8
            ],
            [
                'code' => 'ACC001',
                'name' => 'Wireless Earbuds',
                'price' => 149.99,
                'model' => '2024',
                'description' => 'High-quality wireless earbuds',
                'stock' => 20
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create some sample purchases
        $customer1 = User::where('email', 'customer1@example.com')->first();
        $customer2 = User::where('email', 'customer2@example.com')->first();
        $laptop = Product::where('code', 'LAP001')->first();
        $phone = Product::where('code', 'PHN001')->first();
        $tablet = Product::where('code', 'TAB001')->first();

        // Customer 1 purchases
        Purchase::create([
            'user_id' => $customer1->id,
            'product_id' => $laptop->id,
            'price_at_purchase' => $laptop->price,
            'purchase_date' => now()->subDays(5)
        ]);

        Purchase::create([
            'user_id' => $customer1->id,
            'product_id' => $phone->id,
            'price_at_purchase' => $phone->price,
            'purchase_date' => now()->subDays(2)
        ]);

        // Customer 2 purchases
        Purchase::create([
            'user_id' => $customer2->id,
            'product_id' => $tablet->id,
            'price_at_purchase' => $tablet->price,
            'purchase_date' => now()->subDays(1)
        ]);
    }
} 