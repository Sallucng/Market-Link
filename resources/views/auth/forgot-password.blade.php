@extends('layouts.app')

@section('title', 'Forgot Password — MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom p-4 p-md-5 bg-white border-0 shadow-sm">
                <div class="text-center mb-4">
                    <span class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-key-fill fs-3"></i>
                    </span>
                    <h3 class="heading-serif fw-bold text-dark">Forgot Password?</h3>
                    <p class="text-muted small">Enter your email address and we'll send you a secure link to reset your account password.</p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success d-flex align-items-center small py-2 mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-6"></i>
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

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <!-- Email Address -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Account Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" id="emailField" value="{{ old('email') }}" class="form-control border-start-0" placeholder="name@example.com" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 rounded-pill fw-semibold shadow-sm mb-3">
                        <i class="bi bi-send-fill me-1"></i> Send Password Reset Link
                    </button>

                    <div class="text-center small text-secondary">
                        Remembered your password? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
