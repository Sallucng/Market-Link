@extends('layouts.app')

@section('title', 'Reset Password — MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom p-4 p-md-5 bg-white border-0 shadow-sm">
                <div class="text-center mb-4">
                    <span class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </span>
                    <h3 class="heading-serif fw-bold text-dark">Create New Password</h3>
                    <p class="text-muted small">Please enter your account email and choose a strong new password.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <!-- Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Account Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" id="emailField" value="{{ old('email', $email) }}" class="form-control border-start-0" placeholder="name@example.com" required autofocus>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="passwordField" class="form-control border-start-0" placeholder="At least 6 characters" required>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-muted"></i></span>
                            <input type="password" name="password_confirmation" id="passwordConfirmField" class="form-control border-start-0" placeholder="Re-enter new password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 rounded-pill fw-semibold shadow-sm mb-3">
                        <i class="bi bi-check2-circle me-1"></i> Reset Password
                    </button>

                    <div class="text-center small text-secondary">
                        Already know your credentials? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
