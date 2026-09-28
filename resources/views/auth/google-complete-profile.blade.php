@extends('layouts.app')

@section('title', 'Complete Your Profile — MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-custom p-4 p-md-5 bg-white border-0 shadow-sm">
                <div class="text-center mb-4">
                    <span class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-person-check-fill fs-3"></i>
                    </span>
                    <h3 class="heading-serif fw-bold text-dark">Almost Done!</h3>
                    <p class="text-muted small">Your email is verified by Google. Add your contact information to finish setting up your account.</p>
                </div>

                <!-- Google Account Card -->
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-4">
                    @if(!empty($googleData['avatar']))
                        <img src="{{ $googleData['avatar'] }}" alt="{{ $googleData['name'] }}" class="rounded-circle shadow-sm" width="46" height="46">
                    @else
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 46px; height: 46px; font-size: 1.2rem;">
                            {{ strtoupper(substr($googleData['name'], 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-grow-1 text-truncate">
                        <div class="fw-bold text-dark text-truncate">{{ $googleData['name'] }}</div>
                        <div class="small text-muted d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="text-truncate">{{ $googleData['email'] }}</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                <i class="bi bi-shield-fill-check me-0.5"></i> Google Verified
                            </span>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('auth.google.complete.submit') }}" method="POST">
                    @csrf

                    <!-- Role Selection: Customer or Farmer ONLY -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary d-block">Select Account Role:</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleCustomer" value="customer" {{ old('role', $googleData['role'] ?? 'customer') === 'customer' ? 'checked' : '' }} onchange="toggleFarmerFields()">
                                <label class="btn btn-outline-success w-100 py-2.5 d-flex flex-column align-items-center" for="roleCustomer">
                                    <i class="bi bi-basket2 fs-4 mb-1"></i>
                                    <strong>Customer</strong>
                                    <span class="small text-muted" style="font-size: 0.75rem;">Shop & Pre-order</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleFarmer" value="farmer" {{ old('role', $googleData['role'] ?? '') === 'farmer' ? 'checked' : '' }} onchange="toggleFarmerFields()">
                                <label class="btn btn-outline-success w-100 py-2.5 d-flex flex-column align-items-center" for="roleFarmer">
                                    <i class="bi bi-shop fs-4 mb-1"></i>
                                    <strong>Farmer / Stall</strong>
                                    <span class="small text-muted" style="font-size: 0.75rem;">Sell Farm Produce</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Farmer Stall Name (Conditional) -->
                    <div id="farmerFields" class="mb-3 p-3 bg-light rounded-3 border" style="display: {{ old('role', $googleData['role'] ?? '') === 'farmer' ? 'block' : 'none' }};">
                        <label class="form-label small fw-semibold text-dark">Stall or Farm Business Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-shop-window text-muted"></i></span>
                            <input type="text" name="stall_name" value="{{ old('stall_name') }}" class="form-control border-start-0" placeholder="e.g. Hilltop Organic Orchards">
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle text-success me-1"></i> Farmer stalls undergo swift admin review before produce is listed publicly.
                        </small>
                    </div>

                    <!-- Desired Username -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-at text-muted"></i></span>
                            <input type="text" name="username" value="{{ old('username', $suggestedUsername) }}" class="form-control border-start-0" placeholder="username" required>
                        </div>
                        <small class="text-muted">Unique handle for your MarketLink profile</small>
                    </div>

                    <!-- Contact Phone (SRS §1.6 requirement for pickup communication) -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Contact Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                            <input type="tel" name="contact_number" value="{{ old('contact_number') }}" class="form-control border-start-0" placeholder="+1 (555) 000-0000" required autofocus>
                        </div>
                        <small class="text-muted">Used by farmers & customers to coordinate pickup orders</small>
                    </div>

                    <!-- Address / Neighborhood -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Physical Address or Neighborhood</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
                            <input type="text" name="address" value="{{ old('address') }}" class="form-control border-start-0" placeholder="e.g. 120 Harbor View Road, Waterfront" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-pill fw-semibold shadow-sm mb-3">
                        <i class="bi bi-check-circle-fill me-1"></i> Finish Setup & Enter MarketLink
                    </button>

                    <div class="text-center small text-secondary">
                        Want to use another account? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Cancel & Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleFarmerFields() {
        const isFarmer = document.getElementById('roleFarmer').checked;
        document.getElementById('farmerFields').style.display = isFarmer ? 'block' : 'none';
    }
</script>
@endsection
