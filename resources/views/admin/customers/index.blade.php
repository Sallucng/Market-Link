@extends('layouts.admin')

@section('title', 'Customer Accounts & Access Management — Admin MarketLink')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono-meta">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active text-success" aria-current="page">Customer Accounts</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark" style="letter-spacing: -0.025em;">Customer Accounts & Moderation</h1>
            <p class="text-muted small mb-0">Inspect customer profiles, monitor pre-order participation, and manage shopper access permissions (SRS §1.6).</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill font-mono-meta">
                <i class="bi bi-people me-1"></i> {{ $counts['active'] }} Active Shoppers
            </span>
            @if($counts['suspended'] > 0)
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill font-mono-meta">
                    <i class="bi bi-slash-circle me-1"></i> {{ $counts['suspended'] }} Deactivated
                </span>
            @endif
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

    <!-- Status Tabs & Search -->
    <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <ul class="nav nav-pills gap-1">
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $status === 'all' ? 'active bg-primary' : 'text-dark bg-light' }}" 
                       href="{{ route('admin.customers.index', array_merge(request()->query(), ['status' => 'all'])) }}">
                        All Customers <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25 text-dark' }} ms-1">{{ $counts['all'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $status === 'active' ? 'active bg-success text-white' : 'text-dark bg-light' }}" 
                       href="{{ route('admin.customers.index', array_merge(request()->query(), ['status' => 'active'])) }}">
                        Active <span class="badge {{ $status === 'active' ? 'bg-white text-success' : 'bg-success bg-opacity-25 text-dark' }} ms-1">{{ $counts['active'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $status === 'suspended' ? 'active bg-danger text-white' : 'text-dark bg-light' }}" 
                       href="{{ route('admin.customers.index', array_merge(request()->query(), ['status' => 'suspended'])) }}">
                        Suspended <span class="badge {{ $status === 'suspended' ? 'bg-white text-danger' : 'bg-danger bg-opacity-25 text-dark' }} ms-1">{{ $counts['suspended'] }}</span>
                    </a>
                </li>
            </ul>

            <!-- Search Form -->
            <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="hidden" name="status" value="{{ $status }}">
                
                <div class="input-group input-group-sm" style="min-width: 250px;">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search customer name, email..." value="{{ $search }}">
                </div>

                <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
                @if(!empty($search) || $status !== 'all')
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-link text-muted p-1" title="Clear filter">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Customer Roster Table -->
    <div class="card card-custom p-0 bg-white border-0 shadow-sm overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-4">Shopper</th>
                        <th>Contact Email & Phone</th>
                        <th>Address</th>
                        <th>Pre-Orders Placed</th>
                        <th>Account Created</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Account Control</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <!-- Customer Name & Avatar -->
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary border border-primary-subtle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">{{ $customer->name }}</div>
                                        <div class="text-muted font-mono-meta small" style="font-size: 0.76rem;">{{ '@' . ($customer->username ?: 'customer') }} • ID #{{ $customer->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact info -->
                            <td>
                                <div class="small text-dark">{{ $customer->email }}</div>
                                <div class="text-muted small font-mono-meta">{{ $customer->contact_number ?: 'No phone provided' }}</div>
                            </td>

                            <!-- Address -->
                            <td>
                                <span class="small text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                    {{ $customer->address ?: 'No address specified' }}
                                </span>
                            </td>

                            <!-- Pre-orders -->
                            <td>
                                <span class="badge bg-light text-dark border font-mono-meta">
                                    <i class="bi bi-basket me-1 text-success"></i>{{ $customer->orders_count }} Orders
                                </span>
                            </td>

                            <!-- Member since -->
                            <td class="small text-muted font-mono-meta">
                                {{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'Unknown' }}
                            </td>

                            <!-- Status -->
                            <td>
                                @if($customer->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2.5 py-1.5 rounded-pill font-mono-meta">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill font-mono-meta">
                                        <i class="bi bi-slash-circle me-1"></i> Deactivated
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.customers.toggle', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $customer->is_active ? 'Deactivate' : 'Reactivate' }} customer account for {{ addslashes($customer->name) }}?')">
                                    @csrf
                                    @if($customer->is_active)
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3" title="Suspend customer access">
                                            <i class="bi bi-pause-circle me-1"></i> Deactivate
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" title="Reactivate customer access">
                                            <i class="bi bi-play-circle me-1"></i> Reactivate
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-person-slash fs-1 text-muted d-block mb-2"></i>
                                    <div class="fw-semibold">No customers match the current filter criteria.</div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Reset Search</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} shoppers</small>
                <div>{{ $customers->links() }}</div>
            </div>
        @endif
    </div>

</div>
@endsection
