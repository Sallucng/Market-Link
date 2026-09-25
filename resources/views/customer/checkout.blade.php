@extends('layouts.app')

@section('title', 'Select Pickup Slots & Confirm — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-success">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pickup Booking</li>
        </ol>
    </nav>

    <div class="mb-4">
        <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Step 2: Pickup Scheduling</span>
        <h2 class="heading-serif fw-bold text-dark mb-0">Select Pickup Dates & Windows</h2>
        <p class="text-muted small">Choose your preferred collection time slot for each vendor stall.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger small py-2 mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.place') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Stall Pickup Schedulers -->
            <div class="col-lg-8">
                @foreach($groupedCart as $farmerId => $group)
                    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                            <div>
                                <span class="badge bg-success text-white mb-1"><i class="bi bi-shop me-1"></i>Vendor Stall</span>
                                <h5 class="fw-bold text-dark mb-0">{{ $group['farmer_name'] }}</h5>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $group['market_name'] }} (Open: {{ $group['operating_days'] ?: 'Weekends' }})</small>
                            </div>
                            <span class="badge bg-light text-dark border">
                                Cutoff: {{ $group['cutoff_hours'] ?: 2 }} hours before pickup
                            </span>
                        </div>

                        <!-- Products Reserved in this order -->
                        <h6 class="small fw-bold text-secondary text-uppercase mb-2">Reserved Items:</h6>
                        <div class="bg-light rounded p-3 mb-4">
                            @foreach($group['items'] as $item)
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span>{{ $item['quantity'] }}x {{ $item['name'] }} ({{ $item['unit'] }})</span>
                                    <span class="fw-semibold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pickup Date Selection (SRS §1.6) -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">
                                    <i class="bi bi-calendar-event text-success me-1"></i> Select Pickup Date:
                                </label>
                                <input type="date" name="pickup_date[{{ $farmerId }}]" 
                                       min="{{ date('Y-m-d') }}" 
                                       value="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                       class="form-control" required>
                                <small class="text-muted" style="font-size: 0.72rem;">Farmer operating days: {{ $group['operating_days'] ?: 'Saturdays, Sundays' }}</small>
                            </div>

                            <!-- Pickup Time Windows -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">
                                    <i class="bi bi-clock-history text-warning me-1"></i> Available Time Window:
                                </label>
                                @php
                                    $slots = array_map('trim', explode(',', $group['pickup_time_windows'] ?: '08:30 AM - 10:30 AM, 11:00 AM - 01:00 PM'));
                                @endphp
                                <select name="pickup_time_slot[{{ $farmerId }}]" class="form-select" required>
                                    @foreach($slots as $slot)
                                        <option value="{{ $slot }}">{{ $slot }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.72rem;">Collection slot at market stall</small>
                            </div>
                        </div>

                        <!-- Optional Stall Notes -->
                        <div>
                            <label class="form-label small fw-semibold text-dark">Stall Notes (Optional):</label>
                            <input type="text" name="notes[{{ $farmerId }}]" class="form-control form-control-sm" placeholder="e.g. Please select ripe fruits if available">
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Confirmation & Settlement Box -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm sticky-top" style="top: 80px;">
                    <h5 class="heading-serif fw-bold text-dark mb-3">Reservation Terms</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Due at Stall:</span>
                        <span class="fs-4 fw-bold text-success">${{ number_format($total, 2) }}</span>
                    </div>

                    <div class="small text-muted mb-3">
                        <i class="bi bi-check2 text-success me-1"></i> Live stock is reserved immediately.<br>
                        <i class="bi bi-check2 text-success me-1"></i> You can modify/cancel before cutoff.
                    </div>

                    <div class="p-3 bg-brand-light rounded-3 mb-4 border border-success border-opacity-25">
                        <h6 class="fw-bold mb-1 small text-dark"><i class="bi bi-wallet2 text-success me-1"></i> Strictly Pay-at-Pickup</h6>
                        <p class="small text-secondary mb-0" style="font-size: 0.75rem;">
                            No credit card or online transaction is processed. Payment is settled in cash or direct stall payment with the farmer upon collection.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-semibold shadow">
                        <i class="bi bi-check-circle me-1"></i> Confirm Pre-Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
