@extends('layouts.app')

@section('title', 'MarketLink — Local Farmers Market Pre-Orders')

@section('content')

<!-- Hero Section (eye-catching ambient minimal farm banner) -->
<style>
    /* Lightweight GPU-accelerated motion graphics (antigravity-design-expert) */
    .hero-section-wrapper {
        will-change: transform;
        transform-origin: center center;
    }
    
    /* Gentle ambient organic floating motion (compositor-only 3D transforms, 0% CPU cost) */
    @keyframes floatOrganic1 {
        0%, 100% { transform: translate3d(0, 0, 0) rotate(0deg); }
        50% { transform: translate3d(14px, -18px, 0) rotate(8deg); }
    }
    @keyframes floatOrganic2 {
        0%, 100% { transform: translate3d(0, 0, 0) rotate(0deg); }
        50% { transform: translate3d(-16px, -24px, 0) rotate(-12deg); }
    }
    @keyframes floatOrganic3 {
        0%, 100% { transform: translate3d(0, 0, 0) rotate(0deg); }
        50% { transform: translate3d(12px, 16px, 0) rotate(6deg); }
    }
    @keyframes ambientLevitate {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    @keyframes pulseGlow {
        0%, 100% { opacity: 0.4; transform: scale(1); }
        50% { opacity: 0.75; transform: scale(1.08); }
    }

    .particle-float-1 {
        animation: floatOrganic1 7s ease-in-out infinite;
        will-change: transform;
    }
    .particle-float-2 {
        animation: floatOrganic2 8.5s ease-in-out infinite 0.7s;
        will-change: transform;
    }
    .particle-float-3 {
        animation: floatOrganic3 6.8s ease-in-out infinite 1.4s;
        will-change: transform;
    }
    .ambient-levitate {
        animation: ambientLevitate 4.5s ease-in-out infinite;
        will-change: transform;
    }
    .ambient-glow-circle {
        animation: pulseGlow 5s ease-in-out infinite alternate;
        will-change: transform, opacity;
    }

    /* Shimmer gradient on hero title highlight */
    @keyframes shimmerGoldGreen {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    .shimmer-text {
        background: linear-gradient(90deg, #74c69d 0%, #d8f3dc 25%, #ffd166 50%, #74c69d 75%, #d8f3dc 100%);
        background-size: 250% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: shimmerGoldGreen 12s ease-in-out infinite 0.5s;
    }

    /* Frosted glass pills for sales and metrics */
    .sale-glass-pill {
        background: rgba(255, 255, 255, 0.16) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #ffffff !important;
        backdrop-filter: blur(8px);
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, border-color 0.2s ease;
    }
    .sale-glass-pill:hover {
        background: rgba(255, 255, 255, 0.28) !important;
        border-color: rgba(255, 255, 255, 0.55) !important;
        color: #ffffff !important;
        transform: translateY(-2px);
    }

    /* Live Stat Bar Pill */
    .hero-stat-pill {
        background: rgba(255, 255, 255, 0.13);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 999px;
        padding: 0.4rem 1rem;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.83rem;
        transition: transform 0.22s ease, background-color 0.2s ease;
    }
    .hero-stat-pill:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.22);
    }

    .hero-bento-card {
        transform-style: preserve-3d;
        perspective: 900px;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    }

    /* Top Rated Farmers Horizontal Carousel Styles */
    .top-farmers-carousel-track {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 1.25rem;
        padding: 0.5rem 0.25rem 1.25rem 0.25rem;
        scrollbar-width: none;
        -ms-overflow-style: none;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    .top-farmers-carousel-track::-webkit-scrollbar {
        display: none;
    }
    .top-farmer-carousel-item {
        flex: 0 0 295px;
        max-width: 295px;
        scroll-snap-align: start;
    }
    @media (min-width: 768px) {
        .top-farmer-carousel-item {
            flex: 0 0 310px;
            max-width: 310px;
        }
    }
    .top-farmer-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .top-farmer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 28px -6px rgba(22, 101, 52, 0.16), 0 6px 12px -3px rgba(0, 0, 0, 0.06) !important;
    }
    .top-farmer-banner-wrap {
        height: 140px;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        background: #e9ecef;
    }
    .top-farmer-banner-img {
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .top-farmer-card:hover .top-farmer-banner-img {
        transform: scale(1.05);
    }
    .top-farmer-avatar-badge {
        width: 60px;
        height: 60px;
        border: 3.5px solid #ffffff;
        margin-top: -30px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        overflow: hidden;
    }
    .top-farmer-rank-badge {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        padding: 0.32rem 0.65rem;
        border-radius: 9999px;
        backdrop-filter: blur(8px);
    }
    .top-farmer-rank-badge.rank-1 {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #ffffff;
        border: 1px solid rgba(251, 191, 36, 0.4);
    }
    .top-farmer-rank-badge.rank-2 {
        background: linear-gradient(135deg, #475569, #334155);
        color: #ffffff;
        border: 1px solid rgba(203, 213, 225, 0.4);
    }
    .top-farmer-rank-badge.rank-3 {
        background: linear-gradient(135deg, #b45309, #92400e);
        color: #ffffff;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .top-farmer-rank-badge.rank-other {
        background: rgba(15, 23, 42, 0.78);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .top-farmer-rating-pill {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(6px);
        color: #1e293b;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.3rem 0.55rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
    }
    .top-farmer-rating-pill .rating-sub {
        color: #64748b;
        font-size: 0.7rem;
        margin-left: 2px;
    }
    .carousel-control-pill-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid rgba(22, 101, 52, 0.22);
        background: #ffffff;
        color: var(--brand-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .carousel-control-pill-btn:hover {
        background: var(--brand-primary);
        color: #ffffff;
        border-color: var(--brand-primary);
        transform: scale(1.06);
    }
    .carousel-control-pill-btn:active {
        transform: scale(0.95);
    }
    .carousel-control-pill-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none !important;
    }
</style>

<section class="py-5 py-lg-6 border-bottom position-relative overflow-hidden d-flex align-items-center hero-section-wrapper" 
         style="background: linear-gradient(135deg, rgba(11, 33, 22, 0.92) 0%, rgba(11, 33, 22, 0.80) 45%, rgba(11, 33, 22, 0.50) 100%), url('https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1920&q=85') center center / cover no-repeat; min-height: 700px;">

    <!-- Minimal Organic Floating Motion Particles (SVG silhouettes, 100% GPU accelerated) -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden pointer-events-none" style="z-index: 1; pointer-events: none;">
        <!-- Leaf Silhouette 1 (Top Left) -->
        <svg class="position-absolute particle-float-1" style="top: 14%; left: 8%; width: 44px; height: 44px; opacity: 0.22; fill: #74c69d;" viewBox="0 0 24 24">
            <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z"/>
        </svg>

        <!-- Golden Sun Harvest Mote (Top Right) -->
        <svg class="position-absolute particle-float-2" style="top: 18%; right: 12%; width: 56px; height: 56px; opacity: 0.25; fill: #ffd166;" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="6"/>
            <path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.93 4.93l2.12 2.12M16.95 16.95l2.12 2.12M4.93 19.07l2.12-2.12M16.95 7.05l2.12-2.12" stroke="#ffd166" stroke-width="2" stroke-linecap="round"/>
        </svg>

        <!-- Sprout Silhouette (Bottom Right) -->
        <svg class="position-absolute particle-float-3" style="bottom: 22%; right: 9%; width: 38px; height: 38px; opacity: 0.20; fill: #52b788;" viewBox="0 0 24 24">
            <path d="M2,22V20C2,20 7,20 10,16C12,13.3 12,10 12,10C12,10 13.2,11.8 14.5,13C16,14.4 18,15 20,15V17C17.5,17 15.2,16.2 13.5,14.8C12.6,17.4 10.6,20.2 8,21.3V22H2M12,10C12,6.5 9.5,4 6,4C6,7.5 8.5,10 12,10M12,10C15.5,10 18,7.5 18,4C14.5,4 12,6.5 12,10Z"/>
        </svg>

        <!-- Ambient Glow Orb (Behind search bento) -->
        <div class="position-absolute rounded-circle ambient-glow-circle" 
             style="top: 30%; left: 50%; transform: translate(-50%, -50%); width: 420px; height: 420px; background: radial-gradient(circle, rgba(82, 183, 136, 0.18) 0%, transparent 70%);"></div>
    </div>

    <!-- Verified Local Farmers Badge in bottom left corner of the home page banner -->
    <div class="position-absolute bottom-0 start-0 m-3 m-md-4 d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill ambient-levitate hero-badge-float" 
         style="background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.28); color: #d8f3dc; font-size: 0.85rem; z-index: 10;">
        <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px; box-shadow: 0 0 10px #52b788;"></span>
        <i class="bi bi-shield-check text-success"></i>
        <span class="fw-medium">Verified Local Farmers &amp; Pre-Orders &bull; Season 2026</span>
    </div>

    <div class="container py-3 py-lg-4 position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-9 col-xl-8 hero-content-col d-flex flex-column align-items-center">

                <!-- Kinetic Title with Staggered Visual Reveal -->
                <div class="hero-title-wrap mb-3 text-center">
                    <h1 class="display-4 fw-bold heading-serif text-white mb-2 hero-title-line-1" style="letter-spacing: -0.035em; line-height: 1.12;">
                        Farm Fresh Products,
                    </h1>
                    <div class="hero-title-line-2 display-4 fw-bold heading-serif" style="letter-spacing: -0.035em; line-height: 1.12;">
                        <span class="shimmer-text fst-italic">Just a Click Away.</span>
                    </div>
                </div>

                <p class="text-white-50 mb-4 hero-subtitle mx-auto" style="max-width: 580px; font-size: 0.98rem; line-height: 1.55;">
                    Skip weekend checkout lines. Discover verified neighborhood growers, reserve fresh harvest beforehand, and pick up stall-side.
                </p>

                <!-- Market Day Quick Finder Bento Card -->
                <div class="card card-custom hero-bento-card p-3 p-md-4 mb-4 shadow-lg border-0 mx-auto w-100 text-start" 
                     style="background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(16px); border-radius: 16px; max-width: 640px; box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.35);">
                    <form action="{{ route('markets.index') }}" method="GET">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-7">
                                <label class="small text-muted fw-semibold mb-1 d-flex align-items-center">
                                    <i class="bi bi-calendar3 text-success me-1"></i> Find Markets Open On:
                                </label>
                                <select name="day" class="form-select" style="border: 1px solid var(--border-card); border-radius: 8px;">
                                    <option value="">Select Market Day (e.g. Saturday)</option>
                                    <option value="Saturday">Saturday Harvest Markets</option>
                                    <option value="Sunday">Sunday Harvest Markets</option>
                                    <option value="Wednesday">Wednesday Mid-week Markets</option>
                                </select>
                            </div>
                            <div class="col-md-5 pt-md-4">
                                <button type="submit" class="btn btn-brand w-100 py-2">
                                    <i class="bi bi-search me-1"></i> Locate Markets
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Three Key Pillars (SRS §1.5) -->
                <div class="d-flex flex-wrap justify-content-center gap-2.5 mb-3">
                    <div class="hero-pillar-pill px-3 py-1.5 rounded-pill d-inline-flex align-items-center small" 
                         style="background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff;">
                        <i class="bi bi-shop me-1.5 text-success"></i> In-Person Stall Pickup
                    </div>
                    <div class="hero-pillar-pill px-3 py-1.5 rounded-pill d-inline-flex align-items-center small" 
                         style="background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff;">
                        <i class="bi bi-currency-dollar me-1.5 text-warning"></i> Zero Online Markups
                    </div>
                    <div class="hero-pillar-pill px-3 py-1.5 rounded-pill d-inline-flex align-items-center small" 
                         style="background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff;">
                        <i class="bi bi-map me-1.5 text-info"></i> OpenStreetMap Powered
                    </div>
                </div>

                <!-- Live Animated Community Harvest Metrics Ribbon (Provides active 3-second motion & confidence) -->
                <div class="hero-metrics-bar d-flex flex-wrap justify-content-center gap-2 pt-1">
                    <div class="hero-stat-pill">
                        <i class="bi bi-patch-check-fill text-success"></i>
                        <span class="hero-counter-num fw-bold fs-6" data-target="{{ $stats['farmers'] }}">0</span>
                        <span class="text-white-50">Verified Stalls</span>
                    </div>
                    <div class="hero-stat-pill">
                        <i class="bi bi-geo-alt-fill text-info"></i>
                        <span class="hero-counter-num fw-bold fs-6" data-target="{{ $stats['markets'] }}">0</span>
                        <span class="text-white-50">Market Plazas</span>
                    </div>
                    <div class="hero-stat-pill">
                        <i class="bi bi-basket2-fill text-warning"></i>
                        <span class="hero-counter-num fw-bold fs-6" data-target="{{ $stats['products'] }}">0</span>
                        <span class="text-white-50">Harvest Items</span>
                    </div>
                    <div class="hero-stat-pill">
                        <i class="bi bi-shield-fill-check text-light"></i>
                        <span class="fw-bold fs-6 text-warning">100%</span>
                        <span class="text-white-50">Soil-to-Table</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@if(isset($featuredSale) && $featuredSale)
<!-- Featured Farmer Sale / Harvest Deal Showcase -->
<style>
    .featured-sale-card {
        background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%) !important;
        border-radius: 20px !important;
        color: #ffffff !important;
        box-shadow: 0 16px 36px -8px rgba(27, 67, 50, 0.28) !important;
    }
</style>
<section class="py-4" style="background: linear-gradient(180deg, #f0f7f2 0%, #ffffff 100%);">
    <div class="container py-2">
        <div class="card featured-sale-card border-0 shadow-lg overflow-hidden position-relative">
            
            <!-- Background ambient glow -->
            <div class="position-absolute top-0 end-0 h-100 w-50 d-none d-lg-block" 
                 style="background: radial-gradient(circle at 80% 30%, rgba(116, 198, 157, 0.25) 0%, transparent 70%); pointer-events: none;"></div>

            <div class="row g-0 align-items-center">
                <!-- Sale Banner Image & Badge -->
                <div class="col-lg-5 position-relative" style="min-height: 280px; height: 100%;">
                    @php
                        $bannerImg = $featuredSale->banner_image ?: 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1200&q=80';
                    @endphp
                    <img src="{{ $bannerImg }}" 
                         alt="{{ $featuredSale->title }}" 
                         class="w-100 h-100 position-absolute top-0 start-0" 
                         style="object-fit: cover; opacity: 0.88;">
                    
                    <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-2" style="z-index: 3;">
                        <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem; letter-spacing: 0.02em;">
                            <i class="bi bi-stars text-dark"></i> FEATURED HARVEST SPECIAL
                        </span>
                        <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                            <i class="bi bi-lightning-fill"></i> {{ $featuredSale->computed_badge }}
                        </span>
                    </div>

                    <div class="position-absolute bottom-0 start-0 end-0 p-3" 
                         style="background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, transparent 100%); z-index: 3;">
                        <small class="text-white-50 d-block font-mono-meta" style="font-size: 0.72rem;">HOST MARKET PLAZA</small>
                        <span class="text-white fw-bold small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $featuredSale->farmer->market ? $featuredSale->farmer->market->name : 'Community Market Plaza' }}</span>
                    </div>
                </div>

                <!-- Sale Content & Call to Action -->
                <div class="col-lg-7 p-4 p-md-5 d-flex flex-column justify-content-between position-relative" style="z-index: 2;">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <a href="{{ route('farmers.show', $featuredSale->farmer->id) }}" class="sale-glass-pill text-decoration-none d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill small shadow-2xs">
                                <i class="bi bi-shop text-success"></i>
                                <span class="fw-semibold text-white">{{ $featuredSale->farmer->stall_name }}</span>
                            </a>
                            <span class="text-white-50 small">&bull;</span>
                            <span class="text-white-50 small">
                                <i class="bi bi-calendar3 me-1 text-warning"></i>
                                Valid {{ $featuredSale->start_date->format('M d') }} &ndash; {{ $featuredSale->end_date->format('M d, Y') }}
                                @if($featuredSale->end_date->isToday())
                                    <strong class="text-warning">(Ends Today!)</strong>
                                @elseif($featuredSale->end_date->isFuture())
                                    <span class="text-white-50">({{ $featuredSale->end_date->diffForHumans() }})</span>
                                @endif
                            </span>
                        </div>

                        <h3 class="display-6 fw-bold heading-serif text-white mb-2" style="letter-spacing: -0.02em;">
                            {{ $featuredSale->title }}
                        </h3>

                        <p class="text-white-50 mb-3" style="line-height: 1.55; max-width: 620px;">
                            {{ $featuredSale->description ?: 'Take advantage of limited-time seasonal savings straight from verified community growers!' }}
                        </p>

                        <!-- Attached Products Highlights -->
                        @if($featuredSale->products->isNotEmpty())
                            <div class="mb-4">
                                <div class="text-white-50 small fw-semibold text-uppercase mb-2 font-mono-meta" style="font-size: 0.72rem;">
                                    Discounted Harvest Items Included:
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($featuredSale->products->take(4) as $saleProduct)
                                        <a href="{{ route('products.show', $saleProduct->id) }}" class="sale-glass-pill text-decoration-none rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs">
                                            <i class="bi bi-check-circle-fill text-warning" style="font-size: 0.75rem;"></i>
                                            <span class="text-white fw-medium">{{ $saleProduct->name }}</span>
                                            <span class="text-white-50 small">(${{ number_format($saleProduct->price, 2) }})</span>
                                        </a>
                                    @endforeach
                                    @if($featuredSale->products->count() > 4)
                                        <span class="sale-glass-pill rounded-pill px-2.5 py-1.5 small text-white-50">
                                            +{{ $featuredSale->products->count() - 4 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3 pt-2">
                        <a href="{{ route('farmers.show', $featuredSale->farmer->id) }}" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-basket3-fill"></i>
                            <span>Shop {{ $featuredSale->farmer->stall_name }} Sale</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-light rounded-pill px-4 py-2 small">
                            Browse All Harvest Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Seasonal Categories Bento Grid -->
<style>
    .category-bento-link .category-bento-card {
        transition: transform 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), border-color 0.28s ease;
    }
    .category-bento-link:hover .category-bento-card {
        transform: translateY(-4px);
        border-color: #2d5a27;
        box-shadow: 0 14px 28px -8px rgba(45, 90, 39, 0.12);
    }
    .category-bento-link:hover .category-icon-wrapper {
        transform: translateY(-2px);
        box-shadow: inset 0 1px 2px rgba(255,255,255,0.9), 0 8px 18px rgba(45, 90, 39, 0.1) !important;
    }
    .category-bento-link:hover .category-3d-img {
        transform: scale(1.1) rotate(-2deg);
    }
</style>
<section class="py-5">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge-pastel-green mb-2">Curated Harvest</span>
                <h2 class="heading-serif fw-bold text-dark mb-0">Browse by Fresh Product Category</h2>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-brand-outline btn-sm">
                View All Categories <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($categories as $cat)
                <div class="col-6 col-md-4 col-lg">
                    <a href="{{ route('products.index', ['category' => $cat->id]) }}" class="text-decoration-none category-bento-link d-block h-100">
                        <div class="bento-card category-bento-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-between">
                            <div class="category-icon-wrapper mb-3 d-flex align-items-center justify-content-center" 
                                 style="width: 82px; height: 82px; border-radius: 20px; background: radial-gradient(circle at 50% 35%, #ffffff 0%, #f3f7f2 100%); border: 1px solid rgba(45, 90, 39, 0.08); box-shadow: inset 0 1px 2px rgba(255,255,255,0.8), 0 4px 12px rgba(0, 0, 0, 0.03); transition: transform 0.25s ease, box-shadow 0.25s ease;">
                                <img src="{{ asset('images/categories/' . $cat->slug . '.png') }}" 
                                     alt="{{ $cat->name }}" 
                                     class="category-3d-img" 
                                     style="width: 62px; height: 62px; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.07)); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/categories/vegetables.png') }}';">
                            </div>
                            <div class="w-100">
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem; letter-spacing: -0.01em;">{{ $cat->name }}</h6>
                                <span class="text-muted small font-mono-meta">{{ $cat->products_count }} Products</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Fresh Products Grid -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge-pastel-green mb-2">Available for Pre-Order</span>
                <h2 class="heading-serif fw-bold text-dark mb-0">Harvested from Local Growers</h2>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-brand btn-sm">
                Full Catalog <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $product)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card card-custom h-100 d-flex flex-column position-relative">
                        <div class="position-relative">
                            <a href="{{ route('products.show', $product->id) }}" class="d-block text-decoration-none" title="{{ $product->name }}">
                                <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=600&q=80' }}" 
                                     onerror="this.src='https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=600&q=80'"
                                     class="card-img-top" 
                                     alt="{{ $product->name }}" 
                                     style="height: 175px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                            </a>
                            
                            <span class="position-absolute top-0 end-0 m-2 badge-pastel-slate" style="background: rgba(255,255,255,0.92); backdrop-filter: blur(4px); z-index: 2;">
                                {{ $product->category->name }}
                            </span>
                        </div>
                        <div class="card-body p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="position: relative; z-index: 2;">
                                <a href="{{ route('farmers.show', $product->farmer->id) }}" class="text-success fw-semibold text-decoration-none small">
                                    <i class="bi bi-shop me-1"></i>{{ $product->farmer->stall_name }}
                                </a>
                                @if($product->stock_quantity > 0)
                                    <span class="badge-pastel-green" style="font-size: 0.68rem; padding: 2px 6px;">
                                        {{ $product->stock_quantity }} {{ $product->unit }} left
                                    </span>
                                @else
                                    <span class="badge-pastel-red" style="font-size: 0.68rem; padding: 2px 6px;">
                                        Sold Out
                                    </span>
                                @endif
                            </div>

                            <h6 class="card-title fw-bold text-dark mb-2">
                                <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none stretched-link">
                                    {{ $product->name }}
                                </a>
                            </h6>

                            <div class="d-flex align-items-baseline gap-1 mb-2">
                                <span class="fs-5 fw-bold text-dark font-mono-meta">${{ number_format($product->price, 2) }}</span>
                                <span class="text-muted small">/ {{ $product->unit }}</span>
                            </div>

                            <p class="text-muted small flex-grow-1 mb-3" style="line-height: 1.5;">
                                {{ Str::limit($product->description, 60) }}
                            </p>
                            
                            <div class="mt-auto" style="position: relative; z-index: 2;">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-brand-outline w-100 btn-sm py-2" {{ $product->stock_quantity < 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-plus me-1"></i> {{ $product->stock_quantity < 1 ? 'Sold Out' : 'Pre-Order' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 21st.dev Animated Testimonials Column Grid under Harvested from Local Growers -->
<div id="home-testimonials-root"></div>

<!-- Top 10 Rated Farmers Horizontal Carousel (Directly under "What our community says") -->
@if(isset($topFarmers) && $topFarmers->isNotEmpty())
<section class="py-5" style="background: linear-gradient(180deg, #ffffff 0%, #fbfdfb 50%, #f4f8f4 100%); border-top: 1px solid rgba(22, 101, 52, 0.08); border-bottom: 1px solid rgba(22, 101, 52, 0.08);">
    <div class="container py-2">
        <!-- Section Header with Title & Controls -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end mb-4 gap-3">
            <div>
                <span class="badge-pastel-green mb-2 d-inline-flex align-items-center gap-1">
                    <i class="bi bi-trophy-fill text-warning"></i> Community Favorites
                </span>
                <h2 class="heading-serif fw-bold text-dark mb-2">
                    Top 10 Rated Growers
                </h2>
                <p class="text-muted mb-0" style="max-width: 620px; font-size: 0.95rem; line-height: 1.55;">
                    Meet our highest-rated farmers and independent producers, celebrated by shoppers for exceptional harvests, reliable pre-orders, and warm stall service.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 align-self-stretch align-self-md-auto justify-content-between justify-content-md-end">
                <a href="{{ route('farmers.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold text-decoration-none d-flex align-items-center gap-1 me-2">
                    Browse All <i class="bi bi-arrow-right"></i>
                </a>
                <div class="d-flex gap-2">
                    <button id="topFarmersPrev" class="carousel-control-pill-btn" aria-label="Previous Farmers" title="Scroll Left">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button id="topFarmersNext" class="carousel-control-pill-btn" aria-label="Next Farmers" title="Scroll Right">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Carousel Track Wrapper -->
        <div class="position-relative">
            <div id="topFarmersTrack" class="top-farmers-carousel-track">
                @foreach($topFarmers as $index => $topFarmer)
                    @php
                        $rank = $index + 1;
                        $rating = round($topFarmer->reviews_avg_rating ?? 4.9, 1);
                        $reviewsCount = $topFarmer->reviews_count ?? 0;
                        $productsCount = $topFarmer->products_count ?? 0;
                    @endphp
                    <div class="top-farmer-carousel-item">
                        <div class="card h-100 top-farmer-card border-0 shadow-sm position-relative overflow-hidden">
                            <!-- Card Banner Image & Badges -->
                            <div class="position-relative overflow-hidden top-farmer-banner-wrap">
                                <img src="{{ $topFarmer->cover_image_url ?: 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=600&q=80' }}"
                                     alt="{{ $topFarmer->stall_name }}"
                                     class="top-farmer-banner-img w-100"
                                     onerror="this.src='https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=600&q=80'">
                                
                                <!-- Rank Ribbon Badge -->
                                <div class="position-absolute top-0 start-0 m-2.5 z-2" style="top: 10px; left: 10px;">
                                    @if($rank === 1)
                                        <span class="badge top-farmer-rank-badge rank-1 shadow-sm">
                                            <i class="bi bi-award-fill me-1"></i> #1 Ranked
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="badge top-farmer-rank-badge rank-2 shadow-sm">
                                            <i class="bi bi-award-fill me-1"></i> #2 Ranked
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="badge top-farmer-rank-badge rank-3 shadow-sm">
                                            <i class="bi bi-award-fill me-1"></i> #3 Ranked
                                        </span>
                                    @else
                                        <span class="badge top-farmer-rank-badge rank-other shadow-sm">
                                            #{{ $rank }} Top Rated
                                        </span>
                                    @endif
                                </div>

                                <!-- Star Rating Floating Pill -->
                                <div class="position-absolute top-0 end-0 m-2.5 z-2" style="top: 10px; right: 10px;">
                                    <span class="top-farmer-rating-pill shadow-sm">
                                        <i class="bi bi-star-fill text-warning me-1"></i> <strong>{{ number_format($rating, 1) }}</strong>
                                        <span class="rating-sub">({{ $reviewsCount }})</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Card Content Body -->
                            <div class="card-body p-3 pt-0 d-flex flex-column">
                                <!-- Overlapping Avatar & Market Day Badge -->
                                <div class="d-flex justify-content-between align-items-end mb-2 position-relative" style="z-index: 5;">
                                    <div class="top-farmer-avatar-badge rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm">
                                        @if($topFarmer->image_url)
                                            <img src="{{ $topFarmer->image_url }}" alt="{{ $topFarmer->contact_person }}" class="w-100 h-100 rounded-circle" style="object-fit: cover; object-position: center 20%;">
                                        @else
                                            <span class="fw-bold text-success fs-5">{{ strtoupper(substr($topFarmer->stall_name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <span class="badge-pastel-slate small">
                                        <i class="bi bi-basket2 me-1 text-success"></i> {{ $productsCount }} {{ Str::plural('Item', $productsCount) }}
                                    </span>
                                </div>

                                <!-- Stall Name & Contact -->
                                <h5 class="fw-bold text-dark mb-1 text-truncate" title="{{ $topFarmer->stall_name }}">
                                    <a href="{{ route('farmers.show', $topFarmer->id) }}" class="text-dark text-decoration-none stretched-link">
                                        {{ $topFarmer->stall_name }}
                                    </a>
                                </h5>

                                <div class="small text-muted mb-1 text-truncate">
                                    <i class="bi bi-person-fill text-secondary me-1"></i> {{ $topFarmer->contact_person }}
                                </div>

                                <!-- Market Plaza & Location -->
                                <div class="small mb-2 position-relative text-truncate" style="z-index: 2;">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <span class="fw-medium text-dark">{{ $topFarmer->market->name ?? 'Local Market Plaza' }}</span>
                                </div>

                                <!-- Bio Excerpt -->
                                <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.45; min-height: 38px;">
                                    {{ Str::limit($topFarmer->bio ?: 'Dedicated to harvest-fresh local produce and punctual weekly stall pre-orders.', 72) }}
                                </p>

                                <!-- Action Button -->
                                <div class="mt-auto pt-2 border-top border-light position-relative" style="z-index: 2;">
                                    <a href="{{ route('farmers.show', $topFarmer->id) }}" class="btn btn-sm btn-brand w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                                        <span>Visit Stall</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Map and Discovery Feature Bento Card -->
<section class="py-5" style="background-color: var(--surface-subtle);">
    <div class="container py-3">
        <div class="card card-custom p-4 p-md-5 bg-white">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge-pastel-green mb-2">
                        <i class="bi bi-geo-alt-fill me-1"></i> OpenStreetMap Interactive Map
                    </span>
                    <h2 class="heading-serif fw-bold text-dark mb-3">
                        Locate Neighborhood Markets and Pickup Points
                    </h2>
                    <p class="text-muted mb-4" style="line-height: 1.6;">
                        Find open market plazas, browse vendor stalls, and check weekly pickup schedules on our interactive map.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('markets.index') }}" class="btn btn-brand">
                            <i class="bi bi-map me-1"></i> Open Interactive Map
                        </a>
                        <a href="{{ route('about') }}" class="btn btn-brand-outline">
                            Learn How It Works
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="p-4 rounded-3 text-center" style="background-color: var(--canvas-bg); border: 1px dashed var(--border-card);">
                        <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background-color: #edf3ec; color: var(--brand-primary);">
                            <i class="bi bi-compass fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Live Stall Discovery</h5>
                        <p class="text-muted small mb-0">Explore neighborhood markets, schedule hours, and participating farmers across the city.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
@vite(['resources/js/testimonials-mount.tsx'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof gsap === 'undefined') return;

    // Respect accessibility settings for users with motion sensitivity
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // Master 3.2-Second Choreographed Motion Graphics Timeline (antigravity-design-expert)
    const tl = gsap.timeline({
        defaults: { ease: 'power3.out' }
    });

    // 1. T = 0.0s: Hero Background Ambient Expansion (subtle depth zoom)
    tl.fromTo('.hero-section-wrapper', 
        { scale: 1.04 }, 
        { scale: 1, duration: 2.2, ease: 'power2.out', clearProps: 'transform' },
        0
    );

    // 2. T = 0.15s: Verified Local Farmers floating badge
    tl.fromTo('.hero-badge-float',
        { opacity: 0, y: 22 },
        { opacity: 1, y: 0, duration: 0.8 },
        0.15
    );

    // 3. T = 0.35s: Hero Title Line 1 lifts up
    tl.fromTo('.hero-title-line-1',
        { opacity: 0, y: 32 },
        { opacity: 1, y: 0, duration: 0.85 },
        0.35
    );

    // 4. T = 0.60s: Hero Title Line 2 glides in with shimmer
    tl.fromTo('.hero-title-line-2',
        { opacity: 0, y: 26 },
        { opacity: 1, y: 0, duration: 0.85 },
        0.60
    );

    // 5. T = 0.85s: Hero Subtitle
    tl.fromTo('.hero-subtitle',
        { opacity: 0, y: 18 },
        { opacity: 1, y: 0, duration: 0.75 },
        0.80
    );

    // 6. T = 1.05s: Market Day Quick Finder Bento Card Spring Entrance
    tl.fromTo('.hero-bento-card',
        { opacity: 0, y: 32, scale: 0.96 },
        { opacity: 1, y: 0, scale: 1, duration: 0.9, ease: 'back.out(1.15)', clearProps: 'transform' },
        1.0
    );

    // 7. T = 1.35s: Trust Pillar Pills Cascade
    tl.fromTo('.hero-pillar-pill',
        { opacity: 0, y: 16 },
        { opacity: 1, y: 0, duration: 0.5, stagger: 0.1, clearProps: 'transform' },
        1.3
    );

    // 8. T = 1.65s: Live Community Harvest Metrics Bar Reveal
    tl.fromTo('.hero-metrics-bar',
        { opacity: 0, y: 18 },
        { opacity: 1, y: 0, duration: 0.65, clearProps: 'transform' },
        1.6
    );

    // 9. T = 1.65s - 3.2s: Smooth GSAP Number Counter Animators (Active rolling motion for 1.5s!)
    document.querySelectorAll('.hero-counter-num').forEach(el => {
        const target = parseInt(el.getAttribute('data-target') || 0);
        const obj = { val: 0 };
        gsap.to(obj, {
            val: target,
            duration: 1.5,
            delay: 1.65,
            ease: 'power2.out',
            onUpdate: () => {
                el.innerText = Math.round(obj.val);
            }
        });
    });

    // 10. T = 2.1s - 3.2s: Featured Sale Showcase Card glides into view
    if (document.querySelector('.featured-sale-card')) {
        tl.fromTo('.featured-sale-card',
            { opacity: 0, y: 28 },
            { opacity: 1, y: 0, duration: 0.8, ease: 'power2.out', clearProps: 'opacity,transform' },
            2.0
        );
    }

    // 11. T = 2.4s - 3.4s: Category Bento Cards Domino Stagger
    tl.fromTo('.category-bento-card',
        { opacity: 0, y: 22 },
        { opacity: 1, y: 0, duration: 0.55, stagger: 0.07, ease: 'power2.out', clearProps: 'opacity,transform' },
        2.3
    );

    // Interactive 3D Spatial Micro-Tilt on Bento Search Card (antigravity-design-expert)
    const bentoCard = document.querySelector('.hero-bento-card');
    if (bentoCard) {
        bentoCard.addEventListener('mousemove', (e) => {
            const rect = bentoCard.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            gsap.to(bentoCard, {
                rotateY: x * 6,
                rotateX: -y * 6,
                transformPerspective: 900,
                duration: 0.25,
                ease: 'power1.out'
            });
        });
        bentoCard.addEventListener('mouseleave', () => {
            gsap.to(bentoCard, {
                rotateY: 0,
                rotateX: 0,
                duration: 0.45,
                ease: 'power2.out'
            });
        });
    }
});

// Top 10 Rated Farmers Horizontal Carousel Controls
document.addEventListener('DOMContentLoaded', function () {
    const farmersTrack = document.getElementById('topFarmersTrack');
    const farmersPrev = document.getElementById('topFarmersPrev');
    const farmersNext = document.getElementById('topFarmersNext');

    if (farmersTrack && farmersPrev && farmersNext) {
        const updateCarouselBtnStates = () => {
            const atStart = farmersTrack.scrollLeft <= 5;
            const atEnd = farmersTrack.scrollLeft + farmersTrack.clientWidth >= farmersTrack.scrollWidth - 10;
            farmersPrev.disabled = atStart;
            farmersNext.disabled = atEnd;
        };

        const getScrollStep = () => {
            const firstItem = farmersTrack.querySelector('.top-farmer-carousel-item');
            return firstItem ? (firstItem.offsetWidth + 20) : 320;
        };

        farmersPrev.addEventListener('click', () => {
            farmersTrack.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
        });

        farmersNext.addEventListener('click', () => {
            farmersTrack.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
        });

        farmersTrack.addEventListener('scroll', updateCarouselBtnStates, { passive: true });
        updateCarouselBtnStates();
    }
});
</script>
@endsection
