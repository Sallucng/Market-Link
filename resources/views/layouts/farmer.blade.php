<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Farmer Vendor Portal — MarketLink')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Leaflet CSS for OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Chart.js for Farmer Stall Analytics (SRS §1.6) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- GSAP for Smooth Motion Graphics & Antigravity Interactions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    <!-- Agentation Visual Feedback Toolbar -->
    @vite(['resources/js/agentation.jsx'])

    <!-- iOS 27 Liquid Glass Styling -->
    <link rel="stylesheet" href="{{ asset('css/liquid-glass.css') }}">

    <style>
        :root {
            --brand-primary: #1b4332;
            --brand-primary-hover: #133326;
            --brand-accent: #52b788;
            --sidebar-bg: #11261d;
            --sidebar-hover: #19382b;
            --sidebar-active: #234c3a;
            --canvas-bg: #f8f9fa;
            --surface-card: #ffffff;
            --border-hairline: #e9ecef;
            --border-card: #dee2e6;
            --text-dark: #191c1e;
            --text-muted: #66696d;
            --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-heading: 'Playfair Display', Georgia, serif;
            --font-mono: 'Geist Mono', 'SF Mono', Consolas, monospace;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-dark);
            background-color: var(--canvas-bg);
            min-height: 100vh;
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        .heading-serif {
            font-family: var(--font-heading);
            letter-spacing: -0.025em;
        }

        .font-mono-meta {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            letter-spacing: 0.02em;
        }

        /* Farmer Sidebar Styling (Left Panel) */
        .farmer-sidebar {
            width: 270px;
            background-color: var(--sidebar-bg);
            color: #e2e8f0;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1020;
            overflow-y: auto;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.07);
        }

        .farmer-sidebar-header {
            padding: 1.35rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .farmer-sidebar-nav {
            padding: 1rem 0.85rem;
            flex-grow: 1;
        }

        .farmer-nav-section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #7d9888;
            padding: 0.75rem 0.75rem 0.35rem;
        }

        .farmer-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.85rem;
            border-radius: 8px;
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            margin-bottom: 2px;
        }

        .farmer-nav-link i {
            font-size: 1.1rem;
            color: #94a3b8;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), color 0.15s ease;
        }

        .farmer-nav-link:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .farmer-nav-link:hover i {
            color: #38bdf8;
            transform: scale(1.15) rotate(-3deg);
        }

        .farmer-nav-link.active {
            color: #ffffff;
            background-color: var(--sidebar-active);
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .farmer-nav-link.active i {
            color: #52b788;
        }

        .farmer-sidebar-footer {
            padding: 1rem 1.15rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background-color: rgba(0, 0, 0, 0.15);
        }

        /* Main Content Layout */
        .farmer-main-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        .farmer-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-hairline);
            padding: 0.85rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1010;
        }

        .card-custom {
            background-color: var(--surface-card);
            border: 1px solid var(--border-hairline);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Antigravity Motion Graphics & Interactions */
        .pulse-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 2.2s infinite cubic-bezier(0.4, 0, 0.6, 1);
            vertical-align: middle;
        }
        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .tilt-card {
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
            will-change: transform;
        }
        .tilt-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px -6px rgba(17, 38, 29, 0.08), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
            border-color: #d1d5db !important;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-primary), #52b788);
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .stat-card:hover::after {
            opacity: 1;
        }

        .btn-brand {
            background-color: var(--brand-primary);
            color: #ffffff;
            border: 1px solid var(--brand-primary);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.52rem 1.25rem;
            transition: all 0.18s ease;
        }
        .btn-brand:hover, .btn-brand:focus {
            background-color: var(--brand-primary-hover);
            border-color: var(--brand-primary-hover);
            color: #ffffff;
        }

        .btn-brand-outline {
            border: 1px solid var(--brand-primary);
            color: var(--brand-primary);
            background: transparent;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.52rem 1.25rem;
            transition: all 0.18s ease;
        }
        .btn-brand-outline:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
        }

        @media (max-width: 991.98px) {
            .farmer-sidebar {
                transform: translateX(-100%);
            }
            .farmer-sidebar.show {
                transform: translateX(0);
            }
            .farmer-main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Backdrop on Mobile -->
    <div class="offcanvas-backdrop fade d-none" id="sidebarBackdrop"></div>

    <!-- Farmer Vertical Left Sidebar (Responsive) -->
    <aside class="farmer-sidebar" id="farmerSidebar">
        <!-- Sidebar Brand Header -->
        <div class="farmer-sidebar-header">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ route('farmer.dashboard') }}" class="d-flex align-items-center text-white text-decoration-none">
                    <span class="p-2 bg-success text-white rounded-2 me-2 d-inline-flex align-items-center justify-content-center" style="width:34px; height:34px;">
                        <i class="bi bi-flower1"></i>
                    </span>
                    <div>
                        <div class="heading-serif fw-bold text-white fs-5" style="letter-spacing: -0.02em;">MarketLink</div>
                        <div class="text-uppercase font-mono-meta fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.08em; color: #52b788;">Farmer Portal</div>
                    </div>
                </a>
                <button class="btn btn-link text-white-50 p-0 d-lg-none" id="sidebarCloseBtn">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <!-- "See as Normal User" Switcher Button -->
            <div class="mt-3">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center gap-2 py-2" style="border-color: rgba(255,255,255,0.22); font-size: 0.82rem; font-weight: 500; border-radius: 8px;">
                    <i class="bi bi-box-arrow-up-right text-success"></i>
                    <span>See as Normal User</span>
                </a>
            </div>
        </div>

        <!-- Sidebar Navigation Sections -->
        <div class="farmer-sidebar-nav">
            <!-- SECTION 1: OVERVIEW -->
            <div class="farmer-nav-section-title">Overview</div>
            <a href="{{ route('farmer.dashboard') }}" class="farmer-nav-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <!-- SECTION 2: PRODUCE & INVENTORY -->
            <div class="farmer-nav-section-title mt-3">Produce & Inventory</div>
            <a href="{{ route('farmer.products.index') }}" class="farmer-nav-link {{ request()->routeIs('farmer.products.index') || request()->routeIs('farmer.products.edit') ? 'active' : '' }}">
                <i class="bi bi-basket2"></i>
                <span>Produce Stock</span>
            </a>
            <a href="{{ route('farmer.products.create') }}" class="farmer-nav-link {{ request()->routeIs('farmer.products.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add Produce</span>
            </a>

            <!-- SECTION 3: FULFILLMENT & ORDERS -->
            <div class="farmer-nav-section-title mt-3">Customer Orders</div>
            <a href="{{ route('farmer.orders.index') }}" class="farmer-nav-link {{ request()->routeIs('farmer.orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Pre-Orders Queue</span>
            </a>
            <a href="{{ route('farmer.reviews.index') }}" class="farmer-nav-link {{ request()->routeIs('farmer.reviews.*') ? 'active' : '' }}">
                <i class="bi bi-chat-square-quote"></i>
                <span>Customer Reviews</span>
            </a>

            <!-- SECTION 4: STALL PROFILE -->
            <div class="farmer-nav-section-title mt-3">Stall Settings</div>
            <a href="{{ route('farmer.profile') }}" class="farmer-nav-link {{ request()->routeIs('farmer.profile') ? 'active' : '' }}">
                <i class="bi bi-shop"></i>
                <span>Stall Profile & Schedule</span>
            </a>
        </div>

        <!-- Sidebar Footer / Farmer Session -->
        <div class="farmer-sidebar-footer">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="text-white small fw-bold text-truncate" style="max-width: 140px;">
                            {{ Auth::user()->farmer->stall_name ?? Auth::user()->name }}
                        </div>
                        <div class="text-white-50 small" style="font-size: 0.72rem;">
                            @if(Auth::user()->is_approved)
                                <span class="text-success"><i class="bi bi-patch-check-fill me-1"></i>Verified Grower</span>
                            @else
                                <span class="text-warning"><i class="bi bi-hourglass-split me-1"></i>Awaiting Approval</span>
                            @endif
                        </div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-white-50 p-1" title="Sign Out">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
            <div class="text-white-50 font-mono-meta" style="font-size: 0.68rem;">SRS v1.0 Farmer Portal</div>
        </div>
    </aside>

    <!-- Main Wrapper (TopBar + Content Area) -->
    <div class="farmer-main-wrapper">
        <!-- Top Bar -->
        <header class="farmer-topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" id="sidebarToggleBtn" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1 small d-inline-flex align-items-center">
                        <span class="pulse-dot me-1"></span> Stall Live
                    </span>
                    <span class="text-muted small ms-2 d-none d-md-inline">
                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ Auth::user()->farmer->market->name ?? 'Downtown Farmers Plaza' }}
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Topbar "See as Normal User" Link -->
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3 py-1 rounded-pill">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span class="small fw-semibold">See as Normal User</span>
                </a>

                <div class="dropdown">
                    <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle text-success"></i>
                        <span class="small fw-semibold">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">Verified Farmer Account</h6></li>
                        <li><a class="dropdown-item" href="{{ route('farmer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('farmer.products.index') }}"><i class="bi bi-basket2 me-2"></i>Produce Inventory</a></li>
                        <li><a class="dropdown-item" href="{{ route('farmer.orders.index') }}"><i class="bi bi-receipt me-2"></i>Pre-Orders</a></li>
                        <li><a class="dropdown-item" href="{{ route('farmer.profile') }}"><i class="bi bi-shop me-2"></i>Stall Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Global Flash Alerts -->
        <div class="container-fluid px-4 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        <main class="flex-grow-1 px-4 py-3">
            @yield('content')
        </main>

        <!-- Farmer Footer -->
        <footer class="bg-white border-top py-3 px-4 text-muted small mt-auto">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>&copy; 2026 MarketLink Farmer Vendor Backoffice — Strictly following SRS v1.0.</div>
                <div>TechWiz 7 End-to-End Web Solutions</div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS for OpenStreetMap -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('farmerSidebar');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const closeBtn = document.getElementById('sidebarCloseBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    if (backdrop) {
                        backdrop.classList.toggle('d-none');
                        backdrop.classList.toggle('show');
                    }
                });
            }

            if (closeBtn && sidebar) {
                closeBtn.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    if (backdrop) {
                        backdrop.classList.add('d-none');
                        backdrop.classList.remove('show');
                    }
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    backdrop.classList.add('d-none');
                    backdrop.classList.remove('show');
                });
            }
        });
    </script>

    <!-- iOS 27 Liquid Glass Optical Refraction Engine -->
    <script src="{{ asset('js/liquid-glass.js') }}"></script>

    @yield('scripts')
</body>
</html>
