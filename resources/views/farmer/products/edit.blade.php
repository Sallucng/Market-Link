@extends('layouts.farmer')

@section('title', 'Edit ' . $product->name . ' — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item"><a href="{{ route('farmer.products.index') }}" class="text-success text-decoration-none">Weekly Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit {{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-3 shadow-sm" style="width: 56px; height: 56px; object-fit: cover;">
                    <div>
                        <h3 class="heading-serif fw-bold text-dark mb-0">Edit Product Listing</h3>
                        <small class="text-muted">Update pricing, availability, and weekly template quantities</small>
                    </div>
                </div>

                <form action="{{ route('farmer.products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-dark">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Price ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Selling Unit <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Current Available Stock <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                        </div>
                    </div>

                    <!-- Weekly Stock Template (SRS §1.6) -->
                    <div class="card p-3 bg-light border-0 rounded-3 mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-arrow-repeat text-success"></i>
                            <label class="form-label small fw-bold text-dark mb-0">Recurring Weekly Stock Template</label>
                        </div>
                        <p class="text-muted small mb-2">
                            Target stock quantity to replenish each week when you click "Apply Weekly Template".
                        </p>
                        <div class="col-md-6">
                            <input type="number" min="0" name="weekly_recurring_stock" class="form-control form-control-sm" value="{{ old('weekly_recurring_stock', $product->weekly_recurring_stock) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Image URL</label>
                        <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $product->image_url) }}">
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_available" id="isAvailableSwitch" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold text-dark" for="isAvailableSwitch">
                            Listing is Active & Visible in Public Storefront
                        </label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('farmer.products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-brand rounded-pill px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
