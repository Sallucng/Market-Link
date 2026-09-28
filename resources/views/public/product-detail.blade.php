@extends('layouts.app')

@section('title', $product->name . ' — MarketLink')

@section('styles')
<style>
    .product-zoom-container {
        position: relative;
        cursor: crosshair;
        overflow: hidden;
        border-radius: 12px;
        background: #f8fafc;
    }

    #mainProductImg {
        transition: opacity 0.15s ease;
        user-select: none;
        -webkit-user-drag: none;
    }

    /* Daraz-Style Magnifier Lens */
    .product-zoom-lens {
        position: absolute;
        display: none;
        pointer-events: none;
        cursor: crosshair;
        background-color: rgba(15, 23, 42, 0.38); /* Daraz dark translucent tint */
        border: 1.5px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        border-radius: 4px;
        z-index: 25;
    }

    /* Daraz-Style Zoom Preview Popout Window */
    .product-zoom-preview {
        position: absolute;
        top: 0;
        left: calc(100% + 24px);
        width: 100%;
        height: 100%;
        min-height: 440px;
        background-color: #ffffff;
        background-repeat: no-repeat;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.28), 0 0 0 1px rgba(0, 0, 0, 0.04);
        z-index: 1060;
        display: none;
        pointer-events: none;
        overflow: hidden;
    }

    .zoom-indicator-hint {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        font-size: 0.78rem;
        font-weight: 500;
        color: #334155;
        padding: 5px 14px;
        border-radius: 20px;
        pointer-events: none;
        transition: opacity 0.2s ease;
        z-index: 10;
        white-space: nowrap;
    }

    .product-zoom-container:hover .zoom-indicator-hint {
        opacity: 0;
    }

    .thumb-btn {
        border: 2px solid transparent !important;
        opacity: 0.65;
        padding: 3px;
        background: #ffffff;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .thumb-btn:hover {
        opacity: 1;
        transform: translateY(-2px);
    }
    .thumb-btn.active {
        border-color: #1b4332 !important; /* Daraz/MarketLink highlight */
        opacity: 1;
        box-shadow: 0 4px 10px rgba(27, 67, 50, 0.2);
    }

    @media (max-width: 991.98px) {
        .product-zoom-lens,
        .product-zoom-preview {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-success">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-5 position-relative" id="productDetailsRow">
        <!-- Image Column with Daraz Zoom -->
        <div class="col-lg-6 position-relative">
            <div class="card card-custom p-2 bg-white border-0 shadow-sm overflow-hidden position-relative">
                <div class="product-zoom-container" id="productZoomContainer">
                    <img id="mainProductImg"
                         src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80' }}" 
                         alt="{{ $product->name }}" 
                         class="img-fluid rounded-3 w-100 d-block" 
                         style="max-height: 440px; height: 440px; object-fit: cover;">
                    
                    <!-- Daraz Rectangular Zoom Lens -->
                    <div id="productZoomLens" class="product-zoom-lens"></div>

                    <!-- Roll-over Hint Badge -->
                    <div class="zoom-indicator-hint d-none d-lg-flex align-items-center">
                        <i class="bi bi-zoom-in me-1 text-success"></i> Roll over image to zoom in
                    </div>
                </div>

                <!-- Fullscreen / Lightbox Modal Trigger -->
                <button type="button" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow-xs border" id="btnOpenLightbox" title="Click to view full screen">
                    <i class="bi bi-arrows-fullscreen"></i>
                </button>
            </div>

            <!-- Thumbnail Selector Strip (Daraz-style) -->
            @php
                $mainImg = $product->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80';
                $farmerCover = $product->farmer->cover_image_url ?: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80';
                $farmerAvatar = $product->farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=800&q=80';
                $galleryThumbs = [
                    ['url' => $mainImg, 'label' => 'Harvest View'],
                    ['url' => $farmerCover, 'label' => 'Farm Field'],
                    ['url' => $farmerAvatar, 'label' => 'Grower Stall'],
                ];
            @endphp
            <div class="d-flex align-items-center gap-2 mt-3 overflow-auto py-1" id="productThumbnailsStrip">
                @foreach($galleryThumbs as $idx => $thumb)
                    <button type="button" 
                            class="thumb-btn rounded-3 {{ $idx === 0 ? 'active' : '' }}" 
                            data-img-src="{{ $thumb['url'] }}"
                            title="{{ $thumb['label'] }}"
                            style="width: 64px; height: 64px; cursor: pointer;">
                        <img src="{{ $thumb['url'] }}" alt="{{ $thumb['label'] }}" class="w-100 h-100 rounded-2" style="object-fit: cover;">
                    </button>
                @endforeach
            </div>

            <!-- The Daraz Zoom Preview Window (Pops up over / next to the right pane) -->
            <div id="productZoomPreview" class="product-zoom-preview"></div>
        </div>

        <!-- Product Details Column -->
        <div class="col-lg-6">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-light text-dark border">{{ $product->category->name }}</span>
                    @auth
                        @if(Auth::user()->isCustomer())
                            @php
                                $isProdFav = \App\Models\Favorite::where('customer_id', Auth::id())
                                    ->where('item_type', 'product')
                                    ->where('item_id', $product->id)
                                    ->exists();
                            @endphp
                            <form action="{{ route('customer.favorites.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="item_type" value="product">
                                <input type="hidden" name="item_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-sm {{ $isProdFav ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 py-1">
                                    <i class="bi {{ $isProdFav ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                                    {{ $isProdFav ? 'Favorited' : 'Save Favorite' }}
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
                <h1 class="heading-serif fw-bold text-dark mb-2">{{ $product->name }}</h1>

                <div class="d-flex align-items-baseline gap-2 mb-3">
                    <span class="display-6 fw-bold text-success">${{ number_format($product->price, 2) }}</span>
                    <span class="text-muted">/ {{ $product->unit }}</span>
                </div>

                <p class="text-secondary mb-4">{{ $product->description ?: 'Fresh, locally harvested products straight from community growers.' }}</p>

                <!-- Stock & Pickup Status -->
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-dark">Available Stock This Week:</span>
                        @if($product->is_sold_out || $product->stock_quantity <= 0)
                            <span class="badge bg-danger">Sold Out</span>
                        @else
                            <span class="badge bg-success">{{ $product->stock_quantity }} {{ $product->unit }} Available</span>
                        @endif
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i> Pre-orders are reserved against live inventory and held for you at the stall.
                    </small>
                </div>

                <!-- Add to Pre-Order Cart Form -->
                @if(!$product->is_sold_out && $product->stock_quantity > 0)
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-4">
                                <label class="small text-muted fw-semibold mb-1">Quantity ({{ $product->unit }}):</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control text-center">
                            </div>
                            <div class="col-sm-8 pt-sm-4">
                                <button type="submit" class="btn btn-brand w-100 py-2 rounded-pill">
                                    <i class="bi bi-cart-plus me-1"></i> Pre-Order for Pickup
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <button class="btn btn-secondary w-100 py-2 rounded-pill mb-4" disabled>
                        Currently Sold Out
                    </button>
                @endif

                <!-- Farmer / Stall Quick Info Card (SRS §1.5) -->
                <div class="border rounded-3 p-3 mt-auto bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="{{ $product->farmer->image_url ?: 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=150&q=80' }}" 
                             alt="{{ $product->farmer->stall_name }}" 
                             class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0">{{ $product->farmer->stall_name }}</h6>
                            <small class="text-muted"><i class="bi bi-geo-alt text-success me-1"></i>{{ $product->farmer->market->name ?? 'Local Market' }} &bull; {{ $product->farmer->address }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                            <form action="{{ route('customer.messages.start') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="farmer_id" value="{{ $product->farmer_id }}">
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1" title="Message Stall about this product">
                                    <i class="bi bi-chat-dots-fill me-1"></i><span>Chat</span>
                                </button>
                            </form>
                            <a href="{{ route('farmers.show', $product->farmer->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                                Stall Profile
                            </a>
                        </div>
                    </div>
                    @if($product->farmer->latitude && $product->farmer->longitude)
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                            <span class="text-muted"><i class="bi bi-pin-map text-danger me-1"></i>Pickup Point (GPS {{ number_format($product->farmer->latitude, 3) }}, {{ number_format($product->farmer->longitude, 3) }})</span>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $product->farmer->latitude }},{{ $product->farmer->longitude }}" 
                               target="_blank" rel="noopener" class="text-success text-decoration-none fw-semibold">
                                <i class="bi bi-arrow-up-right-square me-1"></i>Get Directions
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews & Ratings Section (SRS §1.6) -->
    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-5">
        <h4 class="heading-serif fw-bold text-dark mb-3">Customer Ratings & Reviews</h4>

        @forelse($product->reviews as $review)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="fw-bold text-dark">{{ $review->customer->name }}</div>
                    <div class="text-warning small">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                </div>
                <p class="text-secondary small mb-2">{{ $review->comment }}</p>

                @if($review->farmer_response)
                    <div class="bg-light p-2 rounded small ms-3 border-start border-success border-3">
                        <strong class="text-success">{{ $product->farmer->stall_name }} (Farmer):</strong>
                        <span class="text-muted">{{ $review->farmer_response }}</span>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-muted small mb-0">No reviews yet for this harvest. Verified customers can leave reviews after pickup!</p>
        @endforelse
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <h4 class="heading-serif fw-bold text-dark mb-3">More in {{ $product->category->name }}</h4>
        <div class="row g-4">
            @foreach($relatedProducts as $rel)
                <div class="col-md-3">
                    <div class="card card-custom h-100 bg-white p-3">
                        <img src="{{ $rel->image_url ?: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=80' }}" 
                             class="rounded mb-2" style="height: 140px; object-fit: cover;">
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('products.show', $rel->id) }}" class="text-dark text-decoration-none">
                                {{ $rel->name }}
                            </a>
                        </h6>
                        <div class="text-success fw-bold small mb-2">${{ number_format($rel->price, 2) }} / {{ $rel->unit }}</div>
                        <a href="{{ route('products.show', $rel->id) }}" class="btn btn-sm btn-brand-outline rounded-pill mt-auto">View Item</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <!-- Fullscreen Lightbox Modal (Click to expand) -->
    <div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm rounded-circle shadow" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="lightboxImg" src="" class="img-fluid rounded-3 shadow-lg" style="max-height: 82vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('productZoomContainer');
    const mainImg = document.getElementById('mainProductImg');
    const lens = document.getElementById('productZoomLens');
    const preview = document.getElementById('productZoomPreview');
    const thumbBtns = document.querySelectorAll('.thumb-btn');

    if (!container || !mainImg || !lens || !preview) return;

    // Refresh background image of the zoom preview
    function updatePreviewImage() {
        const currentSrc = mainImg.currentSrc || mainImg.src;
        preview.style.backgroundImage = `url("${currentSrc}")`;
    }

    if (mainImg.complete) {
        updatePreviewImage();
    } else {
        mainImg.addEventListener('load', updatePreviewImage);
    }

    // Switch thumbnail selection
    thumbBtns.forEach(btn => {
        function activateThumb() {
            thumbBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const newSrc = btn.getAttribute('data-img-src');
            if (newSrc && mainImg.src !== newSrc) {
                mainImg.style.opacity = '0.5';
                const temp = new Image();
                temp.onload = function() {
                    mainImg.src = newSrc;
                    mainImg.style.opacity = '1';
                    updatePreviewImage();
                };
                temp.src = newSrc;
            }
        }
        btn.addEventListener('click', activateThumb);
        btn.addEventListener('mouseenter', activateThumb);
    });

    const zoomLevel = 2.6; // Daraz zoom magnification ratio

    function onMouseMove(e) {
        if (window.innerWidth < 992) {
            lens.style.display = 'none';
            preview.style.display = 'none';
            return;
        }

        const rect = mainImg.getBoundingClientRect();
        const clientX = e.clientX;
        const clientY = e.clientY;

        // Check if inside bounds
        if (clientX < rect.left || clientX > rect.right || clientY < rect.top || clientY > rect.bottom) {
            lens.style.display = 'none';
            preview.style.display = 'none';
            return;
        }

        lens.style.display = 'block';
        preview.style.display = 'block';

        const previewRect = preview.getBoundingClientRect();
        const lensWidth = previewRect.width / zoomLevel;
        const lensHeight = previewRect.height / zoomLevel;

        lens.style.width = lensWidth + 'px';
        lens.style.height = lensHeight + 'px';

        const x = clientX - rect.left;
        const y = clientY - rect.top;

        let lensLeft = x - (lensWidth / 2);
        let lensTop = y - (lensHeight / 2);

        // Constrain lens within image box
        if (lensLeft < 0) lensLeft = 0;
        if (lensTop < 0) lensTop = 0;
        if (lensLeft > rect.width - lensWidth) lensLeft = rect.width - lensWidth;
        if (lensTop > rect.height - lensHeight) lensTop = rect.height - lensHeight;

        lens.style.left = lensLeft + 'px';
        lens.style.top = lensTop + 'px';

        // Zoom preview background sizing and positioning
        const bgWidth = rect.width * zoomLevel;
        const bgHeight = rect.height * zoomLevel;
        preview.style.backgroundSize = `${bgWidth}px ${bgHeight}px`;

        const bgX = -(lensLeft * zoomLevel);
        const bgY = -(lensTop * zoomLevel);
        preview.style.backgroundPosition = `${bgX}px ${bgY}px`;
    }

    container.addEventListener('mousemove', onMouseMove);
    container.addEventListener('mouseenter', function (e) {
        if (window.innerWidth >= 992) {
            updatePreviewImage();
            lens.style.display = 'block';
            preview.style.display = 'block';
            onMouseMove(e);
        }
    });

    container.addEventListener('mouseleave', function () {
        lens.style.display = 'none';
        preview.style.display = 'none';
    });

    // Lightbox modal handler
    const btnLightbox = document.getElementById('btnOpenLightbox');
    const lightboxModalEl = document.getElementById('imageLightboxModal');
    const lightboxImg = document.getElementById('lightboxImg');

    if (btnLightbox && lightboxModalEl && lightboxImg) {
        const modal = new bootstrap.Modal(lightboxModalEl);
        btnLightbox.addEventListener('click', function () {
            lightboxImg.src = mainImg.src;
            modal.show();
        });
        container.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                lightboxImg.src = mainImg.src;
                modal.show();
            }
        });
    }
});
</script>
@endsection

