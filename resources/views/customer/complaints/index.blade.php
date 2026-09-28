@extends('layouts.app')

@section('title', 'My Farmer Complaints — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-success">Customer Portal</a></li>
            <li class="breadcrumb-item active" aria-current="page">Confidential Complaints</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold">
                    <i class="bi bi-shield-lock-fill me-1"></i> Confidential Moderation
                </span>
                <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                    Admin Eyes Only
                </span>
            </div>
            <h2 class="heading-serif fw-bold text-dark mb-1">Farmer Complaints & Issue Reports</h2>
            <p class="text-muted small mb-0">Track and submit confidential issue reports regarding growers, produce quality, or stall conduct.</p>
        </div>
        <div>
            <a href="{{ route('customer.complaints.create') }}" class="btn btn-danger rounded-pill px-4 shadow-sm fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-flag-fill"></i>
                <span>File New Complaint</span>
            </a>
        </div>
    </div>

    <!-- Confidentiality Guarantee Banner -->
    <div class="card border-0 rounded-4 mb-4 overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #fdf2f2 0%, #fff5f5 100%); border-left: 5px solid #ef4444 !important;">
        <div class="card-body p-3.5 p-md-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-shield-shaded"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-danger-emphasis mb-1">Strict Confidentiality Guarantee</h5>
                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                        Complaints filed here are <strong>strictly internal</strong> and routed directly to MarketLink Platform Administration. 
                        They are <strong>never shown to normal shoppers, public visitors, or posted on the farmer's public profile</strong>. 
                        Our compliance team investigates each case independently to protect market integrity and shopper safety.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-semibold text-uppercase">Total Reports</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ $counts['total'] }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-archive me-1"></i>All filed cases</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-semibold text-uppercase">Pending Review</div>
                <div class="fs-3 fw-bold text-amber-600 text-warning mt-1">{{ $counts['pending'] }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-hourglass-split me-1"></i>Queued for admin</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-semibold text-uppercase">Under Review</div>
                <div class="fs-3 fw-bold text-primary mt-1">{{ $counts['under_review'] }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-search me-1"></i>Active investigation</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-semibold text-uppercase">Resolved Cases</div>
                <div class="fs-3 fw-bold text-success mt-1">{{ $counts['resolved'] }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-check-circle me-1"></i>Completed actions</div>
            </div>
        </div>
    </div>

    <!-- Complaints List -->
    @if($complaints->count() > 0)
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-secondary">
                        <tr>
                            <th class="ps-4">Case #</th>
                            <th>Reported Farmer & Stall</th>
                            <th>Complaint Type & Subject</th>
                            <th>Submitted Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace fw-bold text-dark">#CMP-{{ $complaint->id }}</span>
                                    @if($complaint->order)
                                        <div class="text-muted small" style="font-size: 0.72rem;">Order: #{{ $complaint->order->order_number }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $complaint->farmer->stall_name }}</div>
                                    <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $complaint->farmer->market->name ?? 'Farmers Market' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 280px;">{{ $complaint->subject }}</div>
                                    <span class="badge bg-light text-secondary border small mt-0.5">{{ $complaint->type_label }}</span>
                                </td>
                                <td>
                                    <div class="small text-dark fw-medium">{{ $complaint->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">{{ $complaint->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="text-center">
                                    @php $badge = $complaint->status_badge; @endphp
                                    <span class="badge rounded-pill border {{ $badge['bg'] }} px-2.5 py-1 small">
                                        <i class="bi {{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('customer.complaints.show', $complaint->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                        View Case <i class="bi bi-chevron-right ms-1"></i>
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
            <h4 class="fw-bold text-dark mb-1">No Complaints on Record</h4>
            <p class="text-secondary small mx-auto mb-4" style="max-width: 440px;">
                You haven't filed any complaints against any growers or stalls. If you experience an issue with product freshness, pricing, or order pickup, you can submit a confidential report anytime.
            </p>
            <div>
                <a href="{{ route('customer.complaints.create') }}" class="btn btn-danger rounded-pill px-4">
                    <i class="bi bi-flag-fill me-1"></i> File a Complaint
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
