<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $markets = Market::all();

        $query = Product::where('is_available', true)
            ->whereHas('farmer.user', function ($q) {
                $q->where('is_approved', true)->where('is_active', true);
            })
            ->with(['farmer.market', 'category']);

        // Search Keyword across product name, description, category, stall name, and market name
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%")
                  ->orWhereHas('category', function ($cq) use ($keyword) {
                      $cq->where('name', 'LIKE', "%{$keyword}%");
                  })
                  ->orWhereHas('farmer', function ($fq) use ($keyword) {
                      $fq->where('stall_name', 'LIKE', "%{$keyword}%")
                         ->orWhere('contact_person', 'LIKE', "%{$keyword}%")
                         ->orWhereHas('market', function ($mq) use ($keyword) {
                             $mq->where('name', 'LIKE', "%{$keyword}%");
                         });
                  });
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Market Filter
        if ($request->filled('market')) {
            $query->whereHas('farmer', function ($q) use ($request) {
                $q->where('market_id', $request->input('market'));
            });
        }

        // Day Filter
        if ($request->filled('day')) {
            $day = $request->input('day');
            $query->whereHas('farmer.market', function ($q) use ($day) {
                $q->where('operating_days', 'LIKE', "%{$day}%");
            });
        }

        // In-Stock Only Filter
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Min & Max Price Filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // Sort
        switch ($request->input('sort')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('public.products', compact('products', 'categories', 'markets'));
    }

    /**
     * Fast live search JSON endpoint for global search bar & mobile drawer
     */
    public function liveSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([
                'products' => [],
                'farmers' => [],
                'markets' => [],
                'total' => 0
            ]);
        }

        $products = Product::where('is_available', true)
            ->whereHas('farmer.user', function ($uq) {
                $uq->where('is_approved', true)->where('is_active', true);
            })
            ->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%");
            })
            ->with(['category', 'farmer'])
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => '$' . number_format($p->price, 2),
                    'category' => $p->category->name ?? '',
                    'farmer' => $p->farmer->stall_name ?? '',
                    'image' => $p->image_url ?: 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=300&q=80',
                    'in_stock' => $p->stock_quantity > 0,
                    'url' => route('products.show', $p->id),
                ];
            });

        $farmers = \App\Models\Farmer::whereHas('user', function ($uq) {
                $uq->where('is_approved', true)->where('is_active', true);
            })
            ->where(function ($sub) use ($q) {
                $sub->where('stall_name', 'LIKE', "%{$q}%")
                    ->orWhere('contact_person', 'LIKE', "%{$q}%");
            })
            ->take(3)
            ->get()
            ->map(function ($f) {
                return [
                    'id' => $f->id,
                    'name' => $f->stall_name,
                    'contact_person' => $f->contact_person,
                    'url' => route('farmers.show', $f->id),
                ];
            });

        $markets = Market::where('name', 'LIKE', "%{$q}%")
            ->orWhere('city', 'LIKE', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'city' => $m->city,
                    'days' => $m->operating_days,
                    'url' => route('markets.show', $m->id),
                ];
            });

        return response()->json([
            'products' => $products,
            'farmers' => $farmers,
            'markets' => $markets,
            'total' => $products->count() + $farmers->count() + $markets->count()
        ]);
    }

    public function show($id)
    {
        $product = Product::whereHas('farmer.user', function ($q) {
            $q->where('is_approved', true)->where('is_active', true);
        })->with(['farmer.market', 'category', 'reviews.customer'])->findOrFail($id);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->whereHas('farmer.user', fn($q) => $q->where('is_approved', true))
            ->take(4)
            ->get();

        return view('public.product-detail', compact('product', 'relatedProducts'));
    }
}
