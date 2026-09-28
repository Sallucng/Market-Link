<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display the comprehensive Frequently Asked Questions page.
     */
    public function index(Request $request)
    {
        $faqs = [
            [
                'category' => 'orders',
                'category_label' => 'Pre-Orders & Pickup',
                'icon' => 'bi-basket',
                'question' => 'How does pre-ordering work on MarketLink?',
                'answer' => 'MarketLink allows community shoppers to browse fresh harvest items listed by verified local growers ahead of market day. You can add produce from multiple farmers to your pickup cart, select your preferred market pickup date and time window, and reserve your items. When you visit the farmers market, your harvest basket will be pre-packed and held waiting for you at the stall.',
            ],
            [
                'category' => 'orders',
                'category_label' => 'Pre-Orders & Pickup',
                'icon' => 'bi-wallet2',
                'question' => 'Why is payment settled in person at the stall instead of online?',
                'answer' => 'To keep 100% of harvest revenue directly in the hands of family growers, MarketLink charges zero online payment gateway markups. Online gateways (like Stripe or PayPal) siphon 2.9% + 30¢ from small farmers. By settling payment in person at the booth (via cash or the farmer’s own mobile card terminal), you pay true farm-gate prices with zero platform deductions.',
            ],
            [
                'category' => 'orders',
                'category_label' => 'Pre-Orders & Pickup',
                'icon' => 'bi-clock-history',
                'question' => 'What is the order preparation cutoff window?',
                'answer' => 'Each farmer defines a cutoff window (usually 2 to 12 hours before market opening). This gives growers dedicated time to enter their fields, harvest ripe crops, wash, package, and load produce into transport vans. Once the cutoff time passes, reservations are locked in and cannot be cancelled by shoppers.',
            ],
            [
                'category' => 'farmers',
                'category_label' => 'Farmers & Stalls',
                'icon' => 'bi-shop',
                'question' => 'How do farmers join MarketLink?',
                'answer' => 'Farmers register by selecting the "Farmer" role during signup, providing their farm stall name, market location, contact phone, and operating schedules. For community trust and safety, new farmer registrations undergo rapid administrative review before their produce catalog appears publicly.',
            ],
            [
                'category' => 'farmers',
                'category_label' => 'Farmers & Stalls',
                'icon' => 'bi-geo-alt',
                'question' => 'How are farmer stall locations mapped?',
                'answer' => 'MarketLink integrates OpenStreetMap with Leaflet.js to pinpoint market squares and physical stall coordinates. Each farmer profile lists their physical market booth number (e.g., Stall #14, West Plaza) and GPS coordinates so customers can navigate straight to the booth.',
            ],
            [
                'category' => 'farmers',
                'category_label' => 'Farmers & Stalls',
                'icon' => 'bi-tag',
                'question' => 'How do farmers manage weekly harvest inventory and flash sales?',
                'answer' => 'Farmers log into the dedicated Farmer Portal to update stock quantities in real time. If surplus produce is ready for quick sale, farmers can create Flash Sales or Stall Specials with custom discount percentages, which are highlighted with high-contrast badges across the storefront.',
            ],
            [
                'category' => 'safety',
                'category_label' => 'Safety & Moderation',
                'icon' => 'bi-shield-check',
                'question' => 'How does the customer complaint system work?',
                'answer' => 'If you encounter any issues regarding produce quality, pricing, or missed pickup windows, you can file a confidential complaint directly on the farmer’s profile or your order dashboard. The complaint is sent immediately to Platform Administrators for confidential moderation and dispute resolution, completely hidden from other public shoppers.',
            ],
            [
                'category' => 'safety',
                'category_label' => 'Safety & Moderation',
                'icon' => 'bi-star',
                'question' => 'Who is permitted to leave reviews and ratings?',
                'answer' => 'Only verified customers whose orders have been marked "Completed" by the grower upon pickup can submit a star rating and written review. This strict anti-astroturfing rule guarantees 100% genuine feedback from real community patrons.',
            ],
            [
                'category' => 'emails',
                'category_label' => 'Emails & Notifications',
                'icon' => 'bi-envelope-check',
                'question' => 'What emails will I receive from MarketLink?',
                'answer' => 'MarketLink sends clean, transactional notifications with the sender name "MarketLink": Welcome emails upon registration, Pre-Order Confirmation passes with itemized receipts, Farmer Harvest Alerts, Stall Status Updates (when your basket is packed and ready for pickup), and Farmer Approval notifications.',
            ],
            [
                'category' => 'emails',
                'category_label' => 'Emails & Notifications',
                'icon' => 'bi-inbox',
                'question' => 'Why do MarketLink emails go straight to the Primary Inbox and not Spam?',
                'answer' => 'MarketLink utilizes authenticated SMTP delivery through Google and verified SPF/DKIM cryptographic headers. Clean, semantic HTML and zero spam triggers guarantee that your order receipts, confirmation codes, and harvest notices land directly in your Primary Inbox.',
            ],
        ];

        return view('public.faq', compact('faqs'));
    }
}
