@extends('layouts.app')

@section('title', 'Create Account — MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-custom p-4 p-md-5 bg-white border-0 shadow-sm">
                <div class="text-center mb-4">
                    <span class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-person-plus fs-3"></i>
                    </span>
                    <h3 class="heading-serif fw-bold text-dark">Join MarketLink</h3>
                    <p class="text-muted small">Register as a local customer or as a farmers-market vendor stall</p>
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

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <!-- Role Selection (SRS §1.6) -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary d-block">I am registering as:</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleCustomer" value="customer" checked onchange="toggleFarmerFields()">
                                <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center" for="roleCustomer">
                                    <i class="bi bi-basket2 fs-4 mb-1"></i>
                                    <strong>Customer / Shopper</strong>
                                    <span class="small" style="font-size: 0.75rem;">Pre-order for pickup</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleFarmer" value="farmer" onchange="toggleFarmerFields()">
                                <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center" for="roleFarmer">
                                    <i class="bi bi-shop fs-4 mb-1"></i>
                                    <strong>Farmer / Vendor</strong>
                                    <span class="small" style="font-size: 0.75rem;">Manage stall & weekly stock</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Farmer Stall Name (Conditional) -->
                    <div id="farmerFields" class="mb-3 p-3 bg-light rounded-3 border" style="display: none;">
                        <label class="form-label small fw-semibold text-dark">Stall or Farm Business Name</label>
                        <input type="text" name="stall_name" value="{{ old('stall_name') }}" class="form-control" placeholder="e.g. Hilltop Honey & Orchard">
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-shield-lock text-warning me-1"></i> <strong>Note:</strong> Farmer accounts require Admin approval before listings go live (SRS §1.6).
                        </small>
                    </div>

                    <!-- Full Name & Username -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Jane Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="janedoe" required>
                        </div>
                    </div>

                    <!-- Email & Contact Phone (SRS §1.6 mandatory fields) -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="jane@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Contact Phone</label>
                            <input type="tel" name="contact_number" value="{{ old('contact_number') }}" class="form-control" placeholder="+1 (555) 000-0000" required>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Address / Location</label>
                        <textarea name="address" rows="2" class="form-control" placeholder="Street address or neighborhood" required>{{ old('address') }}</textarea>
                    </div>

                    <!-- Password & Confirmation -->
                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 rounded-pill fw-semibold shadow-sm mb-3">
                        Create Account
                    </button>

                    <!-- Social Registration Divider -->
                    <div class="d-flex align-items-center my-3 text-muted">
                        <hr class="flex-grow-1 my-0 border-secondary-subtle">
                        <span class="px-3 small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">or sign up with</span>
                        <hr class="flex-grow-1 my-0 border-secondary-subtle">
                    </div>

                    <!-- Google Sign Up Button (Customer & Farmer ONLY) -->
                    <a id="googleRegisterBtn" href="{{ route('auth.google', ['role' => 'customer']) }}" class="btn btn-outline-secondary w-100 py-2 rounded-pill fw-semibold shadow-sm mb-2 d-flex align-items-center justify-content-center gap-2 bg-white" style="border-color: #d1d5db;">
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.18 3.665-9.15z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.27 21.36 7.36 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.16 0 9.98 0 12s.46 3.84 1.26 5.42l4.02-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.27 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span id="googleRoleText">Continue with Google as Customer</span>
                    </a>
                    <div class="text-center mb-3">
                        <small class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-shield-check text-success me-1"></i>For Customer & Farmer accounts</small>
                    </div>

                    <div class="text-center small text-secondary">
                        Already have an account? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Sign In</a>
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
        const googleBtn = document.getElementById('googleRegisterBtn');
        const roleText = document.getElementById('googleRoleText');
        if (googleBtn && roleText) {
            googleBtn.href = isFarmer ? "{{ route('auth.google', ['role' => 'farmer']) }}" : "{{ route('auth.google', ['role' => 'customer']) }}";
            roleText.textContent = isFarmer ? 'Continue with Google as Farmer' : 'Continue with Google as Customer';
        }
    }
</script>
@endsection
