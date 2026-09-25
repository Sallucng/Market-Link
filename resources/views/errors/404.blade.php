@extends('layouts.app')

@section('title', 'Page Not Found — MarketLink')

@section('content')
<div class="container py-5 text-center my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-3">Error 404</span>
            <h1 class="heading-serif display-4 fw-bold text-dark mb-3">Harvest Item or Page Not Found</h1>
            <p class="text-secondary lead mb-4">
                The market stall, product listing, or page you were searching for might have moved, concluded for the season, or never existed.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-brand px-4 py-2">
                    <i class="bi bi-house-door me-1"></i> Return Home
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-brand-outline px-4 py-2">
                    <i class="bi bi-basket me-1"></i> Browse Farm Products
                </a>
                <a href="{{ route('markets.index') }}" class="btn btn-brand-outline px-4 py-2">
                    <i class="bi bi-geo-alt me-1"></i> Explore Markets
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
