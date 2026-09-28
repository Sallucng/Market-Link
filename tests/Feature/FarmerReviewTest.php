<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_view_reviews_and_reply(): void
    {
        $farmerUser = User::create([
            'name' => 'Farmer Emma',
            'email' => 'emma@farm.com',
            'password' => bcrypt('Password123!'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        $profile = $farmerUser->farmerProfile()->create([
            'farm_name' => 'Emma Orchards',
            'address' => '300 Apple St',
            'city' => 'Sebastopol',
            'state' => 'CA',
            'postal_code' => '95472',
            'approval_status' => 'approved',
        ]);

        $farmerToken = $farmerUser->createToken('token')->plainTextToken;

        $customer = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@client.com',
            'password' => bcrypt('Password123!'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'farmer_profile_id' => $profile->id,
            'total_amount' => 25.00,
            'status' => 'completed',
            'pickup_slot' => '10:00 AM - 11:00 AM',
        ]);

        $review = Review::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'farmer_profile_id' => $profile->id,
            'rating' => 5,
            'comment' => 'Juiciest honeycrisp apples I have ever tasted!',
            'admin_status' => 'visible',
        ]);

        // 1. View reviews
        $viewResponse = $this->withHeader('Authorization', "Bearer {$farmerToken}")
            ->getJson('/api/farmer/reviews');

        $viewResponse->assertStatus(200)
            ->assertJsonPath('summary.total_reviews', 1)
            ->assertJsonPath('summary.average_rating', 5)
            ->assertJsonPath('data.data.0.comment', 'Juiciest honeycrisp apples I have ever tasted!');

        // 2. Reply to review
        $replyResponse = $this->withHeader('Authorization', "Bearer {$farmerToken}")
            ->postJson("/api/farmer/reviews/{$review->id}/reply", [
                'farmer_reply' => 'Thank you so much Sarah! See you next weekend!',
            ]);

        $replyResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.farmer_reply', 'Thank you so much Sarah! See you next weekend!');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'farmer_reply' => 'Thank you so much Sarah! See you next weekend!',
        ]);
    }
}
