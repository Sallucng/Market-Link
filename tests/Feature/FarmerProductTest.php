<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerProductTest extends TestCase
{
    use RefreshDatabase;

    private User $farmerUser;
    private string $token;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->farmerUser = User::create([
            'name' => 'Farmer John',
            'email' => 'john@farm.com',
            'password' => bcrypt('Password123!'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        $this->farmerUser->farmerProfile()->create([
            'farm_name' => 'John Produce',
            'address' => '100 Farm Way',
            'city' => 'Modesto',
            'state' => 'CA',
            'postal_code' => '95350',
            'approval_status' => 'approved',
        ]);

        $this->token = $this->farmerUser->createToken('test')->plainTextToken;

        $this->category = Category::create([
            'name' => 'Vegetables',
            'slug' => 'vegetables',
            'is_active' => true,
        ]);
    }

    public function test_unapproved_farmer_cannot_manage_products(): void
    {
        $unapprovedUser = User::create([
            'name' => 'Pending Farmer',
            'email' => 'pending@farm.com',
            'password' => bcrypt('Password123!'),
            'role' => 'farmer',
            'status' => 'pending',
        ]);

        $unapprovedUser->farmerProfile()->create([
            'farm_name' => 'Pending Farm',
            'address' => '100 Farm Way',
            'city' => 'Modesto',
            'state' => 'CA',
            'postal_code' => '95350',
            'approval_status' => 'pending',
        ]);

        $unapprovedToken = $unapprovedUser->createToken('pending-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$unapprovedToken}")
            ->postJson('/api/farmer/products', [
                'name' => 'Carrots',
                'category_id' => $this->category->id,
                'price' => 2.50,
                'unit' => 'bunch',
                'stock_quantity' => 20,
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('approval_status', 'pending');
    }

    public function test_approved_farmer_can_create_update_and_delete_product(): void
    {
        // 1. Add Product
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/farmer/products', [
                'name' => 'Crisp Lettuce',
                'category_id' => $this->category->id,
                'description' => 'Freshly harvested romaine lettuce',
                'price' => 2.99,
                'unit' => 'head',
                'stock_quantity' => 40,
                'weekly_stock' => 50,
                'is_available' => true,
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Crisp Lettuce')
            ->assertJsonPath('data.stock_quantity', 40);

        $productId = $createResponse->json('data.id');

        // 2. View Product
        $showResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/farmer/products/{$productId}");

        $showResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Crisp Lettuce');

        // 3. Edit Product
        $updateResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/farmer/products/{$productId}", [
                'price' => 3.25,
                'stock_quantity' => 35,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.price', '3.25')
            ->assertJsonPath('data.stock_quantity', 35);

        // 4. Toggle Availability / Sold Out
        $toggleResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->patchJson("/api/farmer/products/{$productId}/status");

        $toggleResponse->assertStatus(200)
            ->assertJsonPath('data.is_available', false);

        // 5. Weekly Stock Management
        $weeklyResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/farmer/products/{$productId}/weekly-stock", [
                'weekly_stock' => 100,
                'reset_current_stock' => true,
            ]);

        $weeklyResponse->assertStatus(200)
            ->assertJsonPath('data.weekly_stock', 100)
            ->assertJsonPath('data.stock_quantity', 100)
            ->assertJsonPath('data.is_available', true);

        // 6. Delete Product
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/farmer/products/{$productId}");

        $deleteResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }
}
