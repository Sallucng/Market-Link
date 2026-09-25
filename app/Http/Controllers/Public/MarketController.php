<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        $dayFilter = $request->input('day');
        $cityFilter = $request->input('city');
        $search = $request->input('q');

        $query = Market::with(['farmers' => function ($q) {
            $q->whereHas('user', function ($uq) {
                $uq->where('is_approved', true);
            })->with('products');
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhereHas('farmers', function ($fq) use ($search) {
                      $fq->where('stall_name', 'LIKE', "%{$search}%")
                         ->orWhere('contact_person', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($dayFilter) {
            $query->where('operating_days', 'LIKE', "%{$dayFilter}%");
        }

        if ($cityFilter) {
            $query->where('city', $cityFilter);
        }

        $markets = $query->get();
        $cities = Market::distinct()->orderBy('city')->pluck('city');

        // Prepare JSON for Leaflet map markers
        $mapData = [];
        foreach ($markets as $market) {
            $mapData[] = [
                'type' => 'market',
                'id' => $market->id,
                'name' => $market->name,
                'address' => $market->address . ', ' . $market->city,
                'operating_days' => $market->operating_days,
                'timings' => $market->timings,
                'latitude' => (float)$market->latitude,
                'longitude' => (float)$market->longitude,
                'farmer_count' => $market->farmers->count(),
                'url' => route('markets.show', $market->id),
                'farmers' => $market->farmers->map(function ($f) {
                    return [
                        'id' => $f->id,
                        'name' => $f->stall_name,
                        'url' => route('farmers.show', $f->id),
                    ];
                })->values()->all(),
            ];

            foreach ($market->farmers as $farmer) {
                if ($farmer->latitude && $farmer->longitude) {
                    $mapData[] = [
                        'type' => 'farmer',
                        'id' => $farmer->id,
                        'name' => $farmer->stall_name,
                        'contact_person' => $farmer->contact_person,
                        'market_name' => $market->name,
                        'address' => $farmer->address,
                        'operating_days' => $farmer->operating_days ?: $market->operating_days,
                        'pickup_time_windows' => $farmer->pickup_time_windows,
                        'latitude' => (float)$farmer->latitude,
                        'longitude' => (float)$farmer->longitude,
                        'product_count' => $farmer->products->where('is_available', true)->count(),
                        'url' => route('farmers.show', $farmer->id),
                    ];
                }
            }
        }

        $topMarkets = Market::with(['farmers' => function ($q) {
            $q->whereHas('user', function ($uq) {
                $uq->where('is_approved', true);
            })->with('products');
        }])->withCount('farmers')->orderByDesc('farmers_count')->take(10)->get();

        return view('public.markets', compact('markets', 'topMarkets', 'mapData', 'dayFilter', 'cityFilter', 'cities', 'search'));
    }

    public function show($id)
    {
        $market = Market::with(['farmers' => function ($q) {
            $q->whereHas('user', function ($uq) {
                $uq->where('is_approved', true);
            })->with(['products' => function ($pq) {
                $pq->where('is_available', true)->with('category');
            }]);
        }])->findOrFail($id);

        return view('public.market-detail', compact('market'));
    }
}
