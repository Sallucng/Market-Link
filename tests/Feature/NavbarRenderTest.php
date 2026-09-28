<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarRenderTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(): User
    {
        return User::factory()->create(['role' => 'customer', 'name' => 'TestCustomer']);
    }

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'name' => 'TestAdmin']);
    }

    private function createFarmer(): User
    {
        return User::factory()->create(['role' => 'farmer', 'name' => 'TestFarmer']);
    }

    public function test_navbar_on_customer_orders_page(): void
    {
        $customer = $this->createCustomer();
        
        $response = $this->actingAs($customer)->get('/customer/orders');
        
        $response->assertStatus(200);
        $response->assertSee('id="navContent"', false);
        $response->assertSee('navbar-toggler', false);
        $response->assertSee('collapse navbar-collapse', false);
        $response->assertSee('TestCustomer');
    }

    public function test_navbar_on_customer_dashboard(): void
    {
        $customer = $this->createCustomer();
        
        $response = $this->actingAs($customer)->get('/customer/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('id="navContent"', false);
        $response->assertSee('navbar-toggler', false);
    }

    public function test_navbar_on_home_page_logged_in(): void
    {
        $customer = $this->createCustomer();
        
        $response = $this->actingAs($customer)->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('id="navContent"', false);
        $response->assertSee('TestCustomer');
    }

    public function test_navbar_on_products_page(): void
    {
        $customer = $this->createCustomer();
        
        $response = $this->actingAs($customer)->get('/products');
        
        $response->assertStatus(200);
        $response->assertSee('id="navContent"', false);
    }
}
