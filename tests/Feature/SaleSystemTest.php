<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $farmerUser1;
    protected Farmer $farmer1;
    protected User $farmerUser2;
    protected Farmer $farmer2;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Fruits & Berries',
            'slug' => 'fruits-berries',
            'icon' => 'basket',
            'description' => 'Fresh orchard items',
        ]);

        $market = Market::create([
            'name' => 'Downtown Central Market',
            'location' => '100 Main St, Metropolis',
            'operating_hours' => 'Sat 8am-2pm',
            'operating_days' => 'Saturday,Sunday',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        // 1. Create Admin
        $this->admin = User::create([
            'name' => 'Platform Admin',
            'username' => 'admin_user',
            'email' => 'admin@test.com',
            'contact_number' => '1112223333',
            'address' => 'HQ Suite 1',
            'role' => 'admin',
            'is_approved' => true,
            'is_active' => true,
            'password' => bcrypt('password'),
        ]);

        // 2. Create Farmer 1
        $this->farmerUser1 = User::create([
            'name' => 'Farmer Alice',
            'username' => 'farmer_alice',
            'email' => 'alice@test.com',
            'contact_number' => '5551112222',
            'address' => '12 Apple Way',
            'role' => 'farmer',
            'is_approved' => true,
            'is_active' => true,
            'password' => bcrypt('password'),
        ]);
        $this->farmer1 = Farmer::create([
            'user_id' => $this->farmerUser1->id,
            'market_id' => $market->id,
            'stall_name' => 'Evergreen Orchard',
            'contact_person' => 'Alice Green',
            'contact_number' => '5551112222',
            'address' => 'Stall 1A',
            'is_approved' => true,
        ]);
        $this->product1 = Product::create([
            'farmer_id' => $this->farmer1->id,
            'category_id' => $category->id,
            'name' => 'Honeycrisp Apples',
            'description' => 'Crisp sweet apples freshly picked.',
            'price' => 4.50,
            'unit' => 'lb',
            'stock_quantity' => 50,
            'is_available' => true,
            'is_sold_out' => false,
        ]);

        // 3. Create Farmer 2
        $this->farmerUser2 = User::create([
            'name' => 'Farmer Bob',
            'username' => 'farmer_bob',
            'email' => 'bob@test.com',
            'contact_number' => '5553334444',
            'address' => '34 Berry Lane',
            'role' => 'farmer',
            'is_approved' => true,
            'is_active' => true,
            'password' => bcrypt('password'),
        ]);
        $this->farmer2 = Farmer::create([
            'user_id' => $this->farmerUser2->id,
            'market_id' => $market->id,
            'stall_name' => 'Highland Berry Farm',
            'contact_person' => 'Bob Berry',
            'contact_number' => '5553334444',
            'address' => 'Stall 2B',
            'is_approved' => true,
        ]);
        $this->product2 = Product::create([
            'farmer_id' => $this->farmer2->id,
            'category_id' => $category->id,
            'name' => 'Wild Blueberries',
            'description' => 'Organic hillside wild blueberries.',
            'price' => 6.00,
            'unit' => 'pint',
            'stock_quantity' => 30,
            'is_available' => true,
            'is_sold_out' => false,
        ]);
    }

    public function test_farmer_can_view_sales_index(): void
    {
        Sale::create([
            'farmer_id' => $this->farmer1->id,
            'title' => 'Evergreen Apple Days',
            'discount_percentage' => 20,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->farmerUser1)->get(route('farmer.sales.index'));
        $response->assertStatus(200);
        $response->assertSee('Evergreen Apple Days');
        $response->assertSee('Stall Sales', false);
    }

    public function test_farmer_can_create_a_promotional_sale_with_linked_products(): void
    {
        $response = $this->actingAs($this->farmerUser1)->post(route('farmer.sales.store'), [
            'title' => 'Harvest Extravaganza',
            'description' => 'Fresh fruit discounts for market visitors.',
            'discount_percentage' => 30,
            'badge_label' => '30% FLASH DEAL',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'is_active' => '1',
            'product_ids' => [$this->product1->id],
        ]);

        $response->assertRedirect(route('farmer.sales.index'));
        $this->assertDatabaseHas('sales', [
            'farmer_id' => $this->farmer1->id,
            'title' => 'Harvest Extravaganza',
            'discount_percentage' => 30,
            'badge_label' => '30% FLASH DEAL',
            'is_active' => true,
        ]);

        $sale = Sale::where('title', 'Harvest Extravaganza')->first();
        $this->assertTrue($sale->products->contains($this->product1->id));
    }

    public function test_farmer_cannot_modify_another_farmers_sale(): void
    {
        $sale = Sale::create([
            'farmer_id' => $this->farmer1->id,
            'title' => 'Farmer 1 Private Promo',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'is_active' => true,
        ]);

        // Farmer 2 attempts to edit Farmer 1's sale
        $response = $this->actingAs($this->farmerUser2)->get(route('farmer.sales.edit', $sale->id));
        $response->assertStatus(404);

        // Farmer 2 attempts to update Farmer 1's sale
        $responseUpdate = $this->actingAs($this->farmerUser2)->put(route('farmer.sales.update', $sale->id), [
            'title' => 'Hijacked Title',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ]);
        $responseUpdate->assertStatus(404);
        $this->assertDatabaseMissing('sales', ['title' => 'Hijacked Title']);
    }

    public function test_farmer_can_toggle_and_delete_their_sale(): void
    {
        $sale = Sale::create([
            'farmer_id' => $this->farmer1->id,
            'title' => 'Temporary Promo',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'is_active' => true,
        ]);

        // Toggle to paused
        $this->actingAs($this->farmerUser1)->post(route('farmer.sales.toggle', $sale->id));
        $this->assertFalse($sale->fresh()->is_active);

        // Delete
        $this->actingAs($this->farmerUser1)->delete(route('farmer.sales.destroy', $sale->id));
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
    }

    public function test_admin_can_view_all_sales_and_feature_a_sale_on_homepage(): void
    {
        $sale1 = Sale::create([
            'farmer_id' => $this->farmer1->id,
            'title' => 'Apple Fest',
            'discount_percentage' => 15,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'is_active' => true,
            'is_featured' => false,
        ]);

        $sale2 = Sale::create([
            'farmer_id' => $this->farmer2->id,
            'title' => 'Berry Blowout',
            'discount_percentage' => 25,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'is_active' => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.sales.index'));
        $response->assertStatus(200);
        $response->assertSee('Apple Fest');
        $response->assertSee('Berry Blowout');

        // Admin features Sale 2
        $featureResponse = $this->actingAs($this->admin)->post(route('admin.sales.feature', $sale2->id));
        $featureResponse->assertSessionHas('success');

        $this->assertTrue($sale2->fresh()->is_featured);
        $this->assertFalse($sale1->fresh()->is_featured);
    }

    public function test_admin_single_spotlight_gate_unfeatures_other_sales(): void
    {
        $sale1 = Sale::create([
            'farmer_id' => $this->farmer1->id,
            'title' => 'First Featured Sale',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'is_active' => true,
            'is_featured' => true,
        ]);

        $sale2 = Sale::create([
            'farmer_id' => $this->farmer2->id,
            'title' => 'Second Sale Wanting Spotlight',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'is_active' => true,
            'is_featured' => false,
        ]);

        // Admin selects sale2 to be featured
        $this->actingAs($this->admin)->post(route('admin.sales.feature', $sale2->id));

        $this->assertTrue($sale2->fresh()->is_featured, 'Sale 2 should now be featured');
        $this->assertFalse($sale1->fresh()->is_featured, 'Sale 1 should have been automatically unfeatured');
    }

    public function test_homepage_displays_the_featured_farmer_sale(): void
    {
        $sale = Sale::create([
            'farmer_id' => $this->farmer1->id,
            'title' => 'Big Farm Weekend Harvest Deal',
            'description' => 'Save on freshly picked heirloom fruits.',
            'discount_percentage' => 35,
            'badge_label' => '35% HARVEST SPECIAL',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'is_active' => true,
            'is_featured' => true,
        ]);
        $sale->products()->attach($this->product1->id);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Big Farm Weekend Harvest Deal');
        $response->assertSee('35% HARVEST SPECIAL');
        $response->assertSee('Evergreen Orchard');
        $response->assertSee('Shop Evergreen Orchard Sale');
        $response->assertSee('Honeycrisp Apples');
    }
}
