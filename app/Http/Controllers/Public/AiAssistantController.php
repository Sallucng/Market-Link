<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    /**
     * Handle incoming queries to the AI Assistant.
     */
    public function query(Request $request)
    {
        try {
            $userMessage = trim($request->input('message', ''));

            if (empty($userMessage)) {
                return response()->json([
                    'reply' => "Hello! I am your **MarketLink AI Assistant**. Ask me anything about local farmers markets, stall locations, seasonal produce, prices, customer-farmer messaging, or how our pre-order pickup works!"
                ]);
            }

            // 1. Attempt Real Google Gemini AI API Call
            $geminiReply = $this->callGeminiWithContext($userMessage);

            if (!empty($geminiReply)) {
                return response()->json([
                    'reply' => $geminiReply,
                    'source' => 'gemini'
                ]);
            }

            // 2. Graceful Fallback to Local Database Search Engine
            return response()->json([
                'reply' => $this->localFallbackReply($userMessage),
                'source' => 'local_fallback'
            ]);
        } catch (\Throwable $e) {
            Log::error("AiAssistantController exception: " . $e->getMessage());
            return response()->json([
                'reply' => "I am here to help you navigate MarketLink! You can ask about our local farmers markets, seasonal produce, vendor stalls, or pre-order pickup times. What would you like to explore today?",
                'source' => 'safe_fallback'
            ]);
        }
    }

    /**
     * Call Google Gemini API with real-time website database context.
     */
    protected function callGeminiWithContext(string $userMessage): ?string
    {
        $primaryKey = config('services.gemini.key');
        $fallbackKey = config('services.gemini.fallback_key');

        $keys = array_filter([$primaryKey, $fallbackKey]);
        if (empty($keys)) {
            return null;
        }

        $systemPrompt = $this->buildFullWebsiteContext();
        $models = ['gemini-2.5-flash', 'gemini-3.8-flash', 'gemini-flash-latest'];

        foreach ($keys as $key) {
            foreach ($models as $model) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $key;

                    $payload = [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    [
                                        'text' => $systemPrompt . "\n\nUser Question: " . $userMessage
                                    ]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.35,
                            'maxOutputTokens' => 800,
                            'topP' => 0.95
                        ]
                    ];

                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => json_encode($payload),
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            'Accept: application/json'
                        ],
                        CURLOPT_TIMEOUT => 12,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);
                    curl_close($ch);

                    if ($curlError) {
                        Log::warning("Gemini curl error on $model: " . $curlError);
                        continue;
                    }

                    if ($httpCode === 200) {
                        $json = json_decode($response, true);
                        $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if (!empty($reply)) {
                            return trim($reply);
                        }
                    } else {
                        Log::info("Gemini $model HTTP $httpCode on key " . substr($key, 0, 8) . "...");
                    }
                } catch (\Throwable $e) {
                    Log::error("Gemini exception on $model: " . $e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * Assemble 360-degree real-time database context for the AI Assistant.
     */
    protected function buildFullWebsiteContext(): string
    {
        // 1. Markets
        $markets = Market::all();
        $marketLines = [];
        foreach ($markets as $m) {
            $farmerCount = Farmer::where('market_id', $m->id)
                ->whereHas('user', fn($q) => $q->where('is_approved', true))
                ->count();
            $marketLines[] = "- **{$m->name}** (Location: {$m->address}, {$m->city}): Open {$m->operating_days} ({$m->timings}). Active farm stalls: {$farmerCount}.";
        }
        $marketsContext = implode("\n", $marketLines);

        // 2. Approved Farmers & Stalls
        $farmers = Farmer::whereHas('user', fn($q) => $q->where('is_approved', true))
            ->with(['user', 'market'])
            ->get();
        $farmerLines = [];
        foreach ($farmers as $f) {
            $marketName = $f->market->name ?? 'Downtown Farmers Plaza';
            $farmerLines[] = "- **{$f->stall_name}** | Contact: {$f->contact_person} | Market: {$marketName} | Pickup Window: {$f->pickup_time_windows} | Phone: {$f->contact_number} | Bio: {$f->bio}";
        }
        $farmersContext = implode("\n", $farmerLines);

        // 3. Available Products & Inventory
        $products = Product::where('is_available', true)
            ->whereHas('farmer.user', fn($q) => $q->where('is_approved', true))
            ->with(['category', 'farmer.market'])
            ->get();
        $productLines = [];
        foreach ($products as $p) {
            $catName = $p->category->name ?? 'Produce';
            $stall = $p->farmer->stall_name ?? 'Local Farm';
            $market = $p->farmer->market->name ?? 'Market';
            $productLines[] = "- **{$p->name}** (\${$p->price} per {$p->unit}) | Category: {$catName} | Stock: {$p->stock_quantity} {$p->unit} | Stall: {$stall} ({$market}) | Description: {$p->description}";
        }
        $productsContext = implode("\n", $productLines);

        // 4. Categories
        $categories = Category::pluck('name')->toArray();
        $categoriesContext = implode(', ', $categories);

        // 5. Current User Info
        $currentUserInfo = "Guest Visitor (not logged in)";
        if (Auth::check()) {
            $u = Auth::user();
            $currentUserInfo = "Logged in User: {$u->name} ({$u->email}), Role: {$u->role}";
            if ($u->isFarmer() && $u->farmer) {
                $currentUserInfo .= ", Stall: '{$u->farmer->stall_name}' at market '{$u->farmer->market?->name}'";
            }
        }

        // 6. Complete System Prompt & Knowledge Guide
        return <<<PROMPT
You are the official MarketLink AI Assistant. You have full and comprehensive knowledge of the MarketLink farmers market platform. Your duty is to help and guide every kind of user (shoppers/customers, farmers/vendors, market administrators, and visitors).

### CRITICAL PLATFORM RULES & BOUNDARIES (STRICT SRS REQUIREMENTS):
1. **NO ONLINE PAYMENT GATEWAY / NO CARD CHARGES ON WEBSITE**:
   - MarketLink uses a **pay-in-person settlement model**. Customers place pre-orders online for free, and **pay directly to the farmer at the stall upon pickup** (using cash or stall payment methods).
   - If asked about credit cards, Stripe, PayPal, or online payment, clearly and politely inform the user that all orders are settled in person at the stall during pickup.
2. **PICKUP ONLY / NO HOME DELIVERY**:
   - MarketLink is exclusively for **in-person pickup at the farmers market** venue. There is no home shipping or delivery courier. Customers reserve products to guarantee availability and pick up their bag at the market during the designated time slot.
3. **CUSTOMER ↔ FARMER DIRECT CHAT SYSTEM**:
   - Customers and farmers can message each other directly!
   - Shoppers can click **"Message Stall"** on any farmer's stall page, or chat directly from their pre-order details page, or view their inbox at [Customer Messages](/customer/messages).
   - Farmers can view, answer inquiries, and coordinate pickups from their vendor portal at [Farmer Messages](/farmer/messages).
4. **ORDER LIFECYCLE**:
   - Order Status flow: **Placed** -> **Accepted** (farmer confirms harvest capacity) -> **Ready for Pickup** (packaged at stall) -> **Completed** (customer pays and collects at the stall). (Can also be Cancelled if requested).
   - Farmers have order cutoff hours before market open so they can pack fresh harvest.
5. **VERIFIED REVIEWS**:
   - Customers can rate (1-5 stars) and write reviews for stalls after completing their orders.

### LIVE SYSTEM DIRECTORY & DATABASE INFORMATION:

**AVAILABLE PRODUCT CATEGORIES:**
{$categoriesContext}

**PHYSICAL FARMERS MARKETS:**
{$marketsContext}

**ACTIVE & APPROVED FARMERS / STALLS:**
{$farmersContext}

**ACTIVE PRODUCT CATALOG & PRICES:**
{$productsContext}

**CURRENT USER CONTEXT:**
{$currentUserInfo}

### PLATFORM NAVIGATION & ASSISTANCE LINKS:
- Browse Markets: [Markets Directory](/markets)
- Browse Products: [Products Catalog](/products)
- Customer Pre-Orders: [My Orders](/customer/orders)
- Customer Direct Messages: [Customer Messages](/customer/messages)
- Farmer Backoffice: [Farmer Dashboard](/farmer/dashboard)
- Farmer Inventory: [Product Stock](/farmer/products)
- Farmer Inquiries: [Farmer Messages](/farmer/messages)
- Admin Console: [Admin Portal](/admin/dashboard)

### INSTRUCTIONS FOR YOUR REPLIES:
- Answer with warmth, precision, and clarity.
- Format responses nicely using Markdown (bullet points, bold text, and clickable markdown links).
- Always recommend specific stalls, products, prices, and markets from the database when relevant.
- If a user asks a general question, offer helpful next steps or relevant links.
- Keep responses concise (under 250 words) unless the user asks for detailed step-by-step instructions.
PROMPT;
    }

    /**
     * Fallback database search engine if Gemini API is unreachable.
     */
    protected function localFallbackReply(string $message): string
    {
        $lower = strtolower($message);

        // 1. Payment or Delivery Questions (Strictly enforce SRS boundaries)
        if (str_contains($lower, 'payment') || str_contains($lower, 'pay') || str_contains($lower, 'card') || str_contains($lower, 'cash') || str_contains($lower, 'stripe')) {
            return "🛒 **Payment Information:** Pre-orders placed on MarketLink are strictly **settled in person at pickup** (cash or direct stall payment). No online credit card or payment gateway is required on our website!";
        }

        if (str_contains($lower, 'deliver') || str_contains($lower, 'shipping') || str_contains($lower, 'home') || str_contains($lower, 'courier')) {
            return "📍 **Pickup Only:** MarketLink connects you directly with farmers at local markets for **in-person pickup**. Courier or home delivery is not supported — please select a convenient pickup time slot when pre-ordering.";
        }

        // 2. Chat or Message Questions
        if (str_contains($lower, 'chat') || str_contains($lower, 'message') || str_contains($lower, 'contact') || str_contains($lower, 'communicate') || str_contains($lower, 'talk')) {
            return "💬 **Customer ↔ Farmer Direct Chat:**\n" .
                   "• **Shoppers:** You can message any stall owner directly by clicking the **'Message Stall'** button on their profile or from your [Customer Messages](/customer/messages).\n" .
                   "• **Farmers:** You can respond to customer inquiries and coordinate pickups from your [Farmer Messages Inbox](/farmer/messages).";
        }

        // 3. Market Timings and Locations
        if (str_contains($lower, 'time') || str_contains($lower, 'hour') || str_contains($lower, 'when') || str_contains($lower, 'market') || str_contains($lower, 'where') || str_contains($lower, 'location')) {
            $markets = Market::all();
            $info = "🎪 **Local Farmers Markets & Schedules:**\n";
            foreach ($markets as $m) {
                $info .= "• **{$m->name}** ({$m->address}, {$m->city}): Open {$m->operating_days} from {$m->timings}.\n";
            }
            $info .= "\nExplore them all on our [Markets Page](/markets)!";
            return $info;
        }

        // 4. Specific Product Search
        $products = Product::where('is_available', true)
            ->whereHas('farmer.user', fn($q) => $q->where('is_approved', true))
            ->with(['farmer.market', 'category'])
            ->get();

        $matchingProducts = $products->filter(function ($p) use ($lower) {
            return str_contains($lower, strtolower($p->name)) ||
                   str_contains(strtolower($p->name), $lower) ||
                   str_contains($lower, strtolower($p->category->name ?? ''));
        });

        if ($matchingProducts->count() > 0) {
            $reply = "🌱 **Found matching fresh products:**\n";
            foreach ($matchingProducts->take(4) as $p) {
                $marketName = $p->farmer->market->name ?? 'Local Market';
                $reply .= "• **{$p->name}** — \${$p->price} / {$p->unit} (Stall: **{$p->farmer->stall_name}**, {$marketName})\n";
            }
            $reply .= "\nYou can pre-order these directly on our [Products Page](/products)!";
            return $reply;
        }

        // 5. Farmer Inquiries
        if (str_contains($lower, 'farmer') || str_contains($lower, 'stall') || str_contains($lower, 'who') || str_contains($lower, 'vendor')) {
            $farmers = Farmer::whereHas('user', fn($q) => $q->where('is_approved', true))->with('market')->get();
            $reply = "👨‍🌾 **Attending Farmers & Stalls:**\n";
            foreach ($farmers as $f) {
                $reply .= "• **{$f->stall_name}** ({$f->contact_person}) — Market: {$f->market->name}. Pickup Windows: {$f->pickup_time_windows}\n";
            }
            return $reply;
        }

        // Default Helpful Response
        return "I'd be glad to help! You can ask me anything about MarketLink:\n" .
               "• *'What time is Downtown Farmers Market open?'*\n" .
               "• *'Where can I find fresh organic tomatoes or honey?'*\n" .
               "• *'How do I message a farmer about an order?'*\n" .
               "• *'How does in-person pickup and payment work?'*";
    }
}
