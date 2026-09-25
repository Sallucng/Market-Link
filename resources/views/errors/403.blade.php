@extends('layouts.app')

@section('title', 'Access Restricted — MarketLink')

@section('content')
<div class="container py-5 text-center my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-3">Error 403 · Restricted Access</span>
            <h1 class="heading-serif display-4 fw-bold text-dark mb-3">Authorized Stalls Only</h1>
            <p class="text-secondary lead mb-4">
                You do not have administrative or vendor credentials to view this portal area. Please log into an authorized account or return to the marketplace.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-brand px-4 py-2">
                    <i class="bi bi-house-door me-1"></i> Return Home
                </a>
                @auth
                    @if(Auth::user()->isCustomer())
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-brand-outline px-4 py-2">Customer Dashboard</a>
                    @elseif(Auth::user()->isFarmer())
                        <a href="{{ route('farmer.dashboard') }}" class="btn btn-brand-outline px-4 py-2">Farmer Portal</a>
                    @elseif(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-brand-outline px-4 py-2">Admin Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-brand-outline px-4 py-2">Sign In</a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
