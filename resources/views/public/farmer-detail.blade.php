@extends('layouts.app')

@section('title', $farmer->stall_name . ' — MarketLink')

@section('styles')
<style>
    #stall-map {
        height: 220px;
        border-radius: 12px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('markets.index') }}" class="text-success">Markets</a></li>
            @if($farmer->market)
                <li class="breadcrumb-item"><a href="{{ route('markets.show', $farmer->market->id) }}" class="text-success">{{ $farmer->market->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $farmer->stall_name }}</li>
        </ol>
    </nav>

    <!-- Stall Header Card -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        <div class="row align-items-center g-4">
            <div class="col-md-2 text-center">
                <img src="{{ $farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=300&q=80' }}" 
                     alt="{{ $farmer->stall_name }}" 
                     class="img-fluid rounded-circle border p-1" 
                     style="width: 120px; height: 120px; object-fit: cover;">
            </div>

            <div class="col-md-6">
                <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Verified Local Grower</span>
                <h2 class="heading-serif fw-bold text-dark mb-1">{{ $farmer->stall_name }}</h2>
                <p class="text-muted small mb-2"><i class="bi bi-person me-1"></i>Owner: {{ $farmer->contact_person }} | <i class="bi bi-telephone me-1"></i>{{ $farmer->contact_number }}</p>
                <p class="text-secondary small mb-3">{{ $farmer->bio }}</p>

                <div class="d-flex flex-wrap gap-3 small text-secondary">
                    <div>
                        <i class="bi bi-shop text-success me-1"></i><strong>Market:</strong>
                        @if($farmer->market)
                            <a href="{{ route('markets.show', $farmer->market->id) }}" class="text-success text-decoration-none fw-semibold">
                                {{ $farmer->market->name }} <i class="bi bi-box-arrow-up-right small"></i>
                            </a>
                        @else
                            <span>Local Market</span>
                        @endif
                    </div>
                    <div><i class="bi bi-clock-history text-warning me-1"></i><strong>Pickup Windows:</strong> {{ $farmer->pickup_time_windows ?: 'Market Hours' }}</div>
                    <div><i class="bi bi-hourglass-split text-danger me-1"></i><strong>Cutoff:</strong> {{ $farmer->cutoff_hours }} hrs prior</div>
                </div>
            </div>

            <!-- Mini Stall Map Pin -->
            <div class="col-md-4">
                <div class="card border p-2 bg-light">
                    <div id="stall-map"></div>
                    <small class="text-muted text-center mt-2 d-block">
                        <i class="bi bi-pin-map text-danger me-1"></i> {{ $farmer->address }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Stock / Products (SRS §1.6) -->
    <h3 class="heading-serif fw-bold text-dark mb-3">Current Weekly Stock</h3>

    <div class="row g-4 mb-5">
        @forelse($farmer->products as $product)
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom h-100 bg-white d-flex flex-column">
                    <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=80' }}" 
                         class="card-img-top" style="height: 160px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <div class="card-body p-3 d-flex flex-column">
                        <span class="badge bg-light text-dark border align-self-start mb-1 small">{{ $product->category->name }}</span>
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </h6>
                        <div class="text-success fw-bold fs-5 mb-2">${{ number_format($product->price, 2) }} <span class="text-muted small">/ {{ $product->unit }}</span></div>
                        
                        <div class="mt-auto">
                            @if($product->is_sold_out || $product->stock_quantity <= 0)
                                <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>Sold Out</button>
                            @else
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-brand-outline btn-sm w-100 rounded-pill">
                                        <i class="bi bi-cart-plus me-1"></i> Pre-Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">This farmer has not published this week's harvest yet. Check back soon!</div>
            </div>
        @endforelse
    </div>

    <!-- Customer Reviews for this Stall -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm">
        <h4 class="heading-serif fw-bold text-dark mb-3">Customer Feedback & Ratings</h4>
        @forelse($farmer->reviews as $review)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-dark">{{ $review->customer->name }}</span>
                    <div class="text-warning small">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                </div>
                <p class="text-secondary small mb-2">{{ $review->comment }}</p>
                @if($review->farmer_response)
                    <div class="bg-light p-2 rounded small ms-3 border-start border-success border-3">
                        <strong class="text-success">Farmer Response:</strong>
                        <span class="text-muted">{{ $review->farmer_response }}</span>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-muted small mb-0">No reviews published yet for this stall.</p>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
@if($farmer->latitude && $farmer->longitude)
<script>
    const stallMap = L.map('stall-map', {
        zoomControl: false,
        attributionControl: false
    }).setView([{{ $farmer->latitude }}, {{ $farmer->longitude }}], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(stallMap);

    const fIcon = L.divIcon({
        className: 'custom-pin',
        html: `<div style="background-color:#15803d; color:white; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill"></i></div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 28]
    });

    L.marker([{{ $farmer->latitude }}, {{ $farmer->longitude }}], { icon: fIcon })
        .addTo(stallMap)
        .bindPopup("<strong>{{ addslashes($farmer->stall_name) }}</strong><br>{{ addslashes($farmer->address) }}")
        .openPopup();
</script>
@endif
@endsection
