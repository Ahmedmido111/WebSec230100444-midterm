<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PurchaseTest extends TestCase
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

    public function test_customer_can_purchase_product()
    {
        // Create a customer with sufficient credit
        $customer = User::factory()->create(['credit' => 100]);
        $customer->assignRole('customer');

        // Create a product with stock
        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 5
        ]);

        // Attempt to purchase
        $response = $this->actingAs($customer)
            ->post(route('products.purchase', $product));

        // Assert purchase was successful
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert customer's credit was deducted
        $this->assertEquals(50, $customer->fresh()->credit);

        // Assert product stock was decreased
        $this->assertEquals(4, $product->fresh()->stock);

        // Assert purchase record was created
        $this->assertDatabaseHas('purchases', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'price_at_purchase' => 50
        ]);
    }

    public function test_customer_cannot_purchase_with_insufficient_credit()
    {
        // Create a customer with insufficient credit
        $customer = User::factory()->create(['credit' => 10]);
        $customer->assignRole('customer');

        // Create a product
        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 5
        ]);

        // Attempt to purchase
        $response = $this->actingAs($customer)
            ->post(route('products.purchase', $product));

        // Assert purchase failed
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Assert customer's credit wasn't changed
        $this->assertEquals(10, $customer->fresh()->credit);

        // Assert product stock wasn't changed
        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_customer_cannot_purchase_out_of_stock_product()
    {
        // Create a customer with sufficient credit
        $customer = User::factory()->create(['credit' => 100]);
        $customer->assignRole('customer');

        // Create a product with no stock
        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 0
        ]);

        // Attempt to purchase
        $response = $this->actingAs($customer)
            ->post(route('products.purchase', $product));

        // Assert purchase failed
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Assert customer's credit wasn't changed
        $this->assertEquals(100, $customer->fresh()->credit);
    }

    public function test_non_customer_cannot_purchase()
    {
        // Create an employee
        $employee = User::factory()->create(['credit' => 100]);
        $employee->assignRole('employee');

        // Create a product
        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 5
        ]);

        // Attempt to purchase
        $response = $this->actingAs($employee)
            ->post(route('products.purchase', $product));

        // Assert purchase failed
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_customer_can_view_purchase_history()
    {
        // Create a customer
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        // Create some purchases
        $product = Product::factory()->create();
        Purchase::factory()->count(3)->create([
            'user_id' => $customer->id,
            'product_id' => $product->id
        ]);

        // View purchase history
        $response = $this->actingAs($customer)
            ->get(route('purchases.history'));

        // Assert view was loaded
        $response->assertStatus(200);
        $response->assertViewIs('purchases.history');
        $response->assertViewHas('purchases');
    }
}
