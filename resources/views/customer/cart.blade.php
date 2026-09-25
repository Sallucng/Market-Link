@extends('layouts.app')

@section('title', 'Pre-Order Cart — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-success">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pre-Order Cart</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Market Reservation</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Your Pre-Order Cart</h2>
        </div>
        @if(count($cart) > 0)
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Empty your cart?')">
                    <i class="bi bi-trash me-1"></i> Clear Cart
                </button>
            </form>
        @endif
    </div>

    @if(count($cart) > 0)
        <div class="row g-4">
            <!-- Cart Items Table -->
            <div class="col-lg-8">
                <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th scope="col">Product & Vendor</th>
                                    <th scope="col" class="text-center">Price</th>
                                    <th scope="col" class="text-center" style="width: 140px;">Quantity</th>
                                    <th scope="col" class="text-end">Subtotal</th>
                                    <th scope="col" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item['image_url'] ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=100&q=80' }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0">{{ $item['name'] }}</h6>
                                                    <small class="text-success"><i class="bi bi-shop me-1"></i>{{ $item['farmer_name'] }}</small>
                                                    <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i>{{ $item['market_name'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold">
                                            ${{ number_format($item['price'], 2) }} <span class="small text-muted">/ {{ $item['unit'] }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex align-items-center justify-content-center gap-1">
                                                @csrf
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="100" class="form-control form-control-sm text-center" style="width: 65px;">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Update quantity">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                    <i class="bi bi-x-circle fs-5"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Continue Browsing Products
                </a>
            </div>

            <!-- Order Summary Column -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm sticky-top" style="top: 80px;">
                    <h5 class="heading-serif fw-bold text-dark mb-3">Pre-Order Summary</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estimated Total:</span>
                        <span class="fs-4 fw-bold text-success">${{ number_format($total, 2) }}</span>
                    </div>

                    <hr class="my-3">

                    <div class="p-3 bg-brand-light rounded-3 mb-4 border border-success border-opacity-25">
                        <div class="d-flex gap-2">
                            <i class="bi bi-wallet2 fs-5 text-success"></i>
                            <div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">Pay in Person at Pickup</h6>
                                <p class="small text-secondary mb-0" style="font-size: 0.78rem;">
                                    Pre-orders do not require online credit card payments. Your order reserves live inventory and is settled directly at the market stall.
                                </p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('checkout.show') }}" class="btn btn-brand w-100 py-2 rounded-pill fw-semibold shadow-sm">
                        Proceed to Slot Selection <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="card card-custom p-5 text-center bg-white border-0 shadow-sm my-4">
            <div class="p-3 bg-light rounded-circle d-inline-flex mx-auto mb-3" style="width: 80px; height: 80px; align-items:center; justify-content:center;">
                <i class="bi bi-cart-x fs-1 text-muted"></i>
            </div>
            <h4 class="heading-serif fw-bold mb-2">Your Pre-Order Cart is Empty</h4>
            <p class="text-muted small col-md-6 mx-auto mb-4">
                Explore local farmers markets, discover weekly harvest products, and reserve items ahead of time for weekend market pickup.
            </p>
            <div>
                <a href="{{ route('products.index') }}" class="btn btn-brand rounded-pill px-4 py-2">
                    <i class="bi bi-grid-fill me-1"></i> Browse Product Catalog
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
