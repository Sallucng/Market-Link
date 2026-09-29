@extends('layouts.farmer')

@section('title', 'Stall Profile & Geolocation — MarketLink')

@section('styles')
<style>
    #stall-picker-map {
        height: 280px;
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
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stall Profile</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                    <div class="p-3 bg-brand-light text-success rounded-circle">
                        <i class="bi bi-shop fs-3"></i>
                    </div>
                    <div>
                        <h3 class="heading-serif fw-bold text-dark mb-0">Farmer Stall & Pickup Profile</h3>
                        <small class="text-muted">Configure market location, operating days, pickup time windows, and map pin (SRS §1.6)</small>
                    </div>
                </div>

                <form action="{{ route('farmer.profile.update') }}" method="POST">
                    @csrf

                    <!-- Stall Core Info -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Stall / Business Name <span class="text-danger">*</span></label>
                            <input type="text" name="stall_name" class="form-control" value="{{ old('stall_name', $farmer->stall_name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Associated Farmers Market</label>
                            <select name="market_id" class="form-select">
                                <option value="">Select Market Location</option>
                                @foreach($markets as $m)
                                    <option value="{{ $m->id }}" {{ old('market_id', $farmer->market_id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} ({{ $m->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $farmer->contact_person) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Contact Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $farmer->contact_number) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Stall Physical Address / Spot Info <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $farmer->address) }}" placeholder="e.g. Stall #14, North Canopy Row" required>
                    </div>

                    <!-- Market Days & Pickup Windows (SRS §1.6) -->
                    <div class="card p-3 bg-light border-0 rounded-3 mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-week text-success me-1"></i> Operating Schedule & Pre-Order Windows</h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Operating Market Days <span class="text-danger">*</span></label>
                                <input type="text" name="operating_days" class="form-control form-control-sm" value="{{ old('operating_days', is_array($farmer->operating_days) ? implode(', ', $farmer->operating_days) : (string)$farmer->operating_days) }}" placeholder="e.g. Saturday, Sunday" required>
                                <small class="text-muted" style="font-size: 0.72rem;">Days when your stall is active for customer collections</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Pre-Order Cutoff Window (Hours) <span class="text-danger">*</span></label>
                                <input type="number" min="0" max="48" name="cutoff_hours" class="form-control form-control-sm" value="{{ old('cutoff_hours', $farmer->cutoff_hours ?? 2) }}" required>
                                <small class="text-muted" style="font-size: 0.72rem;">Hours before market opening when pre-orders lock (e.g. 2 hours)</small>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-dark">Available Pickup Time Slots (Comma-separated) <span class="text-danger">*</span></label>
                            <input type="text" name="pickup_time_windows" class="form-control form-control-sm" value="{{ old('pickup_time_windows', is_array($farmer->pickup_time_windows) ? implode(', ', $farmer->pickup_time_windows) : (string)$farmer->pickup_time_windows) }}" placeholder="08:00 AM - 10:00 AM, 10:30 AM - 12:30 PM, 01:00 PM - 03:00 PM" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Shoppers select from these windows at checkout</small>
                        </div>
                    </div>

                    <!-- Geolocation & Map Coordinates (SRS §1.6) -->
                    <div class="card p-3 bg-light border-0 rounded-3 mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Stall Geolocation Pin (OpenStreetMap / Google Maps)</h6>
                        <p class="text-muted small mb-3">Click on the map or drag the pin to set your exact stall pickup point for customers.</p>

                        <div id="stall-picker-map" class="mb-3 border"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Latitude</label>
                                <input type="text" id="lat-input" name="latitude" class="form-control form-control-sm" value="{{ old('latitude', $farmer->latitude ?? 40.7128) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Longitude</label>
                                <input type="text" id="lng-input" name="longitude" class="form-control form-control-sm" value="{{ old('longitude', $farmer->longitude ?? -74.0060) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Stall Bio & Growing Philosophy</label>
                        <textarea name="bio" rows="3" class="form-control" placeholder="Share your organic farming techniques, farm history...">{{ old('bio', $farmer->bio) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Stall Banner / Logo Image URL</label>
                        <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $farmer->image_url) }}" placeholder="https://images.unsplash.com/...">
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('farmer.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-brand rounded-pill px-5">Save Stall Profile</button>
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
        const latInput = document.getElementById('lat-input');
        const lngInput = document.getElementById('lng-input');

        let initLat = parseFloat(latInput.value) || 40.7128;
        let initLng = parseFloat(lngInput.value) || -74.0060;

        const map = L.map('stall-picker-map').setView([initLat, initLng], 13);
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
