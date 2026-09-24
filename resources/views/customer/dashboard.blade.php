@extends('layouts.app')

@section('title', 'Customer Dashboard — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Customer Dashboard</li>
        </ol>
    </nav>

    <!-- Header & Profile Info -->
    <div class="card card-custom p-4 bg-white mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge-pastel-green mb-2">Customer Account</span>
                <h2 class="heading-serif fw-bold text-dark mb-1">Welcome back, {{ $user->name }}</h2>
                <div class="small text-muted">
                    <i class="bi bi-envelope me-1"></i>{{ $user->email }} &bull; 
                    <i class="bi bi-telephone me-1"></i>{{ $user->contact_number }} &bull; 
                    <i class="bi bi-geo-alt me-1"></i>{{ $user->address }}
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('customer.favorites.index') }}" class="btn btn-brand-outline btn-sm">
                    <i class="bi bi-heart me-1 text-danger"></i> Saved Favorites
                </a>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-brand btn-sm">
                    <i class="bi bi-receipt me-1"></i> All Orders
                </a>
            </div>
        </div>
    </div>

    <!-- In-App Alerts / Notifications (SRS §1.6) -->
    @if($notifications->isNotEmpty())
        <div class="card card-custom p-3 bg-white mb-4">
            <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-bell text-success me-1"></i> Recent Order Alerts</h6>
            <div class="vstack gap-2">
                @foreach($notifications as $notif)
                    <div class="p-2 rounded bg-light border-start border-success border-3 d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="small text-dark">{{ $notif->title }}</strong>
                            <div class="small text-muted">{{ $notif->message }}</div>
                        </div>
                        <small class="text-muted text-nowrap ms-2 font-mono-meta">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Active Pre-Orders with Status Progression (SRS §1.6) -->
    <div class="card card-custom p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="heading-serif fw-bold text-dark mb-0">Active Pre-Orders Awaiting Pickup</h5>
            <a href="{{ route('customer.orders.index') }}" class="small text-success text-decoration-none fw-semibold">View All &rarr;</a>
        </div>

        @if($activeOrders->isEmpty())
            <div class="text-center py-4">
                <i class="bi bi-bag-check text-muted fs-1"></i>
                <p class="text-muted small mt-2 mb-3">You have no active pre-orders right now.</p>
                <a href="{{ route('products.index') }}" class="btn btn-brand btn-sm">Browse Weekly Produce</a>
            </div>
        @else
            <div class="row g-3">
                @foreach($activeOrders as $ord)
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-3 h-100 d-flex flex-column" style="border: 1px solid var(--border-hairline);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <a href="{{ route('customer.orders.show', $ord->id) }}" class="fw-bold text-dark text-decoration-none font-mono-meta">
                                        #{{ $ord->order_number }}
                                    </a>
                                    <div class="small text-muted">{{ $ord->farmer->stall_name }} &bull; {{ $ord->market->name ?? 'Local Market' }}</div>
                                </div>
                                <span class="
                                    {{ $ord->order_status === 'ready_for_pickup' ? 'badge-pastel-green' : '' }}
                                    {{ $ord->order_status === 'accepted' ? 'badge-pastel-amber' : '' }}
                                    {{ $ord->order_status === 'placed' ? 'badge-pastel-slate' : '' }}
                                    text-uppercase font-mono-meta" style="font-size: 0.7rem;">
                                    {{ str_replace('_', ' ', $ord->order_status) }}
                                </span>
                            </div>

                            <div class="small text-secondary mb-2">
                                <div><i class="bi bi-calendar-check me-1 text-success"></i><strong>Pickup:</strong> {{ $ord->pickup_date->format('l, M d') }} ({{ $ord->pickup_time_slot }})</div>
                                <div><i class="bi bi-cash me-1 text-muted"></i><strong>Settlement Due:</strong> <span class="font-mono-meta fw-bold">${{ number_format($ord->total_amount, 2) }}</span> (Pay at stall)</div>
                            </div>

                            <!-- Items Summary -->
                            <div class="small text-muted mb-3 flex-grow-1">
                                @foreach($ord->items as $item)
                                    <div>&bull; {{ $item->quantity }}x {{ $item->product->name ?? 'Produce' }}</div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <a href="{{ route('customer.orders.show', $ord->id) }}" class="btn btn-brand-outline btn-sm">
                                    Manage Pre-Order
                                </a>

                                @if($ord->canModifyOrCancel())
                                    <small class="text-muted"><i class="bi bi-hourglass text-secondary me-1"></i>Modifiable before cutoff</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <!-- Favorite Produce with Restock Alerts (SRS §1.6) -->
        <div class="col-lg-6">
            <div class="card card-custom p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="heading-serif fw-bold text-dark mb-0">Favorite Produce & Restock Alerts</h5>
                    <a href="{{ route('customer.favorites.index') }}" class="small text-success text-decoration-none fw-semibold">Manage</a>
                </div>

                @if($favoriteProducts->isEmpty())
                    <p class="text-muted small">You haven't saved any favorite produce yet.</p>
                @else
                    <div class="vstack gap-2">
                        @foreach($favoriteProducts as $favProd)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded" style="border: 1px solid var(--border-hairline);">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $favProd->image_url }}" alt="{{ $favProd->name }}" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div>
                                        <a href="{{ route('products.show', $favProd->id) }}" class="fw-bold small text-dark text-decoration-none">
                                            {{ $favProd->name }}
                                        </a>
                                        <div class="text-muted small font-mono-meta">${{ number_format($favProd->price, 2) }} / {{ $favProd->unit }}</div>
                                    </div>
                                </div>

                                <div>
                                    @if($favProd->is_sold_out || $favProd->stock_quantity == 0)
                                        <span class="badge-pastel-red" style="font-size: 0.7rem;">Sold Out</span>
                                    @else
                                        <span class="badge-pastel-green" style="font-size: 0.7rem;">
                                            <i class="bi bi-bell-fill me-1"></i>In Stock ({{ $favProd->stock_quantity }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Preferred Market Locations with Route Details (SRS §1.6) -->
        <div class="col-lg-6">
            <div class="card card-custom p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="heading-serif fw-bold text-dark mb-0">Preferred Market Locations</h5>
                    <a href="{{ route('markets.index') }}" class="small text-success text-decoration-none fw-semibold">Browse All</a>
                </div>

                @if($preferredMarkets->isEmpty())
                    <p class="text-muted small">No preferred markets saved yet. Visit any market page to bookmark your regular market location.</p>
                @else
                    <div class="vstack gap-3">
                        @foreach($preferredMarkets as $prefMarket)
                            <div class="p-3 rounded bg-white" style="border: 1px solid var(--border-hairline);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $prefMarket->name }}</h6>
                                        <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $prefMarket->address }}</small>
                                    </div>
                                    <span class="badge-pastel-slate">{{ $prefMarket->operating_days }}</span>
                                </div>

                                <div class="small text-secondary mb-2">
                                    <strong>Hours:</strong> <span class="font-mono-meta">{{ $prefMarket->timings }}</span>
                                </div>

                                <!-- Route-Friendly Pickup Details (SRS §1.6) -->
                                <div class="d-flex gap-2 mt-2">
                                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $prefMarket->latitude }},{{ $prefMarket->longitude }}" target="_blank" class="btn btn-brand-outline btn-sm">
                                        <i class="bi bi-map me-1 text-danger"></i> Google Maps Directions
                                    </a>
                                    <a href="{{ route('markets.show', $prefMarket->id) }}" class="btn btn-brand btn-sm">
                                        Stall Lineup
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
