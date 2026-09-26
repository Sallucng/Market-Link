@extends('layouts.admin')

@section('title', 'Platform Reports & Analytics — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reports & Analytics</li>
                </ol>
            </nav>
            <h2 class="heading-serif fw-bold text-dark mb-0">Platform Reports & Analytics</h2>
            <small class="text-muted">Multi-market sales volume, order conversion pipelines, and grower performance (SRS §1.6)</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.reports.export') }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Generate CSV Report
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4 align-items-stretch">
        <div class="col">
            <div class="card card-custom stat-card tilt-card p-4 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase font-mono-meta">Total Pre-Orders Placed</div>
                        <h2 class="display-6 fw-bold text-dark mb-0 mt-1 stat-counter" data-target="{{ $totalOrders }}">0</h2>
                        <small class="text-muted">Platform reservations</small>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle">
                        <i class="bi bi-receipt-cutoff fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-4 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase font-mono-meta">Completed Pickups</div>
                        <h2 class="display-6 fw-bold text-primary mb-0 mt-1 stat-counter" data-target="{{ $completedOrders }}">0</h2>
                        <small class="text-primary fw-medium">Settled at stall</small>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="bi bi-bag-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-custom stat-card tilt-card p-4 bg-white border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase font-mono-meta">In-Person Sales Volume</div>
                        <h2 class="display-6 fw-bold text-success mb-0 mt-1 stat-counter" data-target="{{ $totalRevenue }}" data-is-currency="true">$0.00</h2>
                        <small class="text-success fw-medium">Direct farmer earnings</small>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Analytics: Interactive Charts (SRS §1.6) -->
    <div class="row g-4 mb-4">
        <!-- Bar Chart: Revenue by Market -->
        <div class="col-lg-7">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-0">
                            <i class="bi bi-bar-chart-fill text-success me-2"></i>Market Plaza Revenue Comparison
                        </h5>
                        <small class="text-muted">Settled pay-at-pickup volume per location (SRS §1.6)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">USD ($)</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="reportMarketRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Doughnut / Pie Chart: Order Status Breakdown -->
        <div class="col-lg-5">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-0">
                            <i class="bi bi-pie-chart-fill text-primary me-2"></i>Order Status Distribution
                        </h5>
                        <small class="text-muted">Lifecycle fulfillment rates (SRS §1.5)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">{{ $totalOrders }} Orders</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="reportOrderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Pie Chart: Category Popularity -->
        <div class="col-lg-5">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-0">
                            <i class="bi bi-tags-fill text-warning me-2"></i>Product Category Inventory
                        </h5>
                        <small class="text-muted">Active product listings across categories (SRS §1.6)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">{{ array_sum($categoryProductCounts) }} Listings</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="reportCategoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Horizontal Bar Chart: Top Farmer Sales -->
        <div class="col-lg-7">
            <div class="card card-custom chart-card tilt-card p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="heading-serif fw-bold text-dark mb-0">
                            <i class="bi bi-shop text-success me-2"></i>Top Farmer Stalls by Sales Volume
                        </h5>
                        <small class="text-muted">Direct grower settlement volume (SRS §1.6)</small>
                    </div>
                    <span class="badge bg-light text-dark border font-mono-meta">Sales ($)</span>
                </div>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="reportTopFarmersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="row g-4 mb-4">
        <!-- Market Performance Table (SRS §1.6) -->
        <div class="col-lg-8">
            <div class="card card-custom table-card p-4 bg-white border-0 shadow-sm h-100">
                <h5 class="heading-serif fw-bold text-dark mb-3">Market Venue Performance Matrix</h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Market Plaza</th>
                                <th>City</th>
                                <th class="text-center">Attending Stalls</th>
                                <th class="text-center">Total Orders</th>
                                <th class="text-end">Settled Volume</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marketStats as $m)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $m->name }}</div>
                                        <small class="text-muted">{{ $m->address }}</small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $m->city }}</span></td>
                                    <td class="text-center fw-semibold">{{ $m->farmers_count }}</td>
                                    <td class="text-center">{{ $m->orders_count }}</td>
                                    <td class="text-end fw-bold text-success fs-6">${{ number_format($m->revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Status Pipeline Breakdown -->
        <div class="col-lg-4">
            <div class="card card-custom table-card p-4 bg-white border-0 shadow-sm h-100">
                <h5 class="heading-serif fw-bold text-dark mb-3">Pipeline Status Ledger</h5>
                <ul class="list-group list-group-flush">
                    @php
                        $statusLabels = [
                            'placed' => ['name' => 'Newly Placed', 'class' => 'bg-warning text-dark'],
                            'accepted' => ['name' => 'Accepted by Farmer', 'class' => 'bg-primary'],
                            'ready_for_pickup' => ['name' => 'Packed & Ready', 'class' => 'bg-info text-dark'],
                            'completed' => ['name' => 'Completed & Settled', 'class' => 'bg-success'],
                            'cancelled' => ['name' => 'Customer Cancelled', 'class' => 'bg-secondary'],
                            'declined' => ['name' => 'Farmer Declined', 'class' => 'bg-danger'],
                        ];
                    @endphp
                    @foreach($statusLabels as $key => $meta)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="small fw-semibold text-dark">{{ $meta['name'] }}</span>
                            <span class="badge {{ $meta['class'] }} rounded-pill">{{ $statusBreakdown[$key] ?? 0 }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Most Active Farmers (SRS §1.6) -->
    <div class="card card-custom table-card p-4 bg-white border-0 shadow-sm">
        <h5 class="heading-serif fw-bold text-dark mb-3">Top Performing Active Growers</h5>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Farmer Stall</th>
                        <th>Contact Grower</th>
                        <th>Home Market</th>
                        <th class="text-center">Total Orders</th>
                        <th class="text-end">Completed Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topFarmers as $tf)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $tf->stall_name }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $tf->contact_person }}</div>
                                <small class="text-muted">{{ $tf->contact_number }}</small>
                            </td>
                            <td><span class="small text-muted">{{ $tf->market->name ?? 'Unassigned' }}</span></td>
                            <td class="text-center fw-bold">{{ $tf->orders_count }}</td>
                            <td class="text-end fw-bold text-success fs-6">${{ number_format($tf->total_sales, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
            stagger: 0.1,
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

    // 1. Report Market Revenue Bar Chart (Animated Rising with Counting Numbers)
    const marketNames = {!! json_encode($marketNames) !!};
    const targetMarketRevenues = {!! json_encode($marketRevenues) !!};
    const maxMarketRev = Math.max(...targetMarketRevenues, 100);

    const ctxRev = document.getElementById('reportMarketRevenueChart');
    if (ctxRev) {
        const animatedRevenueLabelsPlugin = {
            id: 'animatedReportRevenueLabels',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.font = '700 11px "Poppins", sans-serif';
                ctx.fillStyle = '#1b4332';

                chart.getDatasetMeta(0).data.forEach((bar, index) => {
                    const currentVal = data.datasets[0].data[index];
                    const targetVal = targetMarketRevenues[index];
                    if (targetVal > 0) {
                        ctx.fillText('$' + currentVal.toFixed(2), bar.x, bar.y - 4);
                    }
                });
                ctx.restore();
            }
        };

        const reportRevChart = new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: marketNames,
                datasets: [{
                    label: 'Settled Sales ($)',
                    data: targetMarketRevenues.map(() => 0),
                    backgroundColor: '#1b4332',
                    hoverBackgroundColor: '#2d6a4f',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 45
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
                            label: function(context) { return '$' + context.raw.toFixed(2); }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: maxMarketRev * 1.18,
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
                reportRevChart.data.datasets[0].data = targetMarketRevenues.map(v => v * revAnim.progress);
                reportRevChart.update('none');
            },
            onComplete: () => {
                reportRevChart.data.datasets[0].data = [...targetMarketRevenues];
                reportRevChart.update('none');
            }
        });
    }

    // 2. Report Order Status Doughnut / Pie Chart (Animated Sweep)
    const statusData = {
        'Placed': {{ $statusBreakdown['placed'] ?? 0 }},
        'Accepted': {{ $statusBreakdown['accepted'] ?? 0 }},
        'Ready': {{ $statusBreakdown['ready_for_pickup'] ?? 0 }},
        'Completed': {{ $statusBreakdown['completed'] ?? 0 }},
        'Cancelled': {{ $statusBreakdown['cancelled'] ?? 0 }}
    };
    const targetStatusVals = Object.values(statusData);

    const ctxStatus = document.getElementById('reportOrderStatusChart');
    if (ctxStatus) {
        const reportStatusChart = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: targetStatusVals.map(() => 0),
                    backgroundColor: ['#eab308', '#3b82f6', '#8b5cf6', '#10b981', '#94a3b8'],
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
                        labels: { boxWidth: 12, padding: 12 }
                    }
                },
                cutout: '62%'
            }
        });

        const statusAnim = { progress: 0 };
        gsap.to(statusAnim, {
            progress: 1,
            duration: 1.6,
            delay: 0.35,
            ease: 'power2.out',
            onUpdate: () => {
                reportStatusChart.options.circumference = 360 * statusAnim.progress;
                reportStatusChart.data.datasets[0].data = targetStatusVals.map(v => v * statusAnim.progress);
                reportStatusChart.update('none');
            },
            onComplete: () => {
                reportStatusChart.options.circumference = 360;
                reportStatusChart.data.datasets[0].data = [...targetStatusVals];
                reportStatusChart.update('none');
            }
        });
    }

    // 3. Report Product Category Distribution Pie Chart (Animated Sweep)
    const catNames = {!! json_encode($categoryNames) !!};
    const targetCatCounts = {!! json_encode($categoryProductCounts) !!};

    const ctxCat = document.getElementById('reportCategoryChart');
    if (ctxCat) {
        const reportCatChart = new Chart(ctxCat, {
            type: 'pie',
            data: {
                labels: catNames,
                datasets: [{
                    data: targetCatCounts.map(() => 0),
                    backgroundColor: ['#2d6a4f', '#52b788', '#d4a373', '#e76f51', '#74c69d', '#b7b7a4'],
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
                        labels: { boxWidth: 12, padding: 12 }
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
                reportCatChart.options.circumference = 360 * catAnim.progress;
                reportCatChart.data.datasets[0].data = targetCatCounts.map(v => v * catAnim.progress);
                reportCatChart.update('none');
            },
            onComplete: () => {
                reportCatChart.options.circumference = 360;
                reportCatChart.data.datasets[0].data = [...targetCatCounts];
                reportCatChart.update('none');
            }
        });
    }

    // 4. Report Top Farmer Sales Horizontal Bar Chart (Animated Extending from Left to Right)
    const farmerNames = {!! json_encode($topFarmers->pluck('stall_name')->toArray()) !!};
    const targetFarmerSales = {!! json_encode($topFarmers->pluck('total_sales')->toArray()) !!};
    const maxFarmerSales = Math.max(...targetFarmerSales, 100);

    const ctxFarmers = document.getElementById('reportTopFarmersChart');
    if (ctxFarmers) {
        const animatedFarmerLabelsPlugin = {
            id: 'animatedFarmerSalesLabels',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                ctx.save();
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                ctx.font = '700 11px "Poppins", sans-serif';
                ctx.fillStyle = '#1b4332';

                chart.getDatasetMeta(0).data.forEach((bar, index) => {
                    const currentVal = data.datasets[0].data[index];
                    const targetVal = targetFarmerSales[index];
                    if (targetVal > 0) {
                        ctx.fillText('$' + currentVal.toFixed(2), bar.x + 6, bar.y);
                    }
                });
                ctx.restore();
            }
        };

        const reportFarmersChart = new Chart(ctxFarmers, {
            type: 'bar',
            data: {
                labels: farmerNames,
                datasets: [{
                    axis: 'y',
                    label: 'Completed Sales ($)',
                    data: targetFarmerSales.map(() => 0),
                    backgroundColor: '#d4a373',
                    hoverBackgroundColor: '#c48b52',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 32
                }]
            },
            plugins: [animatedFarmerLabelsPlugin],
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) { return 'Sales: $' + context.raw.toFixed(2); }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        suggestedMax: maxFarmerSales * 1.25,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return '$' + value; }
                        }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });

        const farmersAnim = { progress: 0 };
        gsap.to(farmersAnim, {
            progress: 1,
            duration: 1.6,
            delay: 0.35,
            ease: 'power2.out',
            onUpdate: () => {
                reportFarmersChart.data.datasets[0].data = targetFarmerSales.map(v => v * farmersAnim.progress);
                reportFarmersChart.update('none');
            },
            onComplete: () => {
                reportFarmersChart.data.datasets[0].data = [...targetFarmerSales];
                reportFarmersChart.update('none');
            }
        });
    }
});
</script>
@endsection
