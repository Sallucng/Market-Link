@extends('layouts.app')

@section('title', 'My Saved Favorites — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Saved Favorites</li>
        </ol>
    </nav>

    <div class="mb-4">
        <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Customer Dashboard</span>
        <h2 class="heading-serif fw-bold text-dark mb-0">My Favorite Products and Farmers</h2>
        <p class="text-muted small">Quick access to your preferred growers, weekly stock alerts, and favorite market stalls.</p>
    </div>

    <!-- Preferred Markets (SRS §1.6) -->
    <h4 class="heading-serif fw-bold text-dark mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Preferred Markets ({{ $markets->count() }})</h4>
    <div class="row g-4 mb-5">
        @forelse($markets as $market)
            <div class="col-md-6 col-lg-4">
                <div class="card card-custom p-3 bg-white h-100 border-0 shadow-sm d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold text-dark mb-0">{{ $market->name }}</h5>
                        <form action="{{ route('customer.favorites.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="item_type" value="market">
                            <input type="hidden" name="item_id" value="{{ $market->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Remove from favorites">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    <p class="text-muted small mb-2"><i class="bi bi-pin-map text-secondary me-1"></i>{{ $market->address }}, {{ $market->city }}</p>
                    <div class="small text-secondary mb-3">
                        <div><i class="bi bi-calendar-event text-success me-1"></i>{{ $market->operating_days }}</div>
                        <div><i class="bi bi-clock text-warning me-1"></i>{{ $market->timings }}</div>
                    </div>
                    <div class="mt-auto d-flex gap-2">
                        <a href="{{ route('markets.show', $market->id) }}" class="btn btn-brand-outline btn-sm rounded-pill flex-grow-1">
                            Market Stalls
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($market->address . ', ' . $market->city) }}" 
                           target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Google Directions">
                            <i class="bi bi-compass"></i> Directions
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="p-3 bg-light rounded text-muted small">No preferred markets saved yet. Browse markets and click "Bookmark as Preferred" to add them here.</div>
            </div>
        @endforelse
    </div>

    <!-- Favorite Farmers -->
    <h4 class="heading-serif fw-bold text-dark mb-3"><i class="bi bi-shop text-success me-2"></i>Favorite Farmer Stalls ({{ $farmers->count() }})</h4>
    <div class="row g-4 mb-5">
        @forelse($farmers as $farmer)
            <div class="col-md-6 col-lg-4">
                <div class="card card-custom p-3 bg-white h-100 border-0 shadow-sm d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=150&q=80' }}" 
                                 alt="{{ $farmer->stall_name }}" class="rounded-circle" style="width: 55px; height: 55px; object-fit: cover;">
                            <div>
                                <h6 class="fw-bold mb-0">{{ $farmer->stall_name }}</h6>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $farmer->market->name ?? 'Local Market' }}</small>
                            </div>
                        </div>
                        <form action="{{ route('customer.favorites.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="item_type" value="farmer">
                            <input type="hidden" name="item_id" value="{{ $farmer->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Remove from favorites">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    <p class="text-muted small flex-grow-1">{{ Str::limit($farmer->bio, 80) }}</p>
                    <a href="{{ route('farmers.show', $farmer->id) }}" class="btn btn-brand-outline btn-sm rounded-pill w-100 mt-auto">
                        View Stall Page
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="p-3 bg-light rounded text-muted small">You haven't saved any favorite farmers yet.</div>
            </div>
        @endforelse
    </div>

    <!-- Favorite Products -->
    <h4 class="heading-serif fw-bold text-dark mb-3"><i class="bi bi-basket-fill text-warning me-2"></i>Favorite Harvest Products ({{ $products->count() }})</h4>
    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom h-100 bg-white d-flex flex-column">
                    <div class="position-relative">
                        <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=80' }}" 
                             class="card-img-top" style="height: 160px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                        
                        <form action="{{ route('customer.favorites.toggle') }}" method="POST" class="position-absolute top-0 end-0 m-2">
                            @csrf
                            <input type="hidden" name="item_type" value="product">
                            <input type="hidden" name="item_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm p-1" style="width:30px; height:30px;" title="Remove from favorites">
                                <i class="bi bi-heart-fill text-danger small"></i>
                            </button>
                        </form>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="mb-1">
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success-subtle text-success small mb-1">
                                    <i class="bi bi-check-circle me-1"></i>In Stock ({{ $product->stock_quantity }} {{ $product->unit }})
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger small mb-1">
                                    <i class="bi bi-bell me-1"></i>Sold Out (Restock Alert Active)
                                </span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </h6>
                        <small class="text-success mb-2"><i class="bi bi-shop me-1"></i>{{ $product->farmer->stall_name }}</small>
                        <div class="fw-bold text-dark fs-5 mb-3">${{ number_format($product->price, 2) }} / {{ $product->unit }}</div>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-auto">
                            @csrf
                            <button type="submit" class="btn btn-brand-outline btn-sm w-100 rounded-pill" {{ $product->stock_quantity < 1 ? 'disabled' : '' }}>
                                <i class="bi bi-cart-plus me-1"></i> {{ $product->stock_quantity < 1 ? 'Sold Out' : 'Pre-Order' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="p-3 bg-light rounded text-muted small">You haven't marked any products as favorites yet.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
