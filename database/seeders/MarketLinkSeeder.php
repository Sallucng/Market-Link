<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarketLinkSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Sample Markets
        $market1 = Market::create([
            'market_name'    => 'Downtown Farmers Market',
            'address'        => 'Central Plaza, Main St, Metropolis',
            'operating_days' => 'Monday - Saturday',
            'timings'        => '07:00 AM - 05:00 PM',
            'description'    => 'Fresh organic produce directly from local farms.',
            'is_active'      => true,
        ]);

        $market2 = Market::create([
            'market_name'    => 'Green Valley Organic Market',
            'address'        => '45 River Road, Green Valley',
            'operating_days' => 'Weekends Only',
            'timings'        => '08:00 AM - 04:00 PM',
            'description'    => 'Weekly community farmers market.',
            'is_active'      => true,
        ]);

        // 2. Create Product Categories
        $veg = Category::create([
            'name'        => 'Vegetables', 
            'description' => 'Fresh organic vegetables'
        ]);
        
        $fruits = Category::create([
            'name'        => 'Fruits', 
            'description' => 'Farm fresh seasonal fruits'
        ]);
        
        $dairy = Category::create([
            'name'        => 'Dairy & Eggs', 
            'description' => 'Fresh milk, cheese, and farm eggs'
        ]);

        // 3. Create Sample Test Accounts
        $customer = User::create([
            'name'     => 'John Doe',
            'email'    => 'customer@marketlink.com',
            'password' => Hash::make('password123'),
            'role'     => 'customer',
            'phone'    => '1234567890',
        ]);

        $farmer1 = User::create([
            'name'     => 'Green Acres Farm',
            'email'    => 'farmer@marketlink.com',
            'password' => Hash::make('password123'),
            'role'     => 'farmer',
            'phone'    => '0987654321',
        ]);

        // Matches farmer_profiles migration schema
        FarmerProfile::create([
            'user_id'        => $farmer1->id,
            'stall_name'     => 'Green Acres Stall #A-12',
            'contact_person' => 'Green Acres Farm Manager',
            'address'        => '102 Farm Road, Metropolis',
            'operating_days' => 'Monday - Saturday',
            'pickup_time'    => '08:00 AM - 04:00 PM',
            'description'    => 'Specializing in heirloom tomatoes and leafy greens.',
        ]);

        // 4. Create Sample Products
        Product::create([
            'farmer_id'     => $farmer1->id,
            'category_id'   => $veg->id,
            'name'          => 'Organic Tomatoes',
            'description'   => 'Vine-ripened red tomatoes.',
            'price'         => 3.50,
            'unit'          => 'kg',
            'stock_quantity'=> 50,
            'status'        => 'available',
        ]);

        Product::create([
            'farmer_id'     => $farmer1->id,
            'category_id'   => $fruits->id,
            'name'          => 'Fresh Strawberries',
            'description'   => 'Sweet, freshly picked strawberries.',
            'price'         => 5.00,
            'unit'          => 'box',
            'stock_quantity'=> 20,
            'status'        => 'available',
        ]);

        Product::create([
            'farmer_id'     => $farmer1->id,
            'category_id'   => $dairy->id,
            'name'          => 'Farm Fresh Eggs',
            'description'   => 'Free-range brown eggs.',
            'price'         => 4.20,
            'unit'          => 'dozen',
            'stock_quantity'=> 30,
            'status'        => 'available',
        ]);
    }
}