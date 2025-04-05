<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Example products with image URLs
        $products = [
            [
                'code' => 'LAPTOP001',
                'name' => 'Gaming Laptop',
                'model' => 'GL65 Leopard',
                'description' => 'High-performance gaming laptop with RTX graphics',
                'price' => 1299.99,
                'stock' => 10,
                'photo' => 'https://example.com/images/laptop.jpg'
            ],
            [
                'code' => 'PHONE001',
                'name' => 'Smartphone Pro',
                'model' => 'X12',
                'description' => 'Latest smartphone with advanced camera system',
                'price' => 899.99,
                'stock' => 15,
                'photo' => 'https://example.com/images/phone.jpg'
            ],
            [
                'code' => 'TABLET001',
                'name' => 'Tablet Ultra',
                'model' => 'T20',
                'description' => 'Lightweight tablet with stunning display',
                'price' => 499.99,
                'stock' => 20,
                'photo' => 'https://example.com/images/tablet.jpg'
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
} 