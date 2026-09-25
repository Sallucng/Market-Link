@extends('layouts.farmer')

@section('title', 'Farmer Vendor Dashboard — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <!-- Header & Stall Identification -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">
                    <i class="bi bi-flower1 me-1"></i> Grower Stall Console
                </span>
                <span class="text-muted small">Verified Grower Console</span>
            </div>
            <h2 class="heading-serif fw-bold text-dark mb-0">{{ $farmer->stall_name }}</h2>
            <small class="text-muted">
                Operator: <strong>{{ $user->name }}</strong> &bull; 
                Assigned Market: <span class="badge bg-light text-dark border">{{ $farmer->market->name ?? 'Unassigned Market' }}</span>
            </small>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(!$user->is_approved)
                <div class="alert alert-warning py-2 px-3 mb-0 small border-warning d-flex align-items-center shadow-sm">
                    <i class="bi bi-clock-history me-2 fs-5"></i>
                    <div>
                        <strong>Pending Administrator Approval:</strong><br>
                        Your stall profile is under verification before products go live.
                    </div>
                </div>
            @else
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill d-inline-flex align-items-center small">
                    <span class="pulse-dot me-2"></span> Active Verified Stall
                </span>
            @endif

            <a href="{{ route('farmer.products.create') }}" class="btn btn-brand rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Sales & Pre-Order Insights (SRS §1.6: Level 4-Card Row with Tilt & Stat-Card Gradient) -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 mb-4 align-items-stretch">
        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Total Orders</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small">Lifetime</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-0 mt-2 stat-counter" data-target="{{ $totalOrders }}">0</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">All pre-order reservations</small>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Pending Packing</span>
                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill px-2 py-1 small">Queue</span>
                    </div>
                    <h3 class="fw-bold text-warning mb-0 mt-2 stat-counter" data-target="{{ $pendingOrders }}">0</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">Awaiting packing or pickup</small>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Today's Pickups</span>
                        <span class="badge bg-info bg-opacity-10 text-info-emphasis rounded-pill px-2 py-1 small">Scheduled</span>
                    </div>
                    <h3 class="fw-bold text-info mb-0 mt-2 stat-counter" data-target="{{ $todayPickups }}">0</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">Scheduled for pickup today</small>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-3 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Settled Revenue</span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">Completed</span>
                    </div>
                    <h3 class="fw-bold text-success mb-0 mt-2 stat-counter" data-target="{{ $totalRevenue }}" data-is-currency="true">$0.00</h3>
                </div>
                <small class="text-muted mt-2 d-block text-truncate">Pay-at-pickup collected</small>
            </div>
        </div>
    </div>

    <!-- Visual Analytics: Bar Chart & Interactive Doughnut Chart (SRS §1.6) -->
    <div class="row g-4 mb-4">
        <!-- Bar Chart: 7-Day Revenue Trend -->
        <div class="col-lg-7">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-1">
                            <i class="bi bi-bar-chart-fill text-success me-2"></i>7-Day Settled Pickup Volume
                        </h5>
                        <small class="text-muted">Completed customer pickup revenue across market days (SRS §1.6)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">USD ($)</span>
                </div>
                <div style="position: relative; height: 240px; width: 100%;">
                    <canvas id="farmerRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Doughnut / Pie Chart: Pre-Order Lifecycle Breakdown (Interactive with Center Text Plugin) -->
        <div class="col-lg-5">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h5 class="heading-serif fw-bold text-dark mb-1">
                                <i class="bi bi-pie-chart-fill text-primary me-2"></i>Pre-Order Pipeline Status
                            </h5>
                            <small class="text-muted">Order fulfillment distribution (SRS §1.5)</small>
                        </div>
                        <span class="badge bg-light text-dark border font-mono-meta">{{ array_sum($orderStatusData) }} Total</span>
                    </div>
                    <div style="position: relative; height: 210px; width: 100%;">
                        <canvas id="farmerStatusChart"></canvas>
                    </div>
                </div>

                <!-- Interactive Status Filters/Pills in Same Space -->
                <div class="d-flex flex-wrap justify-content-center gap-1 mt-2 pt-2 border-top" id="farmerChartLegend">
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

    <!-- Operational Queue & Best Sellers Section -->
    <div class="row g-4 mb-4">
        <!-- Recent Incoming Orders -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-1">Incoming Customer Pre-Orders</h5>
                        <small class="text-muted">Reservations waiting for confirmation or ready-for-pickup mark</small>
                    </div>
                    <a href="{{ route('farmer.orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        View All Orders &rarr;
                    </a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="text-center py-5 bg-light rounded-3 border border-dashed my-2">
                        <i class="bi bi-inbox text-muted display-4"></i>
                        <h6 class="fw-bold mt-3 mb-1 text-dark">No Pre-Orders in Queue</h6>
                        <p class="text-muted small mb-3">Your pre-order pickup queue is clear. Make sure your weekly harvest inventory is published!</p>
                        <a href="{{ route('farmer.products.create') }}" class="btn btn-brand btn-sm rounded-pill px-3">
                            <i class="bi bi-plus-circle me-1"></i> Add Product Item
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Pickup Slot</th>
                                    <th>Total Due</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $ord)
                                    <tr>
                                        <td class="fw-bold">
                                            <a href="{{ route('farmer.orders.show', $ord->id) }}" class="text-success text-decoration-none font-mono-meta">
                                                {{ $ord->order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-bold small text-dark">{{ $ord->customer->name ?? 'Guest Customer' }}</div>
                                            <small class="text-muted">{{ $ord->customer->contact_number ?? '' }}</small>
                                        </td>
                                        <td class="small">
                                            <div>{{ $ord->pickup_date->format('M d, Y') }}</div>
                                            <span class="text-muted">{{ $ord->pickup_time_slot }}</span>
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($ord->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $ord->order_status === 'completed' ? 'bg-success' : '' }}
                                                {{ $ord->order_status === 'ready_for_pickup' ? 'bg-info text-dark' : '' }}
                                                {{ $ord->order_status === 'accepted' ? 'bg-primary' : '' }}
                                                {{ $ord->order_status === 'placed' ? 'bg-warning text-dark' : '' }}
                                                {{ in_array($ord->order_status, ['cancelled', 'declined']) ? 'bg-danger' : '' }}
                                                rounded-pill text-uppercase" style="font-size: 0.72rem;">
                                                {{ str_replace('_', ' ', $ord->order_status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('farmer.orders.show', $ord->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                                Manage
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Best-Selling Products & Quick Replenish Sidebar -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="heading-serif fw-bold text-dark mb-0">Best-Selling Products</h5>
                    <a href="{{ route('farmer.products.index') }}" class="small text-success text-decoration-none fw-semibold">Manage</a>
                </div>

                @if($bestSellers->isEmpty())
                    <p class="text-muted small mb-0 py-3 text-center bg-light rounded">Best seller rankings will appear as customers place reservations.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($bestSellers as $prod)
                            <li class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $prod->image_url ?: 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=100&q=80' }}" 
                                         alt="{{ $prod->name }}" 
                                         class="rounded" 
                                         style="width: 42px; height: 42px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold small text-dark">{{ $prod->name }}</div>
                                        <small class="text-muted">${{ number_format($prod->price, 2) }} / {{ $prod->unit }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-brand-light text-success border border-success border-opacity-25 rounded-pill">{{ $prod->order_items_count }} sold</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Weekly Stock Template Quick Action (SRS §1.6) -->
            <div class="card card-custom p-4 bg-light border border-success border-opacity-25 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="p-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Quick Stock Replenish</h6>
                </div>
                <p class="small text-muted mb-3">
                    Reset your harvest inventory to your predefined weekly template quantities with one click for market morning.
                </p>
                <form action="{{ route('farmer.products.replenish') }}" method="POST" onsubmit="return confirm('Apply weekly recurring stock template to all products?')">
                    @csrf
                    <button type="submit" class="btn btn-brand btn-sm w-100 rounded-pill shadow-sm">
                        Apply Weekly Template
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. GSAP Counter Animation for Stat Cards
    const statCounters = document.querySelectorAll('.stat-counter');
    statCounters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target')) || 0;
        const isCurrency = counter.getAttribute('data-is-currency') === 'true';

        gsap.to(counter, {
            innerText: target,
            duration: 1.2,
            ease: "power2.out",
            snap: { innerText: isCurrency ? 0.01 : 1 },
            onUpdate: function() {
                if (isCurrency) {
                    counter.innerText = '$' + parseFloat(counter.innerText).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                } else {
                    counter.innerText = Math.round(counter.innerText).toLocaleString('en-US');
                }
            }
        });
    });

    // 2. Bar Chart: 7-Day Revenue Trend
    const revenueLabels = {!! json_encode($revenueTrendLabels) !!};
    const revenueData = {!! json_encode($revenueTrendData) !!};

    const ctxRev = document.getElementById('farmerRevenueChart');
    if (ctxRev) {
        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Settled Sales ($)',
                    data: revenueData,
                    backgroundColor: '#1b4332',
                    hoverBackgroundColor: '#2d6a4f',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Revenue: $' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
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
    }

    // 3. Interactive Doughnut / Pie Chart: Pre-Order Pipeline Status Breakdown
    const orderStatusLabels = {!! json_encode($orderStatusLabels) !!};
    const orderStatusData = {!! json_encode($orderStatusData) !!};

    const ctxStatus = document.getElementById('farmerStatusChart');
    if (ctxStatus) {
        const centerDoughnutPlugin = {
            id: 'centerFarmerDoughnutText',
            beforeDraw: function(chart) {
                if (chart.config.type !== 'doughnut') return;
                const ctx = chart.ctx;
                const active = chart.getActiveElements();
                const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);

                let mainText = total.toString();
                let subText = 'PRE-ORDERS';

                if (active.length > 0) {
                    const idx = active[0].index;
                    const val = chart.data.datasets[0].data[idx];
                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                    mainText = val + ' (' + pct + '%)';
                    subText = chart.data.labels[idx].toUpperCase();
                }

                ctx.save();
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;

                ctx.font = 'bold 20px "Playfair Display", Georgia, serif';
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
                    data: orderStatusData,
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
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return ' ' + context.label + ': ' + context.raw + ' orders (' + pct + '%)';
                            }
                        }
                    }
                },
                cutout: '66%'
            }
        });

        // Interactive status pills click/hover to highlight chart segment
        document.querySelectorAll('#farmerChartLegend .status-chart-pill').forEach(pill => {
            const itemIdx = parseInt(pill.getAttribute('data-item-index'));
            pill.addEventListener('mouseenter', () => {
                pill.classList.remove('bg-light');
                pill.classList.add('bg-white', 'shadow-sm', 'border-primary');
                statusDoughnutChart.setActiveElements([{ datasetIndex: 0, index: itemIdx }]);
                statusDoughnutChart.tooltip.setActiveElements([{ datasetIndex: 0, index: itemIdx }]);
                statusDoughnutChart.update();
            });
            pill.addEventListener('mouseleave', () => {
                pill.classList.remove('bg-white', 'shadow-sm', 'border-primary');
                pill.classList.add('bg-light');
                statusDoughnutChart.setActiveElements([]);
                statusDoughnutChart.tooltip.setActiveElements([]);
                statusDoughnutChart.update();
            });
        });
    }
});
</script>
@endsection
