<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Farmer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $farmerUser;
    private Farmer $farmer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::create([
            'name' => 'Test Customer',
            'username' => 'test_customer',
            'email' => 'customer@test.com',
            'contact_number' => '1234567890',
            'address' => '123 Main St',
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $this->farmerUser = User::create([
            'name' => 'Test Farmer',
            'username' => 'test_farmer',
            'email' => 'farmer@test.com',
            'contact_number' => '0987654321',
            'address' => '456 Farm Rd',
            'role' => 'farmer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $this->farmer = Farmer::create([
            'user_id' => $this->farmerUser->id,
            'stall_name' => 'Sunny Meadow Farms',
            'contact_person' => 'Farmer Bob',
            'contact_number' => '0987654321',
            'address' => 'Stall 4, Downtown Market',
            'is_approved' => true,
        ]);
    }

    public function test_customer_can_view_messages_inbox(): void
    {
        $this->actingAs($this->customer);

        $response = $this->get(route('customer.messages.index'));
        $response->assertStatus(200);
        $response->assertSee('Stall Messages');
    }

    public function test_customer_can_start_conversation_and_send_message(): void
    {
        $this->actingAs($this->customer);

        $response = $this->post(route('customer.messages.start'), [
            'farmer_id' => $this->farmer->id,
            'message' => 'Hello from automated test! Are you open this Saturday?',
        ]);

        $response->assertStatus(302);

        $conversation = Conversation::where('customer_id', $this->customer->id)
            ->where('farmer_id', $this->farmer->id)
            ->first();

        $this->assertNotNull($conversation);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->customer->id,
            'body' => 'Hello from automated test! Are you open this Saturday?',
        ]);
    }

    public function test_farmer_can_view_incoming_messages_and_reply(): void
    {
        $conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'subject' => 'Test Inquiry',
            'last_message_at' => now(),
        ]);

        $this->actingAs($this->farmerUser);

        $response = $this->get(route('farmer.messages.index', ['conversation_id' => $conversation->id]));
        $response->assertStatus(200);
        $response->assertSee($this->customer->name);

        // Send a reply
        $replyResponse = $this->postJson(route('farmer.messages.send', $conversation->id), [
            'body' => 'Yes, we will be at the stall from 8 AM!',
        ]);

        $replyResponse->assertStatus(200);
        $replyResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->farmerUser->id,
            'body' => 'Yes, we will be at the stall from 8 AM!',
        ]);
    }

    public function test_polling_returns_new_messages_and_marks_them_read(): void
    {
        $conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'subject' => 'Polling Test',
            'last_message_at' => now(),
        ]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->farmerUser->id,
            'body' => 'Message to be polled',
            'is_read' => false,
        ]);

        $this->actingAs($this->customer);

        $pollResponse = $this->getJson(route('customer.messages.poll', [
            'conversation' => $conversation->id,
            'after_id' => 0,
        ]));

        $pollResponse->assertStatus(200);
        $pollResponse->assertJson(['success' => true]);

        $this->assertTrue(
            $msg->fresh()->is_read,
            'Message should be marked as read when recipient polls the thread'
        );
    }

    public function test_unauthorized_user_cannot_access_foreign_conversation(): void
    {
        $otherUser = User::create([
            'name' => 'Intruder',
            'username' => 'intruder',
            'email' => 'intruder@test.com',
            'contact_number' => '5555555555',
            'address' => '789 Somewhere',
            'role' => 'customer',
            'is_active' => true,
            'is_approved' => true,
            'password' => bcrypt('password'),
        ]);

        $conversation = Conversation::create([
            'customer_id' => $this->customer->id,
            'farmer_id' => $this->farmer->id,
            'subject' => 'Private thread',
            'last_message_at' => now(),
        ]);

        $this->actingAs($otherUser);

        $response = $this->postJson(route('customer.messages.send', $conversation->id), [
            'body' => 'Intruder message',
        ]);

        $response->assertStatus(403);
    }
}
