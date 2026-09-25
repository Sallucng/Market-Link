@extends('layouts.app')

@section('title', 'Verified Local Farmers and Stalls — MarketLink')

@section('styles')
<style>
    .farmer-card {
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.22s ease;
        border: 1px solid var(--border-card);
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
    }
    .farmer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px -8px rgba(27, 67, 50, 0.12);
        border-color: #52b788;
    }
    .farmer-header-img {
        height: 160px;
        width: 100%;
        object-fit: cover;
    }
    .farmer-avatar-badge {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 3px solid #ffffff;
        object-fit: cover;
        margin-top: -32px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background-color: #ffffff;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Farmers</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <span class="badge-pastel-green mb-2 d-inline-block">
                    <i class="bi bi-shield-check me-1"></i> Verified Local Growers
                </span>
                <h1 class="heading-serif fw-bold text-dark mb-1">Meet Our Local Farmers</h1>
                <p class="text-muted mb-0">
                    Connect directly with independent growers, artisans, and family farms across our neighborhood markets.
                </p>
            </div>
            <div>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono-meta">
                    <i class="bi bi-people-fill text-success me-1"></i> {{ $farmers->total() }} Verified Growers
                </span>
            </div>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div class="card card-custom p-3 p-md-4 mb-4 bg-white">
        <form action="{{ route('farmers.index') }}" method="GET" class="row g-3 align-items-end">
            <!-- Search Keyword -->
            <div class="col-md-4">
                <label class="form-label small fw-bold text-dark mb-1">
                    <i class="bi bi-search me-1 text-success"></i> Search Farm or Grower
                </label>
                <input type="text" 
                       name="q" 
                       class="form-control" 
                       placeholder="e.g. Green Valley, Marcus, organic..." 
                       value="{{ request('q') }}">
            </div>

            <!-- Market Plaza Filter -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">
                    <i class="bi bi-geo-alt me-1 text-danger"></i> Market Plaza
                </label>
                <select name="market" class="form-select">
                    <option value="">All Markets</option>
                    @foreach($markets as $m)
                        <option value="{{ $m->id }}" {{ request('market') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Operating Day Filter -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-dark mb-1">
                    <i class="bi bi-calendar-event me-1 text-primary"></i> Market Day
                </label>
                <select name="day" class="form-select">
                    <option value="">All Days</option>
                    <option value="Saturday" {{ request('day') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="Sunday" {{ request('day') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                    <option value="Wednesday" {{ request('day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">
                    <i class="bi bi-sort-down me-1 text-secondary"></i> Sort By
                </label>
                <div class="d-flex gap-2">
                    <select name="sort" class="form-select">
                        <option value="">Newest</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="products" {{ request('sort') == 'products' ? 'selected' : '' }}>Most Products</option>
                    </select>
                    <button type="submit" class="btn btn-brand px-3">
                        <i class="bi bi-funnel"></i>
                    </button>
                    @if(request()->anyFilled(['q', 'market', 'day', 'sort']))
                        <a href="{{ route('farmers.index') }}" class="btn btn-light border px-2" title="Reset Filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Farmers Grid -->
    <div class="row g-4">
        @forelse($farmers as $farmer)
            <div class="col-md-6 col-lg-4">
                <div class="card farmer-card h-100 d-flex flex-column position-relative">
                    <!-- Farm Banner Image -->
                    <div class="position-relative">
                        <img src="{{ $farmer->image_url ?: 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=600&q=80' }}" 
                             alt="{{ $farmer->stall_name }}" 
                             class="farmer-header-img"
                             onerror="this.src='https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=600&q=80'">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge-pastel-green" style="background: rgba(255,255,255,0.92); backdrop-filter: blur(4px);">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Verified
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3 pt-0 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div class="farmer-avatar-badge d-flex align-items-center justify-content-center bg-light text-success fs-3 fw-bold">
                                {{ strtoupper(substr($farmer->stall_name, 0, 1)) }}
                            </div>
                            <span class="badge-pastel-slate small">
                                <i class="bi bi-basket2 me-1 text-success"></i> {{ $farmer->products_count }} {{ Str::plural('Product', $farmer->products_count) }}
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-1">
                            <a href="{{ route('farmers.show', $farmer->id) }}" class="text-dark text-decoration-none stretched-link">
                                {{ $farmer->stall_name }}
                            </a>
                        </h5>

                        <div class="small text-muted mb-2">
                            <i class="bi bi-person-fill text-secondary me-1"></i> {{ $farmer->contact_person }}
                        </div>

                        <div class="small mb-2" style="position: relative; z-index: 2;">
                            <a href="{{ route('markets.show', $farmer->market_id) }}" class="text-decoration-none text-success fw-medium">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $farmer->market->name ?? 'Assigned Market' }}
                            </a>
                        </div>

                        <div class="small text-muted mb-2">
                            <i class="bi bi-calendar3 text-success me-1"></i> <strong>Days:</strong> {{ $farmer->operating_days ?: ($farmer->market->operating_days ?? 'Market Days') }}
                        </div>

                        @if($farmer->pickup_time_windows)
                            <div class="small text-muted mb-2">
                                <i class="bi bi-clock text-warning me-1"></i> <strong>Pickup:</strong> {{ $farmer->pickup_time_windows }}
                            </div>
                        @endif

                        <p class="text-secondary small flex-grow-1 mb-3" style="line-height: 1.5;">
                            {{ Str::limit($farmer->bio, 90) ?: 'Dedicated local farmer delivering freshly picked, quality harvest to community stalls.' }}
                        </p>

                        <!-- Bottom Action Button -->
                        <div class="mt-auto pt-2 border-top" style="position: relative; z-index: 2;">
                            <a href="{{ route('farmers.show', $farmer->id) }}" class="btn btn-brand-outline w-100 btn-sm py-2">
                                View Farm Profile and Products <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-custom p-5 text-center bg-white border-0">
                    <i class="bi bi-people text-muted display-4 mb-3"></i>
                    <h5 class="fw-bold">No farmers match your filters</h5>
                    <p class="text-muted small">Try broadening your search keyword or clearing the market/day filters.</p>
                    <div>
                        <a href="{{ route('farmers.index') }}" class="btn btn-brand rounded-pill px-4">
                            View All Farmers
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $farmers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
