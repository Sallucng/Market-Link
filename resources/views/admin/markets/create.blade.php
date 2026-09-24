@extends('layouts.admin')

@section('title', 'Add New Farmers Market — Admin MarketLink')

@section('styles')
<style>
    #market-admin-map {
        height: 260px;
        border-radius: 12px;
        z-index: 1;
    }
</style>
@endsection

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.markets.index') }}" class="text-success text-decoration-none">Farmers Markets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Market</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                    <div class="p-3 bg-brand-light text-success rounded-circle">
                        <i class="bi bi-geo-alt-fill fs-3 text-danger"></i>
                    </div>
                    <div>
                        <h3 class="heading-serif fw-bold text-dark mb-0">Register Farmers Market</h3>
                        <small class="text-muted">Add a physical market plaza, schedule, and map coordinates (SRS §1.6)</small>
                    </div>
                </div>

                <form action="{{ route('admin.markets.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-dark">Market Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Downtown Farmers Market Plaza" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">City / Region <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', 'New York') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Physical Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="e.g. Union Square West & 17th St" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Operating Days <span class="text-danger">*</span></label>
                            <input type="text" name="operating_days" class="form-control" value="{{ old('operating_days', 'Saturday, Sunday') }}" placeholder="e.g. Saturday, Sunday" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Operating Timings <span class="text-danger">*</span></label>
                            <input type="text" name="timings" class="form-control" value="{{ old('timings', '08:00 AM - 02:00 PM') }}" placeholder="e.g. 08:00 AM - 02:00 PM" required>
                        </div>
                    </div>

                    <!-- Coordinates Map Picker -->
                    <div class="card p-3 bg-light border-0 rounded-3 mb-3">
                        <label class="form-label small fw-bold text-dark mb-1"><i class="bi bi-pin-map text-danger me-1"></i> Market Geolocation Pin (Click map to position)</label>
                        <div id="market-admin-map" class="mb-3 border"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Latitude</label>
                                <input type="text" id="m-lat" name="latitude" class="form-control form-control-sm" value="{{ old('latitude', '40.7359') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Longitude</label>
                                <input type="text" id="m-lng" name="longitude" class="form-control form-control-sm" value="{{ old('longitude', '-73.9911') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Community market description, parking access, amenities...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Image URL (Optional)</label>
                        <input type="url" name="image_url" class="form-control" value="{{ old('image_url') }}" placeholder="https://images.unsplash.com/...">
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('admin.markets.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-brand rounded-pill px-5">Save Market</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const latInput = document.getElementById('m-lat');
        const lngInput = document.getElementById('m-lng');

        let initLat = parseFloat(latInput.value) || 40.7359;
        let initLng = parseFloat(lngInput.value) || -73.9911;

        const map = L.map('market-admin-map').setView([initLat, initLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

        marker.on('dragend', function (e) {
            const pos = marker.getLatLng();
            latInput.value = pos.lat.toFixed(6);
            lngInput.value = pos.lng.toFixed(6);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            latInput.value = e.latlng.lat.toFixed(6);
            lngInput.value = e.latlng.lng.toFixed(6);
        });
    });
</script>
@endsection
