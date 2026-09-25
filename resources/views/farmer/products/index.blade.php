@extends('layouts.farmer')

@section('title', 'Weekly Produce Inventory — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Weekly Inventory</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Stall Inventory Management</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Manage Weekly Produce Stock</h2>
            <small class="text-muted">{{ $farmer->stall_name }} &bull; Set quantities and prices for market attendees</small>
        </div>

        <div class="d-flex gap-2">
            <!-- 1-Click Replenish Template (SRS §1.6) -->
            <form action="{{ route('farmer.products.replenish') }}" method="POST" onsubmit="return confirm('Replenish stock for all items using your weekly recurring template?')">
                @csrf
                <button type="submit" class="btn btn-outline-success rounded-pill px-3">
                    <i class="bi bi-arrow-repeat me-1"></i> Apply Weekly Template
                </button>
            </form>

            <a href="{{ route('farmer.products.create') }}" class="btn btn-brand rounded-pill px-4">
                <i class="bi bi-plus-circle me-1"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom p-3 bg-white border-0 shadow-sm mb-4">
        <form action="{{ route('farmer.products.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search your produce (e.g. Tomatoes, Lettuce)...">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-brand btn-sm flex-fill rounded-pill">Filter</button>
                <a href="{{ route('farmer.products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Reset</a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        @if($products->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-basket display-4 text-muted"></i>
                <h5 class="fw-bold mt-3 mb-1">No Produce Items Found</h5>
                <p class="text-muted small mb-4">You have not added any harvest items to your stall inventory yet.</p>
                <a href="{{ route('farmer.products.create') }}" class="btn btn-brand btn-sm rounded-pill px-4">
                    Add Your First Item
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Produce</th>
                            <th>Category</th>
                            <th>Price / Unit</th>
                            <th>Current Stock</th>
                            <th>Weekly Template</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $prod)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="rounded-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $prod->name }}</div>
                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 220px;">
                                                {{ $prod->description }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $prod->category->name ?? 'Produce' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">${{ number_format($prod->price, 2) }}</span>
                                    <span class="text-muted small">/ {{ $prod->unit }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $prod->stock_quantity == 0 ? 'text-danger' : 'text-dark' }}">
                                        {{ $prod->stock_quantity }}
                                    </span>
                                    <span class="text-muted small">{{ $prod->unit }}s</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $prod->weekly_recurring_stock ?? 0 }} {{ $prod->unit }}s / week</span>
                                </td>
                                <td>
                                    @if($prod->is_sold_out || $prod->stock_quantity == 0)
                                        <span class="badge bg-danger rounded-pill">Sold Out</span>
                                    @elseif(!$prod->is_available)
                                        <span class="badge bg-secondary rounded-pill">Hidden</span>
                                    @else
                                        <span class="badge bg-success rounded-pill">In Stock</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- Sold Out Toggle (SRS §1.6) -->
                                        <form action="{{ route('farmer.products.toggle-sold-out', $prod->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $prod->is_sold_out ? 'btn-outline-success' : 'btn-outline-warning' }} rounded-pill" title="Toggle Sold Out Status">
                                                <i class="bi {{ $prod->is_sold_out ? 'bi-check2' : 'bi-slash-circle' }}"></i>
                                                {{ $prod->is_sold_out ? 'Restock' : 'Mark Sold' }}
                                            </button>
                                        </form>

                                        <a href="{{ route('farmer.products.edit', $prod->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('farmer.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Remove {{ $prod->name }} from catalog?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
