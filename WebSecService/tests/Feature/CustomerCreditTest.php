<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class CustomerCreditTest extends TestCase
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

    public function test_employee_can_view_customers()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create some customers
        $customers = User::factory()->count(3)->create();
        foreach ($customers as $customer) {
            $customer->assignRole('customer');
        }

        // View customers list
        $response = $this->actingAs($employee)
            ->get(route('customers.index'));

        // Assert view was loaded
        $response->assertStatus(200);
        $response->assertViewIs('customers.index');
        $response->assertViewHas('customers');
    }

    public function test_employee_can_add_credit_to_customer()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create a customer
        $customer = User::factory()->create(['credit' => 100]);
        $customer->assignRole('customer');

        // Add credit
        $response = $this->actingAs($employee)
            ->post(route('customers.add-credit', $customer), [
                'amount' => 50
            ]);

        // Assert credit was added
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(150, $customer->fresh()->credit);
    }

    public function test_employee_cannot_add_negative_credit()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create a customer
        $customer = User::factory()->create(['credit' => 100]);
        $customer->assignRole('customer');

        // Attempt to add negative credit
        $response = $this->actingAs($employee)
            ->post(route('customers.add-credit', $customer), [
                'amount' => -50
            ]);

        // Assert credit wasn't added
        $response->assertRedirect();
        $response->assertSessionHasErrors('amount');
        $this->assertEquals(100, $customer->fresh()->credit);
    }

    public function test_non_employee_cannot_add_credit()
    {
        // Create a customer
        $customer = User::factory()->create(['credit' => 100]);
        $customer->assignRole('customer');

        // Create another customer
        $otherCustomer = User::factory()->create(['credit' => 100]);
        $otherCustomer->assignRole('customer');

        // Attempt to add credit
        $response = $this->actingAs($customer)
            ->post(route('customers.add-credit', $otherCustomer), [
                'amount' => 50
            ]);

        // Assert credit wasn't added
        $response->assertStatus(403);
        $this->assertEquals(100, $otherCustomer->fresh()->credit);
    }

    public function test_employee_can_view_customer_details()
    {
        // Create an employee
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Create a customer
        $customer = User::factory()->create(['credit' => 100]);
        $customer->assignRole('customer');

        // View customer details
        $response = $this->actingAs($employee)
            ->get(route('customers.show', $customer));

        // Assert view was loaded
        $response->assertStatus(200);
        $response->assertViewIs('customers.show');
        $response->assertViewHas('customer');
    }
}
