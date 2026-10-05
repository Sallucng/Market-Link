<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ur', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MarketLink') — Farm Fresh Just a Click Away</title>
    
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
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

    <!-- Space UI Essentials & Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        /* Multi-Language & RTL Optimization */
        [dir="rtl"] {
            text-align: right;
            font-family: 'Segoe UI', Tahoma, -apple-system, BlinkMacSystemFont, sans-serif;
        }
        [dir="rtl"] .dropdown-menu {
            text-align: right;
        }
        [dir="rtl"] .navbar-nav {
            padding-right: 0;
        }
        [dir="rtl"] .me-auto {
            margin-left: auto !important;
            margin-right: 0 !important;
        }
        [dir="rtl"] .ms-auto {
            margin-right: auto !important;
            margin-left: 0 !important;
        }
        [dir="rtl"] .dropdown-menu-end {
            right: auto !important;
            left: 0 !important;
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

        /* Fully Level Responsive Stat Cards Grids */
        .stat-grid-5 {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1rem;
            align-items: stretch;
        }
        @media (max-width: 1199px) and (min-width: 768px) {
            .stat-grid-5 {
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 0.65rem;
            }
        }
        @media (max-width: 767px) {
            .stat-grid-5 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.75rem;
            }
            .stat-grid-5 > :last-child:nth-child(odd) {
                grid-column: span 2;
            }
        }

        .stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            align-items: stretch;
        }
        @media (max-width: 767px) {
            .stat-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.75rem;
            }
        }

        .stat-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            align-items: stretch;
        }
        @media (max-width: 767px) {
            .stat-grid-3 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 0.75rem;
            }
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

        @media (max-width: 767.98px) {
            #ai-assistant-bubble {
                bottom: 18px !important;
                right: 18px !important;
            }
            #ai-assistant-window {
                bottom: 86px !important;
                right: 12px !important;
                left: 12px !important;
                width: auto !important;
                max-width: 360px !important;
                margin: 0 auto !important;
            }
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

        .dropdown-menu .dropdown-item.active,
        .dropdown-menu .dropdown-item:active,
        .liquid-glass-menu .dropdown-item.active,
        .liquid-glass-menu .dropdown-item:active {
            background: #e8f5e9 !important;
            color: var(--brand-primary) !important;
            font-weight: 700 !important;
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

        /* ── Navbar defence: override any Tailwind `.collapse` utility that
           sets `visibility: collapse` — Bootstrap needs its own `.collapse`
           class to toggle `display` between `none` and `flex/block`. ────── */
        @media (min-width: 992px) {
            .navbar-expand-lg .navbar-collapse {
                display: flex !important;
                visibility: visible !important;
            }
        }
        /* On mobile (<992px), Bootstrap toggles via JS — ensure visibility
           is never the reason the menu is hidden; `display` handles that. */
        .navbar-collapse {
            visibility: visible !important;
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

        /* ── Liquid Glass Flash Alerts (Agentation Feedback) ── */
        .alert-liquid-glass {
            position: relative;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 14px !important;
            padding: 0.85rem 1.25rem !important;
            box-shadow: 0 10px 30px -4px rgba(11, 33, 22, 0.08), 0 4px 12px rgba(0, 0, 0, 0.03), inset 0 1px 1px rgba(255, 255, 255, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            transition: opacity 0.35s ease, transform 0.35s ease, max-height 0.35s ease, margin 0.35s ease, padding 0.35s ease;
            overflow: hidden;
        }
        .alert-liquid-glass.alert-success {
            background: rgba(236, 253, 243, 0.88) !important;
            border: 1px solid rgba(45, 90, 39, 0.25) !important;
            color: #14532d !important;
        }
        .alert-liquid-glass.alert-danger {
            background: rgba(254, 242, 242, 0.88) !important;
            border: 1px solid rgba(220, 38, 38, 0.25) !important;
            color: #991b1b !important;
        }
        .alert-liquid-glass.alert-warning {
            background: rgba(254, 252, 232, 0.88) !important;
            border: 1px solid rgba(202, 138, 4, 0.28) !important;
            color: #854d0e !important;
        }
        .alert-liquid-glass.alert-info {
            background: rgba(239, 246, 255, 0.88) !important;
            border: 1px solid rgba(37, 99, 235, 0.25) !important;
            color: #1e40af !important;
        }
        .alert-liquid-glass .btn-close {
            opacity: 0.45;
            transition: opacity 0.2s ease;
        }
        .alert-liquid-glass .btn-close:hover {
            opacity: 1;
        }
        .alert-liquid-glass::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: currentColor;
            opacity: 0.25;
            transform-origin: left;
            animation: liquid-alert-progress 2s linear forwards;
        }
        @keyframes liquid-alert-progress {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }

        /* Mobile Bottom Floating Menu Trigger Button & Offcanvas Drawer */
        .mobile-menu-trigger-btn {
            background-color: var(--brand-primary) !important;
            color: #ffffff !important;
            border: 2px solid rgba(255, 255, 255, 0.9) !important;
            box-shadow: 0 10px 28px rgba(27, 67, 50, 0.45) !important;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 0.6rem 1.6rem;
            border-radius: 999px;
            pointer-events: auto !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .mobile-menu-trigger-btn:hover,
        .mobile-menu-trigger-btn:focus,
        .mobile-menu-trigger-btn:active {
            background-color: #2d6a4f !important;
            color: #ffffff !important;
            transform: scale(1.04) !important;
            box-shadow: 0 14px 34px rgba(27, 67, 50, 0.55) !important;
        }

        #mobileMenuOffcanvas {
            height: auto !important;
            max-height: 86vh !important;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            border-top: 1px solid rgba(27, 67, 50, 0.15);
            box-shadow: 0 -12px 36px rgba(0, 0, 0, 0.22);
            background: #ffffff;
        }

        #mobileMenuOffcanvas .offcanvas-body {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        #mobileMenuOffcanvas .offcanvas-body::-webkit-scrollbar {
            display: none;
        }

        .mobile-drawer-link {
            font-size: 0.98rem;
            font-weight: 600;
            color: #1e293b;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        .mobile-drawer-link:hover,
        .mobile-drawer-link:active {
            background-color: #f1f8f4;
            color: var(--brand-primary);
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
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="MarketLink Logo" class="brand-logo-img" style="height: 38px; width: auto; max-width: 48px; object-fit: contain;">
                <span class="fw-bold tracking-tight">MarketLink</span>
            </a>

            <!-- Mobile Quick Actions & Toggler -->
            <div class="d-flex align-items-center gap-1 d-lg-none ms-auto me-1">
                <!-- Mobile Language Dropdown -->
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-label="{{ __('Switch Language') }}" style="width: 36px; height: 36px; background: #fbfdfa;">
                        <i class="bi bi-translate text-success"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="min-width: 140px; border-radius: 12px;">
                        <li><a class="dropdown-item py-1.5 {{ app()->getLocale() === 'en' ? 'active fw-bold' : '' }}" href="{{ route('locale.switch', 'en') }}">🇺🇸 English</a></li>
                        <li><a class="dropdown-item py-1.5 {{ app()->getLocale() === 'ur' ? 'active fw-bold' : '' }}" href="{{ route('locale.switch', 'ur') }}">🇵🇰 اردو</a></li>
                        <li><a class="dropdown-item py-1.5 {{ app()->getLocale() === 'es' ? 'active fw-bold' : '' }}" href="{{ route('locale.switch', 'es') }}">🇪🇸 Español</a></li>
                    </ul>
                </div>

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

            <button class="navbar-toggler ms-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuOffcanvas" aria-controls="mobileMenuOffcanvas" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('home') }}">{{ __('Home') }}</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('markets.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ __('Markets') }}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('markets.index') }}"><i class="bi bi-grid-3x3-gap me-2 text-success"></i>{{ __('All Markets & Map') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('markets.nearby') }}"><i class="bi bi-crosshair me-2 text-primary"></i>{{ __('Nearby Markets') }}</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('products.index') }}">{{ __('Farm Products') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('farmers.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('farmers.index') }}">{{ __('Farmers') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('about') }}">{{ __('About Us') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('faq') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('faq') }}">{{ __('FAQs') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 navbar-user-actions">
                    <!-- Language Switcher Dropdown (Desktop) -->
                    <div class="dropdown me-1">
                        <button class="btn btn-sm btn-light border rounded-pill px-2.5 py-1.5 d-flex align-items-center gap-1.5 text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Switch Language') }}" style="font-size: 0.82rem; font-weight: 600; background: #ffffff;">
                            <i class="bi bi-translate text-success"></i>
                            <span class="text-uppercase">{{ app()->getLocale() }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="min-width: 155px; border-radius: 12px;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 {{ app()->getLocale() === 'en' ? 'active fw-bold' : '' }}" href="{{ route('locale.switch', 'en') }}">
                                    <span>🇺🇸 English</span>
                                    @if(app()->getLocale() === 'en') <i class="bi bi-check2 text-success"></i> @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 {{ app()->getLocale() === 'ur' ? 'active fw-bold' : '' }}" href="{{ route('locale.switch', 'ur') }}">
                                    <span>🇵🇰 اردو (Urdu)</span>
                                    @if(app()->getLocale() === 'ur') <i class="bi bi-check2 text-success"></i> @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 {{ app()->getLocale() === 'es' ? 'active fw-bold' : '' }}" href="{{ route('locale.switch', 'es') }}">
                                    <span>🇪🇸 Español</span>
                                    @if(app()->getLocale() === 'es') <i class="bi bi-check2 text-success"></i> @endif
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Pre-Order Cart Button -->
                    <a href="{{ route('cart.index') }}" class="btn btn-brand-outline position-relative me-2 px-3 py-1">
                        <i class="bi bi-cart3 me-1"></i> {{ __('Pickup Cart') }}
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger font-mono-meta">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-liquid-glass dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle text-success fs-5"></i>
                                <span class="fw-semibold navbar-user-name">{{ Auth::user()->name }}</span>
                                <span class="badge bg-secondary ms-1 small text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->role }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end liquid-glass-menu shadow-lg">
                                @if(Auth::user()->isCustomer())
                                    <li><h6 class="dropdown-header">{{ __('Customer Portal') }}</h6></li>
                                    <li><a class="dropdown-item fw-semibold" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-success"></i>{{ __('My Dashboard') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}"><i class="bi bi-box-seam me-2"></i>{{ __('My Pre-Orders') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customer.messages.index') }}"><i class="bi bi-chat-dots me-2 text-success"></i>{{ __('Stall Messages') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customer.favorites.index') }}"><i class="bi bi-heart me-2"></i>{{ __('Saved Favorites') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customer.complaints.index') }}"><i class="bi bi-shield-exclamation me-2 text-danger"></i>{{ __('My Complaints') }}</a></li>
                                @elseif(Auth::user()->isFarmer())
                                    <li><h6 class="dropdown-header">{{ __('Farmer Management') }}</h6></li>
                                    <li><a class="dropdown-item text-success fw-bold" href="{{ route('farmer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>{{ __('Farmer Portal') }}</a></li>
                                @elseif(Auth::user()->isAdmin())
                                    <li><h6 class="dropdown-header">{{ __('Platform Backoffice') }}</h6></li>
                                    <li><a class="dropdown-item text-primary fw-bold" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock me-2"></i>{{ __('Admin Dashboard') }}</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Sign Out') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light border px-3 rounded-pill fw-semibold">{{ __('Sign In') }}</a>
                        <a href="{{ route('register') }}" class="btn btn-brand px-3 rounded-pill">{{ __('Register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Flash Alerts (Liquid Glass UX) -->
    <div class="container mt-3" id="global-flash-alerts">
        @if(session('success'))
            <div class="alert alert-success alert-liquid-glass alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                <div class="fw-medium text-dark">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-liquid-glass alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
                <div class="fw-medium text-dark">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-liquid-glass alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5 text-warning"></i>
                <div class="fw-medium text-dark">{{ session('warning') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-info alert-liquid-glass alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
                <div class="fw-medium text-dark">{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-liquid-glass alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
                <div class="fw-medium text-dark">{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    <h5 class="heading-serif text-white mb-3 d-flex align-items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="MarketLink Logo" style="height: 34px; width: auto; object-fit: contain;">
                        <span>MarketLink</span>
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
                        <li class="mb-2"><a href="{{ route('about') }}" class="footer-link">{{ __('About Us') }}</a></li>
                        <li class="mb-2"><a href="{{ route('faq') }}" class="footer-link">{{ __('Frequently Asked Questions') }}</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="footer-link">{{ __('Contact Us') }}</a></li>
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
                    <h6 class="text-uppercase small fw-bold mb-3" style="color: #d8f3dc; letter-spacing: 0.08em;">Market Updates &amp; Help</h6>
                    <p class="small mb-3" style="color: #a3b8ad; line-height: 1.55;">
                        Subscribe to get notified every Thursday when weekend harvest lists and seasonal stall specials drop.
                    </p>
                    <form onsubmit="event.preventDefault(); this.querySelector('button').innerHTML='<i class=\'bi bi-check-lg\'></i> Subscribed!'; this.querySelector('input').disabled=true;" class="mb-3">
                        <div class="input-group input-group-sm">
                            <input type="email" class="form-control" placeholder="Your email address" required style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.18); color: #fff; font-size: 0.82rem;">
                            <button class="btn btn-success px-3 fw-semibold" type="submit" style="font-size: 0.8rem;">
                                Join
                            </button>
                        </div>
                    </form>
                    <div class="d-flex align-items-center gap-2 small" style="color: #74c69d;">
                        <i class="bi bi-headset fs-6"></i>
                        <span>Support: <a href="mailto:support@marketlink.local" class="text-decoration-none" style="color: #d8f3dc;">support@marketlink.local</a></span>
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
                const response = await fetch("/api/ai-assistant", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
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

        // Auto-dismiss liquid glass flash notifications in 1.8-2 seconds (Agentation UX)
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.alert-liquid-glass, #global-flash-alerts .alert');
            alerts.forEach(function (alert) {
                let timer = setTimeout(function () {
                    dismissAlert(alert);
                }, 2000);

                alert.addEventListener('mouseenter', function () {
                    clearTimeout(timer);
                });
                alert.addEventListener('mouseleave', function () {
                    timer = setTimeout(function () {
                        dismissAlert(alert);
                    }, 1000);
                });
            });

            function dismissAlert(alert) {
                alert.style.transition = 'opacity 0.35s ease, transform 0.35s ease, max-height 0.35s ease, margin 0.35s ease, padding 0.35s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    alert.style.maxHeight = '0';
                    alert.style.paddingTop = '0';
                    alert.style.paddingBottom = '0';
                    alert.style.marginTop = '0';
                    alert.style.marginBottom = '0';
                    alert.style.border = 'none';
                    setTimeout(function () {
                        if (alert.parentNode) alert.remove();
                    }, 350);
                }, 350);
            }
        });
    </script>
    
    <!-- Mobile Bottom Floating "Menu" Button (Mobile Only, d-lg-none) -->
    <div class="fixed-bottom d-lg-none d-flex justify-content-center pb-3" style="z-index: 1040; pointer-events: none;">
        <button type="button" 
                class="btn mobile-menu-trigger-btn d-inline-flex align-items-center gap-2 pointer-events-auto"
                data-bs-toggle="offcanvas" 
                data-bs-target="#mobileMenuOffcanvas" 
                aria-controls="mobileMenuOffcanvas"
                aria-label="Open Navigation Menu">
            <i class="bi bi-list fs-5"></i>
            <span>{{ __('Menu') }}</span>
        </button>
    </div>

    <!-- Mobile Navigation Offcanvas Bottom Sheet (Mobile Only, d-lg-none) -->
    <div class="offcanvas offcanvas-bottom d-lg-none" tabindex="-1" id="mobileMenuOffcanvas" aria-labelledby="mobileMenuOffcanvasLabel">
        <!-- Drawer Drag Handle -->
        <div class="d-flex justify-content-center pt-2">
            <div style="width: 42px; height: 5px; background: #cbd5e1; border-radius: 999px;"></div>
        </div>

        <div class="offcanvas-header pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="MarketLink Logo" style="height: 32px; width: auto; object-fit: contain;">
                <span class="fw-bold text-dark fs-5 font-heading">MarketLink</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-3">
            <!-- Language Quick Switcher (Mobile Drawer) -->
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border mb-3">
                <span class="small fw-semibold text-muted d-flex align-items-center gap-2">
                    <i class="bi bi-translate text-success"></i> {{ __('Language') }}
                </span>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('locale.switch', 'en') }}" class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-success fw-bold' : 'btn-outline-secondary' }} py-0 px-2.5" style="font-size: 0.78rem;">EN</a>
                    <a href="{{ route('locale.switch', 'ur') }}" class="btn btn-sm {{ app()->getLocale() === 'ur' ? 'btn-success fw-bold' : 'btn-outline-secondary' }} py-0 px-2.5" style="font-size: 0.78rem;">اردو</a>
                    <a href="{{ route('locale.switch', 'es') }}" class="btn btn-sm {{ app()->getLocale() === 'es' ? 'btn-success fw-bold' : 'btn-outline-secondary' }} py-0 px-2.5" style="font-size: 0.78rem;">ES</a>
                </div>
            </div>

            <!-- Navigation Links identical to desktop -->
            <div class="d-flex flex-column gap-1 mb-3">
                <a href="{{ route('home') }}" class="mobile-drawer-link d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none {{ request()->routeIs('home') ? 'bg-success-subtle text-success fw-bold' : 'text-dark' }}">
                    <i class="bi bi-house-door fs-5 text-success"></i>
                    <span>{{ __('Home') }}</span>
                </a>

                <!-- Markets Submenu -->
                <div class="mobile-drawer-accordion">
                    <button class="mobile-drawer-link w-100 d-flex align-items-center justify-content-between p-2 rounded-3 text-decoration-none border-0 bg-transparent {{ request()->routeIs('markets.*') ? 'bg-success-subtle text-success fw-bold' : 'text-dark' }}" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#mobileMarketsCollapse" 
                            aria-expanded="{{ request()->routeIs('markets.*') ? 'true' : 'false' }}">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-geo-alt fs-5 text-success"></i>
                            <span>{{ __('Markets') }}</span>
                        </div>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <div class="collapse {{ request()->routeIs('markets.*') ? 'show' : '' }} ps-4 pe-2 pt-1" id="mobileMarketsCollapse">
                        <div class="d-flex flex-column gap-1 border-start border-2 border-success-subtle ps-3 my-1">
                            <a href="{{ route('markets.index') }}" class="py-2 text-decoration-none {{ request()->routeIs('markets.index') ? 'text-success fw-bold' : 'text-secondary' }} small d-flex align-items-center gap-2">
                                <i class="bi bi-grid-3x3-gap text-success"></i> {{ __('All Markets & Map') }}
                            </a>
                            <a href="{{ route('markets.nearby') }}" class="py-2 text-decoration-none {{ request()->routeIs('markets.nearby') ? 'text-success fw-bold' : 'text-secondary' }} small d-flex align-items-center gap-2">
                                <i class="bi bi-crosshair text-primary"></i> {{ __('Nearby Markets') }}
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('products.index') }}" class="mobile-drawer-link d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none {{ request()->routeIs('products.*') ? 'bg-success-subtle text-success fw-bold' : 'text-dark' }}">
                    <i class="bi bi-basket2 fs-5 text-success"></i>
                    <span>{{ __('Farm Products') }}</span>
                </a>

                <a href="{{ route('farmers.index') }}" class="mobile-drawer-link d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none {{ request()->routeIs('farmers.*') ? 'bg-success-subtle text-success fw-bold' : 'text-dark' }}">
                    <i class="bi bi-people fs-5 text-success"></i>
                    <span>{{ __('Farmers') }}</span>
                </a>

                <a href="{{ route('about') }}" class="mobile-drawer-link d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none {{ request()->routeIs('about') ? 'bg-success-subtle text-success fw-bold' : 'text-dark' }}">
                    <i class="bi bi-info-circle fs-5 text-success"></i>
                    <span>{{ __('About Us') }}</span>
                </a>

                <a href="{{ route('faq') }}" class="mobile-drawer-link d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none {{ request()->routeIs('faq') ? 'bg-success-subtle text-success fw-bold' : 'text-dark' }}">
                    <i class="bi bi-question-circle fs-5 text-success"></i>
                    <span>{{ __('FAQs') }}</span>
                </a>

                <a href="{{ route('contact') }}" class="mobile-drawer-link d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none {{ request()->routeIs('contact') ? 'bg-success-subtle text-success fw-bold' : 'text-dark' }}">
                    <i class="bi bi-envelope fs-5 text-success"></i>
                    <span>{{ __('Contact') }}</span>
                </a>
            </div>

            <hr class="my-3 text-muted opacity-25">

            <!-- Pre-Order Pickup Cart -->
            @php $cartCount = count(session('cart', [])); @endphp
            <a href="{{ route('cart.index') }}" class="btn btn-brand-outline w-100 d-flex align-items-center justify-content-between p-2.5 rounded-pill mb-3">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-cart3 fs-5"></i>
                    <span class="fw-semibold">{{ __('Pickup Cart') }}</span>
                </span>
                <span class="badge rounded-pill bg-danger font-mono-meta px-2 py-1">
                    {{ $cartCount }} {{ Str::plural('item', $cartCount) }}
                </span>
            </a>

            <!-- Authentication / User Actions -->
            @auth
                <div class="card bg-light border-0 rounded-3 p-3 mb-2">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-person-circle text-success fs-4"></i>
                        <div>
                            <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                            <span class="badge bg-secondary small text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->role }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1 mt-2 pt-2 border-top">
                        @if(Auth::user()->isCustomer())
                            <a href="{{ route('customer.dashboard') }}" class="py-1.5 text-decoration-none text-success fw-semibold small d-flex align-items-center gap-2">
                                <i class="bi bi-speedometer2"></i> {{ __('My Dashboard') }}
                            </a>
                            <a href="{{ route('customer.orders.index') }}" class="py-1.5 text-decoration-none text-dark small d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam"></i> {{ __('My Pre-Orders') }}
                            </a>
                            <a href="{{ route('customer.messages.index') }}" class="py-1.5 text-decoration-none text-dark small d-flex align-items-center gap-2">
                                <i class="bi bi-chat-dots"></i> {{ __('Stall Messages') }}
                            </a>
                            <a href="{{ route('customer.favorites.index') }}" class="py-1.5 text-decoration-none text-dark small d-flex align-items-center gap-2">
                                <i class="bi bi-heart"></i> {{ __('Saved Favorites') }}
                            </a>
                            <a href="{{ route('customer.complaints.index') }}" class="py-1.5 text-decoration-none text-dark small d-flex align-items-center gap-2">
                                <i class="bi bi-shield-exclamation text-danger"></i> {{ __('My Complaints') }}
                            </a>
                        @elseif(Auth::user()->isFarmer())
                            <a href="{{ route('farmer.dashboard') }}" class="py-1.5 text-decoration-none text-success fw-bold small d-flex align-items-center gap-2">
                                <i class="bi bi-speedometer2"></i> {{ __('Farmer Portal') }}
                            </a>
                        @elseif(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="py-1.5 text-decoration-none text-primary fw-bold small d-flex align-items-center gap-2">
                                <i class="bi bi-shield-lock"></i> {{ __('Admin Dashboard') }}
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill mt-1">
                                <i class="bi bi-box-arrow-right me-1"></i> {{ __('Sign Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-light border rounded-pill fw-semibold py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('Sign In') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-brand rounded-pill py-2">
                        <i class="bi bi-person-plus me-1"></i> {{ __('Register') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>

    @yield('scripts')
</body>
</html>
