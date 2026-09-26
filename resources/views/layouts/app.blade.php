<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MarketLink') — Farm Fresh Just a Click Away</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS for OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <!-- GSAP for Smooth Motion Graphics & Antigravity Interactions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    @vite(['resources/js/agentation.jsx'])

    <style>
        :root {
            --brand-primary: #1b4332;
            --brand-primary-hover: #133326;
            --brand-accent: #c48b52;
            --canvas-bg: #fbfbfa;
            --surface-card: #ffffff;
            --surface-subtle: #f7f6f2;
            --border-hairline: #eae8e2;
            --border-card: #e2dfd7;
            --text-dark: #191c1e;
            --text-muted: #66696d;
            --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-heading: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'Geist Mono', 'SF Mono', Consolas, monospace;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-dark);
            background-color: var(--canvas-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
        }

        /* Headings Typography - Poppins applied across all headings */
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6,
        .heading-serif,
        .display-1, .display-2, .display-3, .display-4, .display-5, .display-6,
        .navbar-brand {
            font-family: var(--font-heading) !important;
        }

        h1, .h1, .display-1, .display-2, .display-3, .display-4 {
            letter-spacing: -0.025em;
            font-weight: 700;
        }

        h2, .h2, .display-5, .display-6 {
            letter-spacing: -0.02em;
            font-weight: 600;
        }

        h3, .h3, h4, .h4 {
            letter-spacing: -0.015em;
            font-weight: 600;
        }

        h5, .h5, h6, .h6 {
            letter-spacing: -0.01em;
            font-weight: 600;
        }

        .heading-serif {
            font-family: var(--font-heading) !important;
            letter-spacing: -0.02em;
        }

        .font-mono-meta {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            letter-spacing: 0.02em;
        }

        .navbar-brand {
            font-family: var(--font-heading) !important;
            font-weight: 700;
            color: var(--brand-primary) !important;
            font-size: 1.45rem;
            letter-spacing: -0.025em;
        }

        /* Universal Button Color Shifts on Hover */
        .btn {
            transition: background-color 0.22s cubic-bezier(0.16, 1, 0.3, 1), 
                        color 0.22s cubic-bezier(0.16, 1, 0.3, 1), 
                        border-color 0.22s cubic-bezier(0.16, 1, 0.3, 1), 
                        box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1), 
                        transform 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .btn-brand {
            background-color: var(--brand-primary);
            color: #ffffff;
            border: 1px solid var(--brand-primary);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.92rem;
            letter-spacing: 0.01em;
            padding: 0.52rem 1.25rem;
        }
        .btn-brand:hover, .btn-brand:focus {
            background-color: #2d6a4f !important;
            border-color: #2d6a4f !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(45, 106, 79, 0.28) !important;
        }
        .btn-brand:active {
            transform: scale(0.98);
        }

        .btn-brand-outline {
            border: 1.5px solid var(--brand-primary);
            color: var(--brand-primary);
            background-color: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.52rem 1.15rem;
        }
        .btn-brand-outline:hover, .btn-brand-outline:focus {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(27, 67, 50, 0.24) !important;
        }
        .btn-brand-outline:active {
            transform: scale(0.98);
        }

        .btn-success:hover, .btn-success:focus {
            background-color: #14532d !important;
            border-color: #14532d !important;
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(20, 83, 45, 0.3) !important;
            transform: translateY(-2px);
        }

        .btn-outline-success:hover, .btn-outline-success:focus {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(25, 135, 84, 0.25) !important;
            transform: translateY(-2px);
        }

        .btn-light:hover, .btn-light:focus {
            background-color: #0f281e !important;
            border-color: #0f281e !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 40, 30, 0.2) !important;
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        .btn-outline-secondary:hover, .btn-outline-secondary:focus {
            background-color: #334155 !important;
            border-color: #334155 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        .footer-link {
            color: #a3b8ad;
            text-decoration: none;
            transition: color 0.18s ease, transform 0.18s ease;
            display: inline-block;
        }
        .footer-link:hover {
            color: #ffffff;
            transform: translateX(3px);
        }

        .card-custom {
            background-color: var(--surface-card);
            border: 1px solid var(--border-hairline);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
            will-change: transform;
        }
        .card-custom:hover {
            border-color: #d6d3c9;
            box-shadow: 0 14px 28px -6px rgba(27, 67, 50, 0.09), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
            transform: translateY(-4px);
        }

        /* Antigravity Motion Graphics & Floating Physics */
        @keyframes float-gentle {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .motion-float {
            animation: float-gentle 4.5s ease-in-out infinite;
            will-change: transform;
        }

        .motion-float-delayed {
            animation: float-gentle 5s ease-in-out 1.5s infinite;
            will-change: transform;
        }

        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* Muted Pastel Badges (minimalist-ui) */
        .badge-pastel-green {
            background-color: #edf3ec;
            color: #2b5932;
            font-weight: 600;
            font-size: 0.74rem;
            letter-spacing: 0.02em;
            border-radius: 6px;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
        }

        .badge-pastel-amber {
            background-color: #fbf3db;
            color: #8a5c00;
            font-weight: 600;
            font-size: 0.74rem;
            letter-spacing: 0.02em;
            border-radius: 6px;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
        }

        .badge-pastel-red {
            background-color: #fdebec;
            color: #9e2a2b;
            font-weight: 600;
            font-size: 0.74rem;
            letter-spacing: 0.02em;
            border-radius: 6px;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
        }

        .badge-pastel-slate {
            background-color: #f1f0ec;
            color: #424340;
            font-weight: 600;
            font-size: 0.74rem;
            letter-spacing: 0.02em;
            border-radius: 6px;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
        }

        .bento-card {
            background: #ffffff;
            border: 1px solid var(--border-hairline);
            border-radius: 12px;
            padding: 24px;
            height: 100%;
            transition: all 0.2s ease;
        }
        .bento-card:hover {
            border-color: #cfcbbe;
            box-shadow: 0 6px 18px -4px rgba(0, 0, 0, 0.04);
        }

        /* AI Floating Assistant */
        #ai-assistant-bubble {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1050;
        }
        #ai-assistant-window {
            position: fixed;
            bottom: 90px;
            right: 24px;
            width: 360px;
            height: 480px;
            z-index: 1050;
            display: none;
            box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.14);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
        }

        /* ==========================================================================
           Pure Apple iOS Liquid Glass Dropdowns (iOS 26 / visionOS Liquid Materials)
           ========================================================================== */
        .dropdown-menu,
        .liquid-glass-menu {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.48) 0%, rgba(255, 255, 255, 0.22) 100%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(220%) contrast(108%) !important;
            backdrop-filter: blur(24px) saturate(220%) contrast(108%) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            border-radius: 20px !important;
            padding: 8px !important;
            min-width: 250px !important;
            box-shadow: 
                0 20px 48px -10px rgba(15, 23, 42, 0.16),
                0 8px 18px -4px rgba(0, 0, 0, 0.05),
                inset 0 1.5px 1px 0 rgba(255, 255, 255, 0.95),
                inset 0 -1px 1px 0 rgba(255, 255, 255, 0.2),
                inset 1px 0 1px 0 rgba(255, 255, 255, 0.6),
                inset -1px 0 1px 0 rgba(255, 255, 255, 0.3) !important;
            overflow: hidden;
            margin-top: 10px !important;
            transform-origin: top right;
        }

        /* Fluid Spring animation on open */
        .dropdown-menu.show,
        .liquid-glass-menu.show {
            animation: ios-liquid-spring 0.28s cubic-bezier(0.175, 0.885, 0.32, 1.15) forwards;
        }

        @keyframes ios-liquid-spring {
            0% {
                opacity: 0;
                transform: translateY(-8px) scale(0.96);
                filter: blur(4px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        /* Liquid Glass Dropdown Header */
        .dropdown-menu .dropdown-header,
        .liquid-glass-menu .dropdown-header {
            font-size: 0.68rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
            color: var(--brand-primary) !important;
            padding: 8px 12px 4px !important;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        /* Liquid Glass Dropdown Item */
        .dropdown-menu .dropdown-item,
        .liquid-glass-menu .dropdown-item {
            border-radius: 12px !important;
            padding: 9px 14px !important;
            font-size: 0.88rem !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            text-shadow: 0 0.5px 0 rgba(255, 255, 255, 0.5);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: flex !important;
            align-items: center !important;
        }

        .dropdown-menu .dropdown-item:hover,
        .dropdown-menu .dropdown-item:focus,
        .liquid-glass-menu .dropdown-item:hover,
        .liquid-glass-menu .dropdown-item:focus {
            background: rgba(255, 255, 255, 0.78) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            backdrop-filter: blur(8px) !important;
            color: var(--brand-primary) !important;
            transform: translateX(4px) !important;
            box-shadow: 
                0 4px 12px -2px rgba(27, 67, 50, 0.10),
                inset 0 1px 0.5px rgba(255, 255, 255, 0.95) !important;
        }

        .dropdown-menu .dropdown-item.text-danger:hover,
        .dropdown-menu .dropdown-item.text-danger:focus,
        .liquid-glass-menu .dropdown-item.text-danger:hover,
        .liquid-glass-menu .dropdown-item.text-danger:focus {
            background: rgba(254, 242, 242, 0.88) !important;
            color: #dc2626 !important;
            box-shadow: 0 4px 12px -2px rgba(220, 38, 38, 0.12) !important;
        }

        /* Liquid Glass Divider */
        .dropdown-menu .dropdown-divider,
        .liquid-glass-menu .dropdown-divider {
            border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.8) !important;
            margin: 6px 4px !important;
            opacity: 1 !important;
        }

        /* Liquid Glass Pill Toggle Button */
        .btn-liquid-glass {
            background: rgba(255, 255, 255, 0.55) !important;
            -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
            backdrop-filter: blur(16px) saturate(180%) !important;
            border: 1px solid rgba(255, 255, 255, 0.7) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-liquid-glass:hover,
        .btn-liquid-glass[aria-expanded="true"] {
            background: rgba(255, 255, 255, 0.82) !important;
            box-shadow: 0 4px 14px rgba(27, 67, 50, 0.12), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
            transform: translateY(-1px);
        }

        @media (prefers-reduced-motion: reduce) {
            .dropdown-menu.show,
            .liquid-glass-menu.show {
                animation: none !important;
            }
        }

        /* Prevent navbar text from wrapping into multi-line stacks */
        .navbar .navbar-brand,
        .navbar-nav .nav-link,
        .navbar .btn {
            white-space: nowrap !important;
        }

        /* Responsive adaptation for compact desktops/tablets (992px - 1200px) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .navbar-nav .nav-link {
                padding-left: 0.45rem !important;
                padding-right: 0.45rem !important;
                font-size: 0.9rem !important;
            }
            .navbar-brand {
                font-size: 1.25rem !important;
                margin-right: 0.5rem !important;
            }
            .navbar-user-name {
                max-width: 85px !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
                display: inline-block !important;
                vertical-align: middle !important;
            }
            .btn-brand-outline {
                padding-left: 0.65rem !important;
                padding-right: 0.65rem !important;
                font-size: 0.88rem !important;
            }
        }

        /* Mobile drawer button layout */
        @media (max-width: 991.98px) {
            .navbar-collapse .navbar-user-actions {
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100%;
                padding-top: 0.75rem;
                margin-top: 0.75rem;
                border-top: 1px solid var(--border-hairline);
            }
            .navbar-collapse .navbar-user-actions .btn,
            .navbar-collapse .navbar-user-actions .dropdown,
            .navbar-collapse .navbar-user-actions .dropdown button {
                width: 100% !important;
                justify-content: center !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Accessible Skip to Content Link (SRS §1.7 Accessibility) -->
    <a href="#main-content" class="visually-hidden-focusable btn btn-brand position-fixed top-0 start-0 m-3 shadow" style="z-index: 9999;">Skip to main content</a>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-2" style="border-bottom: 1px solid var(--border-hairline);">
        <div class="container-xl">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="p-2 text-white rounded-2 me-2 d-inline-flex align-items-center justify-content-center" style="width:34px; height:34px; background-color: var(--brand-primary);">
                    <i class="bi bi-flower2"></i>
                </span>
                MarketLink
            </a>

            <!-- Mobile Quick Actions (Search & Cart directly in mobile header) -->
            <div class="d-flex align-items-center gap-1 d-lg-none ms-auto me-2">
                <button type="button" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#globalSearchModal" aria-label="Search" style="width: 36px; height: 36px; background: #fbfdfa;">
                    <i class="bi bi-search text-success"></i>
                </button>
                <a href="{{ route('cart.index') }}" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center position-relative" style="width: 36px; height: 36px; background: #fbfdfa;" aria-label="Pickup Cart">
                    <i class="bi bi-cart3 text-success"></i>
                    @php $cartCount = count(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.62rem; padding: 0.25em 0.45em;">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('markets.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('markets.index') }}">Markets and Map</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('products.index') }}">Farm Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('farmers.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('farmers.index') }}">Farmers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('about') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 navbar-user-actions">
                    <!-- Pre-Order Cart Button -->
                    <a href="{{ route('cart.index') }}" class="btn btn-brand-outline position-relative me-2 px-3 py-1">
                        <i class="bi bi-cart3 me-1"></i> Pickup Cart
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger font-mono-meta">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-liquid-glass dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3" type="button" data-bs-target="#userMenu" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle text-success fs-5"></i>
                                <span class="fw-semibold navbar-user-name">{{ Auth::user()->name }}</span>
                                <span class="badge bg-secondary ms-1 small text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->role }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end liquid-glass-menu shadow-lg" id="userMenu">
                                @if(Auth::user()->isCustomer())
                                    <li><h6 class="dropdown-header">Customer Portal</h6></li>
                                    <li><a class="dropdown-item fw-semibold" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-success"></i>My Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}"><i class="bi bi-box-seam me-2"></i>My Pre-Orders</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customer.favorites.index') }}"><i class="bi bi-heart me-2"></i>Saved Favorites</a></li>
                                @elseif(Auth::user()->isFarmer())
                                    <li><h6 class="dropdown-header">Farmer Management</h6></li>
                                    <li><a class="dropdown-item text-success fw-bold" href="{{ route('farmer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Farmer Portal</a></li>
                                @elseif(Auth::user()->isAdmin())
                                    <li><h6 class="dropdown-header">Platform Backoffice</h6></li>
                                    <li><a class="dropdown-item text-primary fw-bold" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock me-2"></i>Admin Dashboard</a></li>
                                @endif
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
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light border px-3 rounded-pill fw-semibold">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-brand px-3 rounded-pill">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Flash Alerts -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Body Content -->
    <main id="main-content" role="main" class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="pt-5 pb-4 mt-5 text-light" style="background-color: #0b2116; border-top: 1px solid rgba(82, 183, 136, 0.2);">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="heading-serif text-white mb-3 d-flex align-items-center">
                        <span class="p-1 rounded-2 me-2 d-inline-flex align-items-center justify-content-center" style="width:30px; height:30px; background-color: #2d6a4f;">
                            <i class="bi bi-flower2 text-white fs-6"></i>
                        </span>
                        MarketLink
                    </h5>
                    <p class="small mb-3" style="color: #a3b8ad; line-height: 1.6;">
                        Farm Fresh Just a Click Away. Connecting neighborhood growers directly with local community shoppers for convenient, verified weekend stall pre-orders.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill" style="background-color: rgba(82, 183, 136, 0.18); color: #74c69d; border: 1px solid rgba(82, 183, 136, 0.3);">
                            <i class="bi bi-shield-check me-1"></i> Verified Local Stalls
                        </span>
                        <span class="badge rounded-pill" style="background-color: rgba(82, 183, 136, 0.18); color: #74c69d; border: 1px solid rgba(82, 183, 136, 0.3);">
                            <i class="bi bi-geo-alt me-1"></i> OpenStreetMap
                        </span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="text-uppercase small fw-bold mb-3" style="color: #d8f3dc; letter-spacing: 0.08em;">Explore</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}" class="footer-link">Home</a></li>
                        <li class="mb-2"><a href="{{ route('markets.index') }}" class="footer-link">Markets and Map</a></li>
                        <li class="mb-2"><a href="{{ route('products.index') }}" class="footer-link">Farm Products</a></li>
                        <li class="mb-2"><a href="{{ route('farmers.index') }}" class="footer-link">Local Farmers</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}" class="footer-link">About Us</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="footer-link">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase small fw-bold mb-3" style="color: #d8f3dc; letter-spacing: 0.08em;">Pickup and Settlement</h6>
                    <p class="small mb-2" style="color: #a3b8ad; line-height: 1.6;">
                        <i class="bi bi-wallet2 text-warning me-1"></i> <strong>Pay at Stall Pickup:</strong> Orders placed on MarketLink are settled in-person directly with the farmer at their market booth.
                    </p>
                    <p class="small" style="color: #a3b8ad;">
                        <i class="bi bi-clock-history text-success me-1"></i> Pre-order windows close ahead of market day to give growers harvest preparation time.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase small fw-bold mb-3" style="color: #d8f3dc; letter-spacing: 0.08em;">Quick Demo Logins</h6>
                    <div class="p-3 rounded-3 small" style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); color: #cbd5e1;">
                        <div class="mb-1"><span class="badge bg-secondary me-1">Admin</span> <code class="text-warning">admin</code> / <code class="text-light">Admin@123</code></div>
                        <div class="mb-1"><span class="badge bg-success me-1">Farmer</span> <code class="text-warning">greenvalley</code> / <code class="text-light">Farmer@123</code></div>
                        <div><span class="badge bg-info text-dark me-1">Customer</span> <code class="text-warning">sarah_shopper</code> / <code class="text-light">Customer@123</code></div>
                    </div>
                </div>
            </div>

            <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 2rem 0 1.5rem;">

            <div class="d-flex flex-wrap justify-content-between align-items-center small" style="color: #7d9b8b;">
                <div>&copy; 2026 MarketLink. Local Farmers Market and Harvest Pre-Order Platform. All rights reserved.</div>
                <div>Direct farmer-to-consumer neighborhood food network.</div>
            </div>
        </div>
    </footer>

    <!-- Floating AI Assistant Chatbot (SRS Section 1.6: Optional AI Assistant) -->
    <div id="ai-assistant-bubble">
        <button id="ai-toggle-btn" class="btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center p-3" style="width: 58px; height: 58px;" title="Chat with MarketLink AI Assistant">
            <i class="bi bi-robot fs-4"></i>
        </button>
    </div>

    <div id="ai-assistant-window" class="card shadow-lg border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-robot fs-5"></i>
                <div>
                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">MarketLink Assistant</h6>
                    <small class="text-white-50" style="font-size: 0.72rem;">Ask about markets, stalls and products</small>
                </div>
            </div>
            <button id="ai-close-btn" class="btn btn-sm btn-link text-white p-0 fs-5 text-decoration-none">&times;</button>
        </div>
        <div id="ai-messages" class="card-body p-3 overflow-auto" style="height: 360px; font-size: 0.88rem; background-color: #f8fafc;">
            <div class="d-flex mb-3">
                <div class="bg-white p-2 rounded-3 shadow-sm border" style="max-width: 85%;">
                    👋 Hello! I can help you find fresh items, check market schedules, and answer pickup questions. How can I help today?
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-top p-2">
            <form id="ai-chat-form" class="d-flex gap-2">
                <input type="text" id="ai-input" class="form-control form-control-sm" placeholder="Ask about timings, products..." autocomplete="off">
                <button type="submit" class="btn btn-sm btn-success px-3">Send</button>
            </form>
        </div>
    </div>

    <!-- Global Omnisearch Modal (Ctrl+K / Mobile Search) -->
    <div class="modal fade" id="globalSearchModal" tabindex="-1" aria-labelledby="globalSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
                <div class="modal-header border-bottom p-3" style="background: #fafbfa;">
                    <div class="input-group input-group-lg border-0 align-items-center">
                        <span class="input-group-text bg-transparent border-0 text-success pe-2">
                            <i class="bi bi-search fs-5"></i>
                        </span>
                        <input type="text" id="globalSearchInput" class="form-control bg-transparent border-0 shadow-none ps-0 fs-6" 
                               placeholder="Search fresh products, farmers, market stalls..." 
                               autocomplete="off">
                        <button type="button" id="clearSearchBtn" class="btn btn-link text-muted pe-2 text-decoration-none d-none" onclick="clearGlobalSearch()">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                        <span class="input-group-text bg-transparent border-0 text-muted small d-none d-md-flex align-items-center">
                            <kbd class="bg-light border text-muted px-2 py-0.5 rounded small font-mono-meta">ESC</kbd>
                        </span>
                    </div>
                </div>
                <div class="modal-body p-3 p-md-4" style="max-height: 480px; overflow-y: auto;" id="globalSearchResults">
                    <!-- Default Suggestions State -->
                    <div id="searchSuggestionsState">
                        <div class="mb-3">
                            <span class="text-muted small fw-semibold text-uppercase font-mono-meta" style="letter-spacing: 0.05em;">Popular Searches</span>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-mono-meta" onclick="fillAndSearch('Heirloom Tomatoes')">🍅 Heirloom Tomatoes</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-mono-meta" onclick="fillAndSearch('Organic Eggs')">🥚 Organic Eggs</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-mono-meta" onclick="fillAndSearch('Sourdough')">🥖 Sourdough Bread</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-mono-meta" onclick="fillAndSearch('Honey')">🍯 Raw Farm Honey</button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-mono-meta" onclick="fillAndSearch('Downtown')">📍 Downtown Plaza</button>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <span class="text-muted small fw-semibold text-uppercase font-mono-meta" style="letter-spacing: 0.05em;">Quick Jump</span>
                            <div class="row g-2 mt-1">
                                <div class="col-sm-4">
                                    <a href="{{ route('products.index') }}" class="d-flex align-items-center gap-2 p-2 rounded-3 text-dark text-decoration-none border bg-light bg-opacity-50 hover-lift">
                                        <i class="bi bi-basket text-success fs-5"></i>
                                        <div>
                                            <div class="fw-semibold small">All Products</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Full harvest catalog</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-4">
                                    <a href="{{ route('markets.index') }}" class="d-flex align-items-center gap-2 p-2 rounded-3 text-dark text-decoration-none border bg-light bg-opacity-50 hover-lift">
                                        <i class="bi bi-geo-alt text-success fs-5"></i>
                                        <div>
                                            <div class="fw-semibold small">Markets & Map</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Find open stalls</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-4">
                                    <a href="{{ route('farmers.index') }}" class="d-flex align-items-center gap-2 p-2 rounded-3 text-dark text-decoration-none border bg-light bg-opacity-50 hover-lift">
                                        <i class="bi bi-shop text-success fs-5"></i>
                                        <div>
                                            <div class="fw-semibold small">Growers & Farms</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Meet local farmers</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Dynamic Results State (Populated via JS) -->
                    <div id="searchDynamicResults" class="d-none"></div>

                    <!-- Loading State -->
                    <div id="searchLoadingState" class="text-center py-4 d-none">
                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        <span class="ms-2 text-muted small">Searching catalog...</span>
                    </div>

                    <!-- Empty State -->
                    <div id="searchEmptyState" class="text-center py-4 d-none">
                        <i class="bi bi-emoji-neutral text-muted fs-3 mb-2"></i>
                        <p class="text-muted mb-0 small">No direct matches found. Try searching by generic term like "vegetables" or "apple".</p>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                    <span class="text-muted small" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i> Tip: Press <kbd class="bg-white border text-muted px-1 rounded">↵ Enter</kbd> to view full catalog results
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Global Omnisearch Script -->
    <script>
        let searchDebounceTimer = null;
        const searchInput = document.getElementById('globalSearchInput');
        const suggestionsState = document.getElementById('searchSuggestionsState');
        const dynamicResults = document.getElementById('searchDynamicResults');
        const loadingState = document.getElementById('searchLoadingState');
        const emptyState = document.getElementById('searchEmptyState');
        const clearBtn = document.getElementById('clearSearchBtn');

        function fillAndSearch(term) {
            if (!searchInput) return;
            searchInput.value = term;
            triggerLiveSearch(term);
        }

        function clearGlobalSearch() {
            if (!searchInput) return;
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            suggestionsState.classList.remove('d-none');
            dynamicResults.classList.add('d-none');
            dynamicResults.innerHTML = '';
            emptyState.classList.add('d-none');
            searchInput.focus();
        }

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const val = e.target.value.trim();
                if (val.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }

                clearTimeout(searchDebounceTimer);
                if (val.length < 2) {
                    suggestionsState.classList.remove('d-none');
                    dynamicResults.classList.add('d-none');
                    loadingState.classList.add('d-none');
                    emptyState.classList.add('d-none');
                    return;
                }

                searchDebounceTimer = setTimeout(() => {
                    triggerLiveSearch(val);
                }, 200);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    const val = searchInput.value.trim();
                    if (val) {
                        window.location.href = "{{ route('products.index') }}?q=" + encodeURIComponent(val);
                    }
                }
            });
        }

        // Global Ctrl+K / Cmd+K listener
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const searchModalEl = document.getElementById('globalSearchModal');
                if (searchModalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(searchModalEl);
                    modal.show();
                }
            }
        });

        const globalSearchModalEl = document.getElementById('globalSearchModal');
        if (globalSearchModalEl) {
            globalSearchModalEl.addEventListener('shown.bs.modal', () => {
                if (searchInput) searchInput.focus();
            });
        }

        async function triggerLiveSearch(q) {
            suggestionsState.classList.add('d-none');
            emptyState.classList.add('d-none');
            dynamicResults.classList.add('d-none');
            loadingState.classList.remove('d-none');

            try {
                const res = await fetch("{{ route('api.search.live') }}?q=" + encodeURIComponent(q));
                const data = await res.json();
                loadingState.classList.add('d-none');

                if (data.total === 0) {
                    emptyState.classList.remove('d-none');
                    return;
                }

                let html = '';

                // Products Section
                if (data.products && data.products.length > 0) {
                    html += `<div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-semibold text-uppercase font-mono-meta">Farm Products (${data.products.length})</span>
                            <a href="{{ route('products.index') }}?q=${encodeURIComponent(q)}" class="small text-success text-decoration-none fw-semibold">View all matching products &rarr;</a>
                        </div>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">`;
                    data.products.forEach(p => {
                        html += `
                            <a href="${p.url}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-2 border-bottom">
                                <img src="${p.image}" alt="${escapeHtml(p.name)}" class="rounded-2" style="width: 44px; height: 44px; object-fit: cover;">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark text-truncate">${escapeHtml(p.name)}</span>
                                        <span class="fw-bold text-success font-mono-meta ms-2">${p.price}</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.78rem;">
                                        <span class="badge bg-light text-secondary border me-1">${escapeHtml(p.category)}</span>
                                        <span>${escapeHtml(p.farmer)}</span>
                                    </div>
                                </div>
                            </a>`;
                    });
                    html += `</div></div>`;
                }

                // Farmers Section
                if (data.farmers && data.farmers.length > 0) {
                    html += `<div class="mb-3">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta d-block mb-2">Verified Farmers (${data.farmers.length})</span>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">`;
                    data.farmers.forEach(f => {
                        html += `
                            <a href="${f.url}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2.5">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shop text-success fs-5"></i>
                                    <div>
                                        <div class="fw-bold text-dark small">${escapeHtml(f.name)}</div>
                                        <div class="text-muted" style="font-size: 0.76rem;">${escapeHtml(f.contact_person)}</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted small"></i>
                            </a>`;
                    });
                    html += `</div></div>`;
                }

                // Markets Section
                if (data.markets && data.markets.length > 0) {
                    html += `<div class="mb-2">
                        <span class="text-muted small fw-semibold text-uppercase font-mono-meta d-block mb-2">Farmers Markets (${data.markets.length})</span>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">`;
                    data.markets.forEach(m => {
                        html += `
                            <a href="${m.url}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2.5">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt text-success fs-5"></i>
                                    <div>
                                        <div class="fw-bold text-dark small">${escapeHtml(m.name)}</div>
                                        <div class="text-muted" style="font-size: 0.76rem;">${escapeHtml(m.city)} &bull; ${escapeHtml(m.days)}</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted small"></i>
                            </a>`;
                    });
                    html += `</div></div>`;
                }

                dynamicResults.innerHTML = html;
                dynamicResults.classList.remove('d-none');
            } catch (e) {
                loadingState.classList.add('d-none');
                emptyState.classList.remove('d-none');
            }
        }
    </script>

    <!-- AI Assistant Interactive Script -->
    <script>
        const aiToggleBtn = document.getElementById('ai-toggle-btn');
        const aiCloseBtn = document.getElementById('ai-close-btn');
        const aiWindow = document.getElementById('ai-assistant-window');
        const aiChatForm = document.getElementById('ai-chat-form');
        const aiInput = document.getElementById('ai-input');
        const aiMessages = document.getElementById('ai-messages');

        aiToggleBtn.addEventListener('click', () => {
            aiWindow.style.display = aiWindow.style.display === 'block' ? 'none' : 'block';
            if (aiWindow.style.display === 'block') aiInput.focus();
        });

        aiCloseBtn.addEventListener('click', () => {
            aiWindow.style.display = 'none';
        });

        aiChatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = aiInput.value.trim();
            if (!text) return;

            // User bubble
            const userMsgDiv = document.createElement('div');
            userMsgDiv.className = 'd-flex justify-content-end mb-2';
            userMsgDiv.innerHTML = `<div class="bg-success text-white p-2 rounded-3 shadow-sm" style="max-width: 85%;">${escapeHtml(text)}</div>`;
            aiMessages.appendChild(userMsgDiv);
            aiInput.value = '';
            aiMessages.scrollTop = aiMessages.scrollHeight;

            // Loading bubble
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'd-flex mb-2';
            loadingDiv.innerHTML = `<div class="bg-white p-2 rounded-3 shadow-sm border text-muted" style="max-width: 85%;"><i class="bi bi-hourglass-split me-1"></i> Thinking...</div>`;
            aiMessages.appendChild(loadingDiv);
            aiMessages.scrollTop = aiMessages.scrollHeight;

            try {
                const response = await fetch("{{ route('ai.assistant') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();
                loadingDiv.remove();

                const botDiv = document.createElement('div');
                botDiv.className = 'd-flex mb-2';
                botDiv.innerHTML = `<div class="bg-white p-2 rounded-3 shadow-sm border" style="max-width: 85%;">${formatMarkdown(data.reply)}</div>`;
                aiMessages.appendChild(botDiv);
                aiMessages.scrollTop = aiMessages.scrollHeight;
            } catch (err) {
                loadingDiv.remove();
                const errDiv = document.createElement('div');
                errDiv.className = 'd-flex mb-2';
                errDiv.innerHTML = `<div class="bg-danger text-white p-2 rounded-3 shadow-sm" style="max-width: 85%;">Failed to connect to assistant.</div>`;
                aiMessages.appendChild(errDiv);
            }
        });

        function escapeHtml(str) {
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }

        function formatMarkdown(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-success text-decoration-underline">$1</a>')
                .replace(/\n/g, '<br>');
        }
    </script>
    @yield('scripts')
</body>
</html>
