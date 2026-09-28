@extends('layouts.admin')

@section('title', 'Farmer Sales & Spotlight — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Farmer Sales & Spotlight</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-1 fw-bold">
                <i class="bi bi-stars me-1 text-dark"></i> Marketing & Moderation
            </span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Farmer Sales & Homepage Spotlight</h2>
            <small class="text-muted">Manage promotions created by local growers and select which sale is highlighted on the MarketLink homepage</small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono-meta">
                Total Sales: <strong>{{ $stats['total'] }}</strong>
            </span>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill font-mono-meta">
                Active Now: <strong>{{ $stats['active'] }}</strong>
            </span>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- CURRENT FEATURED SPOTLIGHT CARD -->
    <div class="card card-custom p-4 border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fffdf5 0%, #fffbeb 100%); border: 1.5px solid #fde68a !important;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-25 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                    <i class="bi bi-star-fill fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning text-dark px-2.5 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;">HOMEPAGE SPOTLIGHT</span>
                        @if($currentFeaturedSale)
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">Active Live</span>
                        @endif
                    </div>
                    @if($currentFeaturedSale)
                        <h5 class="fw-bold text-dark mb-0">{{ $currentFeaturedSale->title }}</h5>
                        <div class="text-muted small">
                            Stall: <strong class="text-success">{{ $currentFeaturedSale->farmer->stall_name }}</strong> 
                            &bull; {{ $currentFeaturedSale->computed_badge }} 
                            &bull; Valid {{ $currentFeaturedSale->start_date->format('M d') }} &ndash; {{ $currentFeaturedSale->end_date->format('M d, Y') }}
                        </div>
                    @else
                        <h6 class="fw-bold text-dark mb-0">No Sale Currently Featured on the Homepage</h6>
                        <small class="text-muted">Choose an active campaign from the list below to spotlight it for all visiting shoppers.</small>
                    @endif
                </div>
            </div>

            @if($currentFeaturedSale)
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1.5 shadow-2xs">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Preview on Home
                    </a>
                    <form action="{{ route('admin.sales.unfeature', $currentFeaturedSale->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 shadow-2xs">
                            <i class="bi bi-x-circle me-1"></i> Remove Spotlight
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
        <form action="{{ route('admin.sales.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search by campaign title, stall name, or grower...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Campaigns</option>
                    <option value="featured" {{ request('status') === 'featured' ? 'selected' : '' }}>Featured Spotlight</option>
                    <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-brand btn-sm flex-fill rounded-pill">Filter</button>
                <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Reset</a>
            </div>
        </form>
    </div>

    <!-- ALL SALES TABLE -->
    <div class="card card-custom bg-white border-0 shadow-sm overflow-hidden mb-4">
        @if($sales->isEmpty())
            <div class="p-5 text-center text-muted">
                <i class="bi bi-tag fs-1 text-muted opacity-50 mb-2 d-block"></i>
                <h6 class="fw-bold text-dark">No Farmer Sales Found</h6>
                <small>No campaigns match your current filter criteria.</small>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-4">Stall / Grower</th>
                            <th>Sale Campaign</th>
                            <th>Discount / Badge</th>
                            <th>Dates</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Spotlight on Home</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr class="{{ $sale->is_featured ? 'table-warning bg-opacity-10' : '' }}">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $sale->farmer->stall_name }}</div>
                                    <div class="small text-muted">
                                        {{ $sale->farmer->contact_person }}
                                        @if($sale->farmer->market)
                                            &bull; <span class="text-success"><i class="bi bi-geo-alt"></i> {{ $sale->farmer->market->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if($sale->banner_image)
                                            <img src="{{ $sale->banner_image }}" alt="{{ $sale->title }}" class="rounded-2 object-fit-cover flex-shrink-0" style="width: 44px; height: 44px;">
                                        @else
                                            <div class="rounded-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                                <i class="bi bi-percent fs-5"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $sale->title }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 220px; font-size: 0.76rem;">
                                                {{ $sale->description ?: 'No details provided' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 rounded-pill fw-semibold">
                                        {{ $sale->computed_badge }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        {{ $sale->start_date->format('M d') }} &ndash; {{ $sale->end_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    @if($sale->products->count() > 0)
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-0.5 small">
                                            {{ $sale->products->count() }} items
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-0.5 small">
                                            Stall-wide
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sale->status_color }}-subtle text-{{ $sale->status_color }} border border-{{ $sale->status_color }} border-opacity-25 rounded-pill px-2.5 py-1">
                                        {{ $sale->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($sale->is_featured)
                                        <div class="d-inline-flex align-items-center gap-1.5">
                                            <span class="badge bg-warning text-dark border border-warning px-2.5 py-1 rounded-pill fw-bold">
                                                <i class="bi bi-star-fill text-dark me-1"></i> Featured
                                            </span>
                                            <form action="{{ route('admin.sales.unfeature', $sale->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" title="Remove from homepage spotlight">
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form action="{{ route('admin.sales.feature', $sale->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="font-size: 0.8rem; background-color: #fffdf5;">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <span>Feature on Home</span>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <form action="{{ route('admin.sales.toggle', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" title="{{ $sale->is_active ? 'Pause Campaign' : 'Resume Campaign' }}">
                                                <i class="bi {{ $sale->is_active ? 'bi-pause-fill text-warning' : 'bi-play-fill text-success' }}"></i>
                                                <span>{{ $sale->is_active ? 'Pause' : 'Resume' }}</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.sales.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this farmer sale?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sales->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $sales->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
