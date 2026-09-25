@extends('layouts.app')

@section('title', 'Farmers Markets and Stalls Map Explorer — MarketLink')

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
    .hover-shadow {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-shadow:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,0.12) !important;
        transform: translateY(-1px);
        background-color: #f0f7f2 !important;
        border-color: #74c69d !important;
    }
    .hover-success:hover {
        color: var(--brand-primary) !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="mb-3">
        <span class="badge-pastel-green mb-1">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i> OpenStreetMap Geolocation
        </span>
        <h2 class="heading-serif fw-bold text-dark mb-0">Local Farmers Markets and Stalls</h2>
    </div>

    <!-- Search, Location & Day Filter Bar -->
    <div class="card card-custom p-3 mb-4 bg-white">
        <form action="{{ route('markets.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-lg-4 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           name="q" 
                           class="form-control border-start-0 ps-0" 
                           placeholder="Search markets or stalls..." 
                           value="{{ request('q') }}">
                    <button type="submit" class="btn btn-brand">Search</button>
                    @if(request('q'))
                        <a href="{{ route('markets.index', array_filter(['day' => $dayFilter, 'city' => $cityFilter])) }}" class="btn btn-light border" title="Clear Search">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <select name="city" class="form-select" onchange="this.form.submit()">
                    <option value="">All Locations / Cities</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ ($cityFilter ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-5 col-md-4 d-flex justify-content-md-end align-items-center gap-1 flex-wrap">
                <span class="small text-muted fw-semibold me-1"><i class="bi bi-calendar3 me-1 text-success"></i> Day:</span>
                <div class="btn-group" role="group">
                    <a href="{{ route('markets.index', array_filter(['q' => request('q'), 'city' => $cityFilter])) }}" class="btn btn-sm {{ !$dayFilter ? 'btn-brand' : 'btn-brand-outline' }}">
                        All
                    </a>
                    <a href="{{ route('markets.index', array_filter(['day' => 'Saturday', 'q' => request('q'), 'city' => $cityFilter])) }}" class="btn btn-sm {{ $dayFilter == 'Saturday' ? 'btn-brand' : 'btn-brand-outline' }}">
                        Sat
                    </a>
                    <a href="{{ route('markets.index', array_filter(['day' => 'Sunday', 'q' => request('q'), 'city' => $cityFilter])) }}" class="btn btn-sm {{ $dayFilter == 'Sunday' ? 'btn-brand' : 'btn-brand-outline' }}">
                        Sun
                    </a>
                    <a href="{{ route('markets.index', array_filter(['day' => 'Wednesday', 'q' => request('q'), 'city' => $cityFilter])) }}" class="btn btn-sm {{ $dayFilter == 'Wednesday' ? 'btn-brand' : 'btn-brand-outline' }}">
                        Wed
                    </a>
                </div>
            </div>
            @if($dayFilter)
                <input type="hidden" name="day" value="{{ $dayFilter }}">
            @endif
        </form>
    </div>

    <!-- Top 10 Marketplaces Showcase (SRS §1.6 & Featured Venues) -->
    <div class="mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-3">
            <div>
                <span class="badge-pastel-green mb-1">
                    <i class="bi bi-trophy-fill text-warning me-1"></i> Regional Destinations
                </span>
                <h3 class="heading-serif fw-bold text-dark mb-1">Top 10 Premier Marketplaces</h3>
                <p class="text-muted small mb-0">Browse top community farmers markets. Click any market to view venue details or click attending farmer stall chips to jump directly to their profile.</p>
            </div>
            <div class="small text-muted font-monospace">
                Showing {{ $topMarkets->count() }} Featured Marketplaces
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 g-3">
            @foreach($topMarkets as $top)
                <div class="col">
                    <div class="card card-custom h-100 p-3 bg-white border shadow-sm position-relative">
                        <div class="d-flex gap-3">
                            <div class="position-relative flex-shrink-0">
                                <img src="{{ $top->image_url ?: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=300&q=80' }}" 
                                     alt="{{ $top->name }}" 
                                     class="rounded-3 shadow-sm" 
                                     style="width: 105px; height: 105px; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-1">
                                    @if($loop->iteration === 1)
                                        <span class="badge shadow-sm" style="background: linear-gradient(135deg, #d4af37, #f6e05e); color: #1a202c; font-weight: 800; font-size: 0.72rem;">#1 Top</span>
                                    @elseif($loop->iteration === 2)
                                        <span class="badge shadow-sm" style="background: linear-gradient(135deg, #a0aec0, #edf2f7); color: #1a202c; font-weight: 800; font-size: 0.72rem;">#2</span>
                                    @elseif($loop->iteration === 3)
                                        <span class="badge shadow-sm" style="background: linear-gradient(135deg, #dd6b20, #fbd38d); color: #1a202c; font-weight: 800; font-size: 0.72rem;">#3</span>
                                    @else
                                        <span class="badge bg-dark bg-opacity-75 text-white font-monospace" style="font-size: 0.7rem;">#{{ $loop->iteration }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h5 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 1.05rem;">
                                        <a href="{{ route('markets.show', $top->id) }}" class="text-dark text-decoration-none hover-success" title="View {{ $top->name }} details">
                                            {{ $top->name }}
                                        </a>
                                    </h5>
                                    <span class="badge bg-light text-dark border ms-1">{{ $top->city }}</span>
                                </div>

                                <p class="text-muted small mb-1 text-truncate">
                                    <i class="bi bi-geo-alt text-danger me-1"></i>{{ $top->address }}
                                </p>

                                <div class="small text-secondary mb-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-calendar-check text-success me-1"></i>{{ $top->operating_days }} &bull; <i class="bi bi-clock text-warning me-1"></i>{{ $top->timings }}
                                </div>

                                <!-- Attending Farmers Stalls (Clickable to Farmer Profiles) -->
                                <div class="pt-2 border-top">
                                    <div class="small fw-semibold text-dark mb-1 d-flex align-items-center justify-content-between" style="font-size: 0.76rem;">
                                        <span><i class="bi bi-shop me-1 text-success"></i> Attending Farmers ({{ $top->farmers->count() }}):</span>
                                        <span class="text-muted" style="font-size: 0.70rem;">Click to view stall</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($top->farmers as $farmer)
                                            <a href="{{ route('farmers.show', $farmer->id) }}" 
                                               class="badge bg-light text-dark border py-1 px-2 rounded-pill text-decoration-none d-inline-flex align-items-center gap-1 hover-shadow" 
                                               style="transition: all 0.2s ease;" 
                                               title="Visit {{ $farmer->stall_name }} profile and inventory">
                                                <img src="{{ $farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=60&q=80' }}" 
                                                     class="rounded-circle" style="width: 16px; height: 16px; object-fit: cover;">
                                                <span class="fw-medium" style="font-size: 0.72rem;">{{ Str::limit($farmer->stall_name, 20) }}</span>
                                                <i class="bi bi-arrow-right-short text-success" style="font-size: 0.75rem;"></i>
                                            </a>
                                        @empty
                                            <span class="text-muted small fst-italic" style="font-size: 0.72rem;">Stalls open for vendor bookings</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Bottom Actions -->
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-3 border-top">
                            <button type="button" 
                                    onclick="focusMarker({{ $top->latitude }}, {{ $top->longitude }}, '{{ addslashes($top->name) }}')" 
                                    class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-secondary" 
                                    style="font-size: 0.76rem;" 
                                    title="Locate on Leaflet Map">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Pin on Map
                            </button>
                            <a href="{{ route('markets.show', $top->id) }}" class="btn btn-sm btn-brand rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                View Market <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Map & All Markets Section -->
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <span class="badge-pastel-green mb-1">
                <i class="bi bi-map me-1 text-success"></i> Interactive Discovery
            </span>
            <h4 class="heading-serif fw-bold text-dark mb-0">Explore All Neighborhood Venues</h4>
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
                    <div>Click markers to view operating hours and pickup points</div>
                </div>
            </div>
        </div>

        <!-- Market and Stalls Directory Sidebar -->
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

                        <!-- Attending Farmers Chips -->
                        @if($market->farmers->count() > 0)
                            <div class="pt-2 border-top mb-2">
                                <div class="small fw-semibold text-dark mb-1" style="font-size: 0.72rem;">Attending Growers:</div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($market->farmers as $farmer)
                                        <a href="{{ route('farmers.show', $farmer->id) }}" 
                                           onclick="event.stopPropagation();"
                                           class="badge bg-light text-dark border p-1 pe-2 rounded-pill text-decoration-none d-inline-flex align-items-center gap-1 hover-shadow" 
                                           style="font-size: 0.70rem;" 
                                           title="Visit {{ $farmer->stall_name }} profile">
                                            <img src="{{ $farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=60&q=80' }}" 
                                                 class="rounded-circle" style="width: 14px; height: 14px; object-fit: cover;">
                                            <span>{{ Str::limit($farmer->stall_name, 16) }}</span>
                                            <i class="bi bi-arrow-right-short text-success"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Attending Farmers Count & Action -->
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-success fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-people-fill me-1"></i>{{ $market->farmers->count() }} Attending Stalls
                            </span>
                            <a href="{{ route('markets.show', $market->id) }}" onclick="event.stopPropagation();" class="btn btn-sm btn-brand-outline rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                View Market <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="card card-custom p-4 text-center bg-white border-0">
                        <i class="bi bi-search text-muted fs-2 mb-2"></i>
                        <h6 class="fw-bold">No markets found</h6>
                        <p class="text-muted small mb-2">No markets match your search or day filter.</p>
                        <a href="{{ route('markets.index') }}" class="btn btn-sm btn-brand">View All Markets</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(s) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s];
        });
    }

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
            let farmersHtml = '';
            if (item.farmers && item.farmers.length > 0) {
                farmersHtml = `<div class="mt-2 pt-1 border-top"><strong class="small text-dark" style="font-size:0.72rem;">Attending Stalls:</strong><div class="d-flex flex-wrap gap-1 mt-1">`;
                item.farmers.forEach(f => {
                    farmersHtml += `<a href="${f.url}" class="badge bg-light text-dark border text-decoration-none py-1 px-2 rounded-pill" style="font-size:0.70rem;">${escapeHtml(f.name)} &rarr;</a>`;
                });
                farmersHtml += `</div></div>`;
            }

            popupContent = `
                <div style="font-family:'Plus Jakarta Sans',sans-serif; min-width:220px;">
                    <span class="badge bg-primary text-white mb-1">Farmers Market</span>
                    <h6 class="fw-bold mb-1"><a href="${item.url}" class="text-dark text-decoration-none hover-success">${escapeHtml(item.name)}</a></h6>
                    <p class="small text-muted mb-1"><i class="bi bi-geo-alt"></i> ${escapeHtml(item.address)}</p>
                    <div class="small mb-1"><strong>Hours:</strong> ${escapeHtml(item.timings)}</div>
                    ${farmersHtml}
                    <div class="d-flex gap-2 mt-2 pt-2 border-top">
                        <a href="${item.url}" class="btn btn-sm btn-success text-white py-1 px-2 flex-grow-1 text-center" style="font-size:0.75rem;">Explore Market</a>
                        <a href="https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=%3B${item.latitude}%2C${item.longitude}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.75rem;">Directions <i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                </div>
            `;
        } else {
            popupContent = `
                <div style="font-family:'Plus Jakarta Sans',sans-serif; min-width:200px;">
                    <span class="badge bg-success text-white mb-1">Farmer Stall</span>
                    <h6 class="fw-bold mb-1"><a href="${item.url}" class="text-dark text-decoration-none hover-success">${escapeHtml(item.name)}</a></h6>
                    <p class="small text-muted mb-1"><i class="bi bi-shop"></i> Located at: ${escapeHtml(item.market_name)}</p>
                    <div class="small mb-1"><strong>Pickup Slots:</strong> ${escapeHtml(item.pickup_time_windows || 'Standard hours')}</div>
                    <div class="small mb-2 text-success font-monospace">${item.product_count} fresh items listed</div>
                    <a href="${item.url}" class="btn btn-sm btn-brand text-white w-100 py-1" style="font-size:0.75rem;">View Stall Profile & Stock</a>
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
