<?php

namespace Database\Seeders;

use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Sample Customer Users
        $customersData = [
            ['name' => 'Alice Walker', 'email' => 'customer.alice@marketlink.com'],
            ['name' => 'Bob Davis', 'email' => 'customer.bob@marketlink.com'],
            ['name' => 'Charlie Evans', 'email' => 'customer.charlie@marketlink.com'],
        ];

        $customers = [];
        foreach ($customersData as $data) {
            $customers[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'username'          => explode('@', $data['email'])[0],
                    'password'          => Hash::make('Customer123!'),
                    'role'              => 'customer',
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }

        $farmer1 = FarmerProfile::where('is_approved', true)->first();
        $farmer2 = FarmerProfile::where('is_approved', true)->skip(1)->first() ?? $farmer1;
        $market = Market::first();

        if (!$farmer1) {
            return;
        }

        $farmer1Products = Product::where('farmer_profile_id', $farmer1->id)->get();
        $farmer2Products = Product::where('farmer_profile_id', $farmer2->id)->get();

        if ($farmer1Products->isEmpty()) {
            return;
        }

        // 2. Order A: Pending state
        $p1 = $farmer1Products[0];
        $orderA = Order::firstOrCreate(
            [
                'customer_id'       => $customers[0]->id,
                'farmer_profile_id' => $farmer1->id,
                'pickup_slot'       => '09:00 AM - 10:00 AM',
                'status'            => 'pending',
            ],
            [
                'market_id'    => $market?->id,
                'total_amount' => round($p1->price * 2, 2),
                'cutoff_time'  => now()->addHours(12),
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $orderA->id, 'product_id' => $p1->id],
            [
                'quantity'   => 2,
                'unit_price' => $p1->price,
                'subtotal'   => round($p1->price * 2, 2),
            ]
        );

        // 3. Order B: Accepted state
        $p2 = $farmer1Products->count() > 1 ? $farmer1Products[1] : $p1;
        $orderB = Order::firstOrCreate(
            [
                'customer_id'       => $customers[1]->id,
                'farmer_profile_id' => $farmer1->id,
                'pickup_slot'       => '10:00 AM - 11:00 AM',
                'status'            => 'accepted',
            ],
            [
                'market_id'    => $market?->id,
                'total_amount' => round($p2->price * 3, 2),
                'cutoff_time'  => now()->addHours(6),
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $orderB->id, 'product_id' => $p2->id],
            [
                'quantity'   => 3,
                'unit_price' => $p2->price,
                'subtotal'   => round($p2->price * 3, 2),
            ]
        );

        // 4. Order C: Ready for pickup state
        $orderC = Order::firstOrCreate(
            [
                'customer_id'       => $customers[2]->id,
                'farmer_profile_id' => $farmer1->id,
                'pickup_slot'       => '11:00 AM - 12:00 PM',
                'status'            => 'ready_for_pickup',
            ],
            [
                'market_id'    => $market?->id,
                'total_amount' => round(($p1->price * 1) + ($p2->price * 2), 2),
                'cutoff_time'  => now()->addHours(2),
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $orderC->id, 'product_id' => $p1->id],
            [
                'quantity'   => 1,
                'unit_price' => $p1->price,
                'subtotal'   => round($p1->price * 1, 2),
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $orderC->id, 'product_id' => $p2->id],
            [
                'quantity'   => 2,
                'unit_price' => $p2->price,
                'subtotal'   => round($p2->price * 2, 2),
            ]
        );

        // 5. Order D: Completed state (with Farmer 1)
        $orderD = Order::firstOrCreate(
            [
                'customer_id'       => $customers[0]->id,
                'farmer_profile_id' => $farmer1->id,
                'pickup_slot'       => '10:00 AM - 11:00 AM',
                'status'            => 'completed',
            ],
            [
                'market_id'    => $market?->id,
                'total_amount' => round($p1->price * 4, 2),
                'cutoff_time'  => now()->subDays(1),
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $orderD->id, 'product_id' => $p1->id],
            [
                'quantity'   => 4,
                'unit_price' => $p1->price,
                'subtotal'   => round($p1->price * 4, 2),
            ]
        );

        // 6. Order E: Completed state (with Farmer 2 if available)
        $p3 = $farmer2Products->first() ?? $p1;
        $orderE = Order::firstOrCreate(
            [
                'customer_id'       => $customers[1]->id,
                'farmer_profile_id' => $farmer2->id,
                'pickup_slot'       => '08:30 AM - 09:30 AM',
                'status'            => 'completed',
            ],
            [
                'market_id'    => $market?->id,
                'total_amount' => round($p3->price * 2, 2),
                'cutoff_time'  => now()->subDays(2),
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $orderE->id, 'product_id' => $p3->id],
            [
                'quantity'   => 2,
                'unit_price' => $p3->price,
                'subtotal'   => round($p3->price * 2, 2),
            ]
        );
    }
}
