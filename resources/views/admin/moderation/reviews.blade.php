@extends('layouts.admin')

@section('title', 'Review Moderation — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Review Moderation</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill mb-1">Content Moderation</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Customer Review Moderation</h2>
            <small class="text-muted">Review and remove inappropriate feedback that violates community guidelines (SRS §1.6)</small>
        </div>

        <div class="btn-group">
            <a href="{{ route('admin.moderation.reviews') }}" class="btn btn-sm btn-dark active">Reviews Moderation</a>
            <a href="{{ route('admin.moderation.products') }}" class="btn btn-sm btn-outline-dark">Product Listings</a>
        </div>
    </div>

    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        @if($reviews->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-chat-square-text text-muted display-4"></i>
                <p class="text-muted mt-2">No reviews available to moderate.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Reviewer</th>
                            <th>Stall / Farmer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th class="text-end">Moderation Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $rev)
                            <tr>
                                <td>
                                    <div class="fw-bold small text-dark">{{ $rev->customer->name ?? 'Shopper' }}</div>
                                    <small class="text-muted">{{ $rev->customer->email ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">{{ $rev->farmer->stall_name ?? 'Farmer Stall' }}</span>
                                </td>
                                <td>
                                    <span class="text-warning small">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="bi {{ $i <= $rev->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-secondary font-monospace" style="max-width: 320px; display: inline-block;">
                                        "{{ $rev->comment }}"
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $rev->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.moderation.reviews.delete', $rev->id) }}" method="POST" onsubmit="return confirm('Remove this review from the platform?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
