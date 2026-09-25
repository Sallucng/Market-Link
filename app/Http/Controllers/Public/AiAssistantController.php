<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function query(Request $request)
    {
        $message = trim(strtolower($request->input('message', '')));

        if (empty($message)) {
            return response()->json([
                'reply' => "Hello! I am your MarketLink AI Assistant. Ask me about market locations, operating hours, farmer availability, product prices, or pickup instructions!"
            ]);
        }

        // 1. Payment or Delivery Questions (Strictly enforce SRS boundaries)
        if (str_contains($message, 'payment') || str_contains($message, 'pay') || str_contains($message, 'card') || str_contains($message, 'cash')) {
            return response()->json([
                'reply' => "🛒 **Payment Information:** Pre-orders placed on MarketLink are strictly **settled in person at pickup** (cash or direct stall payment). No online credit card or payment gateway is required!"
            ]);
        }

        if (str_contains($message, 'deliver') || str_contains($message, 'shipping') || str_contains($message, 'home')) {
            return response()->json([
                'reply' => "📍 **Pickup Only:** MarketLink connects you directly with farmers at local markets for **in-person pickup**. Courier or home delivery is not supported — please select a convenient pickup time slot when pre-ordering."
            ]);
        }

        // 2. Market Timings and Locations
        if (str_contains($message, 'time') || str_contains($message, 'hours') || str_contains($message, 'when') || str_contains($message, 'market') || str_contains($message, 'location')) {
            $markets = Market::all();
            $info = "🎪 **Local Farmers Markets & Schedules:**\n";
            foreach ($markets as $m) {
                $info .= "• **{$m->name}** ({$m->address}): Open {$m->operating_days} from {$m->timings}.\n";
            }
            return response()->json(['reply' => $info]);
        }

        // 3. Specific Product Search
        $products = Product::where('is_available', true)
            ->whereHas('farmer.user', fn($q) => $q->where('is_approved', true))
            ->with(['farmer.market'])
            ->get();

        $matchingProducts = $products->filter(function ($p) use ($message) {
            return str_contains($message, strtolower($p->name)) ||
                   str_contains(strtolower($p->name), $message) ||
                   str_contains($message, strtolower($p->category->name ?? ''));
        });

        if ($matchingProducts->count() > 0) {
            $reply = "🌱 **Found matching products:**\n";
            foreach ($matchingProducts->take(4) as $p) {
                $marketName = $p->farmer->market->name ?? 'Local Market';
                $reply .= "• **{$p->name}** — \${$p->price} / {$p->unit} (Available at: {$p->farmer->stall_name}, {$marketName})\n";
            }
            $reply .= "\nYou can pre-order these directly on our [Products Page](/products)!";
            return response()->json(['reply' => $reply]);
        }

        // 4. Farmer Inquiries
        if (str_contains($message, 'farmer') || str_contains($message, 'stall') || str_contains($message, 'who')) {
            $farmers = Farmer::whereHas('user', fn($q) => $q->where('is_approved', true))->with('market')->get();
            $reply = "👨‍🌾 **Attending Farmers:**\n";
            foreach ($farmers as $f) {
                $reply .= "• **{$f->stall_name}** (Managed by {$f->contact_person}) — Market: {$f->market->name}. Pickup Windows: {$f->pickup_time_windows}\n";
            }
            return response()->json(['reply' => $reply]);
        }

        // Default Helpful Response
        return response()->json([
            'reply' => "I'd be glad to help! You can ask me:\n" .
                       "• *'What time is the Downtown Market open?'*\n" .
                       "• *'Where can I find fresh tomatoes?'*\n" .
                       "• *'Who are the attending farmers?'*\n" .
                       "• *'How does pickup and payment work?'*"
        ]);
    }
}
