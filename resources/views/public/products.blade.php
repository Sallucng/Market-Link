@extends('layouts.app')

@section('title', 'Farm Produce Catalog — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge-pastel-green mb-1">Weekly Fresh Inventory</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Browse Farm Produce</h2>
        </div>
        <div class="text-muted small font-mono-meta">
            Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} produce items
        </div>
    </div>

    <div class="row g-4">
        <!-- Filter Sidebar (SRS §1.6: Filters for price, category, market, and day) -->
        <div class="col-lg-3">
            <div class="card card-custom p-4 bg-white sticky-top" style="top: 80px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-funnel me-1 text-success"></i> Filters</h5>
                    <a href="{{ route('products.index') }}" class="small text-muted text-decoration-none">Reset All</a>
                </div>

                <form action="{{ route('products.index') }}" method="GET">
                    <!-- Search Keyword -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Search Keyword</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="e.g. Tomatoes, Kale...">
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Category</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Market Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Farmers Market</label>
                        <select name="market" class="form-select form-select-sm">
                            <option value="">All Markets</option>
                            @foreach($markets as $m)
                                <option value="{{ $m->id }}" {{ request('market') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Operating Day Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Market Day</label>
                        <select name="day" class="form-select form-select-sm">
                            <option value="">Any Day</option>
                            <option value="Saturday" {{ request('day') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                            <option value="Sunday" {{ request('day') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                            <option value="Wednesday" {{ request('day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                        </select>
                    </div>

                    <!-- Max Price -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Max Price ($)</label>
                        <input type="number" step="0.5" name="max_price" value="{{ request('max_price') }}" class="form-control form-control-sm" placeholder="e.g. 10.00">
                    </div>

                    <button type="submit" class="btn btn-brand btn-sm w-100 py-2">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        <!-- Product Grid Column -->
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom h-100 d-flex flex-column bg-white">
                            <div class="position-relative">
                                <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80' }}" 
                                     onerror="this.src='https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80'"
                                     class="card-img-top" 
                                     alt="{{ $product->name }}" 
                                     style="height: 190px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                <span class="position-absolute top-0 end-0 m-2 badge-pastel-slate" style="background: rgba(255,255,255,0.92);">
                                    {{ $product->category->name }}
                                </span>

                                @if($product->is_sold_out || $product->stock_quantity <= 0)
                                    <div class="position-absolute top-0 start-0 m-2 badge-pastel-red">
                                        Sold Out
                                    </div>
                                @else
                                    <div class="position-absolute bottom-0 start-0 m-2 badge-pastel-green">
                                        {{ $product->stock_quantity }} {{ $product->unit }} left
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-3 d-flex flex-column">
                                <small class="text-success fw-semibold mb-1">
                                    <i class="bi bi-shop me-1"></i>{{ $product->farmer->stall_name }}
                                </small>
                                <h6 class="card-title fw-bold text-dark mb-1">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $product->farmer->market->name ?? 'Local Market' }}
                                </div>
                                <div class="d-flex align-items-baseline gap-1 mb-2">
                                    <span class="fs-5 fw-bold text-dark font-mono-meta">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-muted small">/ {{ $product->unit }}</span>
                                </div>

                                <div class="mt-auto pt-2">
                                    @if($product->is_sold_out || $product->stock_quantity <= 0)
                                        <button class="btn btn-secondary btn-sm w-100 py-2" disabled>
                                            Sold Out
                                        </button>
                                    @else
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-brand-outline btn-sm w-100 py-2">
                                                <i class="bi bi-cart-plus me-1"></i> Pre-Order for Pickup
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card card-custom p-5 text-center bg-white border-0">
                            <i class="bi bi-search text-muted display-4 mb-3"></i>
                            <h5 class="fw-bold">No farm produce matches your filters</h5>
                            <p class="text-muted small">Try broadening your search criteria or resetting filters.</p>
                            <div>
                                <a href="{{ route('products.index') }}" class="btn btn-brand rounded-pill px-4">View All Produce</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
