@extends('layouts.app')

@section('title', $market->name . ' — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('markets.index') }}" class="text-success">Markets</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $market->name }}</li>
        </ol>
    </nav>

    <!-- Market Header Card -->
    <div class="card card-custom overflow-hidden bg-white border-0 shadow-sm mb-4">
        <div class="row g-0">
            <div class="col-lg-4 col-md-5">
                <img src="{{ $market->image_url ?: 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80' }}" 
                     alt="{{ $market->name }}" 
                     class="w-100 h-100" 
                     style="object-fit: cover; min-height: 220px; max-height: 320px;">
            </div>
            <div class="col-lg-8 col-md-7 p-4 d-flex flex-column justify-content-between">
                <div>
                    <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Local Farmers Market</span>
                    <h1 class="heading-serif fw-bold text-dark mb-2">{{ $market->name }}</h1>
                    <p class="text-muted mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $market->address }}, {{ $market->city }}</p>
                    
                    @if($market->description)
                        <p class="text-secondary small mb-3">{{ $market->description }}</p>
                    @endif

                    <div class="d-flex flex-wrap gap-4 text-secondary small mb-3">
                        <div><i class="bi bi-calendar-event text-success me-1"></i><strong>Operating Days:</strong> {{ $market->operating_days }}</div>
                        <div><i class="bi bi-clock-history text-warning me-1"></i><strong>Timings:</strong> {{ $market->timings }}</div>
                        <div><i class="bi bi-people-fill text-primary me-1"></i><strong>Attending Stalls:</strong> {{ $market->farmers->count() }} Farmers</div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                    @auth
                        @if(Auth::user()->isCustomer())
                            @php
                                $isMarketFav = \App\Models\Favorite::where('customer_id', Auth::id())
                                    ->where('item_type', 'market')
                                    ->where('item_id', $market->id)
                                    ->exists();
                            @endphp
                            <form action="{{ route('customer.favorites.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="item_type" value="market">
                                <input type="hidden" name="item_id" value="{{ $market->id }}">
                                <button type="submit" class="btn {{ $isMarketFav ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 py-2 btn-sm">
                                    <i class="bi {{ $isMarketFav ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                                    {{ $isMarketFav ? 'Preferred Market' : 'Bookmark as Preferred' }}
                                </button>
                            </form>
                        @endif
                    @endauth
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($market->address . ', ' . $market->city) }}" 
                       target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm">
                        <i class="bi bi-geo-alt me-1"></i> Google Directions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Attending Farmers & Stalls Section (SRS §1.6) -->
    <h3 class="heading-serif fw-bold text-dark mb-3">Attending Farmer Stalls</h3>

    <div class="row g-4">
        @forelse($market->farmers as $farmer)
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex gap-3 align-items-start mb-3">
                        <img src="{{ $farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=200&q=80' }}" 
                             alt="{{ $farmer->stall_name }}" 
                             class="rounded-3" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">{{ $farmer->stall_name }}</h5>
                            <div class="text-muted small mb-1"><i class="bi bi-person me-1"></i>{{ $farmer->contact_person }}</div>
                            <div class="small text-secondary mb-1"><i class="bi bi-calendar-check text-success me-1"></i>Attending: <span class="fw-semibold text-dark">{{ $farmer->operating_days ?: $market->operating_days }}</span></div>
                            <div class="small text-secondary"><i class="bi bi-clock text-warning me-1"></i>Pickup Slots: {{ $farmer->pickup_time_windows ?: 'Market Hours' }}</div>
                        </div>
                    </div>

                    <p class="text-muted small mb-3">{{ $farmer->bio }}</p>

                    <!-- Available Products from this stall -->
                    <div class="border-top pt-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-dark">This Week's Stock ({{ $farmer->products->count() }} items)</span>
                            <a href="{{ route('farmers.show', $farmer->id) }}" class="btn btn-sm btn-brand-outline rounded-pill px-3">
                                Visit Stall Page <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>

                        <div class="row g-2">
                            @foreach($farmer->products->take(3) as $prod)
                                <div class="col-4">
                                    <div class="border rounded p-2 text-center bg-light">
                                        <div class="small fw-semibold text-truncate mb-1">{{ $prod->name }}</div>
                                        <div class="text-success fw-bold small">${{ number_format($prod->price, 2) }} / {{ $prod->unit }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No approved farmer stalls are registered for this market yet.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
