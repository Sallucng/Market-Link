@extends('layouts.farmer')

@section('title', 'Manage Customer Pre-Orders — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pre-Orders Queue</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Stall Fulfillment Queue</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Customer Pre-Orders</h2>
            <small class="text-muted">{{ $farmer->stall_name }} &bull; Review, pack, and mark orders ready for stall pickup</small>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="card card-custom p-2 bg-white border-0 shadow-sm mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link {{ !request('status') ? 'active' : 'text-secondary' }}" href="{{ route('farmer.orders.index') }}">
                    All Orders <span class="badge {{ !request('status') ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['all'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'placed' ? 'active' : 'text-secondary' }}" href="{{ route('farmer.orders.index', ['status' => 'placed']) }}">
                    Newly Placed <span class="badge {{ request('status') === 'placed' ? 'bg-light text-dark' : 'bg-warning text-dark' }} ms-1">{{ $counts['placed'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'accepted' ? 'active' : 'text-secondary' }}" href="{{ route('farmer.orders.index', ['status' => 'accepted']) }}">
                    Accepted <span class="badge {{ request('status') === 'accepted' ? 'bg-light text-dark' : 'bg-primary' }} ms-1">{{ $counts['accepted'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'ready_for_pickup' ? 'active' : 'text-secondary' }}" href="{{ route('farmer.orders.index', ['status' => 'ready_for_pickup']) }}">
                    Ready for Pickup <span class="badge {{ request('status') === 'ready_for_pickup' ? 'bg-light text-dark' : 'bg-info text-dark' }} ms-1">{{ $counts['ready'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'completed' ? 'active' : 'text-secondary' }}" href="{{ route('farmer.orders.index', ['status' => 'completed']) }}">
                    Completed <span class="badge {{ request('status') === 'completed' ? 'bg-light text-dark' : 'bg-success' }} ms-1">{{ $counts['completed'] }}</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Orders List -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        @if($orders->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted display-4"></i>
                <h5 class="fw-bold mt-3 mb-1">No Orders Found</h5>
                <p class="text-muted small">No pre-orders matching the selected status filter.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Order #</th>
                            <th>Customer & Contact</th>
                            <th>Pickup Date & Slot</th>
                            <th>Reserved Items</th>
                            <th>Total Due</th>
                            <th>Status</th>
                            <th class="text-end">Fulfillment Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $ord)
                            <tr>
                                <td>
                                    <a href="{{ route('farmer.orders.show', $ord->id) }}" class="fw-bold text-success text-decoration-none">
                                        {{ $ord->order_number }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $ord->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold small text-dark">{{ $ord->customer->name ?? 'Guest Shopper' }}</div>
                                    <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $ord->customer->contact_number ?? 'N/A' }}</div>
                                    <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $ord->customer->email ?? '' }}</div>
                                </td>
                                <td class="small">
                                    <div class="fw-semibold text-dark"><i class="bi bi-calendar3 me-1 text-success"></i>{{ $ord->pickup_date->format('M d, Y') }}</div>
                                    <div class="text-muted"><i class="bi bi-clock me-1 text-warning"></i>{{ $ord->pickup_time_slot }}</div>
                                </td>
                                <td>
                                    <div class="small">
                                        @foreach($ord->items->take(2) as $item)
                                            <div>&bull; {{ $item->quantity }}x {{ $item->product->name ?? 'Harvest Item' }}</div>
                                        @endforeach
                                        @if($ord->items->count() > 2)
                                            <span class="text-muted" style="font-size: 0.72rem;">+{{ $ord->items->count() - 2 }} more items</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-success fs-6">${{ number_format($ord->total_amount, 2) }}</span>
                                    <div class="text-muted" style="font-size: 0.68rem;">Pay at pickup</div>
                                </td>
                                <td>
                                    <span class="badge 
                                        {{ $ord->order_status === 'completed' ? 'bg-success' : '' }}
                                        {{ $ord->order_status === 'ready_for_pickup' ? 'bg-info text-dark' : '' }}
                                        {{ $ord->order_status === 'accepted' ? 'bg-primary' : '' }}
                                        {{ $ord->order_status === 'placed' ? 'bg-warning text-dark' : '' }}
                                        {{ in_array($ord->order_status, ['cancelled', 'declined']) ? 'bg-danger' : '' }}
                                        rounded-pill text-uppercase" style="font-size: 0.72rem;">
                                        {{ str_replace('_', ' ', $ord->order_status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        @if($ord->order_status === 'placed')
                                            <!-- Accept Action (SRS §1.6) -->
                                            <form action="{{ route('farmer.orders.status', $ord->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="Accept Order">
                                                    Accept
                                                </button>
                                            </form>
                                        @endif

                                        @if($ord->order_status === 'accepted')
                                            <!-- Ready For Pickup Action (SRS §1.6) -->
                                            <form action="{{ route('farmer.orders.status', $ord->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="ready_for_pickup">
                                                <button type="submit" class="btn btn-sm btn-outline-info rounded-pill px-2" title="Mark Ready">
                                                    Ready
                                                </button>
                                            </form>
                                        @endif

                                        @if($ord->order_status === 'ready_for_pickup')
                                            <!-- Mark Completed Action (SRS §1.6) -->
                                            <form action="{{ route('farmer.orders.status', $ord->id) }}" method="POST" onsubmit="return confirm('Customer arrived and settled payment?')">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2" title="Complete Order">
                                                    Complete
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('farmer.orders.receipt', $ord->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-2" title="Download Receipt / Packing Slip">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>

                                        <a href="{{ route('farmer.orders.show', $ord->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-2">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
