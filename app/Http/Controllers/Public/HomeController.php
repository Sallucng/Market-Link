<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Farmer;
use App\Models\Market;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // 1. Fetch featured sale chosen by Admin (or latest active as graceful fallback)
        $featuredSale = Sale::with(['farmer.user', 'farmer.market', 'products'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereHas('farmer.user', function ($q) {
                $q->where('is_approved', true)->where('is_active', true);
            })
            ->first();

        if (!$featuredSale) {
            $featuredSale = Sale::with(['farmer.user', 'farmer.market', 'products'])
                ->where('is_active', true)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereHas('farmer.user', function ($q) {
                    $q->where('is_approved', true)->where('is_active', true);
                })
                ->latest()
                ->first();
        }

        $announcements = Announcement::where('is_active', true)->latest()->take(3)->get();
        $categories = Category::withCount('products')->get();
        $markets = Market::withCount(['farmers' => function ($q) {
            $q->whereHas('user', function ($uq) {
                $uq->where('is_approved', true);
            });
        }])->get();

        $featuredProducts = Product::where('is_available', true)
            ->where('is_sold_out', false)
            ->whereHas('farmer.user', function ($q) {
                $q->where('is_approved', true)->where('is_active', true);
            })
            ->with(['farmer', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'farmers' => Farmer::whereHas('user', fn($q) => $q->where('is_approved', true))->count(),
            'markets' => Market::count(),
            'products' => Product::where('is_available', true)->count(),
        ];

        // Top 10 Rated Active Approved Farmers for Homepage Carousel
        $topFarmers = Farmer::where('is_approved', true)
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->withCount(['reviews', 'products'])
            ->withAvg('reviews', 'rating')
            ->with(['market', 'user'])
            ->orderByRaw('COALESCE(reviews_avg_rating, 4.8) DESC')
            ->orderByDesc('products_count')
            ->take(10)
            ->get();

        return view('public.home', compact('announcements', 'categories', 'markets', 'featuredProducts', 'stats', 'featuredSale', 'topFarmers'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }
}
