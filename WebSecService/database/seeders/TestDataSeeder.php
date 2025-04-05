<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'credit' => 1000
        ]);
        $admin->assignRole('admin');

        // Create employee user
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'credit' => 500
        ]);
        $employee->assignRole('employee');

        // Create customer users
        $customers = [
            [
                'name' => 'Customer One',
                'email' => 'customer1@example.com',
                'credit' => 200
            ],
            [
                'name' => 'Customer Two',
                'email' => 'customer2@example.com',
                'credit' => 300
            ],
            [
                'name' => 'Customer Three',
                'email' => 'customer3@example.com',
                'credit' => 150
            ]
        ];

        foreach ($customers as $customerData) {
            $customer = User::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'password' => Hash::make('password'),
                'credit' => $customerData['credit']
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