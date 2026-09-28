@extends('layouts.admin')

@section('title', 'Farmer Stall Details — ' . $farmer->stall_name)

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono-meta">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.farmers.index') }}" class="text-decoration-none text-muted">Farmers</a></li>
            <li class="breadcrumb-item active text-success" aria-current="page">{{ $farmer->stall_name }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="h3 fw-bold mb-0 text-dark">{{ $farmer->stall_name }}</h1>
                @if($farmer->approval_status === 'suspended')
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill font-mono-meta px-3 py-1">Suspended</span>
                @elseif($farmer->is_approved)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill font-mono-meta px-3 py-1">Approved & Active</span>
                @else
                    <span class="badge bg-warning bg-opacity-20 text-dark border border-warning-subtle rounded-pill font-mono-meta px-3 py-1">Pending Review</span>
                @endif
            </div>
            <p class="text-muted small mb-0">{{ $farmer->farm_name }} • Stall ID #{{ $farmer->id }} • Registered {{ $farmer->created_at->format('M d, Y') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('farmers.show', $farmer->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> Public Stall
            </a>
            @if($farmer->is_approved)
                <form action="{{ route('admin.farmers.suspend', $farmer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Suspend trading for this stall?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                        <i class="bi bi-pause-circle me-1"></i> Suspend Stall
                    </button>
                </form>
            @else
                <form action="{{ route('admin.farmers.approve', $farmer->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                        <i class="bi bi-check-lg me-1"></i> Approve Stall
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Details Grid -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <h5 class="fw-bold mb-3 text-dark">Stall & Contact Information</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Contact Person</span>
                        <strong class="text-dark">{{ $farmer->contact_person }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Phone Number</span>
                        <strong class="text-dark">{{ $farmer->contact_number }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Account Email</span>
                        <strong class="text-dark">{{ $farmer->user->email ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Assigned Market</span>
                        <strong class="text-success">{{ $farmer->market->name ?? 'Unassigned' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Operating Days</span>
                        <span class="text-dark small">{{ is_array($farmer->operating_days) ? implode(', ', $farmer->operating_days) : $farmer->operating_days }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Pickup Windows</span>
                        <span class="text-dark small">{{ $farmer->pickup_time_windows ?: 'Default market hours' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Stall Coordinates</span>
                        <span class="font-mono-meta small">{{ $farmer->latitude ?? '0.00' }}, {{ $farmer->longitude ?? '0.00' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Order Cutoff Lead Time</span>
                        <span class="text-dark small">{{ $farmer->cutoff_hours ?? 2 }} hours before market opening</span>
                    </div>
                </div>

                @if($farmer->bio)
                    <div class="mt-4 pt-3 border-top">
                        <span class="text-muted small d-block mb-1">Stall Bio & Story</span>
                        <p class="text-dark small bg-light p-3 rounded-3 mb-0">{{ $farmer->bio }}</p>
                    </div>
                @endif
            </div>

            <!-- Recent Products listed by farmer -->
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">Active Product Listings ({{ $farmer->products_count }})</h5>
                    <a href="{{ route('admin.moderation.products') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Catalog Moderation</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($farmer->products as $p)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $p->name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $p->category->name ?? 'Fresh' }}</span></td>
                                    <td>${{ number_format($p->price, 2) }} / {{ $p->unit }}</td>
                                    <td>{{ $p->quantity }} available</td>
                                    <td>
                                        @if($p->is_sold_out || $p->quantity <= 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">Sold Out</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No products listed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <!-- Stall Map Pin Inspection -->
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <h5 class="fw-bold mb-2 text-dark">OpenStreetMap Stall Pin</h5>
                <p class="text-muted small mb-3">Location verified for shopper GPS directions and kiosk discovery.</p>
                <div id="adminStallMap" style="height: 250px; border-radius: 12px; border: 1px solid #dee2e6;"></div>
            </div>

            <!-- Pre-order Statistics -->
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <h5 class="fw-bold mb-3 text-dark">Pre-Order History</h5>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Total Orders</span>
                    <strong class="font-mono-meta">{{ $farmer->orders_count }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Current Approval Status</span>
                    <span class="badge {{ $farmer->is_approved ? 'bg-success' : 'bg-warning text-dark' }} font-mono-meta">
                        {{ strtoupper($farmer->approval_status ?? ($farmer->is_approved ? 'approved' : 'pending')) }}
                    </span>
                </div>
                @if($farmer->rejection_reason)
                    <div class="mt-3 p-3 bg-danger bg-opacity-10 border border-danger-subtle rounded-3">
                        <small class="text-danger fw-bold d-block">Admin Moderation Note:</small>
                        <span class="text-dark small">{{ $farmer->rejection_reason }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var lat = {{ $farmer->latitude ?? 40.7128 }};
        var lng = {{ $farmer->longitude ?? -74.0060 }};
        var map = L.map('adminStallMap').setView([lat, lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>{{ addslashes($farmer->stall_name) }}</b><br>{{ addslashes($farmer->market->name ?? 'Assigned Stall') }}")
            .openPopup();
    });
</script>
@endpush
@endsection
