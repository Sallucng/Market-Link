@extends('layouts.app')

@section('title', 'My Pre-Orders — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Pre-Orders</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Customer Dashboard</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">My Pre-Order History</h2>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-brand btn-sm rounded-pill px-3">
            <i class="bi bi-cart-plus me-1"></i> Order More Produce
        </a>
    </div>

    @if($orders->count() > 0)
        <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-secondary">
                        <tr>
                            <th>Order #</th>
                            <th>Vendor Stall & Market</th>
                            <th>Pickup Date & Slot</th>
                            <th class="text-center">Total (At Pickup)</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark font-monospace">{{ $order->order_number }}</span>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Placed {{ $order->created_at->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $order->farmer->stall_name }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $order->market->name ?? 'Market Stall' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><i class="bi bi-calendar3 text-success me-1"></i>{{ $order->pickup_date->format('M d, Y') }}</div>
                                    <small class="text-secondary"><i class="bi bi-clock text-warning me-1"></i>{{ $order->pickup_time_slot }}</small>
                                </td>
                                <td class="text-center fw-bold text-dark">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusBadges = [
                                            'placed' => 'bg-secondary text-white',
                                            'accepted' => 'bg-info text-dark',
                                            'ready_for_pickup' => 'bg-warning text-dark',
                                            'completed' => 'bg-success text-white',
                                            'cancelled' => 'bg-danger text-white',
                                            'declined' => 'bg-dark text-white',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusBadges[$order->order_status] ?? 'bg-secondary' }} px-2 py-1 text-uppercase" style="font-size: 0.72rem;">
                                        {{ str_replace('_', ' ', $order->order_status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- View Details -->
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-light border" title="View Details">
                                            <i class="bi bi-eye"></i> Details
                                        </a>

                                        <!-- Download PDF Receipt -->
                                        <a href="{{ route('customer.orders.receipt', $order->id) }}" class="btn btn-sm btn-outline-success" title="Download PDF Receipt">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </a>

                                        <!-- Quick Reorder Button (SRS §1.6) -->
                                        <form action="{{ route('customer.orders.reorder', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Quick Re-Order Items">
                                                <i class="bi bi-arrow-repeat"></i> Re-Order
                                            </button>
                                        </form>

                                        <!-- Cancel Order (before cutoff) -->
                                        @if($order->canModifyOrCancel())
                                            <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this pre-order? Reserved stock will be restored.')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel Pre-Order">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="card card-custom p-5 text-center bg-white border-0 shadow-sm">
            <i class="bi bi-box-seam display-4 text-muted mb-3"></i>
            <h4 class="heading-serif fw-bold">No Pre-Orders Yet</h4>
            <p class="text-muted small col-md-6 mx-auto mb-4">
                You haven't reserved any produce yet. Discover what local growers are harvesting this week and place your first pre-order!
            </p>
            <div>
                <a href="{{ route('products.index') }}" class="btn btn-brand rounded-pill px-4">Browse Produce Catalog</a>
            </div>
        </div>
    @endif
</div>
@endsection
