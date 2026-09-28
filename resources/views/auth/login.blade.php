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

                    <div class="text-center small text-secondary">
                        Don't have an account yet? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Create Account</a>
                    </div>
                </form>

                <!-- One-Click Test Accounts for Evaluators (SRS §1.9 Mandatory Deliverable) -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="small fw-bold text-muted text-uppercase mb-2 text-center">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Demo Logins
                    </h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary text-start" onclick="fillLogin('admin', 'Admin@123')">
                            <strong>Admin:</strong> <code>admin</code> / <code>Admin@123</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-start" onclick="fillLogin('greenvalley', 'Farmer@123')">
                            <strong>Farmer:</strong> <code>greenvalley</code> / <code>Farmer@123</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-start" onclick="fillLogin('sarah_shopper', 'Customer@123')">
                            <strong>Customer:</strong> <code>sarah_shopper</code> / <code>Customer@123</code>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function fillLogin(user, pass) {
        document.getElementById('loginField').value = user;
        document.getElementById('passwordField').value = pass;
    }
</script>
@endsection
