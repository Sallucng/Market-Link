@extends('layouts.admin')

@section('title', 'Farmer Complaints Moderation — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Farmer Complaints</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="heading-serif fw-bold text-dark mb-0">Customer Complaints Moderation</h2>
                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small">
                    <i class="bi bi-shield-lock-fill me-1"></i> Confidential
                </span>
            </div>
            <small class="text-muted">Review, investigate, and resolve confidential shopper complaints regarding growers and market stalls.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.farmers.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                <i class="bi bi-person-check-fill me-1"></i> View All Farmers
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 bg-white border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase font-mono-meta">Total Reports</div>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total'] }}</h3>
                        <small class="text-muted">All-time filed</small>
                    </div>
                    <div class="p-3 bg-light rounded-circle text-secondary">
                        <i class="bi bi-folder-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 bg-white border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase font-mono-meta">Pending Action</div>
                        <h3 class="fw-bold text-danger mb-0 mt-1">{{ $stats['pending'] }}</h3>
                        <small class="text-danger fw-medium">Requires triage</small>
                    </div>
                    <div class="p-3 bg-danger bg-opacity-10 rounded-circle text-danger">
                        <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 bg-white border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase font-mono-meta">Under Investigation</div>
                        <h3 class="fw-bold text-primary mb-0 mt-1">{{ $stats['under_review'] }}</h3>
                        <small class="text-primary fw-medium">Actively researching</small>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle text-primary">
                        <i class="bi bi-search fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 bg-white border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase font-mono-meta">Resolved / Closed</div>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ $stats['resolved'] + $stats['dismissed'] }}</h3>
                        <small class="text-success fw-medium">{{ $stats['resolved'] }} resolved &bull; {{ $stats['dismissed'] }} closed</small>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle text-success">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
        <form action="{{ route('admin.complaints.index') }}" method="GET" class="row g-2 align-items-center">
            <!-- Status Tabs -->
            <div class="col-lg-5">
                <div class="btn-group btn-group-sm w-100" role="group">
                    <a href="{{ route('admin.complaints.index') }}" class="btn {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">
                        All ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('admin.complaints.index', ['status' => 'pending']) }}" class="btn {{ request('status') === 'pending' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Pending ({{ $stats['pending'] }})
                    </a>
                    <a href="{{ route('admin.complaints.index', ['status' => 'under_review']) }}" class="btn {{ request('status') === 'under_review' ? 'btn-primary' : 'btn-outline-primary' }}">
                        In Review ({{ $stats['under_review'] }})
                    </a>
                    <a href="{{ route('admin.complaints.index', ['status' => 'resolved']) }}" class="btn {{ request('status') === 'resolved' ? 'btn-success' : 'btn-outline-success' }}">
                        Resolved ({{ $stats['resolved'] }})
                    </a>
                </div>
            </div>

            <!-- Farmer Filter -->
            <div class="col-sm-6 col-lg-3">
                <select name="farmer_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Farmer Stalls</option>
                    @foreach($farmers as $farmer)
                        <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                            {{ $farmer->stall_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Field -->
            <div class="col-sm-6 col-lg-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search subject, customer, farmer..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request()->anyFilled(['status', 'farmer_id', 'search', 'type']))
                        <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-danger" title="Clear Filters">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Complaints Moderation Table -->
    @if($complaints->count() > 0)
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-secondary">
                        <tr>
                            <th class="ps-4">Case #</th>
                            <th>Customer (Reporter)</th>
                            <th>Reported Farmer & Market</th>
                            <th>Reason / Subject</th>
                            <th>Filed Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace fw-bold text-dark">#CMP-{{ $complaint->id }}</span>
                                    @if($complaint->order)
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            Order: #{{ $complaint->order->order_number }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $complaint->customer->name ?? 'User #' . $complaint->customer_id }}</div>
                                    <div class="text-muted small" style="font-size: 0.78rem;">{{ $complaint->customer->email ?? '' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $complaint->farmer->stall_name ?? 'Farmer #' . $complaint->farmer_id }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $complaint->farmer->market->name ?? 'Market Stall' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 260px;">{{ $complaint->subject }}</div>
                                    <span class="badge bg-light text-secondary border small mt-0.5">{{ $complaint->type_label }}</span>
                                </td>
                                <td>
                                    <div class="small text-dark fw-medium">{{ $complaint->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">{{ $complaint->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-center">
                                    @php $badge = $complaint->status_badge; @endphp
                                    <span class="badge rounded-pill border {{ $badge['bg'] }} px-2.5 py-1 small">
                                        <i class="bi {{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="btn btn-sm btn-dark rounded-pill px-3">
                                        Review <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($complaints->hasPages())
                <div class="p-3 border-top">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <div class="mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                <i class="bi bi-shield-check text-success fs-1"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">No Complaints Found</h4>
            <p class="text-secondary small mx-auto mb-0" style="max-width: 440px;">
                @if(request()->anyFilled(['status', 'farmer_id', 'search']))
                    No complaint records matched your active filter criteria. Try clearing search filters.
                @else
                    All market operations are clean. There are currently no customer complaints filed in the system.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
