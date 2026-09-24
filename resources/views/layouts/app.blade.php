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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

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
            --font-heading: 'Playfair Display', Georgia, serif;
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

        .heading-serif {
            font-family: var(--font-heading);
            letter-spacing: -0.025em;
        }

        .font-mono-meta {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            letter-spacing: 0.02em;
        }

        .navbar-brand {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--brand-primary) !important;
            font-size: 1.45rem;
            letter-spacing: -0.03em;
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
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-brand:hover, .btn-brand:focus {
            background-color: var(--brand-primary-hover);
            border-color: var(--brand-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(27, 67, 50, 0.16);
        }
        .btn-brand:active {
            transform: scale(0.98);
        }

        .btn-brand-outline {
            border: 1px solid var(--border-card);
            color: var(--brand-primary);
            background-color: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.52rem 1.15rem;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-brand-outline:hover, .btn-brand-outline:focus {
            background-color: var(--surface-subtle);
            border-color: #cfcbbe;
            color: var(--brand-primary-hover);
            transform: translateY(-1px);
        }
        .btn-brand-outline:active {
            transform: scale(0.98);
        }

        .card-custom {
            background-color: var(--surface-card);
            border: 1px solid var(--border-hairline);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .card-custom:hover {
            border-color: #d6d3c9;
            box-shadow: 0 8px 20px -6px rgba(27, 67, 50, 0.07);
            transform: translateY(-2px);
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
    </style>
    @yield('styles')
</head>
<body>

    <!-- Top Announcement Ribbon -->
    <div class="py-1 px-3 text-center small fw-semibold" style="background-color: var(--brand-primary); color: #edf3ec; font-size: 0.8rem; letter-spacing: 0.02em;">
        <i class="bi bi-basket-fill me-1 text-warning"></i> TechWiz 7: eGreen Basket Edition — In-person stall pickup only. Zero online convenience fees.
    </div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-2" style="border-bottom: 1px solid var(--border-hairline);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="p-2 text-white rounded-2 me-2 d-inline-flex align-items-center justify-content-center" style="width:34px; height:34px; background-color: var(--brand-primary);">
                    <i class="bi bi-flower2"></i>
                </span>
                MarketLink
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('markets.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('markets.index') }}">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>Markets & Map
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('products.index') }}">
                            <i class="bi bi-grid-fill text-success me-1"></i>Farm Produce
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('about') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active fw-bold text-success' : 'text-dark' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <!-- Pre-Order Cart Button -->
                    <a href="{{ route('cart.index') }}" class="btn btn-brand-outline position-relative me-2 px-3 py-1">
                        <i class="bi bi-cart3 me-1"></i> Pickup Cart
                        @php
                            $cartCount = count(session('cart', []));
                        @endphp
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger font-mono-meta">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3" type="button" data-bs-target="#userMenu" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle text-success fs-5"></i>
                                <span class="fw-semibold">{{ Auth::user()->name }}</span>
                                <span class="badge bg-secondary ms-1 small text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->role }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" id="userMenu">
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
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light pt-5 pb-4 mt-5 border-top">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="heading-serif text-white mb-3">
                        <i class="bi bi-flower2 text-success me-1"></i> MarketLink
                    </h5>
                    <p class="text-secondary small">
                        Farm Fresh Just a Click Away. MarketLink empowers neighborhood farmers to list weekly harvests, take pre-orders ahead of market day, and coordinate stall pickups.
                    </p>
                    <div class="badge bg-secondary">Theme: eGreen Basket</div>
                    <div class="badge bg-success ms-1">TechWiz 7 Championship</div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="text-uppercase text-white small fw-bold mb-3">Explore</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-secondary text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="{{ route('markets.index') }}" class="text-secondary text-decoration-none">Markets & Map</a></li>
                        <li class="mb-2"><a href="{{ route('products.index') }}" class="text-secondary text-decoration-none">Produce Catalog</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}" class="text-secondary text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="text-secondary text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase text-white small fw-bold mb-3">Important Notice</h6>
                    <p class="text-secondary small mb-2">
                        <i class="bi bi-wallet2 text-warning me-1"></i> <strong>Pay at Pickup Only:</strong> In strict accordance with competition SRS Section 1.5, payment is settled directly with the farmer at stall pickup.
                    </p>
                    <p class="text-secondary small">
                        <i class="bi bi-geo-alt text-danger me-1"></i> Powered by <strong>OpenStreetMap</strong>.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase text-white small fw-bold mb-3">Test Accounts</h6>
                    <div class="bg-secondary bg-opacity-25 p-2 rounded small text-secondary">
                        <div><strong>Admin:</strong> admin / Admin@123</div>
                        <div><strong>Farmer:</strong> greenvalley / Farmer@123</div>
                        <div><strong>Customer:</strong> sarah_shopper / Customer@123</div>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-secondary">
                <div>&copy; 2026 MarketLink. Developed for TechWiz 7 — Category: End-to-End Web Solutions.</div>
                <div>Strictly following SRS v1.0 specifications.</div>
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
                    <small class="text-white-50" style="font-size: 0.72rem;">Ask about markets, stalls & produce</small>
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
                <input type="text" id="ai-input" class="form-control form-control-sm" placeholder="Ask about timings, produce..." autocomplete="off">
                <button type="submit" class="btn btn-sm btn-success px-3">Send</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
