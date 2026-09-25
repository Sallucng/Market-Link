@extends('layouts.farmer')

@section('title', 'Manage Pre-Order #' . $order->order_number . ' — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item"><a href="{{ route('farmer.orders.index') }}" class="text-success text-decoration-none">Pre-Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Pre-Order Details</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Order #{{ $order->order_number }}</h2>
            <small class="text-muted">Reserved on {{ $order->created_at->format('M d, Y - h:i A') }}</small>
        </div>

        <!-- Current Status Badge & Print Receipt -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('farmer.orders.receipt', $order->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print / Download Receipt
            </a>
            <span class="badge 
                {{ $order->order_status === 'completed' ? 'bg-success' : '' }}
                {{ $order->order_status === 'ready_for_pickup' ? 'bg-info text-dark' : '' }}
                {{ $order->order_status === 'accepted' ? 'bg-primary' : '' }}
                {{ $order->order_status === 'placed' ? 'bg-warning text-dark' : '' }}
                {{ in_array($order->order_status, ['cancelled', 'declined']) ? 'bg-danger' : '' }}
                px-3 py-2 text-uppercase fs-6 rounded-pill">
                Status: {{ str_replace('_', ' ', $order->order_status) }}
            </span>
        </div>
    </div>

    <!-- Action Bar (Accept, Decline, Ready, Complete) (SRS §1.6) -->
    <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <span class="fw-semibold text-dark small"><i class="bi bi-gear me-1 text-success"></i> Pre-Order Fulfillment Controls:</span>

            <div class="d-flex flex-wrap gap-2">
                @if($order->order_status === 'placed')
                    <form action="{{ route('farmer.orders.status', $order->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-check2-circle me-1"></i> Accept Pre-Order
                        </button>
                    </form>

                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#declineOrderModal">
                        <i class="bi bi-slash-circle me-1"></i> Decline Pre-Order
                    </button>
                @endif

                @if($order->order_status === 'accepted')
                    <form action="{{ route('farmer.orders.status', $order->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="ready_for_pickup">
                        <button type="submit" class="btn btn-info btn-sm rounded-pill px-3 text-dark">
                            <i class="bi bi-box-seam me-1"></i> Mark Ready for Pickup
                        </button>
                    </form>
                @endif

                @if($order->order_status === 'ready_for_pickup')
                    <form action="{{ route('farmer.orders.status', $order->id) }}" method="POST" onsubmit="return confirm('Customer collected produce and settled payment?')">
                        @csrf
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                            <i class="bi bi-check-all me-1"></i> Mark Order Completed & Settled
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <h5 class="heading-serif fw-bold text-dark mb-3">Reserved Produce Items</h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $item->product->image_url ?? '' }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->product->name ?? 'Harvest Item' }}</div>
                                                <small class="text-muted">{{ $item->product->unit ?? 'unit' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold text-success">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold">Total Cash/Stall Settlement Due:</td>
                                <td class="text-end fw-bold text-success fs-5">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($order->notes)
                    <div class="mt-3 p-3 bg-light rounded small">
                        <strong>Customer Instructions:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Customer Review if present -->
            @if($order->review)
                <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                    <h5 class="heading-serif fw-bold text-dark mb-2">Customer Feedback for this Order</h5>
                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="text-dark">{{ $order->customer->name ?? 'Customer' }} says:</strong>
                            <span class="text-warning">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi {{ $i <= $order->review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </span>
                        </div>
                        <p class="text-secondary small mb-2">{{ $order->review->comment }}</p>
                        
                        @if($order->review->farmer_response)
                            <div class="bg-white p-2 rounded small ms-3 border-start border-success border-3">
                                <strong class="text-success">Your Reply:</strong> {{ $order->review->farmer_response }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Customer & Pickup Schedule Sidebar -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <h6 class="small fw-bold text-muted text-uppercase mb-3">Customer Information</h6>
                
                <h5 class="fw-bold text-dark mb-1">{{ $order->customer->name ?? 'Guest Shopper' }}</h5>
                <div class="small text-muted mb-2"><i class="bi bi-telephone me-1 text-success"></i> {{ $order->customer->contact_number ?? 'N/A' }}</div>
                <div class="small text-muted mb-3"><i class="bi bi-envelope me-1 text-primary"></i> {{ $order->customer->email ?? 'N/A' }}</div>

                @if($order->customer->address)
                    <div class="p-2 bg-light rounded small text-secondary mb-3">
                        <i class="bi bi-geo me-1"></i> Customer Address: {{ $order->customer->address }}
                    </div>
                @endif

                <hr>

                <h6 class="small fw-bold text-muted text-uppercase mb-3">Pickup Time & Location</h6>
                <div class="p-3 bg-light rounded small mb-3">
                    <div class="mb-2">
                        <i class="bi bi-calendar3 text-success me-1"></i><strong>Pickup Date:</strong>
                        <div class="ms-3">{{ $order->pickup_date->format('l, F j, Y') }}</div>
                    </div>
                    <div>
                        <i class="bi bi-clock-history text-warning me-1"></i><strong>Pickup Slot:</strong>
                        <div class="ms-3">{{ $order->pickup_time_slot }}</div>
                    </div>
                </div>

                <div class="p-3 bg-brand-light rounded small border border-success border-opacity-25">
                    <strong>Payment Rule:</strong> Cash or direct stall payment upon customer arrival.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Decline Order Modal -->
<div class="modal fade" id="declineOrderModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('farmer.orders.status', $order->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="declined">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="declineModalLabel">Decline Pre-Order #{{ $order->order_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Declining this order will restore reserved items to your stock inventory and send an in-app notice to the customer.</p>
                    <label class="form-label small fw-semibold">Reason for declining:</label>
                    <textarea name="reason" rows="3" class="form-control form-control-sm" placeholder="e.g. Stock shortage after morning harvest..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4">Confirm Decline</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
