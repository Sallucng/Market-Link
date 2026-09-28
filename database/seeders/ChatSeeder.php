<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Farmer;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('username', 'sarah_shopper')->first();
        $farmerUser = User::where('username', 'greenvalley')->first();

        if (!$customer || !$farmerUser || !$farmerUser->farmer) {
            return;
        }

        $farmer = $farmerUser->farmer;
        $order = Order::where('customer_id', $customer->id)
            ->where('farmer_id', $farmer->id)
            ->first();

        // 1. Order-linked Conversation
        $conv1 = Conversation::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'farmer_id' => $farmer->id,
                'order_id' => $order ? $order->id : null,
            ],
            [
                'subject' => $order ? "Pickup Coordination — Order #{$order->order_number}" : "Pickup Coordination with Green Valley",
                'last_message_at' => Carbon::now()->subMinutes(12),
            ]
        );

        Message::updateOrCreate(
            ['conversation_id' => $conv1->id, 'body' => "Hi John! Just wanted to confirm if our order pickup window can be around 10:15 AM this Saturday?"],
            [
                'sender_id' => $customer->id,
                'is_read' => true,
                'read_at' => Carbon::now()->subMinutes(30),
                'created_at' => Carbon::now()->subMinutes(35),
            ]
        );

        Message::updateOrCreate(
            ['conversation_id' => $conv1->id, 'body' => "Hello Sarah! Absolutely. Your harvest box is reserved and ready at Stall #4 near the north canopy entrance. See you then!"],
            [
                'sender_id' => $farmerUser->id,
                'is_read' => true,
                'read_at' => Carbon::now()->subMinutes(20),
                'created_at' => Carbon::now()->subMinutes(25),
            ]
        );

        Message::updateOrCreate(
            ['conversation_id' => $conv1->id, 'body' => "Wonderful, thank you! Looking forward to the fresh heirloom tomatoes."],
            [
                'sender_id' => $customer->id,
                'is_read' => false,
                'created_at' => Carbon::now()->subMinutes(12),
            ]
        );
    }
}
