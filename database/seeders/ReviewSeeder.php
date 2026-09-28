<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $completedOrders = Order::where('status', 'completed')
            ->with(['items.product', 'customer', 'farmerProfile'])
            ->get();

        $sampleReviews = [
            [
                'rating'       => 5,
                'comment'      => 'The heirloom tomatoes were extraordinarily fresh and flavorful! Friendly pickup experience at the market stall.',
                'farmer_reply' => 'Thank you for supporting our family farm! So glad you enjoyed the fresh harvest this week.',
            ],
            [
                'rating'       => 5,
                'comment'      => 'Crisp, sweet Honeycrisp apples and the freshest eggs I have ever purchased. Will definitely be a regular customer!',
                'farmer_reply' => 'We really appreciate your kind words! We bring fresh orchard picks every Saturday morning.',
            ],
            [
                'rating'       => 4,
                'comment'      => 'Top quality seasonal vegetables! Pickup went smoothly, and produce was well-packaged in eco-friendly bags.',
                'farmer_reply' => 'Thank you for your feedback! We look forward to seeing you at the next market day.',
            ],
        ];

        foreach ($completedOrders as $index => $order) {
            $productItem = $order->items->first();
            $reviewData = $sampleReviews[$index % count($sampleReviews)];

            Review::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'customer_id'       => $order->customer_id,
                    'farmer_profile_id' => $order->farmer_profile_id,
                    'product_id'        => $productItem?->product_id,
                    'rating'            => $reviewData['rating'],
                    'comment'           => $reviewData['comment'],
                    'farmer_reply'      => $reviewData['farmer_reply'],
                    'is_moderated'      => false,
                ]
            );
        }
    }
}
