@extends('layouts.app')

@section('title', 'Farmers Markets & Stalls Map Explorer — MarketLink')

@section('styles')
<style>
    #market-map {
        height: 600px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
    .market-sidebar {
        max-height: 600px;
        overflow-y: auto;
    }
    .market-item-card {
        cursor: pointer;
        transition: all 0.18s ease;
        border: 1px solid var(--border-hairline);
    }
    .market-item-card:hover {
        background-color: #f7f9f7 !important;
        border-color: #2b5932 !important;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header & Filter Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge-pastel-green mb-1">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i> OpenStreetMap Geolocation
            </span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Local Farmers Markets & Stalls</h2>
        </div>

        <!-- Filter by Operating Day (SRS §1.6) -->
        <div class="btn-group" role="group">
            <a href="{{ route('markets.index') }}" class="btn btn-sm {{ !$dayFilter ? 'btn-brand' : 'btn-brand-outline' }}">
                All Days
            </a>
            <a href="{{ route('markets.index', ['day' => 'Saturday']) }}" class="btn btn-sm {{ $dayFilter == 'Saturday' ? 'btn-brand' : 'btn-brand-outline' }}">
                Saturday
            </a>
            <a href="{{ route('markets.index', ['day' => 'Sunday']) }}" class="btn btn-sm {{ $dayFilter == 'Sunday' ? 'btn-brand' : 'btn-brand-outline' }}">
                Sunday
            </a>
            <a href="{{ route('markets.index', ['day' => 'Wednesday']) }}" class="btn btn-sm {{ $dayFilter == 'Wednesday' ? 'btn-brand' : 'btn-brand-outline' }}">
                Wednesday
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Interactive Leaflet Map Column -->
        <div class="col-lg-8">
            <div class="card card-custom p-2 bg-white shadow-sm">
                <div id="market-map"></div>
                <div class="p-2 d-flex justify-content-between align-items-center small text-muted">
                    <div>
                        <span class="badge bg-primary me-1"><i class="bi bi-shop"></i> Market Plaza</span>
                        <span class="badge bg-success"><i class="bi bi-geo"></i> Farmer Stall Pin</span>
                    </div>
                    <div>Click markers to view operating hours & pickup points</div>
                </div>
            </div>
        </div>

        <!-- Market & Stalls Directory Sidebar -->
        <div class="col-lg-4">
            <div class="market-sidebar pe-1">
                <h5 class="fw-bold text-dark mb-3">Markets Directory ({{ $markets->count() }})</h5>

                @forelse($markets as $market)
                    <div class="card card-custom p-3 mb-3 bg-white market-item-card" 
                         onclick="focusMarker({{ $market->latitude }}, {{ $market->longitude }}, '{{ addslashes($market->name) }}')">
                        <div class="d-flex gap-3 mb-2">
                            <img src="{{ $market->image_url ?: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=200&q=80' }}" 
                                 alt="{{ $market->name }}" 
                                 class="rounded-3 shadow-sm flex-shrink-0" 
                                 style="width: 58px; height: 58px; object-fit: cover;">
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $market->name }}</h6>
                                    <span class="badge bg-light text-dark border ms-1">{{ $market->city }}</span>
                                </div>
                                <p class="text-muted small mb-0 text-truncate"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $market->address }}</p>
                            </div>
                        </div>
                        <div class="small text-secondary mb-2">
                            <div><i class="bi bi-calendar-check text-success me-1"></i><strong>Days:</strong> {{ $market->operating_days }}</div>
                            <div><i class="bi bi-clock text-warning me-1"></i><strong>Hours:</strong> {{ $market->timings }}</div>
                        </div>

                        <!-- Attending Farmers Count -->
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-success fw-semibold">
                                <i class="bi bi-people-fill me-1"></i>{{ $market->farmers->count() }} Attending Stalls
                            </span>
                            <a href="{{ route('markets.show', $market->id) }}" class="btn btn-sm btn-brand-outline rounded-pill px-3 py-1">
                                View Market <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">No markets found for the selected day filter.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const mapData = @json($mapData);

    // Initialize Leaflet Map centered around first point or default Metropolis coordinates
    const defaultLat = mapData.length > 0 ? mapData[0].latitude : 40.7128;
    const defaultLng = mapData.length > 0 ? mapData[0].longitude : -74.0060;

    const map = L.map('market-map').setView([defaultLat, defaultLng], 13);

    // OpenStreetMap Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const markerMap = {};

    // Custom Icon styles
    const marketIcon = L.divIcon({
        className: 'custom-market-pin',
        html: `<div style="background-color:#2563eb; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 6px rgba(0,0,0,0.3); border:2px solid white;"><i class="bi bi-shop"></i></div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 34]
    });

    const farmerIcon = L.divIcon({
        className: 'custom-farmer-pin',
        html: `<div style="background-color:#16a34a; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 6px rgba(0,0,0,0.3); border:2px solid white;"><i class="bi bi-basket-fill"></i></div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });

    mapData.forEach(item => {
        const icon = item.type === 'market' ? marketIcon : farmerIcon;
        const marker = L.marker([item.latitude, item.longitude], { icon: icon }).addTo(map);

        let popupContent = '';
        if (item.type === 'market') {
            popupContent = `
                <div style="font-family:'Plus Jakarta Sans',sans-serif; min-width:200px;">
                    <span class="badge bg-primary text-white mb-1">Farmers Market</span>
                    <h6 class="fw-bold mb-1">${escapeHtml(item.name)}</h6>
                    <p class="small text-muted mb-1"><i class="bi bi-geo-alt"></i> ${escapeHtml(item.address)}</p>
                    <div class="small mb-2"><strong>Hours:</strong> ${escapeHtml(item.timings)}</div>
                    <div class="d-flex gap-2">
                        <a href="${item.url}" class="btn btn-sm btn-success text-white py-1 px-2" style="font-size:0.75rem;">View Stalls</a>
                        <a href="https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=%3B${item.latitude}%2C${item.longitude}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.75rem;">Directions <i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                </div>
            `;
        } else {
            popupContent = `
                <div style="font-family:'Plus Jakarta Sans',sans-serif; min-width:200px;">
                    <span class="badge bg-success text-white mb-1">Farmer Stall</span>
                    <h6 class="fw-bold mb-1">${escapeHtml(item.name)}</h6>
                    <p class="small text-muted mb-1"><i class="bi bi-shop"></i> Located at: ${escapeHtml(item.market_name)}</p>
                    <div class="small mb-1"><strong>Pickup Slots:</strong> ${escapeHtml(item.pickup_time_windows || 'Standard hours')}</div>
                    <div class="small mb-2 text-success font-monospace">${item.product_count} fresh items listed</div>
                    <a href="${item.url}" class="btn btn-sm btn-brand text-white w-100 py-1" style="font-size:0.75rem;">View Stall Stock</a>
                </div>
            `;
        }

        marker.bindPopup(popupContent);
        markerMap[`${item.latitude},${item.longitude}`] = marker;
    });

    function focusMarker(lat, lng, name) {
        map.setView([lat, lng], 15);
        const key = `${lat},${lng}`;
        if (markerMap[key]) {
            markerMap[key].openPopup();
        }
    }
</script>
@endsection
