@extends('layouts.admin')

@section('title', 'Review Complaint #CMP-' . $complaint->id . ' — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.complaints.index') }}" class="text-success text-decoration-none">Complaints</a></li>
                    <li class="breadcrumb-item active" aria-current="page">#CMP-{{ $complaint->id }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="heading-serif fw-bold text-dark mb-0">Case #CMP-{{ $complaint->id }}</h2>
                @php $badge = $complaint->status_badge; @endphp
                <span class="badge rounded-pill border {{ $badge['bg'] }} px-3 py-1 fw-semibold">
                    <i class="bi {{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
                </span>
                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small">
                    <i class="bi bi-shield-lock-fill me-1"></i> Confidential
                </span>
            </div>
            <small class="text-muted">Report filed by shopper <strong>{{ $complaint->customer->name ?? 'User' }}</strong> on {{ $complaint->created_at->format('M d, Y \a\t h:i A') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Complaints List
            </a>
        </div>
    </div>

    <!-- Confidential File Alert -->
    <div class="alert alert-dark border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3 bg-dark text-white shadow-sm">
        <i class="bi bi-shield-lock-fill text-warning fs-3 flex-shrink-0"></i>
        <div class="small">
            <strong class="text-warning">Confidential Admin Moderation File:</strong> This report is strictly private and accessible only to platform administrators. It is <strong>never visible to public users or normal shoppers</strong>, and is not shown on the farmer's public stall profile.
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Complaint Overview Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small mb-1">
                            {{ $complaint->type_label }}
                        </span>
                        <h4 class="fw-bold text-dark mb-0 mt-1">{{ $complaint->subject }}</h4>
                    </div>
                    <div class="text-muted small">
                        Case #CMP-{{ $complaint->id }}
                    </div>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-muted small fw-semibold text-uppercase font-mono-meta mb-2">Complaint Statement</h6>
                    <div class="p-3.5 bg-light rounded-3 text-dark mb-3" style="line-height: 1.8; white-space: pre-line; font-size: 0.95rem;">
                        {{ $complaint->description }}
                    </div>
                    <div class="d-flex flex-wrap gap-4 text-muted small border-top pt-3">
                        <div><i class="bi bi-calendar3 me-1"></i> <strong>Submitted:</strong> {{ $complaint->created_at->format('M d, Y h:i A') }}</div>
                        @if($complaint->resolved_at)
                            <div><i class="bi bi-check-circle me-1"></i> <strong>Resolved:</strong> {{ $complaint->resolved_at->format('M d, Y h:i A') }} (by {{ $complaint->resolver->name ?? 'Admin' }})</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Associated Pre-Order (If attached) -->
            @if($complaint->order)
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-receipt text-success"></i>
                            <span>Associated Pre-Order #{{ $complaint->order->order_number }}</span>
                        </h5>
                        <span class="badge bg-light text-secondary border small">{{ ucfirst($complaint->order->order_status) }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-4">
                                <div class="text-muted small">Pickup Date & Time</div>
                                <div class="fw-bold text-dark">{{ $complaint->order->pickup_date->format('M d, Y') }}</div>
                                <div class="small text-secondary">{{ $complaint->order->pickup_time_slot }}</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Total Pre-Order Value</div>
                                <div class="fw-bold text-dark fs-5">${{ number_format($complaint->order->total_amount, 2) }}</div>
                                <div class="small text-secondary">Pay at pickup</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Host Market</div>
                                <div class="fw-bold text-dark">{{ $complaint->order->market->name ?? 'Farmers Market' }}</div>
                            </div>
                        </div>

                        @if($complaint->order->items->count() > 0)
                            <div class="table-responsive border rounded-3 mt-3">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light small text-secondary">
                                        <tr>
                                            <th>Harvest Product</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($complaint->order->items as $item)
                                            <tr>
                                                <td class="fw-medium text-dark">{{ $item->product->name ?? 'Harvest Item' }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Admin Action / Status Update Form -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-gear-fill text-primary"></i>
                        <span>Moderation Action & Status Management</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.complaints.status', $complaint->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold text-dark small text-uppercase font-mono-meta">
                                Update Case Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select rounded-3" required>
                                <option value="pending" {{ $complaint->status === 'pending' ? 'selected' : '' }}>
                                    ⏳ Pending Review (Initial intake)
                                </option>
                                <option value="under_review" {{ $complaint->status === 'under_review' ? 'selected' : '' }}>
                                    🔍 Under Active Investigation (Communicating with grower/market)
                                </option>
                                <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>
                                    ✅ Resolved (Corrective action completed)
                                </option>
                                <option value="dismissed" {{ $complaint->status === 'dismissed' ? 'selected' : '' }}>
                                    🚫 Dismissed / Closed (No violation found or duplicate report)
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label fw-bold text-dark small text-uppercase font-mono-meta">
                                Admin Investigation & Resolution Notes
                            </label>
                            <textarea name="admin_notes" 
                                      id="admin_notes" 
                                      rows="4" 
                                      class="form-control rounded-3" 
                                      placeholder="Internal findings, grower response, warning issued, refund arranged at stall, etc. (Shared with the reporting customer as case response)">{{ old('admin_notes', $complaint->admin_notes) }}</textarea>
                            <div class="form-text small text-muted">
                                Entering notes provides transparency to the shopper regarding how their issue was addressed.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Save Moderation Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone: Delete Complaint -->
            <div class="card border border-danger-subtle rounded-4 bg-white p-3.5 d-flex flex-row justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold text-danger mb-0">Delete Complaint Record</h6>
                    <small class="text-muted">Permanently remove this report if it is spam or invalid.</small>
                </div>
                <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this complaint record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="bi bi-trash3 me-1"></i> Delete Record
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="col-lg-4">
            <!-- Customer Details Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-person-fill text-primary"></i>
                        <span>Reporting Customer</span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="fw-bold text-dark fs-6">{{ $complaint->customer->name ?? 'User #' . $complaint->customer_id }}</div>
                    <div class="text-muted small mb-2">{{ $complaint->customer->email ?? 'No email' }}</div>
                    @if($complaint->customer->contact_number)
                        <div class="small text-secondary mb-2">
                            <i class="bi bi-telephone text-primary me-1"></i> {{ $complaint->customer->contact_number }}
                        </div>
                    @endif
                    <div class="small text-muted mb-3">
                        <i class="bi bi-clock-history me-1"></i> Shopper joined {{ $complaint->customer->created_at->format('M Y') }}
                    </div>
                    <div class="border-top pt-2">
                        <span class="text-muted small">Total orders by customer:</span>
                        <span class="fw-bold text-dark ms-1">{{ $complaint->customer->orders()->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Reported Farmer Details Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-shop text-success"></i>
                        <span>Reported Grower / Stall</span>
                    </h6>
                    <span class="badge {{ $complaint->farmer->is_approved ? 'bg-success' : 'bg-warning text-dark' }} small">
                        {{ $complaint->farmer->is_approved ? 'Active Stall' : 'Pending/Suspended' }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $complaint->farmer->user->profile_photo_path ?? 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=150&q=80' }}" 
                             alt="{{ $complaint->farmer->stall_name }}" 
                             class="rounded-circle object-fit-cover border" 
                             style="width: 52px; height: 52px;">
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $complaint->farmer->stall_name }}</div>
                            <div class="text-muted small">{{ $complaint->farmer->contact_person }}</div>
                        </div>
                    </div>
                    <div class="small text-secondary mb-2">
                        <i class="bi bi-geo-alt text-success me-1"></i>
                        <strong>Market:</strong> {{ $complaint->farmer->market->name ?? 'Local Farmers Market' }}
                    </div>
                    @if($complaint->farmer->contact_number)
                        <div class="small text-secondary mb-3">
                            <i class="bi bi-telephone text-primary me-1"></i>
                            <strong>Phone:</strong> {{ $complaint->farmer->contact_number }}
                        </div>
                    @endif
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.farmers.show', $complaint->farmer->id) }}" class="btn btn-sm btn-outline-dark rounded-pill">
                            <i class="bi bi-sliders me-1"></i> Manage Farmer Account
                        </a>
                        <a href="{{ route('farmers.show', $complaint->farmer->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View Public Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Farmer's Complaint History (Pattern Detection) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">Farmer's Other Reports</h6>
                    <span class="badge bg-light text-secondary border small">{{ $farmerComplaintHistory->count() }} prior</span>
                </div>
                <div class="card-body p-3">
                    @if($farmerComplaintHistory->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($farmerComplaintHistory as $historyItem)
                                <a href="{{ route('admin.complaints.show', $historyItem->id) }}" class="p-2.5 rounded-3 border bg-light bg-opacity-50 text-decoration-none text-dark hover-lift d-block">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="font-monospace fw-bold small">#CMP-{{ $historyItem->id }}</span>
                                        @php $hBadge = $historyItem->status_badge; @endphp
                                        <span class="badge rounded-pill border {{ $hBadge['bg'] }}" style="font-size: 0.65rem;">
                                            {{ $hBadge['label'] }}
                                        </span>
                                    </div>
                                    <div class="small fw-semibold text-truncate">{{ $historyItem->subject }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $historyItem->created_at->format('M d, Y') }}</div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted small">
                            <i class="bi bi-check2-circle text-success fs-4 d-block mb-1"></i>
                            No other complaints on file for this grower.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
