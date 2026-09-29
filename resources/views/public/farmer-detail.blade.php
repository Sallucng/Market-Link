@extends('layouts.app')

@section('title', $farmer->stall_name . ' — MarketLink')

@section('styles')
<style>
    .farmer-cover-banner {
        height: 220px;
        width: 100%;
        object-fit: cover;
        border-radius: 16px 16px 0 0;
        background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
    }

    .farmer-avatar-overlap {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        object-fit: cover;
        background-color: #ffffff;
        margin-top: -65px;
        position: relative;
        z-index: 2;
    }

    #stall-map {
        height: 240px;
        border-radius: 12px;
        width: 100%;
        z-index: 1;
    }

    @media (max-width: 767.98px) {
        .farmer-cover-banner {
            height: 160px;
        }
        .farmer-avatar-overlap {
            width: 100px;
            height: 100px;
            margin-top: -50px;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('markets.index') }}" class="text-success text-decoration-none">Markets</a></li>
            @if($farmer->market)
                <li class="breadcrumb-item"><a href="{{ route('markets.show', $farmer->market->id) }}" class="text-success text-decoration-none">{{ $farmer->market->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $farmer->stall_name }}</li>
        </ol>
    </nav>

    <!-- Top Farmer Profile Card with Cover Banner & Avatar (Matches User Annotations) -->
    <div class="card card-custom bg-white border-0 shadow-sm mb-4 overflow-hidden">
        <!-- Cover Banner Background -->
        <div class="position-relative">
            <img src="{{ $farmer->cover_image_url ?: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1600&q=80' }}" 
                 alt="Farm Cover Banner" 
                 class="farmer-cover-banner">
            <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.45), transparent);"></div>
        </div>

        <div class="card-body px-4 pb-4 pt-0">
            <!-- Overlapping Profile Avatar -->
            <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    <img src="{{ $farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=300&q=80' }}" 
                         alt="{{ $farmer->stall_name }}" 
                         class="farmer-avatar-overlap">
                    <div class="pt-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="badge badge-brand px-3 py-1 rounded-pill">
                                <i class="bi bi-patch-check-fill me-1"></i> Verified Local Grower
                            </span>
                            @if($farmer->reviews->count() > 0)
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                    <i class="bi bi-star-fill text-warning me-1"></i> {{ number_format($farmer->averageRating(), 1) }} ({{ $farmer->reviews->count() }} reviews)
                                </span>
                            @endif
                        </div>
                        <h2 class="heading-serif fw-bold text-dark mb-0">{{ $farmer->contact_person ?: ($farmer->user->name ?? 'Local Grower') }}</h2>
                        <h5 class="text-success fw-semibold mb-0">{{ $farmer->stall_name }}</h5>
                    </div>
                </div>

                <!-- Favorite / Message Actions -->
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    @auth
                        @if(Auth::user()->isCustomer())
                            <form action="{{ route('customer.messages.start') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                                <button type="submit" class="btn btn-sm btn-brand rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-chat-dots-fill"></i>
                                    <span>Message Stall</span>
                                </button>
                            </form>
                            @php
                                $isFarmerFav = \App\Models\Favorite::where('customer_id', Auth::id())
                                    ->where('item_type', 'farmer')
                                    ->where('item_id', $farmer->id)
                                    ->exists();
                            @endphp
                            <form action="{{ route('customer.favorites.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="item_type" value="farmer">
                                <input type="hidden" name="item_id" value="{{ $farmer->id }}">
                                <button type="submit" class="btn btn-sm {{ $isFarmerFav ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 py-2 shadow-xs">
                                    <i class="bi {{ $isFarmerFav ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                                    {{ $isFarmerFav ? 'Saved in Favorites' : 'Save Favorite' }}
                                </button>
                            </form>
                        @endif
                    @else
                        <form action="{{ route('customer.messages.start') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                            <button type="submit" class="btn btn-sm btn-brand-outline rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>Message Stall</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>

            <!-- About Farmer Bio Section -->
            <div class="mt-4 pt-3 border-top">
                <h5 class="fw-bold text-dark mb-2" style="font-family: var(--font-heading, 'Poppins', sans-serif);">
                    <i class="bi bi-person-lines-fill text-success me-2"></i>About Farmer
                </h5>
                <p class="text-secondary leading-relaxed mb-0" style="max-width: 880px; font-size: 0.95rem;">
                    {{ $farmer->bio ?: 'Sustainable regional grower dedicated to providing fresh, organic, seasonal produce harvested with care and distributed through community farmers markets.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Active Farmer Sale / Promotional Showcase Banner -->
    @if(isset($activeSale) && $activeSale)
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative text-white" 
             style="background: linear-gradient(135deg, #0b2e1b 0%, #164e2b 45%, #2d6a4f 100%);">
            
            @if($activeSale->banner_image)
                <div class="position-absolute top-0 start-0 w-100 h-100" 
                     style="opacity: 0.28; background: url('{{ $activeSale->banner_image }}') center/cover no-repeat; filter: saturate(1.2);"></div>
            @endif
            
            <div class="card-body p-4 p-md-5 position-relative z-1">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge px-3 py-1.5 rounded-pill shadow-xs text-uppercase fw-bold" 
                                  style="background: #ffd166; color: #1b4332; font-size: 0.8rem; letter-spacing: 0.04em;">
                                <i class="bi bi-tag-fill me-1"></i> {{ $activeSale->computed_badge }}
                            </span>
                            @if($activeSale->is_featured)
                                <span class="badge px-2.5 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); font-size: 0.75rem;">
                                    ⭐ Homepage Spotlight Special
                                </span>
                            @endif
                            <span class="text-white-50 small">
                                <i class="bi bi-calendar-event me-1"></i> Valid through {{ $activeSale->end_date->format('M d, Y') }}
                            </span>
                        </div>

                        <h2 class="heading-serif fw-bold text-white mb-2" style="font-size: 1.85rem;">
                            {{ $activeSale->title }}
                        </h2>

                        @if($activeSale->description)
                            <p class="text-white-50 mb-3" style="font-size: 0.95rem; line-height: 1.6; max-width: 680px;">
                                {{ $activeSale->description }}
                            </p>
                        @endif

                        <div class="d-flex align-items-center gap-3 flex-wrap pt-1">
                            @if($activeSale->products->isNotEmpty())
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-1.5 small">
                                    <i class="bi bi-bag-check-fill text-warning me-1"></i> Applies to {{ $activeSale->products->count() }} selected {{ Str::plural('harvest', $activeSale->products->count()) }} below
                                </span>
                            @else
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-1.5 small">
                                    <i class="bi bi-stars text-warning me-1"></i> Applies stall-wide across all weekly harvests
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <div class="p-3 rounded-3 d-inline-block text-center w-100" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.2); max-width: 320px;">
                            <div class="small text-uppercase tracking-wider text-white-50 fw-semibold mb-1">Stall Special</div>
                            @if($activeSale->discount_percentage)
                                <div class="display-6 fw-bold text-warning mb-1" style="font-family: var(--font-heading, sans-serif);">
                                    {{ $activeSale->discount_percentage }}% <span class="fs-4">OFF</span>
                                </div>
                            @else
                                <div class="fs-4 fw-bold text-warning mb-1">
                                    {{ $activeSale->badge_label ?: 'SPECIAL OFFER' }}
                                </div>
                            @endif
                            <div class="small text-white-50">Pre-order now & settle at market pickup</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Row: Left Products/Reviews vs Right Stall Info/Map (Per User Red Annotation) -->
    <div class="row g-4 mb-5">
        <!-- LEFT COLUMN (col-lg-8): Products & Customer Reviews -->
        <div class="col-lg-8">
            <!-- Current Weekly Stock Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="heading-serif fw-bold text-dark mb-0">Current Weekly Stock</h3>
                <span class="badge bg-light text-secondary border px-3 py-1">
                    {{ $farmer->products->count() }} fresh harvests available
                </span>
            </div>

            <div class="row g-3 mb-5">
                @forelse($farmer->products as $product)
                    @php
                        $isOnSale = false;
                        $productSaleBadge = null;
                        $discountPct = null;
                        if (isset($activeSale) && $activeSale) {
                            if ($activeSale->products->isEmpty() || $activeSale->products->contains('id', $product->id)) {
                                $isOnSale = true;
                                $productSaleBadge = $activeSale->computed_badge;
                                $discountPct = $activeSale->discount_percentage;
                            }
                        }
                    @endphp
                    <div class="col-md-6">
                        <div class="card card-custom h-100 bg-white border-0 shadow-sm d-flex flex-column position-relative overflow-hidden">
                            <!-- Image Wrap with Optional Sale Badge -->
                            <div class="position-relative overflow-hidden">
                                <a href="{{ route('products.show', $product->id) }}" class="d-block text-decoration-none" title="{{ $product->name }}">
                                    <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=80' }}" 
                                         class="card-img-top" 
                                         alt="{{ $product->name }}"
                                         style="height: 180px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px; transition: transform 0.3s ease;">
                                </a>
                                
                                @if($isOnSale)
                                    <div class="position-absolute top-0 start-0 m-2 z-2">
                                        <span class="badge shadow-sm px-2.5 py-1 text-uppercase fw-bold" 
                                              style="background: #e63946; color: #ffffff; font-size: 0.72rem; letter-spacing: 0.03em;">
                                            <i class="bi bi-tag-fill me-1"></i> {{ $productSaleBadge }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-3 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="badge bg-light text-dark border small">{{ $product->category->name }}</span>
                                    @if($product->stock_quantity > 0 && $product->stock_quantity <= 5)
                                        <span class="badge bg-warning bg-opacity-10 text-danger border border-warning small">Only {{ $product->stock_quantity }} left</span>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-2 text-truncate-2" style="font-size: 0.82rem; min-height: 2.4em;">
                                    {{ $product->description }}
                                </p>

                                <!-- Pricing with Optional Sale Discount Display -->
                                @if($isOnSale && $discountPct)
                                    @php $discountedPrice = round($product->price * (1 - ($discountPct / 100)), 2); @endphp
                                    <div class="d-flex align-items-baseline gap-2 mb-3">
                                        <span class="text-danger fw-bold fs-5">${{ number_format($discountedPrice, 2) }}</span>
                                        <span class="text-muted text-decoration-line-through small">${{ number_format($product->price, 2) }}</span>
                                        <span class="text-muted small fw-normal">/ {{ $product->unit }}</span>
                                    </div>
                                @else
                                    <div class="text-success fw-bold fs-5 mb-3">
                                        ${{ number_format($product->price, 2) }} 
                                        <span class="text-muted small fw-normal">/ {{ $product->unit }}</span>
                                    </div>
                                @endif
                                
                                <div class="mt-auto">
                                    @if($product->is_sold_out || $product->stock_quantity <= 0)
                                        <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>Sold Out This Week</button>
                                    @else
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-brand-outline btn-sm w-100 rounded-pill fw-semibold">
                                                <i class="bi bi-cart-plus me-1"></i> Pre-Order Harvest
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info border-0 shadow-xs rounded-3 p-4 text-center">
                            <i class="bi bi-calendar-x fs-2 text-info d-block mb-2"></i>
                            <strong>No harvests published for this week yet.</strong>
                            <p class="small text-muted mb-0 mt-1">This grower usually posts fresh inventory 48 hours prior to market day. Please check back soon!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Customer Reviews for this Stall -->
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="heading-serif fw-bold text-dark mb-0">Customer Feedback & Ratings</h4>
                    @if($farmer->reviews->count() > 0)
                        <div class="text-warning small d-flex align-items-center gap-1">
                            <span class="fw-bold text-dark fs-6">{{ number_format($farmer->averageRating(), 1) }}</span>
                            @for($i=1; $i<=5; $i++)
                                <i class="bi {{ $i <= round($farmer->averageRating()) ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                            <span class="text-muted ms-1">({{ $farmer->reviews->count() }})</span>
                        </div>
                    @endif
                </div>

                @forelse($farmer->reviews as $review)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <span class="fw-bold text-dark">{{ $review->customer->name ?? 'Verified Shopper' }}</span>
                                <small class="text-muted ms-2">{{ $review->created_at ? $review->created_at->format('M d, Y') : 'Recent' }}</small>
                            </div>
                            <div class="text-warning small">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-secondary small mb-2">{{ $review->comment }}</p>
                        @if($review->farmer_response)
                            <div class="bg-light p-2 rounded small ms-3 border-start border-success border-3">
                                <strong class="text-success"><i class="bi bi-reply-fill me-1"></i>Grower Response:</strong>
                                <span class="text-muted">{{ $review->farmer_response }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted small mb-0"><i class="bi bi-chat-square-text me-1"></i>No customer reviews submitted for this stall yet.</p>
                @endforelse
            </div>
        </div>

        <!-- RIGHT COLUMN (col-lg-4): Stall Information & Interactive Map (Moved Per User Red Arrows) -->
        <div class="col-lg-4">
            <!-- Vendor & Stall Information Card -->
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                    <div class="rounded-circle bg-brand-light p-2 text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-shop fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-family: var(--font-heading, 'Poppins', sans-serif);">Vendor & Stall Details</h6>
                        <small class="text-muted">MarketLink Verified Stall</small>
                    </div>
                </div>

                <div class="vstack gap-3 small">
                    <!-- Host Market -->
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-geo-alt text-success me-1"></i> Host Market</div>
                        @if($farmer->market)
                            <a href="{{ route('markets.show', $farmer->market->id) }}" class="fw-bold text-success text-decoration-none d-flex align-items-center justify-content-between bg-light p-2 rounded border border-light">
                                <span>{{ $farmer->market->name }}</span>
                                <i class="bi bi-arrow-right-short fs-5"></i>
                            </a>
                        @else
                            <span class="text-dark fw-semibold">Local Community Market</span>
                        @endif
                    </div>

                    <!-- Stall Location -->
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-pin-map text-danger me-1"></i> Stall Location</div>
                        <div class="fw-semibold text-dark">{{ $farmer->address }}</div>
                    </div>

                    <!-- Operating Days -->
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-calendar-check text-primary me-1"></i> Operating Days</div>
                        <div class="fw-semibold text-dark">{{ is_array($farmer->operating_days) ? implode(', ', $farmer->operating_days) : ($farmer->operating_days ?: 'Saturday, Sunday') }}</div>
                    </div>

                    <!-- Pickup Windows -->
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-clock-history text-warning me-1"></i> Pickup Windows</div>
                        <div class="fw-semibold text-dark">{{ is_array($farmer->pickup_time_windows) ? implode(', ', $farmer->pickup_time_windows) : ($farmer->pickup_time_windows ?: '08:30 AM - 10:30 AM, 11:00 AM - 01:00 PM') }}</div>
                    </div>

                    <!-- Order Cutoff -->
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-hourglass-split text-danger me-1"></i> Pre-Order Cutoff</div>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                            {{ $farmer->cutoff_hours ?: 2 }} hours prior to market opening
                        </span>
                    </div>

                    <!-- Stall Representative & In-App Messaging -->
                    <div class="pt-2 border-top">
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-person-badge text-secondary me-1"></i> Stall Representative</div>
                        <div class="text-dark fw-semibold mb-2">{{ $farmer->contact_person }}</div>
                        <form action="{{ route('customer.messages.start') }}" method="POST">
                            @csrf
                            <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                            <button type="submit" class="btn btn-sm btn-brand rounded-pill px-3 py-1.5 w-100 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                                <i class="bi bi-chat-dots-fill"></i> Chat with Stall
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Adjusted Interactive Stall Map Card -->
            <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Stall Map Pin</span>
                    <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">Live GPS</span>
                </div>
                
                <div id="stall-map" class="shadow-inner"></div>

                <div class="mt-2 px-1 d-flex justify-content-between align-items-center">
                    <small class="text-muted text-truncate me-2" style="font-size: 0.76rem;" title="{{ $farmer->address }}">
                        <i class="bi bi-pin-map text-danger me-1"></i> {{ $farmer->address }}
                    </small>
                    @if($farmer->latitude && $farmer->longitude)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $farmer->latitude }},{{ $farmer->longitude }}" 
                           target="_blank" 
                           rel="noopener" 
                           class="btn btn-xs btn-outline-success rounded-pill px-2 py-0 text-nowrap" 
                           style="font-size: 0.72rem;">
                            Directions <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Confidential Stall Feedback / Report Option (Admin Eyes Only) -->
            <div class="card border-0 rounded-4 bg-light bg-opacity-75 p-3 mb-4 text-center">
                <div class="small text-muted mb-2">
                    <i class="bi bi-shield-lock text-danger me-1"></i> Experienced an issue with this stall?
                </div>
                @auth
                    @if(Auth::user()->isCustomer())
                        <a href="{{ route('customer.complaints.create', ['farmer_id' => $farmer->id]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 w-100 d-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.82rem;">
                            <i class="bi bi-flag"></i> File Confidential Complaint
                        </a>
                    @else
                        <span class="text-muted small" style="font-size: 0.75rem;">Shopper account required to report stall.</span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 w-100" style="font-size: 0.82rem;">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Report Stall
                    </a>
                @endauth
                <div class="text-muted mt-2" style="font-size: 0.72rem; line-height: 1.4;">
                    <i class="bi bi-lock-fill text-secondary"></i> Strictly confidential: Submitted directly to platform administration. Never shown publicly or to the grower.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($farmer->latitude && $farmer->longitude)
<script>
    document.addEventListener("DOMContentLoaded", function() {
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

        setTimeout(() => stallMap.invalidateSize(), 300);
    });
</script>
@endif
@endsection
