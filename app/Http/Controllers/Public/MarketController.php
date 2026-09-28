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

    /**
     * Nearby Markets Browse page — uses browser geolocation.
     */
    public function nearby(Request $request)
    {
        $lat = $request->float('lat');
        $lng = $request->float('lng');
        $radius = $request->float('radius', 25);

        $markets = null;
        $mapData = [];

        if ($lat && $lng) {
            $markets = Market::nearby($lat, $lng, $radius)
                ->with(['farmers' => function ($q) {
                    $q->whereHas('user', function ($uq) {
                        $uq->where('is_approved', true);
                    })->with('products');
                }])
                ->get();

            foreach ($markets as $market) {
                // Calculate real Haversine distance in PHP
                $market->distance_km = $this->haversineDistance($lat, $lng, $market->latitude, $market->longitude);

                $mapData[] = [
                    'type' => 'market',
                    'id' => $market->id,
                    'name' => $market->name,
                    'address' => $market->address . ', ' . $market->city,
                    'operating_days' => $market->operating_days,
                    'timings' => $market->timings,
                    'latitude' => (float) $market->latitude,
                    'longitude' => (float) $market->longitude,
                    'distance_km' => round($market->distance_km, 1),
                    'farmer_count' => $market->farmers->count(),
                    'url' => route('markets.show', $market->id),
                ];
            }
        }

        return view('public.markets-nearby', compact('markets', 'mapData', 'lat', 'lng', 'radius'));
    }

    /**
     * JSON endpoint for AJAX-powered nearby search.
     */
    public function nearbyJson(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:200',
        ]);

        $lat = $request->float('lat');
        $lng = $request->float('lng');
        $radius = $request->float('radius', 25);

        $markets = Market::nearby($lat, $lng, $radius)
            ->with(['farmers' => function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('is_approved', true);
                });
            }])
            ->get()
            ->map(function ($market) use ($lat, $lng) {
                $market->distance_km = round($this->haversineDistance($lat, $lng, $market->latitude, $market->longitude), 1);
                return [
                    'id' => $market->id,
                    'name' => $market->name,
                    'address' => $market->address . ', ' . $market->city,
                    'operating_days' => (string) $market->operating_days,
                    'timings' => $market->timings,
                    'latitude' => (float) $market->latitude,
                    'longitude' => (float) $market->longitude,
                    'distance_km' => $market->distance_km,
                    'farmer_count' => $market->farmers->count(),
                    'url' => route('markets.show', $market->id),
                ];
            });

        return response()->json(['markets' => $markets]);
    }

    /**
     * Haversine formula: distance between two lat/lng pairs in km.
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
