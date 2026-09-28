<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Farmer;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /* ---------------------------------------------------------
     | Customer Chat Endpoints
     | --------------------------------------------------------- */

    public function customerIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('login');
        }

        $conversations = Conversation::where('customer_id', $user->id)
            ->with(['farmer.user', 'order', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $activeConversation = null;
        $activeId = $request->query('conversation_id');

        if ($activeId) {
            $activeConversation = $conversations->firstWhere('id', (int)$activeId);
        }

        if (!$activeConversation && $conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
        }

        if ($activeConversation) {
            // Mark incoming messages as read
            Message::where('conversation_id', $activeConversation->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => Carbon::now()]);

            $activeConversation->load(['messages.sender', 'farmer.user', 'order.items.product']);
        }

        return view('customer.messages.index', compact('conversations', 'activeConversation'));
    }

    public function startFromCustomer(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Please log in to message this farm stall.');
        }

        $data = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'order_id' => 'nullable|exists:orders,id',
            'message' => 'nullable|string|max:1500',
            'subject' => 'nullable|string|max:200',
        ]);

        $farmer = Farmer::findOrFail($data['farmer_id']);
        $order = !empty($data['order_id']) ? Order::find($data['order_id']) : null;

        $subject = $data['subject'] ?? ($order ? "Order #{$order->order_number} Inquiry" : "Inquiry for {$farmer->stall_name}");

        // Look for existing thread between this customer and farmer (and order if applicable)
        $query = Conversation::where('customer_id', $user->id)
            ->where('farmer_id', $farmer->id);

        if ($order) {
            $query->where('order_id', $order->id);
        }

        $conversation = $query->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'customer_id' => $user->id,
                'farmer_id' => $farmer->id,
                'order_id' => $order ? $order->id : null,
                'subject' => $subject,
                'last_message_at' => Carbon::now(),
            ]);
        }

        if (!empty($data['message'])) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'body' => trim($data['message']),
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => Carbon::now()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'redirect_url' => route('customer.messages.index', ['conversation_id' => $conversation->id]),
            ]);
        }

        return redirect()->route('customer.messages.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Conversation started with ' . $farmer->stall_name . '.');
    }

    /* ---------------------------------------------------------
     | Farmer Chat Endpoints
     | --------------------------------------------------------- */

    public function farmerIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isFarmer() || !$user->farmer) {
            return redirect()->route('farmer.dashboard');
        }

        $farmer = $user->farmer;

        $conversations = Conversation::where('farmer_id', $farmer->id)
            ->with(['customer', 'order', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $activeConversation = null;
        $activeId = $request->query('conversation_id');

        if ($activeId) {
            $activeConversation = $conversations->firstWhere('id', (int)$activeId);
        }

        if (!$activeConversation && $conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
        }

        if ($activeConversation) {
            // Mark incoming messages as read
            Message::where('conversation_id', $activeConversation->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => Carbon::now()]);

            $activeConversation->load(['messages.sender', 'customer', 'order.items.product']);
        }

        return view('farmer.messages.index', compact('conversations', 'activeConversation', 'farmer'));
    }

    public function startFromFarmer(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isFarmer() || !$user->farmer) {
            return redirect()->route('farmer.dashboard');
        }

        $farmer = $user->farmer;

        $data = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'message' => 'nullable|string|max:1500',
            'subject' => 'nullable|string|max:200',
        ]);

        $order = !empty($data['order_id']) ? Order::find($data['order_id']) : null;
        $subject = $data['subject'] ?? ($order ? "Order #{$order->order_number} Update" : "Message from {$farmer->stall_name}");

        $query = Conversation::where('customer_id', $data['customer_id'])
            ->where('farmer_id', $farmer->id);

        if ($order) {
            $query->where('order_id', $order->id);
        }

        $conversation = $query->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'customer_id' => $data['customer_id'],
                'farmer_id' => $farmer->id,
                'order_id' => $order ? $order->id : null,
                'subject' => $subject,
                'last_message_at' => Carbon::now(),
            ]);
        }

        if (!empty($data['message'])) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'body' => trim($data['message']),
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => Carbon::now()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'redirect_url' => route('farmer.messages.index', ['conversation_id' => $conversation->id]),
            ]);
        }

        return redirect()->route('farmer.messages.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Conversation opened with customer.');
    }

    /* ---------------------------------------------------------
     | Common Send & Polling Endpoints
     | --------------------------------------------------------- */

    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = Auth::user();
        if (!$this->authorizeAccess($user, $conversation)) {
            return response()->json(['error' => 'Unauthorized access to this conversation.'], 403);
        }

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => trim($data['body']),
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => Carbon::now()]);

        // Send in-app notification to the message recipient
        $recipientId = ($conversation->customer_id === $user->id)
            ? ($conversation->farmer?->user_id)
            : $conversation->customer_id;

        if ($recipientId) {
            Notification::create([
                'user_id' => $recipientId,
                'title' => "New message from {$user->name}",
                'message' => Str::limit($message->body, 120),
                'type' => 'chat',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_id' => $message->sender_id,
                'sender_name' => $user->name,
                'is_me' => true,
                'created_at_human' => $message->created_at->diffForHumans(),
                'created_at_time' => $message->created_at->format('g:i A'),
            ],
        ]);
    }

    public function pollMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = Auth::user();
        if (!$this->authorizeAccess($user, $conversation)) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $afterId = (int)$request->query('after_id', 0);

        // Mark incoming messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => Carbon::now()]);

        $messages = Message::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($m) use ($user) {
                return [
                    'id' => $m->id,
                    'body' => $m->body,
                    'sender_id' => $m->sender_id,
                    'sender_name' => $m->sender->name ?? 'User',
                    'is_me' => $m->sender_id === $user->id,
                    'created_at_human' => $m->created_at->diffForHumans(),
                    'created_at_time' => $m->created_at->format('g:i A'),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    protected function authorizeAccess(?\App\Models\User $user, Conversation $conversation): bool
    {
        if (!$user) return false;

        if ($user->isAdmin()) return true;

        if ($conversation->customer_id === $user->id) return true;

        if ($user->isFarmer() && $user->farmer && $conversation->farmer_id === $user->farmer->id) {
            return true;
        }

        return false;
    }
}
