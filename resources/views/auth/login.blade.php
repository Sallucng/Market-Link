@extends('layouts.app')

@section('title', 'Sign In — MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom p-4 p-md-5 bg-white border-0 shadow-sm">
                <div class="text-center mb-4">
                    <span class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-box-arrow-in-right fs-3"></i>
                    </span>
                    <h3 class="heading-serif fw-bold text-dark">Welcome Back</h3>
                    <p class="text-muted small">Sign in to manage your pre-orders, stall inventory, or system</p>
                </div>

                <!-- Quick Demo Credentials Helper -->
                <div class="p-2 mb-3 bg-light rounded-3 border text-center" style="font-size: 0.78rem;">
                    <div class="text-muted fw-semibold mb-1"><i class="bi bi-key-fill text-warning me-1"></i> Quick Demo Accounts (Click to Fill):</div>
                    <div class="d-flex flex-wrap justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill py-0 px-2 fw-semibold" style="font-size: 0.72rem;" onclick="fillDemo('admin@marketlink.com', 'Admin123!')">
                            🛡️ Admin
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill py-0 px-2 fw-semibold" style="font-size: 0.72rem;" onclick="fillDemo('farmer.john@marketlink.com', 'Farmer123!')">
                            👨‍🌾 Farmer
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 fw-semibold" style="font-size: 0.72rem;" onclick="fillDemo('customer.alice@marketlink.com', 'Customer123!')">
                            🛒 Customer
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center small py-2 mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-success d-flex align-items-center small py-2 mb-3" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger d-flex align-items-center small py-2 mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <!-- Username or Email -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="login" id="loginField" value="{{ old('login') }}" class="form-control border-start-0" placeholder="Enter username or email" required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="passwordField" class="form-control border-start-0" placeholder="Enter password" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label text-secondary" for="rememberMe">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-success text-decoration-none fw-semibold">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 rounded-pill fw-semibold shadow-sm mb-3">
                        Sign In
                    </button>

                    <!-- Social Login Divider -->
                    <div class="d-flex align-items-center my-3 text-muted">
                        <hr class="flex-grow-1 my-0 border-secondary-subtle">
                        <span class="px-3 small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">or</span>
                        <hr class="flex-grow-1 my-0 border-secondary-subtle">
                    </div>

                    <!-- Google Sign In Button (Customer & Farmer ONLY) -->
                    <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary w-100 py-2 rounded-pill fw-semibold shadow-sm mb-2 d-flex align-items-center justify-content-center gap-2 bg-white" style="border-color: #d1d5db;">
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.18 3.665-9.15z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.27 21.36 7.36 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.16 0 9.98 0 12s.46 3.84 1.26 5.42l4.02-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.27 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>
                    <div class="text-center mb-3">
                        <small class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-shield-check text-success me-1"></i>For Customer & Farmer accounts</small>
                    </div>

                    <div class="text-center small text-secondary">
                        Don't have an account yet? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function fillDemo(login, pass) {
    var loginEl = document.getElementById('loginField');
    var passEl = document.getElementById('passwordField');
    if (loginEl && passEl) {
        loginEl.value = login;
        passEl.value = pass;
        loginEl.focus();
    }
}
</script>
@endsection
