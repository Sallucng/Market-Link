@extends('layouts.admin')

@section('title', 'Platform Dashboard & Visual Analytics — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb & Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
            <h2 class="heading-serif fw-bold text-dark mb-0">Platform Overview & Live Analytics</h2>
            <small class="text-muted">High-level market performance, grower approvals, and pre-order pipelines (SRS §1.6)</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 rounded-pill px-3">
                <i class="bi bi-file-earmark-bar-graph"></i> Detailed Reports
            </a>
            <a href="{{ route('admin.markets.create') }}" class="btn btn-sm btn-brand d-flex align-items-center gap-1 rounded-pill px-3">
                <i class="bi bi-plus-circle"></i> Add Market
            </a>
        </div>
    </div>

    <!-- Platform Key Metrics (SRS §1.6) -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-4 align-items-stretch">
        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Total Farmers</span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">Growers</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-0 mt-2 stat-counter" data-target="{{ $metrics['total_farmers'] }}">0</h3>
                </div>
                <small class="text-success fw-medium mt-2 d-block text-truncate"><i class="bi bi-check-circle-fill me-1"></i>{{ $activeFarmers->count() }} Approved</small>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Customers</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small">Shoppers</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-0 mt-2 stat-counter" data-target="{{ $metrics['total_customers'] }}">0</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">Registered shoppers</small>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Markets</span>
                        <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-2 py-1 small">Locations</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-0 mt-2 stat-counter" data-target="{{ $metrics['total_markets'] }}">0</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">Physical plazas</small>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Pre-Orders</span>
                        <span class="badge bg-info bg-opacity-10 text-info-emphasis rounded-pill px-2 py-1 small">Pickups</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-0 mt-2 stat-counter" data-target="{{ $metrics['total_orders'] }}">0</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">Stall reservations</small>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Platform Volume</span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">Sales</span>
                    </div>
                    <h3 class="fw-bold text-success mb-0 mt-2 stat-counter" data-target="{{ $metrics['total_volume'] }}" data-is-currency="true">$0.00</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">Pay-at-pickup volume</small>
            </div>
        </div>
    </div>

    <!-- Visual Analytics: Bar Graphs & Pie Charts (SRS §1.6) -->
    <div class="row g-4 mb-4">
        <!-- Bar Chart: Revenue Generated per Market -->
        <div class="col-lg-7">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-1">
                            <i class="bi bi-bar-chart-fill text-success me-2"></i>Revenue Generated per Market
                        </h5>
                        <small class="text-muted">Total settled in-person sales by physical market plaza (SRS §1.6)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">USD ($)</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="marketRevenueBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Doughnut / Pie Chart: Pre-Order Pipeline Status -->
        <div class="col-lg-5">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h5 class="heading-serif fw-bold text-dark mb-1">
                                <i class="bi bi-pie-chart-fill text-primary me-2"></i>Pre-Order Status Breakdown
                            </h5>
                            <small class="text-muted">Order lifecycle distribution (SRS §1.5)</small>
                        </div>
                        <span class="badge bg-light text-dark border font-mono-meta" id="orderStatusTotalBadge">{{ array_sum($orderStatusData) }} Total</span>
                    </div>
                    <div style="position: relative; height: 220px; width: 100%;">
                        <canvas id="orderStatusPieChart"></canvas>
                    </div>
                </div>

                <!-- Interactive Status Filters/Pills in Same Space (SRS §1.5 & Agentation feedback) -->
                <div class="d-flex flex-wrap justify-content-center gap-1 mt-2 pt-2 border-top" id="chartStatusLegend">
                    @php
                        $statusColors = ['#eab308', '#3b82f6', '#8b5cf6', '#10b981', '#94a3b8'];
                    @endphp
                    @foreach($orderStatusLabels as $idx => $label)
                        <span class="badge rounded-pill status-chart-pill px-2 py-1 bg-light text-dark border" 
                              style="cursor: pointer; font-size: 0.74rem; transition: all 0.2s ease;"
                              data-item-index="{{ $idx }}"
                              title="Hover to highlight {{ $label }} in chart">
                            <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $statusColors[$idx] ?? '#94a3b8' }};"></span>
                            {{ $label }}: <strong>{{ $orderStatusData[$idx] }}</strong>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Doughnut / Pie Chart: Popular Categories -->
        <div class="col-lg-5">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-1">
                            <i class="bi bi-tags-fill text-warning me-2"></i>Popular Product Categories
                        </h5>
                        <small class="text-muted">Distribution of items across taxonomy (SRS §1.6)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">{{ array_sum($categoryCounts) }} Items</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="categoryDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Pre-Order Reservation Volume by Market -->
        <div class="col-lg-7">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-1">
                            <i class="bi bi-receipt text-info me-2"></i>Order Reservation Volumes by Venue
                        </h5>
                        <small class="text-muted">Customer pre-order pickup frequency per farmers market (SRS §1.6)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">Orders</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="marketOrdersBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Farmer Approvals Section (SRS §1.6: Admin can view, approve, or suspend Farmer registrations) -->
    <div class="card card-custom table-card p-4 bg-white border-0 shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="heading-serif fw-bold text-dark mb-0">Farmer Approval Gate</h5>
                <small class="text-muted">Growers cannot publish weekly products until verified and approved by an administrator.</small>
            </div>
            <span class="badge bg-warning text-dark">{{ $pendingFarmers->count() }} Pending Review</span>
        </div>

        @if($pendingFarmers->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Stall Name & Contact</th>
                            <th>Market Assigned</th>
                            <th>Phone & Email</th>
                            <th>Registration Date</th>
                            <th class="text-end">Approval Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingFarmers as $pf)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pf->stall_name }}</div>
                                    <small class="text-muted">{{ $pf->contact_person }}</small>
                                </td>
                                <td>{{ $pf->market->name ?? 'Unassigned Market' }}</td>
                                <td>
                                    <div>{{ $pf->contact_number }}</div>
                                    <small class="text-muted">{{ $pf->user->email }}</small>
                                </td>
                                <td>{{ $pf->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.farmers.approve', $pf->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                            <i class="bi bi-check-lg me-1"></i> Approve
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-3 rounded-3 bg-light border border-success border-opacity-25 mb-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Verification Queue Clear</div>
                            <small class="text-muted">100% of registered grower accounts have been vetted and approved to trade.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small">
                            <i class="bi bi-patch-check-fill me-1"></i> {{ $activeFarmers->count() }} Active Stalls Vetted
                        </span>
                        <a href="{{ route('admin.markets.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-geo-alt me-1"></i> Markets ({{ $metrics['total_markets'] }})
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Farmers Roster Preview (Fills empty area with high-value operational context) -->
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-sm">
                    <thead class="table-light small">
                        <tr>
                            <th>Recently Active Stall</th>
                            <th>Market Assigned</th>
                            <th>Contact Person</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeFarmers->take(4) as $af)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $af->stall_name }}</div>
                                    <small class="text-muted">{{ $af->user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $af->market->name ?? 'Unassigned' }}</span>
                                </td>
                                <td class="small">{{ $af->contact_person }} ({{ $af->contact_number }})</td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">
                                        <i class="bi bi-check-circle me-1"></i> Live Trading
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.farmers.suspend', $af->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Suspend trading for {{ addslashes($af->stall_name) }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-2 py-0" style="font-size: 0.76rem;" title="Suspend stall privileges">
                                            Suspend
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Active Farmers & Customer Moderation -->
    <div class="row g-4">
        <!-- Active Farmers Table -->
        <div class="col-lg-6">
            <div class="card card-custom table-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="heading-serif fw-bold text-dark mb-0">Approved Farmer Stalls ({{ $activeFarmers->count() }})</h5>
                    <a href="{{ route('admin.moderation.products') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Product Listings</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Stall</th>
                                <th>Market</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeFarmers as $af)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $af->stall_name }}</div>
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ $af->contact_person }}</span>
                                    </td>
                                    <td>{{ $af->market->name ?? 'Local Market' }}</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Active</span></td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.farmers.suspend', $af->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Suspend this farmer stall?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem;">
                                                Suspend
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No active farmers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Moderation Table (SRS §1.6) -->
        <div class="col-lg-6">
            <div class="card card-custom table-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="heading-serif fw-bold text-dark mb-0">Customer Accounts ({{ $customers->count() }})</h5>
                    <span class="badge bg-light text-dark border font-mono-meta">Moderation</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $c)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $c->name }}</div>
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ $c->email }}</span>
                                    </td>
                                    <td>{{ $c->contact_number ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $c->is_active ? 'bg-success bg-opacity-10 text-success border border-success-subtle' : 'bg-danger bg-opacity-10 text-danger border border-danger-subtle' }}">
                                            {{ $c->is_active ? 'Active' : 'Suspended' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.customers.toggle', $c->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $c->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} py-0 px-2" style="font-size: 0.75rem;">
                                                {{ $c->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No customers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // GSAP Motion Graphics & Entrances (antigravity-design-expert)
    // ----------------------------------------------------
    if (typeof gsap !== 'undefined') {
        // Staggered entrance for Stat Cards
        gsap.from('.stat-card', {
            opacity: 0,
            y: 28,
            duration: 0.65,
            stagger: 0.08,
            ease: 'power3.out'
        });

        // Staggered entrance for Analytics Chart Cards
        gsap.from('.chart-card', {
            opacity: 0,
            y: 24,
            duration: 0.75,
            delay: 0.25,
            stagger: 0.12,
            ease: 'power3.out'
        });

        // Entrance for Management Tables
        gsap.from('.table-card', {
            opacity: 0,
            y: 20,
            duration: 0.65,
            delay: 0.45,
            stagger: 0.1,
            ease: 'power3.out'
        });

        // Smooth Counter Animation for Metric Numbers
        document.querySelectorAll('.stat-counter').forEach(el => {
            const target = parseFloat(el.getAttribute('data-target') || 0);
            const isCurrency = el.getAttribute('data-is-currency') === 'true';
            const counter = { val: 0 };

            gsap.to(counter, {
                val: target,
                duration: 1.4,
                delay: 0.15,
                ease: 'power2.out',
                onUpdate: () => {
                    if (isCurrency) {
                        el.innerText = '$' + counter.val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    } else {
                        el.innerText = Math.round(counter.val).toLocaleString('en-US');
                    }
                }
            });
        });

        // 3D Spatial Micro-Tilt Interaction on Hover
        document.querySelectorAll('.tilt-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                gsap.to(card, {
                    rotateY: x * 5,
                    rotateX: -y * 5,
                    transformPerspective: 800,
                    duration: 0.25,
                    ease: 'power1.out'
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    rotateY: 0,
                    rotateX: 0,
                    duration: 0.45,
                    ease: 'power2.out'
                });
            });
        });
    }

    // ----------------------------------------------------
    // Chart.js Configuration with Animated Easings
    // ----------------------------------------------------
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. Bar Chart: Revenue Generated per Market (Animated Rising with Counting Numbers)
    const marketLabels = {!! json_encode($marketRevenueLabels) !!};
    const targetMarketRevenueData = {!! json_encode($marketRevenueData) !!};
    const maxMarketRevenue = Math.max(...targetMarketRevenueData, 100);

    const ctxRev = document.getElementById('marketRevenueBarChart');
    if (ctxRev) {
        const animatedRevenueLabelsPlugin = {
            id: 'animatedMarketRevenueLabels',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.font = '700 11px "Poppins", sans-serif';
                ctx.fillStyle = '#1b4332';

                chart.getDatasetMeta(0).data.forEach((bar, index) => {
                    const currentVal = data.datasets[0].data[index];
                    const targetVal = targetMarketRevenueData[index];
                    if (targetVal > 0) {
                        const formatted = '$' + currentVal.toFixed(2);
                        ctx.fillText(formatted, bar.x, bar.y - 4);
                    }
                });
                ctx.restore();
            }
        };

        const marketRevenueChart = new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: marketLabels,
                datasets: [{
                    label: 'Completed Revenue ($)',
                    data: targetMarketRevenueData.map(() => 0),
                    backgroundColor: '#1b4332',
                    hoverBackgroundColor: '#2d6a4f',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 48
                }]
            },
            plugins: [animatedRevenueLabelsPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: $' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: maxMarketRevenue * 1.18,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return '$' + value; }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        const revAnim = { progress: 0 };
        gsap.to(revAnim, {
            progress: 1,
            duration: 1.6,
            delay: 0.2,
            ease: 'power2.out',
            onUpdate: () => {
                marketRevenueChart.data.datasets[0].data = targetMarketRevenueData.map(v => v * revAnim.progress);
                marketRevenueChart.update('none');
            },
            onComplete: () => {
                marketRevenueChart.data.datasets[0].data = [...targetMarketRevenueData];
                marketRevenueChart.update('none');
            }
        });
    }

    // 2. Doughnut / Pie Chart: Pre-Order Pipeline Status Breakdown (Animated Sweep with Counting Center Number)
    const orderStatusLabels = {!! json_encode($orderStatusLabels) !!};
    const targetOrderStatusData = {!! json_encode($orderStatusData) !!};
    const totalOrders = targetOrderStatusData.reduce((a, b) => a + b, 0);

    const ctxStatus = document.getElementById('orderStatusPieChart');
    if (ctxStatus) {
        let currentAnimatedTotal = 0;
        let isStatusAnimating = true;

        const centerDoughnutPlugin = {
            id: 'centerDoughnutText',
            beforeDraw: function(chart) {
                if (chart.config.type !== 'doughnut') return;
                const ctx = chart.ctx;
                const active = chart.getActiveElements();

                let mainText = currentAnimatedTotal.toString();
                let subText = 'TOTAL ORDERS';

                if (!isStatusAnimating && active.length > 0) {
                    const idx = active[0].index;
                    const val = targetOrderStatusData[idx];
                    const pct = totalOrders > 0 ? Math.round((val / totalOrders) * 100) : 0;
                    mainText = val + ' (' + pct + '%)';
                    subText = chart.data.labels[idx].toUpperCase();
                }

                ctx.save();
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;

                ctx.font = '700 22px "Poppins", sans-serif';
                ctx.fillStyle = '#1b4332';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(mainText, centerX, centerY - 8);

                ctx.font = '600 10px "Plus Jakarta Sans", sans-serif';
                ctx.fillStyle = '#64748b';
                ctx.fillText(subText, centerX, centerY + 13);
                ctx.restore();
            }
        };

        const statusDoughnutChart = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: orderStatusLabels,
                datasets: [{
                    data: targetOrderStatusData.map(() => 0),
                    backgroundColor: [
                        '#eab308', // Placed: Amber
                        '#3b82f6', // Accepted: Blue
                        '#8b5cf6', // Ready: Purple
                        '#10b981', // Completed: Emerald
                        '#94a3b8'  // Cancelled: Gray
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 12
                }]
            },
            plugins: [centerDoughnutPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                circumference: 0,
                rotation: -90,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = targetOrderStatusData.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return ' ' + context.label + ': ' + Math.round(context.raw) + ' orders (' + pct + '%)';
                            }
                        }
                    }
                },
                cutout: '66%'
            }
        });

        const statusAnim = { progress: 0 };
        gsap.to(statusAnim, {
            progress: 1,
            duration: 1.6,
            delay: 0.35,
            ease: 'power2.out',
            onUpdate: () => {
                statusDoughnutChart.options.circumference = 360 * statusAnim.progress;
                statusDoughnutChart.data.datasets[0].data = targetOrderStatusData.map(v => v * statusAnim.progress);
                currentAnimatedTotal = Math.round(totalOrders * statusAnim.progress);
                statusDoughnutChart.update('none');
            },
            onComplete: () => {
                isStatusAnimating = false;
                statusDoughnutChart.options.circumference = 360;
                statusDoughnutChart.data.datasets[0].data = [...targetOrderStatusData];
                currentAnimatedTotal = totalOrders;
                statusDoughnutChart.update('none');
            }
        });

        // Animate the status pill badge numbers underneath
        document.querySelectorAll('#chartStatusLegend .status-chart-pill').forEach(pill => {
            const itemIdx = parseInt(pill.getAttribute('data-item-index'));
            const strong = pill.querySelector('strong');
            if (strong) {
                const targetVal = targetOrderStatusData[itemIdx] || 0;
                strong.innerText = '0';
                const counterObj = { val: 0 };
                gsap.to(counterObj, {
                    val: targetVal,
                    duration: 1.6,
                    delay: 0.35,
                    ease: 'power2.out',
                    onUpdate: () => {
                        strong.innerText = Math.round(counterObj.val);
                    }
                });
            }

            pill.addEventListener('mouseenter', () => {
                if (isStatusAnimating) return;
                pill.classList.remove('bg-light');
                pill.classList.add('bg-white', 'shadow-sm', 'border-primary');
                statusDoughnutChart.setActiveElements([{ datasetIndex: 0, index: itemIdx }]);
                statusDoughnutChart.tooltip.setActiveElements([{ datasetIndex: 0, index: itemIdx }]);
                statusDoughnutChart.update();
            });
            pill.addEventListener('mouseleave', () => {
                if (isStatusAnimating) return;
                pill.classList.remove('bg-white', 'shadow-sm', 'border-primary');
                pill.classList.add('bg-light');
                statusDoughnutChart.setActiveElements([]);
                statusDoughnutChart.tooltip.setActiveElements([]);
                statusDoughnutChart.update();
            });
        });
    }

    // 3. Doughnut / Pie Chart: Popular Product Categories (Animated Sweep)
    const categoryLabels = {!! json_encode($categoryLabels) !!};
    const targetCategoryCounts = {!! json_encode($categoryCounts) !!};

    const ctxCat = document.getElementById('categoryDistributionChart');
    if (ctxCat) {
        const categoryPieChart = new Chart(ctxCat, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: targetCategoryCounts.map(() => 0),
                    backgroundColor: [
                        '#2d6a4f', // Deep Green
                        '#52b788', // Light Sage
                        '#d4a373', // Warm Terracotta/Amber
                        '#e76f51', // Warm Coral
                        '#74c69d', // Mint Green
                        '#b7b7a4'  // Olive Gray
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                circumference: 0,
                rotation: -90,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });

        const catAnim = { progress: 0 };
        gsap.to(catAnim, {
            progress: 1,
            duration: 1.6,
            delay: 0.4,
            ease: 'power2.out',
            onUpdate: () => {
                categoryPieChart.options.circumference = 360 * catAnim.progress;
                categoryPieChart.data.datasets[0].data = targetCategoryCounts.map(v => v * catAnim.progress);
                categoryPieChart.update('none');
            },
            onComplete: () => {
                categoryPieChart.options.circumference = 360;
                categoryPieChart.data.datasets[0].data = [...targetCategoryCounts];
                categoryPieChart.update('none');
            }
        });
    }

    // 4. Bar Chart: Order Reservation Volumes by Market (Animated Rising with Counting Numbers)
    const targetMarketOrderCounts = {!! json_encode($marketOrderCountData) !!};
    const maxMarketOrders = Math.max(...targetMarketOrderCounts, 5);

    const ctxOrders = document.getElementById('marketOrdersBarChart');
    if (ctxOrders) {
        const animatedOrderLabelsPlugin = {
            id: 'animatedMarketOrderLabels',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.font = '700 11px "Poppins", sans-serif';
                ctx.fillStyle = '#0284c7';

                chart.getDatasetMeta(0).data.forEach((bar, index) => {
                    const currentVal = data.datasets[0].data[index];
                    const targetVal = targetMarketOrderCounts[index];
                    if (targetVal > 0) {
                        ctx.fillText(Math.round(currentVal).toString(), bar.x, bar.y - 4);
                    }
                });
                ctx.restore();
            }
        };

        const marketOrdersChart = new Chart(ctxOrders, {
            type: 'bar',
            data: {
                labels: marketLabels,
                datasets: [{
                    label: 'Total Orders',
                    data: targetMarketOrderCounts.map(() => 0),
                    backgroundColor: '#0284c7',
                    hoverBackgroundColor: '#0369a1',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 48
                }]
            },
            plugins: [animatedOrderLabelsPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: maxMarketOrders * 1.25,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        const ordersAnim = { progress: 0 };
        gsap.to(ordersAnim, {
            progress: 1,
            duration: 1.6,
            delay: 0.3,
            ease: 'power2.out',
            onUpdate: () => {
                marketOrdersChart.data.datasets[0].data = targetMarketOrderCounts.map(v => v * ordersAnim.progress);
                marketOrdersChart.update('none');
            },
            onComplete: () => {
                marketOrdersChart.data.datasets[0].data = [...targetMarketOrderCounts];
                marketOrdersChart.update('none');
            }
        });
    }
});
</script>
@endsection
