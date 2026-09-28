<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FarmerInventoryEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    private User $farmerUserA;
    private FarmerProfile $profileA;
    private string $tokenA;

    private User $farmerUserB;
    private FarmerProfile $profileB;
    private string $tokenB;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Setup Farmer A
        $this->farmerUserA = User::create([
            'name'     => 'Farmer Alice',
            'email'    => 'alice@farm.com',
            'password' => bcrypt('Secret123!'),
            'role'     => 'farmer',
            'status'   => 'active',
        ]);

        $this->profileA = $this->farmerUserA->farmerProfile()->create([
            'business_name'     => 'Alice Organic Farm',
            'stall_number'      => 'Stall #1',
            'address'           => '100 Green Acres Rd',
            'operating_days'    => ['Saturday', 'Sunday'],
            'pickup_start_time' => '08:00',
            'pickup_end_time'   => '13:00',
            'is_approved'       => true,
        ]);

        $this->tokenA = $this->farmerUserA->createToken('alice-token')->plainTextToken;

        // Setup Farmer B
        $this->farmerUserB = User::create([
            'name'     => 'Farmer Bob',
            'email'    => 'bob@farm.com',
            'password' => bcrypt('Secret123!'),
            'role'     => 'farmer',
            'status'   => 'active',
        ]);

        $this->profileB = $this->farmerUserB->farmerProfile()->create([
            'business_name'     => 'Bob Berry Patch',
            'stall_number'      => 'Stall #2',
            'address'           => '200 Berry Lane',
            'operating_days'    => ['Saturday'],
            'pickup_start_time' => '09:00',
            'pickup_end_time'   => '14:00',
            'is_approved'       => true,
        ]);

        $this->tokenB = $this->farmerUserB->createToken('bob-token')->plainTextToken;

        $this->category = Category::create([
            'name'        => 'Fresh Produce',
            'slug'        => 'fresh-produce',
            'description' => 'Locally grown organic produce',
            'is_active'   => true,
        ]);
    }

    /**
     * 1. Farmer cannot set negative price or stock quantity.
     */
    public function test_farmer_cannot_set_negative_price_or_stock_quantity(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->tokenA}")
            ->postJson('/api/v1/farmer/products', [
                'name'           => 'Organic Carrots',
                'category_id'    => $this->category->id,
                'price'          => -4.50,
                'unit'           => 'kg',
                'stock_quantity' => -10,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price', 'stock_quantity']);
    }

    /**
     * 2. Farmer cannot access, update, or delete another farmer's product (Tenancy validation).
     */
    public function test_farmer_cannot_access_update_or_delete_another_farmers_product(): void
    {
        // Product belongs to Farmer A
        $productA = Product::create([
            'farmer_profile_id' => $this->profileA->id,
            'category_id'       => $this->category->id,
            'name'              => 'Heirloom Tomatoes',
            'slug'              => 'heirloom-tomatoes-' . uniqid(),
            'price'             => 4.99,
            'unit'              => 'kg',
            'stock_quantity'    => 50,
            'weekly_quota'      => 60,
            'is_available'      => true,
        ]);

        // Farmer B attempts to view Farmer A's product
        $viewResponse = $this->withHeader('Authorization', "Bearer {$this->tokenB}")
            ->getJson("/api/v1/farmer/products/{$productA->id}");
        $viewResponse->assertStatus(404);

        // Farmer B attempts to update Farmer A's product
        $updateResponse = $this->withHeader('Authorization', "Bearer {$this->tokenB}")
            ->putJson("/api/v1/farmer/products/{$productA->id}", [
                'name'           => 'Hacked Tomatoes',
                'category_id'    => $this->category->id,
                'price'          => 0.99,
                'unit'           => 'kg',
                'stock_quantity' => 100,
            ]);
        $updateResponse->assertStatus(404);

        // Farmer B attempts to delete Farmer A's product
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->tokenB}")
            ->deleteJson("/api/v1/farmer/products/{$productA->id}");
        $deleteResponse->assertStatus(404);

        // Assert product A remains completely untouched
        $this->assertDatabaseHas('products', [
            'id'    => $productA->id,
            'name'  => 'Heirloom Tomatoes',
            'price' => 4.99,
        ]);
    }

    /**
     * 3. Toggling product availability immediately reflects in query scopes.
     */
    public function test_toggling_product_availability_immediately_reflects_in_query_scopes(): void
    {
        $product = Product::create([
            'farmer_profile_id' => $this->profileA->id,
            'category_id'       => $this->category->id,
            'name'              => 'Sweet Corn',
            'slug'              => 'sweet-corn-' . uniqid(),
            'price'             => 1.50,
            'unit'              => 'piece',
            'stock_quantity'    => 30,
            'weekly_quota'      => 40,
            'is_available'      => true,
        ]);

        $this->assertTrue(Product::available()->where('id', $product->id)->exists());

        // Toggle availability to false
        $response = $this->withHeader('Authorization', "Bearer {$this->tokenA}")
            ->patchJson("/api/v1/farmer/products/{$product->id}/toggle-availability");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertFalse(Product::available()->where('id', $product->id)->exists());
        $this->assertDatabaseHas('products', [
            'id'           => $product->id,
            'is_available' => false,
        ]);

        // Toggle availability back to true
        $response2 = $this->withHeader('Authorization', "Bearer {$this->tokenA}")
            ->patchJson("/api/v1/farmer/products/{$product->id}/toggle-availability");

        $response2->assertStatus(200);
        $this->assertTrue(Product::available()->where('id', $product->id)->exists());
    }

    /**
     * 4. Uploading an invalid file type or file exceeding size limits fails validation with 422.
     */
    public function test_uploading_invalid_file_type_or_oversized_file_fails_validation(): void
    {
        // Invalid file format (text script instead of image)
        $invalidFile = UploadedFile::fake()->create('exploit.txt', 50, 'text/plain');

        $response1 = $this->withHeader('Authorization', "Bearer {$this->tokenA}")
            ->postJson('/api/v1/farmer/products', [
                'name'           => 'Organic Spinach',
                'category_id'    => $this->category->id,
                'price'          => 3.25,
                'unit'           => 'bunch',
                'stock_quantity' => 20,
                'image'          => $invalidFile,
            ]);

        $response1->assertStatus(422)
            ->assertJsonValidationErrors(['image']);

        // Oversized file (3MB exceeds 2048KB limit)
        $oversizedFile = UploadedFile::fake()->image('huge.jpg')->size(3072);

        $response2 = $this->withHeader('Authorization', "Bearer {$this->tokenA}")
            ->postJson('/api/v1/farmer/products', [
                'name'           => 'Organic Kale',
                'category_id'    => $this->category->id,
                'price'          => 2.80,
                'unit'           => 'bunch',
                'stock_quantity' => 25,
                'image'          => $oversizedFile,
            ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }
}
