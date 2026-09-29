@extends('layouts.app')

@section('title', 'Pre-Order Pickup Booking — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-success text-decoration-none">Pickup Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout & Pickup Scheduling</li>
        </ol>
    </nav>

    <div class="mb-4">
        <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Pre-Order Reservation</span>
        <h2 class="heading-serif fw-bold text-dark mb-1">Pre-Order Confirmation & Pickup Booking</h2>
        <p class="text-muted small mb-0">Verify your customer contact details and vendor stall pickup windows before reserving fresh harvest.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger small py-2 mb-4 border-0 shadow-xs rounded-3">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please correct the following errors:</div>
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
            <!-- Left Column: Customer Info & Vendor/Stall Info -->
            <div class="col-lg-8">

                <!-- 1. CUSTOMER INFORMATION CARD (As Requested) -->
                <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-brand-light p-2 text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-person-check-fill fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 font-mono-meta text-uppercase tracking-wider">Customer Information</h5>
                                <small class="text-muted">Verified account holder profile</small>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill small">
                            <i class="bi bi-patch-check-fill me-1"></i> Verified Customer
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border border-light h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Customer Full Name</span>
                                <div class="fw-bold text-dark">{{ $customer->name ?? Auth::user()->name }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border border-light h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Email Address</span>
                                <div class="fw-bold text-dark text-truncate">{{ $customer->email ?? Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border border-light h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Contact Phone</span>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-telephone text-success me-1"></i>
                                    {{ $customer->contact_number ?? (Auth::user()->contact_number ?? 'Not provided') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border border-light h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Registered Address / City</span>
                                <div class="fw-bold text-dark text-truncate">
                                    <i class="bi bi-geo-alt text-danger me-1"></i>
                                    {{ $customer->address ?? (Auth::user()->address ?? 'Metropolis Community Member') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 text-muted small d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success"></i>
                        <span>Order reservation receipts and stall pickup notifications will be routed to this profile.</span>
                    </div>
                </div>

                <!-- 2. VENDOR & STALL INFORMATION CARDS (For each vendor in cart) -->
                @foreach($groupedCart as $farmerId => $group)
                    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                        <!-- Vendor & Stall Header -->
                        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-amber-50 p-2 text-amber-700 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fef3c7; color: #b45309;">
                                    <i class="bi bi-shop fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0 font-mono-meta text-uppercase tracking-wider">Vendor & Stall Information</h5>
                                    <small class="text-muted">{{ $group['farmer_name'] }}</small>
                                </div>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded">
                                <i class="bi bi-hourglass-split me-1"></i> Cutoff: {{ $group['cutoff_hours'] ?: 2 }} hrs prior
                            </span>
                        </div>

                        <!-- Stall Details Grid -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border border-light h-100">
                                    <div class="mb-2">
                                        <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.7rem;">Stall Name</span>
                                        <strong class="text-dark fs-6">{{ $group['farmer_name'] }}</strong>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.7rem;">Grower / Contact Person</span>
                                        <span class="text-dark fw-semibold">{{ $group['contact_person'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border border-light h-100">
                                    <div class="mb-2">
                                        <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.7rem;">Host Market Plaza</span>
                                        <strong class="text-success">{{ $group['market_name'] }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.7rem;">Stall Location</span>
                                        <span class="text-dark">{{ $group['stall_address'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.7rem;">Operating Days</span>
                                        <span class="badge bg-white text-dark border">{{ is_array($group['operating_days']) ? implode(', ', $group['operating_days']) : ($group['operating_days'] ?: 'Saturday, Sunday') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reserved Harvest Items for this stall -->
                        <h6 class="small fw-bold text-secondary text-uppercase mb-2">
                            <i class="bi bi-basket2 text-success me-1"></i> Reserved Items for this Stall:
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.88rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Harvest Item</th>
                                        <th class="text-center" style="width: 80px;">Qty</th>
                                        <th class="text-center" style="width: 100px;">Unit</th>
                                        <th class="text-end" style="width: 100px;">Price</th>
                                        <th class="text-end" style="width: 110px;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $stallSubtotal = 0; @endphp
                                    @foreach($group['items'] as $item)
                                        @php $lineTotal = $item['price'] * $item['quantity']; $stallSubtotal += $lineTotal; @endphp
                                        <tr>
                                            <td>
                                                <strong class="text-dark">{{ $item['name'] }}</strong>
                                            </td>
                                            <td class="text-center fw-bold">{{ $item['quantity'] }}</td>
                                            <td class="text-center text-muted small">{{ $item['unit'] }}</td>
                                            <td class="text-end text-muted">${{ number_format($item['price'], 2) }}</td>
                                            <td class="text-end fw-semibold text-success">${{ number_format($lineTotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th colspan="4" class="text-end small text-uppercase">Stall Pre-Order Subtotal:</th>
                                        <th class="text-end fw-bold text-success">${{ number_format($stallSubtotal, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pickup Date & Window Selection (SRS §1.6) -->
                        <div class="p-3 bg-brand-light rounded-3 border border-success border-opacity-25 mb-3">
                            <h6 class="fw-bold text-dark small mb-3">
                                <i class="bi bi-calendar-check text-success me-1"></i> Choose Your Pickup Slot for {{ $group['farmer_name'] }}:
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">
                                        Pickup Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="pickup_date[{{ $farmerId }}]" 
                                           min="{{ date('Y-m-d') }}" 
                                           value="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                           class="form-control form-control-sm bg-white" 
                                           required>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                        Open Days: <strong>{{ is_array($group['operating_days']) ? implode(', ', $group['operating_days']) : ($group['operating_days'] ?: 'Saturday, Sunday') }}</strong>
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">
                                        Available Time Window <span class="text-danger">*</span>
                                    </label>
                                    @php
                                        $pickupWindowsStr = is_array($group['pickup_time_windows']) ? implode(',', $group['pickup_time_windows']) : ($group['pickup_time_windows'] ?: '08:30 AM - 10:30 AM, 11:00 AM - 01:00 PM');
                                        $slots = array_map('trim', explode(',', $pickupWindowsStr));
                                    @endphp
                                    <select name="pickup_time_slot[{{ $farmerId }}]" class="form-select form-select-sm bg-white" required>
                                        @foreach($slots as $slot)
                                            <option value="{{ $slot }}">{{ $slot }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                        Collect directly from stall counter
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Optional Stall Notes -->
                        <div>
                            <label class="form-label small fw-semibold text-dark mb-1">
                                Stall Instructions / Notes (Optional):
                            </label>
                            <input type="text" 
                                   name="notes[{{ $farmerId }}]" 
                                   class="form-control form-control-sm" 
                                   placeholder="e.g. Please pack in paper bag or prepare ripe produce if available">
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Right Column: Settlement & Terms Card -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm sticky-top" style="top: 84px;">
                    <div class="d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                        <div class="rounded-circle bg-brand-light p-2 text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-receipt fs-5"></i>
                        </div>
                        <div>
                            <h5 class="heading-serif fw-bold text-dark mb-0">Pre-Order Summary</h5>
                            <small class="text-muted">Total Due at Stall</small>
                        </div>
                    </div>

                    <div class="vstack gap-2 mb-3">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Total Items Reserved:</span>
                            <span class="fw-semibold text-dark">{{ count(session('cart', [])) }} items</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Participating Stalls:</span>
                            <span class="fw-semibold text-dark">{{ count($groupedCart) }} stall(s)</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Platform Reservation Fee:</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">FREE ($0.00)</span>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Total Due at Pickup:</span>
                            <span class="fs-4 fw-bold text-success">${{ number_format($total, 2) }}</span>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                            Payable directly to the farmer(s) at pickup
                        </small>
                    </div>

                    <!-- Strictly Pay-at-Pickup Policy Banner -->
                    <div class="p-3 bg-brand-light rounded-3 mb-4 border border-success border-opacity-25">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-wallet2 text-success fs-5 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1 small text-dark">Strictly Pay-at-Pickup</h6>
                                <p class="small text-secondary mb-0" style="font-size: 0.75rem; line-height: 1.35;">
                                    No upfront online credit card transaction is processed. Payment is settled in cash or card directly with each grower when you collect your order.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Confirm Pre-Order Reservation</span>
                    </button>

                    <div class="text-center mt-3">
                        <a href="{{ route('cart.index') }}" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Return to Shopping Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
