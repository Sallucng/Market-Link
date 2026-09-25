@extends('layouts.app')

@section('title', 'MarketLink — Local Farmers Market Pre-Orders')

@section('content')

<!-- Hero Section (eye-catching ambient minimal farm banner) -->
<section class="py-5 border-bottom position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, rgba(11, 33, 22, 0.90) 0%, rgba(11, 33, 22, 0.78) 45%, rgba(11, 33, 22, 0.45) 100%), url('https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1920&q=85') center center / cover no-repeat; min-height: 520px;">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 rounded-pill" 
                     style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.25); color: #b7e4c7; font-size: 0.85rem;">
                    <i class="bi bi-shield-check"></i>
                    <span>Verified Local Farmers and Pre-Orders</span>
                </div>

                <h1 class="display-4 fw-bold heading-serif text-white mb-3" style="letter-spacing: -0.035em; line-height: 1.12;">
                    Farm Fresh Products, <br>
                    <span style="color: #74c69d; font-style: italic;">Just a Click Away.</span>
                </h1>

                <p class="text-white text-opacity-85 lead fs-6 mb-4 col-xl-10" style="line-height: 1.6;">
                    Pre-order fresh local harvests directly from verified neighborhood growers. Pick up and pay in-person at your weekend market stall.
                </p>

                <!-- Market Day Quick Finder Bento Card -->
                <div class="card card-custom p-3 p-md-4 mb-4 shadow-lg border-0" 
                     style="background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(16px); border-radius: 14px;">
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
                                <button type="submit" class="btn btn-brand w-100">
                                    <i class="bi bi-search me-1"></i> Locate Markets
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Three Key Pillars (SRS §1.5) -->
                <div class="d-flex flex-wrap gap-3">
                    <div class="px-3 py-2 rounded-pill d-inline-flex align-items-center small" 
                         style="background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff;">
                        <i class="bi bi-shop me-1 text-success"></i> In-Person Stall Pickup
                    </div>
                    <div class="px-3 py-2 rounded-pill d-inline-flex align-items-center small" 
                         style="background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff;">
                        <i class="bi bi-currency-dollar me-1 text-warning"></i> Zero Online Markups
                    </div>
                    <div class="px-3 py-2 rounded-pill d-inline-flex align-items-center small" 
                         style="background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff;">
                        <i class="bi bi-map me-1 text-info"></i> OpenStreetMap Powered
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <div class="card p-2" style="border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 18px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(14px); box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);">
                        <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=900&q=80" 
                             alt="Local Farmers Market Stall" 
                             class="w-100 shadow-sm" 
                             style="height: 420px; object-fit: cover; border-radius: 14px;">
                    </div>
                    
                    <!-- Tactile Overlay Badge (Antigravity Floating Motion) -->
                    <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-white d-none d-sm-flex align-items-center gap-3 motion-float" 
                         style="border: 1px solid var(--border-card); border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.08);">
                        <div class="p-2 rounded-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: var(--brand-primary); color: #fff;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">Pay at Stall Pickup</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Direct vendor settlement — In-person collection</div>
                        </div>
                    </div>

                    <!-- Secondary Floating Badge -->
                    <div class="position-absolute top-0 end-0 m-3 px-3 py-2 bg-white d-none d-md-flex align-items-center gap-2 motion-float-delayed"
                         style="border: 1px solid var(--border-card); border-radius: 30px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                        <i class="bi bi-clock-history text-success"></i>
                        <span class="small fw-semibold text-dark">Pre-Order 24h Ahead</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Seasonal Categories Bento Grid -->
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
                    <a href="{{ route('products.index', ['category' => $cat->id]) }}" class="text-decoration-none">
                        <div class="bento-card text-center p-3">
                            <div class="mx-auto mb-2 rounded-3 d-inline-flex align-items-center justify-content-center" 
                                 style="width: 52px; height: 52px; background-color: #edf3ec; color: var(--brand-primary);">
                                @if($cat->slug == 'vegetables')
                                    <!-- Fresh Vegetables Carrot / Greens Icon -->
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.27 21.7s9.87-3.5 12.73-6.36a4.5 4.5 0 0 0-6.36-6.37C5.77 11.84 2.27 21.7 2.27 21.7zM8.64 14l4-4"/>
                                        <path d="m14 9 4-4"/>
                                        <path d="M17 4v3"/>
                                        <path d="M17 4h3"/>
                                    </svg>
                                @elseif($cat->slug == 'fruits')
                                    <!-- Orchard Fruits Basket / Berry Icon (not Apple Inc logo) -->
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="13" r="8"/>
                                        <path d="M12 5V2"/>
                                        <path d="M12 2c2 1 3 3 3 3"/>
                                    </svg>
                                @elseif($cat->slug == 'dairy-eggs')
                                    <!-- Dairy and Eggs Icon -->
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3C8.5 3 6 8 6 13a6 6 0 0 0 12 0c0-5-2.5-10-6-10z"/>
                                    </svg>
                                @elseif($cat->slug == 'baked-goods')
                                    <!-- Artisanal Bread / Baked Goods Icon -->
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 11h16a2 2 0 0 1 2 2v2a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5v-2a2 2 0 0 1 2-2z"/>
                                        <path d="M6 11V8a6 6 0 0 1 12 0v3"/>
                                    </svg>
                                @else
                                    <!-- Herbs and Honey Botanical Icon -->
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22v-9"/>
                                        <path d="M12 13a6 6 0 0 1 6-6 6 6 0 0 1-6 6z"/>
                                        <path d="M12 13a6 6 0 0 0-6-6 6 6 0 0 0 6 6z"/>
                                    </svg>
                                @endif
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $cat->name }}</h6>
                            <span class="text-muted small font-mono-meta">{{ $cat->products_count }} Products</span>
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
                            <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=600&q=80' }}" 
                                 onerror="this.src='https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=600&q=80'"
                                 class="card-img-top" 
                                 alt="{{ $product->name }}" 
                                 style="height: 175px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                            
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof gsap !== 'undefined') {
        // Staggered card entrance on load
        gsap.from('.card-custom', {
            opacity: 0,
            y: 20,
            duration: 0.6,
            stagger: 0.08,
            ease: 'power3.out'
        });
    }
});
</script>
@endsection
