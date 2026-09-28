@extends('layouts.farmer')

@section('title', 'Edit Promotional Sale — MarketLink')

@section('content')
<div class="py-2" style="max-width: 960px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item"><a href="{{ route('farmer.sales.index') }}" class="text-success text-decoration-none">Promotions & Sales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Campaign</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Update Promotion</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Edit: {{ $sale->title }}</h2>
            <small class="text-muted">Modify dates, discount rates, banner, or linked products for this sale campaign.</small>
        </div>

        @if($sale->is_featured)
            <span class="badge bg-warning bg-opacity-20 text-dark border border-warning rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-star-fill text-warning"></i>
                <span>Currently Spotlighted on Homepage</span>
            </span>
        @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please check the required fields:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('farmer.sales.update', $sale->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
            <h5 class="fw-bold text-dark mb-3">1. Campaign Details</h5>

            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Sale Campaign Title <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title', $sale->title) }}" class="form-control" placeholder="e.g. Weekend Harvest Flash Sale" required>
                <div class="form-text small">A concise, punchy title that catches shoppers' attention.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Campaign Description</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Share details about what makes this deal special...">{{ old('description', $sale->description) }}</textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Discount Percentage (%)</label>
                    <div class="input-group">
                        <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $sale->discount_percentage) }}" min="1" max="99" class="form-control" placeholder="15">
                        <span class="input-group-text bg-light text-muted">% OFF</span>
                    </div>
                    <div class="form-text small">Used to automatically compute savings badges.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Custom Badge Label</label>
                    <input type="text" name="badge_label" value="{{ old('badge_label', $sale->badge_label) }}" class="form-control" placeholder="e.g. 20% OFF or HARVEST SPECIAL">
                    <div class="form-text small">Leave blank to use default computed percentage badge.</div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
            <h5 class="fw-bold text-dark mb-3">2. Timeframe & Active Status</h5>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $sale->start_date ? $sale->start_date->toDateString() : '') }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $sale->end_date ? $sale->end_date->toDateString() : '') }}" class="form-control" required>
                </div>
            </div>

            <div class="form-check form-switch p-0 pt-2">
                <div class="d-flex align-items-center gap-3">
                    <input class="form-check-input ms-0" type="checkbox" role="switch" id="isActiveSwitch" name="is_active" value="1" {{ old('is_active', $sale->is_active) ? 'checked' : '' }} style="width: 2.5rem; height: 1.25rem;">
                    <label class="form-check-label fw-semibold text-dark small" for="isActiveSwitch">
                        Active status (Turn off to pause without deleting)
                    </label>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
            <h5 class="fw-bold text-dark mb-3">3. Promotional Banner Imagery</h5>

            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Banner Image URL</label>
                <input type="url" name="banner_image" id="bannerImageUrlInput" value="{{ old('banner_image', $sale->banner_image) }}" class="form-control" placeholder="https://...">
                <div class="form-text small">Direct link to a high-resolution banner image.</div>
            </div>

            <div>
                <label class="form-label fw-semibold text-muted small d-block mb-2">Or Pick a Curated Farm Fresh Preset:</label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 banner-preset-btn" data-url="https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1200&q=80">
                        🍓 Fresh Harvest Berries
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 banner-preset-btn" data-url="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=1200&q=80">
                        🍅 Heirloom Tomatoes
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 banner-preset-btn" data-url="https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=1200&q=80">
                        🥬 Crisp Greens & Herbs
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 banner-preset-btn" data-url="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1200&q=80">
                        🌾 Golden Orchard Fields
                    </button>
                </div>
            </div>
        </div>

        @php
            $selectedProductIds = old('product_ids', $sale->products->pluck('id')->toArray());
        @endphp

        <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">4. Highlight Specific Products (Optional)</h5>
                    <small class="text-muted">Select specific items from your inventory to spotlight on this sale banner.</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="selectAllProductsBtn">
                    Select All
                </button>
            </div>

            @if($products->isEmpty())
                <div class="p-4 bg-light rounded-3 text-center text-muted small">
                    You don't have any products in your inventory yet. This sale will highlight your entire stall.
                </div>
            @else
                <div class="row g-2" style="max-height: 320px; overflow-y: auto;">
                    @foreach($products as $product)
                        <div class="col-md-6">
                            <label class="card card-custom p-2.5 h-100 border d-flex flex-row align-items-center gap-3 cursor-pointer" style="cursor: pointer;">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="form-check-input product-check-item ms-1" {{ in_array($product->id, $selectedProductIds) ? 'checked' : '' }}>
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-2 object-fit-cover flex-shrink-0" style="width: 44px; height: 44px;">
                                @else
                                    <div class="rounded-2 bg-light text-muted d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="bi bi-basket2"></i>
                                    </div>
                                @endif
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-dark small text-truncate">{{ $product->name }}</div>
                                    <div class="text-muted small font-mono-meta" style="font-size: 0.72rem;">${{ number_format($product->price, 2) }} / {{ $product->unit }}</div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center pt-2">
            <a href="{{ route('farmer.sales.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                Cancel
            </a>
            <button type="submit" class="btn btn-brand rounded-pill px-5 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Banner Presets
        document.querySelectorAll('.banner-preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                document.getElementById('bannerImageUrlInput').value = url;
            });
        });

        // Select all products
        const selectAllBtn = document.getElementById('selectAllProductsBtn');
        if (selectAllBtn) {
            let allSelected = false;
            selectAllBtn.addEventListener('click', function() {
                allSelected = !allSelected;
                document.querySelectorAll('.product-check-item').forEach(chk => chk.checked = allSelected);
                selectAllBtn.innerText = allSelected ? 'Deselect All' : 'Select All';
            });
        }
    });
</script>
@endsection
