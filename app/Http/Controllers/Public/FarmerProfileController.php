<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use Illuminate\Http\Request;

class FarmerProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = Farmer::whereHas('user', function ($q) {
            $q->where('is_approved', true)->where('is_active', true);
        })->with(['market', 'reviews'])
          ->withCount(['products' => function ($q) {
              $q->where('is_available', true);
          }]);

        // Search Keyword (farm name, contact person, address, bio)
        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('stall_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('contact_person', 'LIKE', "%{$keyword}%")
                  ->orWhere('address', 'LIKE', "%{$keyword}%")
                  ->orWhere('bio', 'LIKE', "%{$keyword}%");
            });
        }

        // Market Filter
        if ($request->filled('market')) {
            $query->where('market_id', $request->input('market'));
        }

        // Operating Day Filter
        if ($request->filled('day')) {
            $day = $request->input('day');
            $query->where('operating_days', 'LIKE', "%{$day}%");
        }

        // Sorting
        switch ($request->input('sort')) {
            case 'name':
                $query->orderBy('stall_name', 'asc');
                break;
            case 'products':
                $query->orderBy('products_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $farmers = $query->paginate(9)->withQueryString();
        $markets = \App\Models\Market::orderBy('name')->get();

        return view('public.farmers', compact('farmers', 'markets'));
    }

    public function show($id)
    {
        $farmer = Farmer::whereHas('user', function ($q) {
            $q->where('is_approved', true)->where('is_active', true);
        })->with([
            'market',
            'products' => function ($q) {
                $q->where('is_available', true)->with('category');
            },
            'reviews.customer',
            'reviews.product',
        ])->findOrFail($id);

        return view('public.farmer-detail', compact('farmer'));
    }
}
