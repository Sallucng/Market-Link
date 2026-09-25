@extends('layouts.app')

@section('title', 'Pre-Order #' . $order->order_number . ' — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}" class="text-success">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Pre-Order Receipt</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Order #{{ $order->order_number }}</h2>
            <small class="text-muted">Reserved on {{ $order->created_at->format('M d, Y - h:i A') }}</small>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <!-- Download PDF Receipt -->
            <a href="{{ route('customer.orders.receipt', $order->id) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF Receipt
            </a>

            <!-- 1-Click Reorder (SRS §1.6) -->
            <form action="{{ route('customer.orders.reorder', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-success rounded-pill px-3">
                    <i class="bi bi-arrow-repeat me-1"></i> Quick Re-Order
                </button>
            </form>

            <!-- Modify Button if before cutoff (SRS §1.6) -->
            @if($order->canModifyOrCancel())
                <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modifyOrderModal">
                    <i class="bi bi-pencil-square me-1"></i> Modify Order
                </button>
            @endif

            <!-- Cancel Button if before cutoff -->
            @if($order->canModifyOrCancel())
                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this pre-order? Your reserved stock will be released.')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i> Cancel Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Order Status Pipeline (SRS §1.6: placed, accepted, ready, completed) -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        <h6 class="small fw-bold text-muted text-uppercase mb-3">Order Status Progression</h6>
        
        @php
            $statuses = ['placed', 'accepted', 'ready_for_pickup', 'completed'];
            $currentIndex = array_search($order->order_status, $statuses);
            if ($order->order_status === 'cancelled' || $order->order_status === 'declined') {
                $currentIndex = -1;
            }
        @endphp

        @if($order->order_status === 'cancelled')
            <div class="alert alert-danger mb-0">
                <i class="bi bi-x-octagon-fill me-1"></i> This order was <strong>cancelled</strong> before pickup.
            </div>
        @elseif($order->order_status === 'declined')
            <div class="alert alert-dark mb-0">
                <i class="bi bi-slash-circle-fill me-1"></i> This order was <strong>declined</strong> by the farmer due to stock shortage.
            </div>
        @else
            <div class="position-relative m-4">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($currentIndex / 3) * 100 }}%;"></div>
                </div>
                <div class="d-flex justify-content-between position-absolute top-0 start-0 w-100 translate-middle-y">
                    <button type="button" class="btn btn-sm {{ $currentIndex >= 0 ? 'btn-success' : 'btn-secondary' }} rounded-pill" style="width: 2rem; height:2rem;">1</button>
                    <button type="button" class="btn btn-sm {{ $currentIndex >= 1 ? 'btn-success' : 'btn-secondary' }} rounded-pill" style="width: 2rem; height:2rem;">2</button>
                    <button type="button" class="btn btn-sm {{ $currentIndex >= 2 ? 'btn-success' : 'btn-secondary' }} rounded-pill" style="width: 2rem; height:2rem;">3</button>
                    <button type="button" class="btn btn-sm {{ $currentIndex >= 3 ? 'btn-success' : 'btn-secondary' }} rounded-pill" style="width: 2rem; height:2rem;">4</button>
                </div>
            </div>
            <div class="d-flex justify-content-between text-center small fw-semibold text-muted mt-2">
                <div style="width: 25%;">Placed</div>
                <div style="width: 25%;">Accepted</div>
                <div style="width: 25%;">Ready for Pickup</div>
                <div style="width: 25%;">Completed</div>
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <!-- Order Items Breakdown -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <h5 class="heading-serif fw-bold text-dark mb-3">Products Reserved in this Order</h5>
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
                                        <div class="fw-bold text-dark">{{ $item->product->name ?? 'Harvest Product' }}</div>
                                        <small class="text-muted">{{ $item->product->unit ?? 'unit' }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold">Total Amount Due at Pickup:</td>
                                <td class="text-end fw-bold fs-5 text-success">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($order->notes)
                    <div class="mt-3 p-3 bg-light rounded small">
                        <strong>Customer Notes:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Review Submission Form (SRS §1.6: ONLY AFTER ORDER IS COMPLETED) -->
            @if($order->order_status === 'completed')
                <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                    <h5 class="heading-serif fw-bold text-dark mb-2">Leave a Rating & Review</h5>
                    <p class="text-muted small mb-3">Since your pre-order is marked as completed, you can share feedback for the farmer!</p>

                    @if($order->review)
                        <div class="p-3 bg-light rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong class="text-dark">Your Review:</strong>
                                    @if($order->review->product)
                                        <span class="badge bg-success-subtle text-success ms-2">
                                            <i class="bi bi-tag-fill me-1"></i>Product: {{ $order->review->product->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary ms-2">
                                            <i class="bi bi-shop me-1"></i>Stall: {{ $order->farmer->stall_name }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-warning">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="bi {{ $i <= $order->review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <p class="text-secondary small mb-2">{{ $order->review->comment }}</p>
                            
                            @if($order->review->farmer_response)
                                <div class="bg-white p-2 rounded small ms-3 border-start border-success border-3">
                                    <strong class="text-success">{{ $order->farmer->stall_name }}:</strong>
                                    <span class="text-muted">{{ $order->review->farmer_response }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('customer.orders.review', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Review Target:</label>
                                <select name="product_id" class="form-select form-select-sm" style="max-width: 380px;">
                                    <option value="">Farmer Stall Overall ({{ $order->farmer->stall_name }})</option>
                                    @foreach($order->items as $item)
                                        @if($item->product)
                                            <option value="{{ $item->product->id }}">Individual Product: {{ $item->product->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="form-text small">You can rate the stall overall or review an individual purchased product (SRS §1.6).</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Star Rating (1 to 5):</label>
                                <select name="rating" class="form-select form-select-sm" style="max-width: 220px;" required>
                                    <option value="5">5 Stars — Excellent Harvest</option>
                                    <option value="4">4 Stars — Very Good</option>
                                    <option value="3">3 Stars — Average</option>
                                    <option value="2">2 Stars — Subpar</option>
                                    <option value="1">1 Star — Poor Experience</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Your Written Feedback:</label>
                                <textarea name="comment" rows="3" class="form-control form-control-sm" placeholder="How was the freshness, taste, and stall pickup experience?" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-brand btn-sm rounded-pill px-4">
                                Submit Feedback
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- Pickup Stall Details Sidebar -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <h6 class="small fw-bold text-muted text-uppercase mb-3">Pickup Location & Window</h6>
                
                <h5 class="fw-bold text-dark mb-1">{{ $order->farmer->stall_name }}</h5>
                <p class="text-muted small mb-3"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $order->farmer->address }}</p>

                <div class="p-3 bg-light rounded small mb-3">
                    <div class="mb-2">
                        <i class="bi bi-calendar-check text-success me-1"></i><strong>Pickup Date:</strong>
                        <div class="ms-3">{{ $order->pickup_date->format('l, F j, Y') }}</div>
                    </div>
                    <div>
                        <i class="bi bi-clock-history text-warning me-1"></i><strong>Pickup Window:</strong>
                        <div class="ms-3">{{ $order->pickup_time_slot }}</div>
                    </div>
                </div>

                @if($order->cutoff_time)
                    <div class="small text-muted mb-3">
                        <i class="bi bi-hourglass text-secondary me-1"></i><strong>Order Cutoff:</strong> {{ $order->cutoff_time->format('M d, h:i A') }}
                    </div>
                @endif

                <div class="p-3 bg-brand-light rounded small border border-success border-opacity-25 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Total Amount:</span>
                        <strong class="text-success fs-5">${{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Payment Status:</span>
                        @if($order->isPaid())
                            <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check2-circle me-1"></i> Paid</span>
                        @else
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-clock me-1"></i> Due at Pickup</span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem;">Settle in cash or card directly at the stall upon collecting your products.</div>
                </div>

                <!-- PDF Download CTA -->
                <a href="{{ route('customer.orders.receipt', $order->id) }}" class="btn btn-success w-100 rounded-pill py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    <span>Download PDF Receipt</span>
                </a>
            </div>
    </div>
</div>

@if($order->canModifyOrCancel())
<!-- Modify Order Modal (SRS §1.6) -->
<div class="modal fade" id="modifyOrderModal" tabindex="-1" aria-labelledby="modifyOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.orders.modify', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modifyOrderModalLabel">Modify Pre-Order #{{ $order->order_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">You can adjust your pickup date, time slot, and special requests prior to the cutoff deadline.</p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pickup Date:</label>
                        <input type="date" name="pickup_date" class="form-control form-control-sm" min="{{ date('Y-m-d') }}" value="{{ $order->pickup_date->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pickup Time Window:</label>
                        <select name="pickup_time_slot" class="form-select form-select-sm" required>
                            @php
                                $slots = is_array($order->farmer->pickup_time_windows) ? $order->farmer->pickup_time_windows : explode(',', $order->farmer->pickup_time_windows ?? '08:00 AM - 10:00 AM,10:30 AM - 12:30 PM,01:00 PM - 03:00 PM');
                            @endphp
                            @foreach($slots as $slot)
                                @php $cleanSlot = trim($slot); @endphp
                                <option value="{{ $cleanSlot }}" {{ $order->pickup_time_slot == $cleanSlot ? 'selected' : '' }}>{{ $cleanSlot }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pickup Instructions / Special Notes:</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="e.g. Please select firm tomatoes">{{ $order->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-brand btn-sm rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
