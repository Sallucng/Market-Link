@extends('layouts.app')

@section('title', 'Server Encountered an Issue — MarketLink')

@section('content')
<div class="container py-5 text-center my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <span class="badge bg-danger px-3 py-1 rounded-pill mb-3">Error 500 · System Exception</span>
            <h1 class="heading-serif display-4 fw-bold text-dark mb-3">Temporary System Hiccup</h1>
            <p class="text-secondary lead mb-4">
                The platform experienced an unexpected issue processing your request. Our technical stewards have been logged of the occurrence. Please refresh or try again shortly.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-brand px-4 py-2">
                    <i class="bi bi-house-door me-1"></i> Return Home
                </a>
                <a href="{{ route('contact') }}" class="btn btn-brand-outline px-4 py-2">
                    <i class="bi bi-envelope me-1"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
