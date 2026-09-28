<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_favorite_farmer_and_product_polymorphically(): void
    {
        $customer = User::create([
            'name'     => 'Customer Jane',
            'email'    => 'jane@customer.local',
            'password' => bcrypt('Password123!'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        $farmerUser = User::create([
            'name'     => 'Farmer John',
            'email'    => 'john@farm.local',
            'password' => bcrypt('Password123!'),
            'role'     => 'farmer',
            'status'   => 'active',
        ]);

        $farmerProfile = $farmerUser->farmerProfile()->create([
            'business_name'  => 'Sunny Fields',
            'stall_number'   => 'Stall #5',
            'address'        => '123 Meadow Rd',
            'operating_days' => ['Saturday'],
            'is_approved'    => true,
        ]);

        $category = Category::create([
            'name'      => 'Vegetables',
            'slug'      => 'vegetables',
            'is_active' => true,
        ]);

        $product = Product::create([
            'farmer_profile_id' => $farmerProfile->id,
            'category_id'       => $category->id,
            'name'              => 'Organic Broccoli',
            'slug'              => 'organic-broccoli-1',
            'price'             => 2.99,
            'unit'              => 'bunch',
            'stock_quantity'    => 20,
            'weekly_quota'      => 30,
            'is_available'      => true,
        ]);

        // 1. Favorite a Farmer Profile
        $farmerFav = Favorite::create([
            'user_id'          => $customer->id,
            'favoritable_id'   => $farmerProfile->id,
            'favoritable_type' => FarmerProfile::class,
        ]);

        // 2. Favorite a Product
        $productFav = Favorite::create([
            'user_id'          => $customer->id,
            'favoritable_id'   => $product->id,
            'favoritable_type' => Product::class,
        ]);

        // Assert relationships
        $this->assertEquals(2, $customer->favorites()->count());
        $this->assertTrue($farmerFav->favoritable->is($farmerProfile));
        $this->assertTrue($productFav->favoritable->is($product));

        // Assert reciprocal morphMany relationships
        $this->assertEquals(1, $farmerProfile->favorites()->count());
        $this->assertEquals(1, $product->favorites()->count());
    }

    public function test_user_cannot_favorite_same_item_twice_unique_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $customer = User::create([
            'name'     => 'Customer Bob',
            'email'    => 'bob@customer.local',
            'password' => bcrypt('Password123!'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        Favorite::create([
            'user_id'          => $customer->id,
            'favoritable_id'   => 99,
            'favoritable_type' => Product::class,
        ]);

        // Duplicate insert triggers unique constraint violation
        Favorite::create([
            'user_id'          => $customer->id,
            'favoritable_id'   => 99,
            'favoritable_type' => Product::class,
        ]);
    }
}
