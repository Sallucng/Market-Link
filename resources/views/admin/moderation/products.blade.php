@extends('layouts.admin')

@section('title', 'Produce Listing Moderation — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Produce Moderation</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill mb-1">Content Moderation</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Produce Listings Moderation</h2>
            <small class="text-muted">Oversee vendor product listings and enforce catalogue standards (SRS §1.6)</small>
        </div>

        <div class="btn-group">
            <a href="{{ route('admin.moderation.reviews') }}" class="btn btn-sm btn-outline-dark">Reviews Moderation</a>
            <a href="{{ route('admin.moderation.products') }}" class="btn btn-sm btn-dark active">Produce Listings</a>
        </div>
    </div>

    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Produce</th>
                        <th>Farmer Stall</th>
                        <th>Category</th>
                        <th>Price / Unit</th>
                        <th>Visibility</th>
                        <th class="text-end">Moderation Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $prod)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="rounded-3 shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $prod->name }}</div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $prod->description }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-success">{{ $prod->farmer->stall_name ?? 'Farmer Stall' }}</div>
                                <small class="text-muted">{{ $prod->farmer->user->email ?? '' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $prod->category->name ?? 'Produce' }}</span></td>
                            <td><span class="fw-bold text-dark">${{ number_format($prod->price, 2) }}</span> <small class="text-muted">/ {{ $prod->unit }}</small></td>
                            <td>
                                @if($prod->is_available)
                                    <span class="badge bg-success rounded-pill">Active / Public</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">Hidden / Delisted</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.moderation.products.toggle', $prod->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $prod->is_available ? 'btn-outline-danger' : 'btn-outline-success' }} rounded-pill px-3">
                                        <i class="bi {{ $prod->is_available ? 'bi-eye-slash' : 'bi-eye' }} me-1"></i>
                                        {{ $prod->is_available ? 'Delist Item' : 'Restore Listing' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
