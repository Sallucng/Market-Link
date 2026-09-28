<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Organic Heirloom Tomatoes',
            'Crisp Romaine Lettuce',
            'Sweet Honeycrisp Apples',
            'Pasture-Raised Brown Eggs',
            'Wild Blossom Clover Honey',
            'Fresh Sweet Basil Bunch',
            'California Strawberries',
            'Tender Baby Spinach',
            'Grass-Fed Salted Butter',
            'Free-Range Whole Chicken',
            'Rainbow Swiss Chard',
            'Farmhouse Goat Cheese',
            'Juicy Yellow Peaches',
            'Fresh Rosemary Bunch',
            'Organic Hass Avocados',
            'Sweet Nantes Carrots',
            'Whole Pasteurized Milk',
            'Farmhouse Pork Sausages',
            'Wild Mountain Blueberries',
            'Fresh Garden Spearmint',
        ]);

        $stock = fake()->numberBetween(25, 120);

        return [
            'farmer_id' => \App\\Models\\User::factory()->state(['role' => 'farmer']),
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 9999),
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 2.50, 18.50),
            'unit' => fake()->randomElement(['kg', 'bunch', 'dozen', 'piece', 'box', 'head']),
            'stock_quantity' => $stock,
            'weekly_quota' => $stock + fake()->numberBetween(0, 40),
            'image' => 'products/sample_' . fake()->numberBetween(1, 5) . '.jpg',
            'status' => 'available',
        ];
    }
}
