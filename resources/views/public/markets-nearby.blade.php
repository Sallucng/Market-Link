@extends('layouts.app')

@section('title', 'Nearby Markets — MarketLink')

@section('content')
<style>
    #nearbyMap .leaflet-control-attribution,
    .leaflet-control-attribution {
        display: none !important;
    }
</style>
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('markets.index') }}" class="text-success">Markets & Map</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nearby Markets</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Location-Based Discovery</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Markets Near You</h2>
            <p class="text-muted small mb-0">Find farmers markets within walking or driving distance using your device's location.</p>
        </div>
        <a href="{{ route('markets.index') }}" class="btn btn-outline-success rounded-pill px-3">
            <i class="bi bi-grid-3x3-gap me-1"></i> View All Markets
        </a>
    </div>

    <!-- Location Controls -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div id="locationStatus" class="d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm text-success d-none" id="locationSpinner" role="status">
                        <span class="visually-hidden">Finding your location...</span>
                    </div>
                    <i class="bi bi-geo-alt-fill text-success fs-5" id="locationIcon"></i>
                    <div>
                        <div class="fw-semibold text-dark" id="locationLabel">Enable Location to Find Nearby Markets</div>
                        <small class="text-muted" id="locationDetail">Your location stays private — it's only used to calculate distances.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Search Radius</label>
                <select id="radiusSelect" class="form-select form-select-sm">
                    <option value="5" {{ ($radius ?? 25) == 5 ? 'selected' : '' }}>5 km</option>
                    <option value="10" {{ ($radius ?? 25) == 10 ? 'selected' : '' }}>10 km</option>
                    <option value="25" {{ ($radius ?? 25) == 25 ? 'selected' : '' }}>25 km</option>
                    <option value="50" {{ ($radius ?? 25) == 50 ? 'selected' : '' }}>50 km</option>
                    <option value="100" {{ ($radius ?? 25) == 100 ? 'selected' : '' }}>100 km</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" id="locateMeBtn" class="btn btn-brand rounded-pill w-100 mt-md-4" onclick="requestLocation()">
                    <i class="bi bi-crosshair me-1"></i> Use My Location
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Map Column -->
        <div class="col-lg-7">
            <div class="card card-custom bg-white border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
                <div id="nearbyMap" style="height: 480px; background: #f0f0f0;"></div>
            </div>
        </div>

        <!-- Results Column -->
        <div class="col-lg-5">
            <div id="marketsResultsArea">
                @if($markets !== null && $markets->count() > 0)
                    <!-- Server-rendered results (when accessed with ?lat=&lng= params) -->
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-pin-map text-success me-1"></i>
                        {{ $markets->count() }} market{{ $markets->count() !== 1 ? 's' : '' }} found within {{ $radius ?? 25 }} km
                    </h6>
                    @foreach($markets as $market)
                        <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-3" style="border-left: 4px solid #1b4332;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $market->name }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ $market->address }}, {{ $market->city }}
                                    </p>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-clock text-warning me-1"></i>{{ $market->timings }}
                                        &nbsp;·&nbsp;
                                        <i class="bi bi-calendar3 text-info me-1"></i>{{ $market->operating_days }}
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-people text-primary me-1"></i>{{ $market->farmers->count() }} farmer{{ $market->farmers->count() !== 1 ? 's' : '' }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    @if(isset($market->distance_km))
                                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded-pill" style="font-size: 0.78rem;">
                                            {{ round($market->distance_km, 1) }} km
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2 d-flex gap-2">
                                <a href="{{ route('markets.show', $market->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> View Market
                                </a>
                            </div>
                        </div>
                    @endforeach
                @elseif($markets !== null && $markets->count() === 0)
                    <div class="card card-custom p-4 text-center bg-white border-0 shadow-sm">
                        <i class="bi bi-geo display-4 text-muted mb-2"></i>
                        <h5 class="heading-serif fw-bold">No Markets Within Range</h5>
                        <p class="text-muted small">Try increasing your search radius or <a href="{{ route('markets.index') }}" class="text-success">browse all markets</a>.</p>
                    </div>
                @else
                    <!-- Default state: waiting for location -->
                    <div class="card card-custom p-5 text-center bg-white border-0 shadow-sm" id="awaitingLocation">
                        <i class="bi bi-compass display-4 text-success mb-3"></i>
                        <h5 class="heading-serif fw-bold">Discover Your Local Markets</h5>
                        <p class="text-muted small col-10 mx-auto">
                            Click <strong>"Use My Location"</strong> to find farmers markets near you. We'll show the closest markets sorted by distance.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let nearbyMap = null;
    let markerGroup = null;
    let userMarker = null;

    // Initialize Leaflet map
    document.addEventListener('DOMContentLoaded', function() {
        nearbyMap = L.map('nearbyMap', {
            center: [{{ $lat ?: 40.7128 }}, {{ $lng ?: -74.0060 }}],
            zoom: {{ ($lat && $lng) ? 12 : 4 }},
            scrollWheelZoom: true,
            attributionControl: false,
        });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(nearbyMap);

        markerGroup = L.layerGroup().addTo(nearbyMap);

        @if($lat && $lng)
            // Show user position
            addUserMarker({{ $lat }}, {{ $lng }});
            
            // Plot server-rendered markets
            const serverMarkets = @json($mapData);
            plotMarkets(serverMarkets);
        @endif
    });

    function addUserMarker(lat, lng) {
        if (userMarker) nearbyMap.removeLayer(userMarker);
        const icon = L.divIcon({
            html: '<div style="width:18px;height:18px;background:#1b4332;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [18, 18],
            className: ''
        });
        userMarker = L.marker([lat, lng], { icon: icon, zIndexOffset: 1000 })
            .addTo(nearbyMap)
            .bindPopup('<strong>Your Location</strong>');
    }

    function plotMarkets(markets) {
        markerGroup.clearLayers();
        markets.forEach(m => {
            const icon = L.divIcon({
                html: `<div style="width:32px;height:32px;background:#16a34a;border:2px solid white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 8px rgba(0,0,0,0.25);color:white;font-size:14px;"><i class="bi bi-shop"></i></div>`,
                iconSize: [32, 32],
                className: ''
            });
            L.marker([m.latitude, m.longitude], { icon: icon })
                .addTo(markerGroup)
                .bindPopup(`
                    <div style="min-width:180px;">
                        <strong>${m.name}</strong><br>
                        <small class="text-muted">${m.address}</small><br>
                        <small><i class="bi bi-clock"></i> ${m.timings || 'Check schedule'}</small><br>
                        ${m.distance_km ? `<span class="badge bg-success mt-1">${m.distance_km} km away</span><br>` : ''}
                        <a href="${m.url}" class="btn btn-sm btn-outline-success mt-1 rounded-pill px-2">View Market</a>
                    </div>
                `);
        });
    }

    function requestLocation() {
        if (!navigator.geolocation) {
            document.getElementById('locationLabel').textContent = 'Geolocation Not Supported';
            document.getElementById('locationDetail').textContent = 'Your browser does not support location services.';
            return;
        }

        const spinner = document.getElementById('locationSpinner');
        const icon = document.getElementById('locationIcon');
        const label = document.getElementById('locationLabel');
        const detail = document.getElementById('locationDetail');
        const btn = document.getElementById('locateMeBtn');

        spinner.classList.remove('d-none');
        icon.classList.add('d-none');
        label.textContent = 'Finding your location...';
        detail.textContent = 'Please allow location access when prompted.';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const radius = document.getElementById('radiusSelect').value;

                spinner.classList.add('d-none');
                icon.classList.remove('d-none');
                label.textContent = `Location Found: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                detail.textContent = 'Searching for nearby markets...';

                // AJAX fetch nearby markets
                fetch(`{{ route('markets.nearby.json') }}?lat=${lat}&lng=${lng}&radius=${radius}`)
                    .then(r => r.json())
                    .then(data => {
                        btn.disabled = false;
                        detail.textContent = `${data.markets.length} market${data.markets.length !== 1 ? 's' : ''} found within ${radius} km.`;

                        // Update map
                        addUserMarker(lat, lng);
                        const marketsWithUrl = data.markets.map(m => ({...m, url: m.url}));
                        plotMarkets(marketsWithUrl);

                        // Fit bounds
                        if (data.markets.length > 0) {
                            const bounds = L.latLngBounds([[lat, lng]]);
                            data.markets.forEach(m => bounds.extend([m.latitude, m.longitude]));
                            nearbyMap.fitBounds(bounds.pad(0.15));
                        } else {
                            nearbyMap.setView([lat, lng], 12);
                        }

                        // Render results list
                        renderResults(data.markets, radius);
                    })
                    .catch(err => {
                        btn.disabled = false;
                        detail.textContent = 'Failed to fetch nearby markets. Try again.';
                        console.error(err);
                    });
            },
            function(error) {
                spinner.classList.add('d-none');
                icon.classList.remove('d-none');
                btn.disabled = false;
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        label.textContent = 'Location Access Denied';
                        detail.textContent = 'Please enable location permissions in your browser settings.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        label.textContent = 'Location Unavailable';
                        detail.textContent = 'Your device could not determine its position.';
                        break;
                    case error.TIMEOUT:
                        label.textContent = 'Location Timeout';
                        detail.textContent = 'Location request timed out. Try again.';
                        break;
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
        );
    }

    function renderResults(markets, radius) {
        const area = document.getElementById('marketsResultsArea');
        if (markets.length === 0) {
            area.innerHTML = `
                <div class="card card-custom p-4 text-center bg-white border-0 shadow-sm">
                    <i class="bi bi-geo display-4 text-muted mb-2"></i>
                    <h5 class="heading-serif fw-bold">No Markets Within ${radius} km</h5>
                    <p class="text-muted small">Try increasing your search radius or <a href="{{ route('markets.index') }}" class="text-success">browse all markets</a>.</p>
                </div>`;
            return;
        }

        let html = `<h6 class="fw-bold text-dark mb-3">
            <i class="bi bi-pin-map text-success me-1"></i>
            ${markets.length} market${markets.length !== 1 ? 's' : ''} found within ${radius} km
        </h6>`;

        markets.forEach(m => {
            html += `
            <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-3" style="border-left: 4px solid #1b4332;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">${escapeHtml(m.name)}</h6>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-geo-alt text-danger me-1"></i>${escapeHtml(m.address)}
                        </p>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-clock text-warning me-1"></i>${m.timings || 'Check schedule'}
                            &nbsp;·&nbsp;
                            <i class="bi bi-calendar3 text-info me-1"></i>${m.operating_days || 'Various'}
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-people text-primary me-1"></i>${m.farmer_count} farmer${m.farmer_count !== 1 ? 's' : ''}
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded-pill" style="font-size: 0.78rem;">
                            ${m.distance_km} km
                        </span>
                    </div>
                </div>
                <div class="mt-2 d-flex gap-2">
                    <a href="${m.url}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bi bi-eye me-1"></i> View Market
                    </a>
                </div>
            </div>`;
        });

        area.innerHTML = html;
    }

    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    // Auto-search when radius changes
    document.getElementById('radiusSelect').addEventListener('change', function() {
        if (userMarker) {
            const ll = userMarker.getLatLng();
            const radius = this.value;
            document.getElementById('locationDetail').textContent = 'Updating search radius...';

            fetch(`{{ route('markets.nearby.json') }}?lat=${ll.lat}&lng=${ll.lng}&radius=${radius}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('locationDetail').textContent = `${data.markets.length} market${data.markets.length !== 1 ? 's' : ''} found within ${radius} km.`;
                    plotMarkets(data.markets);
                    renderResults(data.markets, radius);

                    if (data.markets.length > 0) {
                        const bounds = L.latLngBounds([[ll.lat, ll.lng]]);
                        data.markets.forEach(m => bounds.extend([m.latitude, m.longitude]));
                        nearbyMap.fitBounds(bounds.pad(0.15));
                    }
                });
        }
    });
</script>
@endsection
