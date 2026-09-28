@extends('layouts.admin')

@section('title', 'Farmer Stalls & Vendor Approvals — Admin MarketLink')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono-meta">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active text-success" aria-current="page">Farmer Management</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark" style="letter-spacing: -0.025em;">Farmer Stalls & Approval Gate</h1>
            <p class="text-muted small mb-0">Approve verified growers, audit stall licenses, suspend non-compliant vendors, or reinstate trading privileges (SRS §1.6).</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning-subtle px-3 py-2 rounded-pill font-mono-meta">
                <i class="bi bi-clock-history me-1 text-warning"></i> {{ $counts['pending'] }} Pending Review
            </span>
            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill font-mono-meta">
                <i class="bi bi-patch-check-fill me-1"></i> {{ $counts['approved'] }} Approved & Active
            </span>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="alert alert-liquid-glass alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-liquid-glass alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-liquid-glass alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Tabs & Metrics -->
    <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <ul class="nav nav-pills gap-1">
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $status === 'all' ? 'active bg-success' : 'text-dark bg-light' }}" 
                       href="{{ route('admin.farmers.index', array_merge(request()->query(), ['status' => 'all'])) }}">
                        All Stalls <span class="badge {{ $status === 'all' ? 'bg-white text-success' : 'bg-secondary bg-opacity-25 text-dark' }} ms-1">{{ $counts['all'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $status === 'pending' ? 'active bg-warning text-dark' : 'text-dark bg-light' }}" 
                       href="{{ route('admin.farmers.index', array_merge(request()->query(), ['status' => 'pending'])) }}">
                        Pending Verification <span class="badge {{ $status === 'pending' ? 'bg-dark text-white' : 'bg-warning bg-opacity-25 text-dark' }} ms-1">{{ $counts['pending'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $status === 'approved' || $status === 'active' ? 'active bg-success' : 'text-dark bg-light' }}" 
                       href="{{ route('admin.farmers.index', array_merge(request()->query(), ['status' => 'approved'])) }}">
                        Approved / Active <span class="badge {{ $status === 'approved' || $status === 'active' ? 'bg-white text-success' : 'bg-success bg-opacity-25 text-dark' }} ms-1">{{ $counts['approved'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $status === 'suspended' ? 'active bg-danger' : 'text-dark bg-light' }}" 
                       href="{{ route('admin.farmers.index', array_merge(request()->query(), ['status' => 'suspended'])) }}">
                        Suspended <span class="badge {{ $status === 'suspended' ? 'bg-white text-danger' : 'bg-danger bg-opacity-25 text-dark' }} ms-1">{{ $counts['suspended'] }}</span>
                    </a>
                </li>
            </ul>

            <!-- Search & Market Filter Form -->
            <form action="{{ route('admin.farmers.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="hidden" name="status" value="{{ $status }}">
                
                <div class="input-group input-group-sm" style="min-width: 220px;">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search stall, grower, email..." value="{{ $search }}">
                </div>

                <select name="market_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All Markets</option>
                    @foreach($markets as $m)
                        <option value="{{ $m->id }}" {{ $marketId == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                @if(!empty($search) || !empty($marketId) || $status !== 'all')
                    <a href="{{ route('admin.farmers.index') }}" class="btn btn-sm btn-link text-muted p-1" title="Clear all filters">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Farmer Roster Table -->
    <div class="card card-custom p-0 bg-white border-0 shadow-sm overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-4">Stall / Business</th>
                        <th>Assigned Market</th>
                        <th>Contact Person & Phone</th>
                        <th>Catalog</th>
                        <th>Operating Days</th>
                        <th>Approval Status</th>
                        <th class="text-end pe-4">Admin Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farmers as $farmer)
                        @php
                            $isFarmerApproved = (bool) $farmer->is_approved && ($farmer->user->is_approved ?? true) && ($farmer->user->is_active ?? true);
                            $isFarmerSuspended = ($farmer->approval_status === 'suspended') || (!($farmer->user->is_active ?? true));
                        @endphp
                        <tr>
                            <!-- Stall Name -->
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-success fw-bold overflow-hidden" style="width: 42px; height: 42px; flex-shrink: 0;">
                                        @if($farmer->image_url)
                                            <img src="{{ $farmer->image_url }}" alt="{{ $farmer->stall_name }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <i class="bi bi-shop fs-5"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">
                                            {{ $farmer->stall_name }}
                                        </div>
                                        <div class="text-muted font-mono-meta small" style="font-size: 0.76rem;">
                                            {{ $farmer->user->email ?? 'No email' }} • ID #{{ $farmer->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Assigned Market -->
                            <td>
                                @if($farmer->market)
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-geo-alt text-success me-1"></i>{{ $farmer->market->name }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Unassigned</span>
                                @endif
                            </td>

                            <!-- Contact Person & Phone -->
                            <td>
                                <div class="small fw-semibold text-dark">{{ $farmer->contact_person ?: ($farmer->user->name ?? 'N/A') }}</div>
                                <div class="text-muted small font-mono-meta">{{ $farmer->contact_number ?: ($farmer->user->contact_number ?? 'No phone') }}</div>
                            </td>

                            <!-- Catalog Stats -->
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle font-mono-meta">
                                    {{ $farmer->products_count ?? $farmer->products->count() }} Products
                                </span>
                                <span class="badge bg-light text-muted border font-mono-meta">
                                    {{ $farmer->orders_count ?? $farmer->orders->count() }} Orders
                                </span>
                            </td>

                            <!-- Operating Days -->
                            <td>
                                @php
                                    $days = is_array($farmer->operating_days) ? $farmer->operating_days : explode(',', (string) $farmer->operating_days);
                                    $days = array_filter(array_map('trim', $days));
                                @endphp
                                <div class="d-flex flex-wrap gap-1" style="max-width: 170px;">
                                    @forelse(array_slice($days, 0, 3) as $day)
                                        <span class="badge bg-light text-dark border font-mono-meta" style="font-size: 0.7rem;">{{ substr($day, 0, 3) }}</span>
                                    @empty
                                        <span class="text-muted small">Not set</span>
                                    @endforelse
                                    @if(count($days) > 3)
                                        <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">+{{ count($days) - 3 }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td>
                                @if($isFarmerSuspended)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill font-mono-meta">
                                        <i class="bi bi-slash-circle me-1"></i> Suspended
                                    </span>
                                @elseif($isFarmerApproved)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2.5 py-1.5 rounded-pill font-mono-meta">
                                        <i class="bi bi-patch-check-fill me-1"></i> Approved
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-15 text-dark border border-warning-subtle px-2.5 py-1.5 rounded-pill font-mono-meta">
                                        <i class="bi bi-hourglass-split me-1 text-warning"></i> Pending Review
                                    </span>
                                @endif
                            </td>

                            <!-- Quick Actions -->
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    <!-- Public Stall View -->
                                    <a href="{{ route('farmers.show', $farmer->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="View Public Stallfront">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>

                                    <!-- Quick Details Modal Trigger -->
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#farmerDetailModal{{ $farmer->id }}" title="View Stall Info">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- APPROVE BUTTON (if pending or suspended) -->
                                    @if(!$isFarmerApproved && !$isFarmerSuspended)
                                        <form action="{{ route('admin.farmers.approve', $farmer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" title="Approve stall for public trading">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </form>
                                    @endif

                                    <!-- REINSTATE BUTTON (if suspended) -->
                                    @if($isFarmerSuspended)
                                        <form action="{{ route('admin.farmers.reinstate', $farmer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm" title="Reinstate trading privileges">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reinstate
                                            </button>
                                        </form>
                                    @endif

                                    <!-- SUSPEND BUTTON (if currently approved) -->
                                    @if($isFarmerApproved)
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $farmer->id }}" title="Suspend stall privileges">
                                            <i class="bi bi-pause-circle me-1"></i> Suspend
                                        </button>
                                    @endif
                                </div>

                                <!-- SUSPEND CONFIRMATION MODAL -->
                                @if($isFarmerApproved)
                                    <div class="modal fade" id="suspendModal{{ $farmer->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                                <form action="{{ route('admin.farmers.suspend', $farmer->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold text-dark">
                                                            <i class="bi bi-exclamation-octagon text-warning me-2"></i>Suspend Stall: {{ $farmer->stall_name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-3">
                                                        <p class="text-muted small">
                                                            Suspending this grower temporarily locks customer pre-order reservations and marks their products as inactive. You can reinstate this stall at any time.
                                                        </p>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold text-dark">Suspension Reason (Visible in Audit Log):</label>
                                                            <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="e.g. Incomplete food safety certification or customer dispute under review.">Quality review check or seasonal compliance verification.</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold">
                                                            Confirm Suspension
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- STALL DETAILS MODAL -->
                                <div class="modal fade" id="farmerDetailModal{{ $farmer->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                                            <div class="modal-header border-bottom pb-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <i class="bi bi-shop fs-4"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="modal-title fw-bold text-dark mb-0">{{ $farmer->stall_name }}</h5>
                                                        <small class="text-muted">{{ $farmer->farm_name ?: 'Registered Local Farm' }} • ID #{{ $farmer->id }}</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">Contact Person</span>
                                                        <strong class="text-dark">{{ $farmer->contact_person ?: ($farmer->user->name ?? 'N/A') }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">User Account Email</span>
                                                        <strong class="text-dark">{{ $farmer->user->email ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">Contact Phone</span>
                                                        <strong class="text-dark">{{ $farmer->contact_number ?: 'Not provided' }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">Assigned Market</span>
                                                        <strong class="text-success">{{ $farmer->market->name ?? 'Unassigned' }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">Physical Stall Address</span>
                                                        <span class="text-dark small">{{ $farmer->address ?: 'Plaza assigned on site' }}, {{ $farmer->city ?? '' }} {{ $farmer->state ?? '' }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">Map Coordinates</span>
                                                        <span class="font-mono-meta small">{{ $farmer->latitude ?? '0.00' }}, {{ $farmer->longitude ?? '0.00' }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">Pickup Windows</span>
                                                        <span class="text-dark small">{{ $farmer->pickup_time_windows ?: 'Standard market hours' }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted small d-block">Pre-Order Cutoff</span>
                                                        <span class="text-dark small">{{ $farmer->cutoff_hours ?? 2 }} hours before market opening</span>
                                                    </div>
                                                    @if($farmer->bio)
                                                        <div class="col-12 pt-2 border-top">
                                                            <span class="text-muted small d-block mb-1">Farmer Bio & Farm Story</span>
                                                            <p class="text-dark small mb-0 bg-light p-2.5 rounded-3">{{ $farmer->bio }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top pt-3">
                                                <a href="{{ route('farmers.show', $farmer->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Public Stallfront
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-person-x fs-1 text-muted d-block mb-2"></i>
                                    <div class="fw-semibold">No farmers match the current filter criteria.</div>
                                    <small class="text-muted">Try clearing the search or switching to "All Stalls".</small>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.farmers.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Reset Filters</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($farmers->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $farmers->firstItem() }} to {{ $farmers->lastItem() }} of {{ $farmers->total() }} farmers</small>
                <div>{{ $farmers->links() }}</div>
            </div>
        @endif
    </div>

</div>
@endsection
