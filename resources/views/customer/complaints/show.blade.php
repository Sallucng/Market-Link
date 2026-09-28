@extends('layouts.app')

@section('title', 'Complaint #CMP-' . $complaint->id . ' — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-success">Customer Portal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.complaints.index') }}" class="text-success">Complaints</a></li>
            <li class="breadcrumb-item active" aria-current="page">Case #CMP-{{ $complaint->id }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="font-monospace fw-bold fs-5 text-dark">Case #CMP-{{ $complaint->id }}</span>
                @php $badge = $complaint->status_badge; @endphp
                <span class="badge rounded-pill border {{ $badge['bg'] }} px-3 py-1 fw-semibold">
                    <i class="bi {{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
                </span>
                <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                    Admin Eyes Only
                </span>
            </div>
            <h3 class="heading-serif fw-bold text-dark mb-0">{{ $complaint->subject }}</h3>
            <div class="text-muted small mt-1">Submitted on {{ $complaint->created_at->format('F d, Y \a\t h:i A') }}</div>
        </div>
        <div>
            <a href="{{ route('customer.complaints.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Back to Complaints
            </a>
        </div>
    </div>

    <!-- Confidentiality Reminder -->
    <div class="alert alert-light border border-secondary-subtle rounded-3 p-3 mb-4 d-flex align-items-center gap-3">
        <i class="bi bi-shield-lock-fill text-danger fs-3 flex-shrink-0"></i>
        <div class="small text-secondary">
            <strong>Confidential Case Record:</strong> This report is securely stored and visible exclusively to you and MarketLink Platform Administrators. It is <strong>hidden from normal users and the public</strong>.
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Details -->
        <div class="col-lg-8">
            <!-- Complaint Content Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-card-text text-danger"></i>
                        <span>Complaint Statement</span>
                    </h5>
                    <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                        {{ $complaint->type_label }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="p-3.5 bg-light rounded-3 text-secondary mb-3" style="line-height: 1.7; white-space: pre-line; font-size: 0.95rem;">
                        {{ $complaint->description }}
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-clock me-1"></i> Logged into system: {{ $complaint->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            <!-- Administration Resolution / Investigation Status -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success"></i>
                        <span>Administration Review & Resolution</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($complaint->status === 'pending')
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-3 d-flex gap-3 align-items-start">
                            <i class="bi bi-hourglass-top fs-4 text-warning flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold text-amber-900 mb-1">Awaiting Initial Review</h6>
                                <p class="text-secondary small mb-0">
                                    Our platform administration team has received this report and queued it for verification. You will receive an in-app update when an administrator begins review.
                                </p>
                            </div>
                        </div>
                    @elseif($complaint->status === 'under_review')
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-3 d-flex gap-3 align-items-start">
                            <i class="bi bi-search fs-4 text-primary flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold text-blue-900 mb-1">Investigation Underway</h6>
                                <p class="text-secondary small mb-0">
                                    An administrator is currently reviewing the details of this complaint and evaluating farmer compliance standards.
                                </p>
                            </div>
                        </div>
                    @elseif($complaint->status === 'resolved')
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-3 d-flex gap-3 align-items-start mb-3">
                            <i class="bi bi-check-circle-fill fs-4 text-success flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold text-emerald-900 mb-1">Case Resolved</h6>
                                <p class="text-secondary small mb-0">
                                    Administration has completed its review and taken appropriate moderation action. Case finalized on {{ $complaint->resolved_at?->format('M d, Y') }}.
                                </p>
                            </div>
                        </div>
                        @if($complaint->admin_notes)
                            <div class="border rounded-3 p-3 bg-white">
                                <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Admin Response Note:</span>
                                <p class="text-dark small mb-0 mt-1" style="line-height: 1.6;">{{ $complaint->admin_notes }}</p>
                            </div>
                        @endif
                    @elseif($complaint->status === 'dismissed')
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-3 d-flex gap-3 align-items-start">
                            <i class="bi bi-slash-circle fs-4 text-secondary flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold text-slate-800 mb-1">Case Reviewed & Closed</h6>
                                <p class="text-secondary small mb-0">
                                    Administration evaluated this report and closed the record on {{ $complaint->resolved_at?->format('M d, Y') }}.
                                </p>
                            </div>
                        </div>
                        @if($complaint->admin_notes)
                            <div class="border rounded-3 p-3 bg-white mt-3">
                                <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Admin Note:</span>
                                <p class="text-dark small mb-0 mt-1" style="line-height: 1.6;">{{ $complaint->admin_notes }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Reported Farmer Stall Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5">
                    <h6 class="fw-bold text-dark mb-0">Reported Farmer & Stall</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $complaint->farmer->user->profile_photo_path ?? 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=150&q=80' }}" 
                             alt="{{ $complaint->farmer->stall_name }}" 
                             class="rounded-circle object-fit-cover border" 
                             style="width: 52px; height: 52px;">
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $complaint->farmer->stall_name }}</div>
                            <div class="text-muted small"><i class="bi bi-person me-1"></i>{{ $complaint->farmer->contact_person }}</div>
                        </div>
                    </div>
                    <div class="small text-secondary mb-2">
                        <i class="bi bi-geo-alt text-success me-1"></i>
                        <strong>Host Market:</strong> {{ $complaint->farmer->market->name ?? 'Farmers Market' }}
                    </div>
                    @if($complaint->farmer->contact_number)
                        <div class="small text-secondary mb-3">
                            <i class="bi bi-telephone text-primary me-1"></i>
                            <strong>Phone:</strong> {{ $complaint->farmer->contact_number }}
                        </div>
                    @endif
                    <a href="{{ route('farmers.show', $complaint->farmer->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                        <i class="bi bi-box-arrow-up-right me-1"></i> View Farmer Profile
                    </a>
                </div>
            </div>

            <!-- Linked Order (If any) -->
            @if($complaint->order)
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-header bg-white border-bottom p-3.5">
                        <h6 class="fw-bold text-dark mb-0">Associated Pre-Order</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-monospace fw-bold text-dark">#{{ $complaint->order->order_number }}</span>
                            <span class="badge bg-light text-secondary border small">{{ ucfirst($complaint->order->order_status) }}</span>
                        </div>
                        <div class="small text-muted mb-2">
                            <i class="bi bi-calendar3 me-1"></i> Pickup: {{ $complaint->order->pickup_date->format('M d, Y') }}
                        </div>
                        <div class="small text-muted mb-3">
                            <i class="bi bi-wallet2 me-1"></i> Total: <strong>${{ number_format($complaint->order->total_amount, 2) }}</strong>
                        </div>

                        @if($complaint->order->items->count() > 0)
                            <div class="border-top pt-2 mt-2">
                                <span class="text-muted small fw-semibold">Ordered Items:</span>
                                <ul class="list-unstyled small mt-1 mb-0">
                                    @foreach($complaint->order->items as $item)
                                        <li class="d-flex justify-content-between py-1 border-bottom border-light">
                                            <span>{{ $item->quantity }}x {{ $item->product->name ?? 'Harvest Item' }}</span>
                                            <span class="text-muted">${{ number_format($item->subtotal, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <a href="{{ route('customer.orders.show', $complaint->order->id) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-3">
                            <i class="bi bi-box-seam me-1"></i> View Full Order
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
