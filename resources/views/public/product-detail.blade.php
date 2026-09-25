@extends('layouts.app')

@section('title', $product->name . ' — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-success">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-5">
        <!-- Image Column -->
        <div class="col-lg-6">
            <div class="card card-custom p-2 bg-white border-0 shadow-sm overflow-hidden">
                <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80' }}" 
                     alt="{{ $product->name }}" 
                     class="img-fluid rounded-3 w-100" 
                     style="max-height: 420px; object-fit: cover;">
            </div>
        </div>

        <!-- Product Details Column -->
        <div class="col-lg-6">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-light text-dark border">{{ $product->category->name }}</span>
                    @auth
                        @if(Auth::user()->isCustomer())
                            @php
                                $isProdFav = \App\Models\Favorite::where('customer_id', Auth::id())
                                    ->where('item_type', 'product')
                                    ->where('item_id', $product->id)
                                    ->exists();
                            @endphp
                            <form action="{{ route('customer.favorites.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="item_type" value="product">
                                <input type="hidden" name="item_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-sm {{ $isProdFav ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 py-1">
                                    <i class="bi {{ $isProdFav ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                                    {{ $isProdFav ? 'Favorited' : 'Save Favorite' }}
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
                <h1 class="heading-serif fw-bold text-dark mb-2">{{ $product->name }}</h1>

                <div class="d-flex align-items-baseline gap-2 mb-3">
                    <span class="display-6 fw-bold text-success">${{ number_format($product->price, 2) }}</span>
                    <span class="text-muted">/ {{ $product->unit }}</span>
                </div>

                <p class="text-secondary mb-4">{{ $product->description ?: 'Fresh, locally harvested products straight from community growers.' }}</p>

                <!-- Stock & Pickup Status -->
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-dark">Available Stock This Week:</span>
                        @if($product->is_sold_out || $product->stock_quantity <= 0)
                            <span class="badge bg-danger">Sold Out</span>
                        @else
                            <span class="badge bg-success">{{ $product->stock_quantity }} {{ $product->unit }} Available</span>
                        @endif
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i> Pre-orders are reserved against live inventory and held for you at the stall.
                    </small>
                </div>

                <!-- Add to Pre-Order Cart Form -->
                @if(!$product->is_sold_out && $product->stock_quantity > 0)
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-4">
                                <label class="small text-muted fw-semibold mb-1">Quantity ({{ $product->unit }}):</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control text-center">
                            </div>
                            <div class="col-sm-8 pt-sm-4">
                                <button type="submit" class="btn btn-brand w-100 py-2 rounded-pill">
                                    <i class="bi bi-cart-plus me-1"></i> Pre-Order for Pickup
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <button class="btn btn-secondary w-100 py-2 rounded-pill mb-4" disabled>
                        Currently Sold Out
                    </button>
                @endif

                <!-- Farmer / Stall Quick Info Card -->
                <div class="border rounded-3 p-3 mt-auto">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $product->farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=150&q=80' }}" 
                             alt="{{ $product->farmer->stall_name }}" 
                             class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0">{{ $product->farmer->stall_name }}</h6>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $product->farmer->market->name ?? 'Local Market' }}</small>
                        </div>
                        <a href="{{ route('farmers.show', $product->farmer->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            Stall Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews & Ratings Section (SRS §1.6) -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-5">
        <h4 class="heading-serif fw-bold text-dark mb-3">Customer Ratings & Reviews</h4>

        @forelse($product->reviews as $review)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="fw-bold text-dark">{{ $review->customer->name }}</div>
                    <div class="text-warning small">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                </div>
                <p class="text-secondary small mb-2">{{ $review->comment }}</p>

                @if($review->farmer_response)
                    <div class="bg-light p-2 rounded small ms-3 border-start border-success border-3">
                        <strong class="text-success">{{ $product->farmer->stall_name }} (Farmer):</strong>
                        <span class="text-muted">{{ $review->farmer_response }}</span>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-muted small mb-0">No reviews yet for this harvest. Verified customers can leave reviews after pickup!</p>
        @endforelse
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <h4 class="heading-serif fw-bold text-dark mb-3">More in {{ $product->category->name }}</h4>
        <div class="row g-4">
            @foreach($relatedProducts as $rel)
                <div class="col-md-3">
                    <div class="card card-custom h-100 bg-white p-3">
                        <img src="{{ $rel->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=80' }}" 
                             class="rounded mb-2" style="height: 140px; object-fit: cover;">
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('products.show', $rel->id) }}" class="text-dark text-decoration-none">
                                {{ $rel->name }}
                            </a>
                        </h6>
                        <div class="text-success fw-bold small mb-2">${{ number_format($rel->price, 2) }} / {{ $rel->unit }}</div>
                        <a href="{{ route('products.show', $rel->id) }}" class="btn btn-sm btn-brand-outline rounded-pill mt-auto">View Item</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
