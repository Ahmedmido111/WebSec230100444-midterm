<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserRegistrationTest extends TestCase
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

    public function test_new_user_registration_assigns_customer_role()
    {
        // Register a new user
        $response = $this->post(route('do_register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        // Assert registration was successful
        $response->assertRedirect();
        
        // Assert user was created
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        
        // Assert customer role was assigned
        $this->assertTrue($user->hasRole('customer'));
    }

    public function test_admin_can_create_employee()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create employee data
        $employeeData = [
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'employee'
        ];

        // Create employee
        $response = $this->actingAs($admin)
            ->post(route('do_register'), $employeeData);

        // Assert employee was created
        $response->assertRedirect();
        
        // Assert employee was created with correct role
        $employee = User::where('email', 'employee@example.com')->first();
        $this->assertNotNull($employee);
        $this->assertTrue($employee->hasRole('employee'));
    }

    public function test_non_admin_cannot_create_employee()
    {
        // Create a customer
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        // Attempt to create employee
        $response = $this->actingAs($customer)
            ->post(route('do_register'), [
                'name' => 'Employee User',
                'email' => 'employee@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'employee'
            ]);

        // Assert access was denied
        $response->assertStatus(403);
        
        // Assert employee was not created
        $this->assertNull(User::where('email', 'employee@example.com')->first());
    }

    public function test_registration_requires_valid_email()
    {
        // Attempt to register with invalid email
        $response = $this->post(route('do_register'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        // Assert registration failed
        $response->assertSessionHasErrors('email');
        
        // Assert user was not created
        $this->assertNull(User::where('email', 'invalid-email')->first());
    }

    public function test_registration_requires_matching_passwords()
    {
        // Attempt to register with mismatched passwords
        $response = $this->post(route('do_register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password'
        ]);

        // Assert registration failed
        $response->assertSessionHasErrors('password');
        
        // Assert user was not created
        $this->assertNull(User::where('email', 'test@example.com')->first());
    }
}
