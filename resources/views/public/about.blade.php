@extends('layouts.app')

@section('title', 'About Us — MarketLink')

@section('content')
<!-- Header Banner -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-3 text-center">
        <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Verified Neighborhood Food Network</span>
        <h1 class="heading-serif display-5 fw-bold text-dark mb-3">About MarketLink</h1>
        <p class="lead text-muted col-lg-8 mx-auto">
            Bridging the gap between passionate local producers and conscious consumers — creating a transparent, predictable, and community-driven farmers market ecosystem.
        </p>
    </div>
</section>

<!-- Mission and Necessity -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <span class="text-success fw-bold text-uppercase small">The Challenge</span>
                <h2 class="heading-serif fw-bold text-dark mb-3">Why MarketLink Was Born</h2>
                <p class="text-secondary">
                    Local farmers markets are booming as shoppers seek out nutritious, seasonal, and locally grown food. However, customers rarely know in advance which farmers will be present on a given market day, what stock they will bring, or at what prices.
                </p>
                <p class="text-secondary">
                    Historically, availability was communicated only through chalkboards, paper flyers, or word of mouth. Customers often arrive after traveling across town only to discover that popular items are already sold out or their favorite farmer took the week off.
                </p>
                <p class="text-secondary mb-0">
                    Farmers, in turn, lacked an efficient channel to publish weekly stock ahead of market morning or gauge demand before harvest. <strong>MarketLink</strong> solves this by centralizing weekly inventory, interactive stall maps, and a pre-order reservation system.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80" 
                     alt="Harvest basket" class="img-fluid rounded-4 shadow">
            </div>
        </div>

        <!-- Core Pillars -->
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card card-custom p-4 bg-white h-100 border-0 shadow-sm text-center">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-geo-alt-fill fs-3 text-danger"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Location Transparency</h5>
                    <p class="text-muted small mb-0">
                        Interactive OpenStreetMap markers show exactly which markets are operating, exact vendor stall coordinates, and step-by-step directions to pickup points.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom p-4 bg-white h-100 border-0 shadow-sm text-center">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-calendar-check-fill fs-3 text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Guaranteed Availability</h5>
                    <p class="text-muted small mb-0">
                        Customers can pre-order favorites against real-time weekly stock, reserving products ahead of time with zero risk of sold-out stalls.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom p-4 bg-white h-100 border-0 shadow-sm text-center">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-cash-stack fs-3 text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Direct Stall Settlement</h5>
                    <p class="text-muted small mb-0">
                        No predatory credit card processing fees or online middleman commissions. Pre-orders are settled directly in person with the grower at pickup.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section (SRS §1.6) -->
<section class="py-5 bg-white border-top">
    <div class="container py-2">
        <div class="text-center mb-5">
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Platform Architects & Stewards</span>
            <h2 class="heading-serif fw-bold text-dark mb-2">The Team Behind MarketLink</h2>
            <p class="text-muted col-lg-7 mx-auto small">Dedicated engineers, agricultural liaisons, and designers passionate about local food security and community growers.</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80" 
                         alt="Elena Rostova" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">Elena Rostova</h6>
                    <small class="text-success fw-semibold d-block mb-2">Platform Lead & Architect</small>
                    <p class="text-secondary small mb-0">Directs technical infrastructure and real-time pre-order inventory systems.</p>
                </div>
            </div>

            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80" 
                         alt="Marcus Vance" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">Marcus Vance</h6>
                    <small class="text-success fw-semibold d-block mb-2">Grower Community Liaison</small>
                    <p class="text-secondary small mb-0">Coordinates physical stall onboardings and market association partnerships.</p>
                </div>
            </div>

            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80" 
                         alt="Sarah Chen" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">Sarah Chen</h6>
                    <small class="text-success fw-semibold d-block mb-2">Lead UI/UX Designer</small>
                    <p class="text-secondary small mb-0">Crafts responsive, accessible interfaces for shoppers and multi-device vendors.</p>
                </div>
            </div>

            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80" 
                         alt="David Kim" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">David Kim</h6>
                    <small class="text-success fw-semibold d-block mb-2">Operations & Support</small>
                    <p class="text-secondary small mb-0">Ensures seamless customer communications and pickup resolution workflows.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Community Commitment Section -->
<section class="py-5 bg-brand-light border-top">
    <div class="container text-center">
        <h3 class="heading-serif fw-bold text-dark mb-3">Our Commitment to Sustainable Local Agriculture</h3>
        <p class="text-muted col-lg-7 mx-auto mb-4">
            MarketLink connects local growers directly with conscious consumers. By streamlining weekly inventory pre-orders, we help eliminate food waste, support family farms, and guarantee fresh harvests for every table.
        </p>
        <div class="d-inline-flex gap-3">
            <a href="{{ route('markets.index') }}" class="btn btn-brand rounded-pill px-4">Explore Markets</a>
            <a href="{{ route('farmers.index') }}" class="btn btn-brand-outline rounded-pill px-4">Meet Our Farmers</a>
        </div>
    </div>
</section>
@endsection
