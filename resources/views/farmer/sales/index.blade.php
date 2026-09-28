@extends('layouts.farmer')

@section('title', 'Stall Sales & Promotions — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Promotions & Sales</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Growth & Marketing</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Stall Sales & Special Deals</h2>
            <small class="text-muted">{{ $farmer->stall_name }} &bull; Run seasonal discounts, flash sales, and harvest offers</small>
        </div>

        <a href="{{ route('farmer.sales.create') }}" class="btn btn-brand rounded-pill px-4 d-flex align-items-center gap-1.5 shadow-sm">
            <i class="bi bi-plus-circle"></i>
            <span>Create New Sale</span>
        </a>
    </div>

    <!-- Alert Notices -->
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

    <!-- Sales Table / Cards -->
    <div class="card card-custom bg-white border-0 shadow-sm overflow-hidden mb-4">
        @if($sales->isEmpty())
            <div class="p-5 text-center">
                <div class="mb-3 text-muted">
                    <i class="bi bi-tag fs-1 text-success opacity-50"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Promotional Sales Created Yet</h5>
                <p class="text-muted small mb-4" style="max-width: 480px; margin-inline: auto;">
                    Attract market shoppers by running seasonal deals, weekend discounts, or surplus produce flash sales. When active, your sale can be spotlighted directly on the MarketLink home page by the site administrator!
                </p>
                <a href="{{ route('farmer.sales.create') }}" class="btn btn-brand rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i> Launch Your First Sale
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-4">Sale Campaign</th>
                            <th>Discount / Badge</th>
                            <th>Timeframe</th>
                            <th>Linked Items</th>
                            <th>Status</th>
                            <th>Homepage Spotlight</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($sale->banner_image)
                                            <img src="{{ $sale->banner_image }}" alt="{{ $sale->title }}" class="rounded-3 object-fit-cover shadow-2xs" style="width: 52px; height: 52px;">
                                        @else
                                            <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold" style="width: 52px; height: 52px;">
                                                <i class="bi bi-percent fs-5"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">{{ $sale->title }}</h6>
                                            <p class="text-muted small mb-0 text-truncate" style="max-width: 260px;">
                                                {{ $sale->description ?: 'No additional description' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 rounded-pill fw-semibold">
                                        <i class="bi bi-lightning-fill me-0.5"></i> {{ $sale->computed_badge }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        {{ $sale->start_date->format('M d') }} &ndash; {{ $sale->end_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        @if($sale->isRunning())
                                            <span class="text-success"><i class="bi bi-dot"></i>Live Today</span>
                                        @elseif($sale->start_date > now())
                                            <span class="text-info"><i class="bi bi-clock"></i>Starts {{ $sale->start_date->diffForHumans() }}</span>
                                        @else
                                            <span class="text-secondary"><i class="bi bi-check2"></i>Concluded</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($sale->products_count > 0)
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                            <i class="bi bi-box-seam me-1 text-success"></i>{{ $sale->products_count }} {{ Str::plural('product', $sale->products_count) }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1">
                                            Entire Stall
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
                                        <span class="badge bg-warning bg-opacity-15 text-dark border border-warning rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <span>Featured on Home</span>
                                        </span>
                                    @else
                                        <span class="text-muted small" style="font-size: 0.78rem;">Regular</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <form action="{{ route('farmer.sales.toggle', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" title="{{ $sale->is_active ? 'Pause Campaign' : 'Resume Campaign' }}">
                                                <i class="bi {{ $sale->is_active ? 'bi-pause-fill text-warning' : 'bi-play-fill text-success' }}"></i>
                                                <span>{{ $sale->is_active ? 'Pause' : 'Resume' }}</span>
                                            </button>
                                        </form>

                                        <a href="{{ route('farmer.sales.edit', $sale->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('farmer.sales.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this promotional sale?');">
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
