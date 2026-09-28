@extends('layouts.app')

@section('title', 'Farm Product Catalog — MarketLink')

@section('styles')
<style>
    /* Smooth horizontal chip scrolling without scrollbar */
    .filter-chips-row {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        gap: 0.5rem;
        padding-bottom: 0.35rem;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .filter-chips-row::-webkit-scrollbar {
        display: none;
    }

    .filter-chip {
        font-size: 0.82rem;
        padding: 0.38rem 0.9rem;
        border-radius: 9999px;
        transition: all 0.2s ease;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        flex-shrink: 0;
    }
    .filter-chip.active {
        background-color: var(--brand-primary);
        color: #ffffff !important;
        border-color: var(--brand-primary) !important;
        box-shadow: 0 2px 6px rgba(27, 67, 50, 0.25);
    }
    .filter-chip:not(.active) {
        background-color: #ffffff;
        color: var(--text-dark);
        border: 1px solid var(--border-hairline);
    }
    .filter-chip:not(.active):hover {
        background-color: #f7f9f7;
        border-color: #c9d8c8;
        color: var(--brand-primary);
    }

    /* Responsive product card styling for 2-column mobile */
    @media (max-width: 575.98px) {
        .product-card-img {
            height: 135px !important;
        }
        .product-card-title {
            font-size: 0.88rem !important;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.25 !important;
            min-height: 2.2rem;
        }
        .product-card-stall {
            font-size: 0.72rem !important;
        }
        .product-card-price {
            font-size: 1rem !important;
        }
        .product-card-btn {
            font-size: 0.75rem !important;
            padding: 0.35rem 0.5rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-3 py-md-4">
    <!-- Header with Breadcrumb & Sort -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <span class="badge-pastel-green mb-1">Weekly Fresh Inventory</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Browse Farm Products</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small font-mono-meta d-none d-sm-inline">
                {{ $products->total() }} available
            </span>
            <!-- Desktop / Tablet Sort Selector -->
            <form action="{{ route('products.index') }}" method="GET" class="d-inline-flex align-items-center gap-1">
                @foreach(request()->except('sort', 'page') as $key => $val)
                    @if(is_array($val))
                        @foreach($val as $v) <input type="hidden" name="{{ $key }}[]" value="{{ $v }}"> @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
                <label for="sort-select" class="small text-muted text-nowrap d-none d-md-inline">Sort by:</label>
                <select id="sort-select" name="sort" class="form-select form-select-sm" style="min-width: 150px; border-radius: 8px;" onchange="this.form.submit()">
                    <option value="" {{ !request('sort') ? 'selected' : '' }}>Latest Harvests</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                </select>
            </form>
        </div>
    </div>

    @php
        $inStock = request()->has('in_stock') ? request()->boolean('in_stock') : true;
    @endphp

    <!-- Mobile-First Horizontal Category & Quick Filter Chips -->
    <div class="filter-chips-row mb-3">
        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" 
           class="filter-chip {{ !request('category') && $inStock && !request('day') ? 'active' : '' }}">
            <span>🌱 All Products</span>
        </a>
        @foreach($categories as $cat)
            <a href="{{ request()->fullUrlWithQuery(['category' => request('category') == $cat->id ? null : $cat->id, 'page' => null]) }}" 
               class="filter-chip {{ request('category') == $cat->id ? 'active' : '' }}">
                @if($cat->slug == 'vegetables') 🥕
                @elseif($cat->slug == 'fruits') 🍎
                @elseif($cat->slug == 'dairy-eggs') 🥛
                @elseif($cat->slug == 'baked-goods') 🥖
                @else 🍯
                @endif
                <span>{{ $cat->name }}</span>
            </a>
        @endforeach
        <a href="{{ request()->fullUrlWithQuery(['in_stock' => $inStock ? 0 : 1, 'page' => null]) }}" 
           class="filter-chip {{ $inStock ? 'active' : '' }}" title="{{ $inStock ? 'Click to show all (including out-of-stock)' : 'Click to filter in-stock only' }}">
            <i class="bi bi-lightning-charge"></i>
            <span>In-Stock Only</span>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['day' => request('day') == 'Saturday' ? null : 'Saturday', 'page' => null]) }}" 
           class="filter-chip {{ request('day') == 'Saturday' ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i>
            <span>Saturday Markets</span>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['day' => request('day') == 'Sunday' ? null : 'Sunday', 'page' => null]) }}" 
           class="filter-chip {{ request('day') == 'Sunday' ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i>
            <span>Sunday Markets</span>
        </a>
    </div>

    <!-- Mobile Search & Filter Action Bar (Visible only on <992px) -->
    <div class="card card-custom p-2 mb-3 d-lg-none bg-white">
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('products.index') }}" method="GET" class="flex-grow-1 position-relative" id="mobileProductSearchForm">
                @foreach(request()->except('q', 'page') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted small"></i>
                <input type="text" name="q" id="mobileProductSearchInput" value="{{ request('q') }}" class="form-control form-control-sm ps-4 pe-4" placeholder="Search tomatoes, bakery, farm..." style="border-radius: 8px;" autocomplete="off">
                @if(request('q'))
                    <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="position-absolute top-50 end-0 translate-middle-y me-2 text-muted text-decoration-none small">
                        <i class="bi bi-x-circle-fill"></i>
                    </a>
                @endif
                <div id="mobileProductSearchSuggestions" class="position-absolute start-0 end-0 bg-white border rounded-3 shadow-lg p-2 d-none" style="top: 100%; z-index: 1050; margin-top: 4px; max-height: 280px; overflow-y: auto;"></div>
            </form>
            <button class="btn btn-sm btn-brand-outline d-flex align-items-center gap-1 flex-shrink-0 px-2.5 py-1.5" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterDrawer" style="border-radius: 8px;">
                <i class="bi bi-sliders"></i>
                <span>Filters</span>
                @php
                    $activeFilterCount = (request('category') ? 1 : 0) + (request('market') ? 1 : 0) + (request('day') ? 1 : 0) + (request('max_price') ? 1 : 0) + (request()->has('in_stock') && !request()->boolean('in_stock') ? 1 : 0);
                @endphp
                @if($activeFilterCount > 0)
                    <span class="badge bg-success rounded-pill font-mono-meta ms-1">{{ $activeFilterCount }}</span>
                @endif
            </button>
        </div>
    </div>

    <!-- Active Filters Dismissible Tags -->
    @if(request('q') || request('category') || request('market') || request('day') || request('max_price') || request('in_stock'))
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 rounded-3" style="background-color: #f5f8f5; border: 1px solid #e1ebe0;">
            <span class="small text-muted fw-semibold me-1 d-flex align-items-center gap-1">
                <i class="bi bi-funnel-fill text-success"></i> Active Filters:
            </span>
            @if(request('q'))
                <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="badge bg-white text-dark border text-decoration-none px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                    Keyword: "{{ request('q') }}" <i class="bi bi-x-circle-fill text-muted"></i>
                </a>
            @endif
            @if(request('category'))
                @php $activeCat = $categories->firstWhere('id', request('category')); @endphp
                @if($activeCat)
                    <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="badge bg-white text-dark border text-decoration-none px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                        Category: {{ $activeCat->name }} <i class="bi bi-x-circle-fill text-muted"></i>
                    </a>
                @endif
            @endif
            @if(request('market'))
                @php $activeMarket = $markets->firstWhere('id', request('market')); @endphp
                @if($activeMarket)
                    <a href="{{ request()->fullUrlWithQuery(['market' => null, 'page' => null]) }}" class="badge bg-white text-dark border text-decoration-none px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                        Market: {{ $activeMarket->name }} <i class="bi bi-x-circle-fill text-muted"></i>
                    </a>
                @endif
            @endif
            @if(request('day'))
                <a href="{{ request()->fullUrlWithQuery(['day' => null, 'page' => null]) }}" class="badge bg-white text-dark border text-decoration-none px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                    Day: {{ request('day') }} <i class="bi bi-x-circle-fill text-muted"></i>
                </a>
            @endif
            @if(request('max_price'))
                <a href="{{ request()->fullUrlWithQuery(['max_price' => null, 'page' => null]) }}" class="badge bg-white text-dark border text-decoration-none px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                    Price: &le; ${{ request('max_price') }} <i class="bi bi-x-circle-fill text-muted"></i>
                </a>
            @endif
            @if(request()->has('in_stock') && !request()->boolean('in_stock'))
                <a href="{{ request()->fullUrlWithQuery(['in_stock' => null, 'page' => null]) }}" class="badge bg-white text-dark border text-decoration-none px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                    All Items (Including Out-of-Stock) <i class="bi bi-x-circle-fill text-muted"></i>
                </a>
            @endif
            <a href="{{ route('products.index') }}" class="small text-danger text-decoration-none ms-auto fw-semibold">
                Reset All
            </a>
        </div>
    @endif

    <div class="row g-4">
        <!-- Desktop Filter Sidebar (d-none d-lg-block) -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card card-custom p-4 bg-white sticky-top" style="top: 80px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-funnel me-1 text-success"></i> Filters</h5>
                    <a href="{{ route('products.index') }}" class="small text-muted text-decoration-none">Reset All</a>
                </div>

                <form action="{{ route('products.index') }}" method="GET" id="desktopFilterForm">
                    <!-- Search Keyword with Live Recommendations -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Search Keyword</label>
                        <div class="position-relative" id="desktopSearchWrap">
                            <input type="text" name="q" id="desktopProductSearchInput" value="{{ request('q') }}" class="form-control form-control-sm pe-4" placeholder="e.g. Tomatoes, Kale, Apples..." autocomplete="off">
                            @if(request('q'))
                                <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="position-absolute top-50 end-0 translate-middle-y me-2 text-muted text-decoration-none small">✕</a>
                            @endif
                            <div id="desktopProductSearchSuggestions" class="position-absolute start-0 end-0 bg-white border rounded-3 shadow-lg p-2 d-none" style="top: 100%; z-index: 1050; margin-top: 4px; max-height: 280px; overflow-y: auto;"></div>
                        </div>
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
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Max Price ($)</label>
                        <input type="number" step="0.5" name="max_price" value="{{ request('max_price') }}" class="form-control form-control-sm" placeholder="e.g. 10.00">
                    </div>

                    <!-- In-Stock Only Toggle (Default ON) -->
                    <div class="mb-4 form-check form-switch">
                        <input type="hidden" name="in_stock" value="0">
                        <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="inStockDesktop" {{ $inStock ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold text-secondary" for="inStockDesktop">
                            In-Stock Only
                        </label>
                    </div>

                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <button type="submit" class="btn btn-brand btn-sm w-100 py-2">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        <!-- Product Grid Column -->
        <div class="col-12 col-lg-9">
            <div class="row g-3 g-md-4">
                @forelse($products as $product)
                    <div class="col-6 col-md-4 col-lg-4">
                        <div class="card card-custom h-100 d-flex flex-column bg-white position-relative">
                            <div class="position-relative overflow-hidden" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                <a href="{{ route('products.show', $product->id) }}" class="d-block text-decoration-none" title="{{ $product->name }}">
                                    <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=600&q=80' }}" 
                                         onerror="this.src='https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=600&q=80'"
                                         class="card-img-top product-card-img" 
                                         alt="{{ $product->name }}" 
                                         style="height: 180px; object-fit: cover; transition: transform 0.3s ease;">
                                </a>
                                
                                <span class="position-absolute top-0 end-0 m-1.5 m-md-2 badge-pastel-slate small" style="background: rgba(255,255,255,0.92); z-index: 2; font-size: 0.68rem;">
                                    {{ $product->category->name }}
                                </span>

                                @if($product->is_sold_out || $product->stock_quantity <= 0)
                                    <div class="position-absolute top-0 start-0 m-1.5 m-md-2 badge-pastel-red" style="z-index: 2; font-size: 0.68rem;">
                                        Sold Out
                                    </div>
                                @else
                                    <div class="position-absolute bottom-0 start-0 m-1.5 m-md-2 badge-pastel-green" style="z-index: 2; font-size: 0.68rem;">
                                        {{ $product->stock_quantity }} left
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-2.5 p-md-3 d-flex flex-column">
                                <h6 class="card-title fw-bold text-dark mb-1 product-card-title">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none stretched-link">
                                        {{ $product->name }}
                                    </a>
                                </h6>
                                
                                <div class="text-muted small mb-1 text-truncate product-card-stall" style="font-size: 0.76rem;">
                                    <i class="bi bi-geo-alt me-0.5 text-success"></i>{{ $product->farmer->market->name ?? 'Local Market' }}
                                </div>
                                
                                <div class="small mb-2 product-card-stall" style="position: relative; z-index: 2;">
                                    <a href="{{ route('farmers.show', $product->farmer->id) }}" class="text-success fw-semibold text-decoration-none d-inline-flex align-items-center text-truncate w-100">
                                        <i class="bi bi-shop me-1 flex-shrink-0"></i>
                                        <span class="text-truncate">{{ $product->farmer->stall_name }}</span>
                                    </a>
                                </div>
                                
                                <div class="d-flex align-items-baseline gap-1 mb-2">
                                    <span class="fs-5 fw-bold text-dark font-mono-meta product-card-price">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-muted small">/ {{ $product->unit }}</span>
                                </div>

                                <div class="mt-auto pt-1" style="position: relative; z-index: 2;">
                                    @if($product->is_sold_out || $product->stock_quantity <= 0)
                                        <button class="btn btn-secondary btn-sm w-100 py-1.5 product-card-btn" disabled>
                                            Sold Out
                                        </button>
                                    @else
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-brand-outline btn-sm w-100 py-1.5 product-card-btn">
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
                        <div class="card card-custom p-4 p-md-5 text-center bg-white border-0">
                            <i class="bi bi-search text-muted display-4 mb-3"></i>
                            <h5 class="fw-bold">No farm products match your search criteria</h5>
                            <p class="text-muted small col-md-8 mx-auto">
                                @if(request('q'))
                                    We couldn't find harvests matching <strong>"{{ request('q') }}"</strong>. Try searching for generic items like "tomatoes", "eggs", or browse popular categories below.
                                @else
                                    No harvests found matching the selected filters. Try broadening your market or price filters.
                                @endif
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                                <a href="{{ route('products.index') }}" class="btn btn-brand rounded-pill px-4 btn-sm">Reset All Filters</a>
                                <a href="{{ route('products.index', ['category' => 1]) }}" class="btn btn-brand-outline rounded-pill px-3 btn-sm">Vegetables</a>
                                <a href="{{ route('products.index', ['category' => 2]) }}" class="btn btn-brand-outline rounded-pill px-3 btn-sm">Fruits</a>
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

<!-- Mobile Filter Offcanvas Drawer -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilterDrawer" aria-labelledby="mobileFilterDrawerLabel">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center gap-2" id="mobileFilterDrawerLabel">
            <i class="bi bi-sliders text-success"></i> Filter Products
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form action="{{ route('products.index') }}" method="GET">
            <!-- Search Keyword in drawer -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Search Keyword</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="e.g. Tomatoes, Honey...">
            </div>

            <!-- Category Filter in drawer -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Product Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Market Filter in drawer -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Farmers Market Location</label>
                <select name="market" class="form-select">
                    <option value="">All Markets</option>
                    @foreach($markets as $m)
                        <option value="{{ $m->id }}" {{ request('market') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Market Day in drawer -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Market Operating Day</label>
                <select name="day" class="form-select">
                    <option value="">Any Day</option>
                    <option value="Saturday" {{ request('day') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="Sunday" {{ request('day') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                    <option value="Wednesday" {{ request('day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                </select>
            </div>

            <!-- Max Price in drawer -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Maximum Price ($)</label>
                <input type="number" step="0.5" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="e.g. 10.00">
            </div>

            <!-- In-Stock Only Switch in drawer (Default ON) -->
            <div class="mb-4 form-check form-switch p-0 d-flex justify-content-between align-items-center">
                <label class="form-check-label small fw-semibold text-secondary mb-0" for="inStockMobile">
                    Show Only In-Stock Harvests
                </label>
                <input type="hidden" name="in_stock" value="0">
                <input class="form-check-input ms-0" type="checkbox" name="in_stock" value="1" id="inStockMobile" {{ $inStock ? 'checked' : '' }} style="width: 2.2em; height: 1.2em;">
            </div>

            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <div class="d-grid gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-brand py-2.5 fw-semibold">
                    Apply Filters
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-light border py-2 text-muted">
                    Reset All Filters
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupRecommendations(inputId, suggestionsContainerId, formId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(suggestionsContainerId);
        const form = document.getElementById(formId);
        if (!input || !container) return;

        let debounceTimer = null;

        input.addEventListener('input', function () {
            const query = input.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                container.innerHTML = '';
                container.classList.add('d-none');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/api/search/live?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        const suggestions = data.suggestions || [];
                        const products = data.products || [];

                        if (suggestions.length === 0 && products.length === 0) {
                            container.innerHTML = `<div class="p-2 text-center text-muted small"><i class="bi bi-search me-1"></i> No recommendations for "<strong>${escapeHtml(query)}</strong>"</div>`;
                            container.classList.remove('d-none');
                            return;
                        }

                        let html = '';

                        if (suggestions.length > 0) {
                            html += `<div class="px-2 pt-1 pb-1 small fw-bold text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 0.05em;"><i class="bi bi-lightbulb text-warning me-1"></i> Suggested Recommendations</div>`;
                            html += `<div class="d-flex flex-wrap gap-1 p-1 mb-2">`;
                            suggestions.forEach(sug => {
                                html += `<button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-dark recommendation-chip" style="font-size: 0.8rem;" data-val="${escapeHtml(sug)}">
                                    <i class="bi bi-arrow-up-right-circle text-success me-1"></i>${escapeHtml(sug)}
                                </button>`;
                            });
                            html += `</div>`;
                        }

                        if (products.length > 0) {
                            html += `<div class="px-2 pt-1 pb-1 small fw-bold text-uppercase text-muted border-top" style="font-size: 0.7rem; letter-spacing: 0.05em;"><i class="bi bi-basket text-success me-1"></i> Matching Produce</div>`;
                            html += `<div class="list-group list-group-flush">`;
                            products.forEach(prod => {
                                html += `<a href="${prod.url}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 p-2 border-0 rounded-2" style="font-size: 0.82rem;">
                                    <img src="${prod.image}" alt="${escapeHtml(prod.name)}" class="rounded" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div class="flex-grow-1 text-truncate">
                                        <div class="fw-semibold text-dark text-truncate">${escapeHtml(prod.name)}</div>
                                        <div class="text-muted small">${escapeHtml(prod.farmer)}</div>
                                    </div>
                                    <span class="text-success fw-bold">${prod.price}</span>
                                </a>`;
                            });
                            html += `</div>`;
                        }

                        container.innerHTML = html;
                        container.classList.remove('d-none');

                        // Bind chip click events
                        container.querySelectorAll('.recommendation-chip').forEach(btn => {
                            btn.addEventListener('click', function () {
                                input.value = this.getAttribute('data-val');
                                container.classList.add('d-none');
                                if (form) form.submit();
                            });
                        });
                    })
                    .catch(() => {
                        container.classList.add('d-none');
                    });
            }, 220);
        });

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !container.contains(e.target)) {
                container.classList.add('d-none');
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                container.classList.add('d-none');
            }
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    setupRecommendations('desktopProductSearchInput', 'desktopProductSearchSuggestions', 'desktopFilterForm');
    setupRecommendations('mobileProductSearchInput', 'mobileProductSearchSuggestions', 'mobileProductSearchForm');
});
</script>
@endsection
