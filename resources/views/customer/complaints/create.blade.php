@extends('layouts.app')

@section('title', 'File a Farmer Complaint — MarketLink')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-success">Customer Portal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.complaints.index') }}" class="text-success">Complaints</a></li>
            <li class="breadcrumb-item active" aria-current="page">File Complaint</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                <div class="p-4 border-bottom bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold">
                            <i class="bi bi-shield-lock-fill me-1"></i> Confidential Report
                        </span>
                        <span class="badge bg-white text-secondary border px-2.5 py-1 rounded-pill small">
                            Admin Eyes Only
                        </span>
                    </div>
                    <h3 class="heading-serif fw-bold text-dark mb-1">File a Complaint About a Farmer</h3>
                    <p class="text-secondary small mb-0">
                        Help us maintain the highest standard of local agriculture and customer trust. All reports are strictly confidential.
                    </p>
                </div>

                <div class="p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.complaints.store') }}" method="POST">
                        @csrf

                        <!-- Confidentiality Box -->
                        <div class="alert alert-light border rounded-3 p-3 mb-4 d-flex gap-3 align-items-center">
                            <i class="bi bi-eye-slash-fill fs-3 text-danger flex-shrink-0"></i>
                            <div class="small text-secondary">
                                <strong class="text-dark">Privacy Assured:</strong> This report is delivered straight to the platform administrative moderation team. It is <strong>never displayed publicly</strong> on MarketLink or shared with other shoppers.
                            </div>
                        </div>

                        <!-- 1. Select Farmer -->
                        <div class="mb-4">
                            <label for="farmer_id" class="form-label fw-bold text-dark small text-uppercase font-mono-meta">
                                1. Select Farmer / Stall <span class="text-danger">*</span>
                            </label>
                            <select name="farmer_id" id="farmer_id" class="form-select form-select-lg rounded-3 @error('farmer_id') is-invalid @enderror" required>
                                <option value="" disabled {{ !old('farmer_id', $preselectedFarmer?->id) ? 'selected' : '' }}>-- Select Farmer Stall --</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}" {{ old('farmer_id', $preselectedFarmer?->id) == $farmer->id ? 'selected' : '' }}>
                                        {{ $farmer->stall_name }} ({{ $farmer->contact_person }}) &bull; {{ $farmer->market->name ?? 'Local Market' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('farmer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 2. Select Associated Pre-Order (Optional) -->
                        <div class="mb-4">
                            <label for="order_id" class="form-label fw-bold text-dark small text-uppercase font-mono-meta">
                                2. Associated Pre-Order (Optional)
                            </label>
                            <select name="order_id" id="order_id" class="form-select rounded-3 @error('order_id') is-invalid @enderror">
                                <option value="">-- No specific order linked / General stall issue --</option>
                                @foreach($customerOrders as $order)
                                    <option value="{{ $order->id }}" {{ old('order_id', $preselectedOrder?->id) == $order->id ? 'selected' : '' }}>
                                        Order #{{ $order->order_number }} &bull; {{ $order->farmer->stall_name }} &bull; {{ $order->pickup_date->format('M d, Y') }} (${{ number_format($order->total_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-muted">
                                If this complaint pertains to a pickup order you recently placed, selecting it helps administrators review details quickly.
                            </div>
                            @error('order_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 3. Complaint Category -->
                        <div class="mb-4">
                            <label for="complaint_type" class="form-label fw-bold text-dark small text-uppercase font-mono-meta">
                                3. Complaint Reason / Category <span class="text-danger">*</span>
                            </label>
                            <select name="complaint_type" id="complaint_type" class="form-select rounded-3 @error('complaint_type') is-invalid @enderror" required>
                                <option value="poor_quality" {{ old('complaint_type') == 'poor_quality' ? 'selected' : '' }}>🥦 Poor Quality / Expired / Damaged Produce</option>
                                <option value="unfulfilled_order" {{ old('complaint_type') == 'unfulfilled_order' ? 'selected' : '' }}>📦 Unfulfilled / Incomplete Order at Pickup</option>
                                <option value="pricing_issue" {{ old('complaint_type') == 'pricing_issue' ? 'selected' : '' }}>💵 Pricing / Billing Discrepancy at Stall</option>
                                <option value="unprofessional_conduct" {{ old('complaint_type') == 'unprofessional_conduct' ? 'selected' : '' }}>🗣️ Unprofessional / Inappropriate Stall Conduct</option>
                                <option value="inaccurate_listing" {{ old('complaint_type') == 'inaccurate_listing' ? 'selected' : '' }}>⚠️ Misleading Product Listing or Weight</option>
                                <option value="other" {{ old('complaint_type', 'other') == 'other' ? 'selected' : '' }}>📝 Other Issue / Platform Inconsistency</option>
                            </select>
                            @error('complaint_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 4. Subject Line -->
                        <div class="mb-4">
                            <label for="subject" class="form-label fw-bold text-dark small text-uppercase font-mono-meta">
                                4. Subject Summary <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   class="form-control rounded-3 @error('subject') is-invalid @enderror" 
                                   placeholder="e.g. Tomatoes were severely bruised and spoiled upon pickup" 
                                   value="{{ old('subject') }}" 
                                   required 
                                   maxlength="150">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 5. Detailed Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-dark small text-uppercase font-mono-meta">
                                5. Detailed Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="6" 
                                      class="form-control rounded-3 @error('description') is-invalid @enderror" 
                                      placeholder="Please provide specific details: dates, times, interactions at the stall, or product condition..." 
                                      required>{{ old('description') }}</textarea>
                            <div class="form-text small text-muted">Minimum 10 characters. Please be as objective and specific as possible.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('customer.complaints.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-shield-check me-1"></i> Submit Confidential Complaint
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
