@extends('layouts.farmer')

@section('title', 'Customer Reviews & Feedback — MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none">Stall Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Customer Reviews</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-1">Reputation & Feedback</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Customer Ratings & Reviews</h2>
            <small class="text-muted">{{ $farmer->stall_name }} &bull; Respond directly to community feedback (SRS §1.6)</small>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="fs-4 fw-bold text-dark">
                    <i class="bi bi-star-fill text-warning me-1"></i> {{ number_format($avgRating, 1) }} / 5.0
                </div>
                <small class="text-muted">Based on {{ $totalReviews }} customer reviews</small>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        @if($reviews->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-star display-4 text-muted"></i>
                <h5 class="fw-bold mt-3 mb-1">No Reviews Yet</h5>
                <p class="text-muted small">Reviews submitted by customers after collecting orders will appear here.</p>
            </div>
        @else
            <div class="vstack gap-4">
                @foreach($reviews as $rev)
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $rev->customer->name ?? 'Verified Shopper' }}</h6>
                                <small class="text-muted">Order #{{ $rev->order->order_number ?? 'N/A' }} &bull; {{ $rev->created_at->format('M d, Y') }}</small>
                            </div>
                            <div class="text-warning">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi {{ $i <= $rev->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <p class="text-secondary small mb-3">"{{ $rev->comment }}"</p>

                        <!-- Existing Response -->
                        @if($rev->farmer_response)
                            <div class="p-3 bg-white rounded-3 border-start border-success border-3 mb-2 shadow-sm">
                                <strong class="text-success small d-block mb-1"><i class="bi bi-reply-fill me-1"></i>Your Response:</strong>
                                <p class="small text-dark mb-0">{{ $rev->farmer_response }}</p>
                            </div>
                        @else
                            <!-- Reply Form (SRS §1.6) -->
                            <form action="{{ route('farmer.reviews.respond', $rev->id) }}" method="POST" class="mt-2">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="farmer_response" class="form-control form-control-sm" placeholder="Write a polite response to {{ $rev->customer->name ?? 'this customer' }}..." required>
                                    <button type="submit" class="btn btn-brand btn-sm px-3">Reply</button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
