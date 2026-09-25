@extends('layouts.farmer')

@section('title', 'Add New Product — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item"><a href="{{ route('farmer.products.index') }}" class="text-success text-decoration-none">Weekly Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Product</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                    <div class="p-3 bg-brand-light text-success rounded-circle">
                        <i class="bi bi-plus-lg fs-3"></i>
                    </div>
                    <div>
                        <h3 class="heading-serif fw-bold text-dark mb-0">List New Farm Product</h3>
                        <small class="text-muted">Enter product specifications and initial inventory for your stall</small>
                    </div>
                </div>

                <form action="{{ route('farmer.products.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-dark">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Organic Beefsteak Tomatoes" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                                <input type="number" step="0.01" min="0.01" name="price" class="form-control" value="{{ old('price') }}" placeholder="4.50" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Selling Unit <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', 'lb') }}" placeholder="e.g. lb, bunch, pint, box" required>
                            <small class="text-muted" style="font-size: 0.72rem;">e.g. kg, lb, bunch, dozen, pint, basket</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Current Available Stock <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 25) }}" required>
                        </div>
                    </div>

                    <!-- Weekly Stock Template (SRS §1.6) -->
                    <div class="card p-3 bg-light border-0 rounded-3 mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-arrow-repeat text-success"></i>
                            <label class="form-label small fw-bold text-dark mb-0">Recurring Weekly Stock Template</label>
                        </div>
                        <p class="text-muted small mb-2">
                            Set the default number of units harvested each week. You can replenish your stall back to this quantity every market morning with one click.
                        </p>
                        <div class="col-md-6">
                            <input type="number" min="0" name="weekly_recurring_stock" class="form-control form-control-sm" value="{{ old('weekly_recurring_stock', 30) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Describe flavor profile, harvest details, organic methods...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Image URL (Optional)</label>
                        <input type="url" name="image_url" class="form-control" value="{{ old('image_url') }}" placeholder="https://images.unsplash.com/...">
                        <small class="text-muted" style="font-size: 0.72rem;">Leave empty to use a fresh product stock image automatically.</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('farmer.products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-brand rounded-pill px-5">Publish Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
