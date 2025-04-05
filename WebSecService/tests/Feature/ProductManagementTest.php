<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'employee', 'guard_name' => 'web']);
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_employee_can_create_product()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create product data
        $productData = [
            'code' => 'TEST001',
            'name' => 'Test Product',
            'price' => 100,
            'model' => 'Test Model',
            'description' => 'Test Description',
            'stock' => 10
        ];

        // Create product
        $response = $this->actingAs($employee)
            ->post(route('products_save'), $productData);

        // Assert product was created
        $response->assertRedirect();
        $this->assertDatabaseHas('products', $productData);
    }

    public function test_employee_can_update_product()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create a product
        $product = Product::factory()->create();

        // Update product data
        $updateData = [
            'code' => 'UPDATED001',
            'name' => 'Updated Product',
            'price' => 200,
            'model' => 'Updated Model',
            'description' => 'Updated Description',
            'stock' => 20
        ];

        // Update product
        $response = $this->actingAs($employee)
            ->post(route('products_save', $product), $updateData);

        // Assert product was updated
        $response->assertRedirect();
        $this->assertDatabaseHas('products', $updateData);
    }

    public function test_employee_can_delete_product()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create a product
        $product = Product::factory()->create();

        // Delete product
        $response = $this->actingAs($employee)
            ->get(route('products_delete', $product));

        // Assert product was deleted
        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_non_employee_cannot_manage_products()
    {
        // Create a customer
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        // Create a product
        $product = Product::factory()->create();

        // Attempt to create product
        $response = $this->actingAs($customer)
            ->post(route('products_save'), [
                'code' => 'TEST001',
                'name' => 'Test Product',
                'price' => 100,
                'model' => 'Test Model',
                'description' => 'Test Description',
                'stock' => 10
            ]);

        // Assert access was denied
        $response->assertStatus(403);

        // Attempt to update product
        $response = $this->actingAs($customer)
            ->post(route('products_save', $product), [
                'code' => 'UPDATED001',
                'name' => 'Updated Product',
                'price' => 200,
                'model' => 'Updated Model',
                'description' => 'Updated Description',
                'stock' => 20
            ]);

        // Assert access was denied
        $response->assertStatus(403);

        // Attempt to delete product
        $response = $this->actingAs($customer)
            ->get(route('products_delete', $product));

        // Assert access was denied
        $response->assertStatus(403);
    }

    public function test_product_stock_cannot_be_negative()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create a product
        $product = Product::factory()->create();

        // Attempt to update with negative stock
        $response = $this->actingAs($employee)
            ->post(route('products_save', $product), [
                'code' => $product->code,
                'name' => $product->name,
                'price' => $product->price,
                'model' => $product->model,
                'description' => $product->description,
                'stock' => -1
            ]);

        // Assert update was rejected
        $response->assertRedirect();
        $response->assertSessionHasErrors('stock');
        $this->assertEquals($product->stock, $product->fresh()->stock);
    }
}
