@extends('layouts.app')

@section('title', 'Account Settings & Preferences — MarketLink')

@section('content')
<div class="container-xl py-4">

    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono-meta">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-decoration-none text-muted">Customer Dashboard</a></li>
            <li class="breadcrumb-item active text-success" aria-current="page">Preferences & Settings</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark" style="letter-spacing: -0.025em;">Account Preferences & Notifications</h1>
            <p class="text-muted small mb-0">Personalize your market pickup alerts, favorite venue, and organic dietary preferences.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('customer.settings.update') }}" method="POST" id="customerSettingsForm">
        @csrf

        <div class="row g-4">
            <!-- CARD 1: Contact & Profile Details -->
            <div class="col-lg-6">
                <div class="card bento-card p-4 bg-white border shadow-sm h-100">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-success bg-opacity-10 p-2.5 text-success d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-person-badge fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Shopper Profile & Contact</h5>
                            <small class="text-muted">Used for stall pickup identification and SMS notices</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        <small class="text-muted" style="font-size: 0.78rem;">Displayed on your order pickup slips.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Account Email</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                        <small class="text-muted" style="font-size: 0.78rem;">Primary email for pre-order digital receipts.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Mobile Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $user->contact_number) }}" placeholder="+1 (555) 000-0000">
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Used by farmers to text you when your basket is assembled.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Address (Neighborhood / Suburb)</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="e.g. 742 Evergreen Terrace, Apt 4B">{{ old('address', $user->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 2: Market Discovery & Dietary Preferences -->
            <div class="col-lg-6">
                <div class="card bento-card p-4 bg-white border shadow-sm h-100">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-2.5 text-primary d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-geo-alt fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Preferred Venue & Pickup Alerts</h5>
                            <small class="text-muted">Default filtering and collection reminders</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Preferred Farmers Market Plaza</label>
                        <select name="preferred_market_id" class="form-select">
                            <option value="">No Default (Show All Plazas)</option>
                            @foreach($markets as $m)
                                <option value="{{ $m->id }}" {{ ($preferences['preferred_market_id'] ?? null) == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }} ({{ $m->city }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.78rem;">Pre-selects this venue when searching fresh harvests.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Order Pickup Reminder Alert</label>
                        <select name="pickup_reminder_timing" class="form-select">
                            <option value="1h" {{ ($preferences['pickup_reminder_timing'] ?? '2h') == '1h' ? 'selected' : '' }}>1 Hour Before Collection Slot</option>
                            <option value="2h" {{ ($preferences['pickup_reminder_timing'] ?? '2h') == '2h' ? 'selected' : '' }}>2 Hours Before Collection Slot (Recommended)</option>
                            <option value="morning" {{ ($preferences['pickup_reminder_timing'] ?? '') == 'morning' ? 'selected' : '' }}>Morning of Market Day (08:00 AM)</option>
                            <option value="none" {{ ($preferences['pickup_reminder_timing'] ?? '') == 'none' ? 'selected' : '' }}>Do Not Remind Me</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.78rem;">Timing for email & in-app reminder before your scheduled pickup window.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark mb-2">Dietary & Quality Tags</label>
                        @php
                            $userDiet = $preferences['dietary_preferences'] ?? [];
                            $dietOptions = [
                                'organic' => 'Certified Organic',
                                'pesticide_free' => 'Pesticide-Free',
                                'heirloom' => 'Heirloom Varieties',
                                'non_gmo' => 'Non-GMO Verified',
                                'gluten_free' => 'Gluten-Free',
                                'dairy_free' => 'Dairy-Free / Vegan',
                            ];
                        @endphp
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($dietOptions as $key => $label)
                                <label class="btn btn-sm {{ in_array($key, $userDiet) ? 'btn-success text-white' : 'btn-outline-secondary' }} rounded-pill d-inline-flex align-items-center gap-1.5 px-3 py-1.5 cursor-pointer">
                                    <input type="checkbox" name="dietary_preferences[]" value="{{ $key }}" class="d-none" {{ in_array($key, $userDiet) ? 'checked' : '' }} onchange="this.parentElement.classList.toggle('btn-success'); this.parentElement.classList.toggle('text-white'); this.parentElement.classList.toggle('btn-outline-secondary');">
                                    <i class="bi bi-tag-fill small"></i>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small class="text-muted mt-2 d-block" style="font-size: 0.78rem;">Highlight items that match your dietary standards.</small>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold small text-dark">Preferred Pickup Window Slot</label>
                        <select name="default_pickup_window_preference" class="form-select form-select-sm">
                            <option value="early_morning" {{ ($preferences['default_pickup_window_preference'] ?? '') == 'early_morning' ? 'selected' : '' }}>Early Morning (Opening - 10:00 AM)</option>
                            <option value="midday" {{ ($preferences['default_pickup_window_preference'] ?? '') == 'midday' ? 'selected' : '' }}>Midday (10:30 AM - 01:00 PM)</option>
                            <option value="afternoon" {{ ($preferences['default_pickup_window_preference'] ?? '') == 'afternoon' ? 'selected' : '' }}>Afternoon (01:00 PM - Closing)</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.78rem;">Pre-populates your preferred collection slot at stall checkout.</small>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold small text-dark">Allergy & Dietary Notes for Farmers</label>
                        <textarea name="allergy_notes" class="form-control form-control-sm" rows="2" placeholder="e.g. Severe peanut allergy; please ensure clean handling bags.">{{ $preferences['allergy_notes'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 3: Notification Channels & Privacy -->
            <div class="col-12">
                <div class="card bento-card p-4 bg-white border shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-amber-500 bg-opacity-10 p-2.5 text-warning d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fef3c7;">
                            <i class="bi bi-bell-fill fs-5 text-warning"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Notification Triggers & Privacy Options</h5>
                            <small class="text-muted">Manage communication frequency and public feedback visibility</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="small fw-bold text-dark">Order Confirmed Alert</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="notify_email_order_accepted" value="1" {{ !empty($preferences['notify_email_order_accepted']) || !isset($preferences['notify_email_order_accepted']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.76rem;">Email notice when the farmer confirms your reservation.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="small fw-bold text-dark">Ready for Pickup Alert</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="notify_email_ready_pickup" value="1" {{ !empty($preferences['notify_email_ready_pickup']) || !isset($preferences['notify_email_ready_pickup']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.76rem;">Instant notification when stall marks order packed.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="small fw-bold text-dark">SMS Check-in Alerts</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="notify_sms_ready" value="1" {{ !empty($preferences['notify_sms_ready']) || !isset($preferences['notify_sms_ready']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.76rem;">Receive text reminder when approaching market plaza.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="small fw-bold text-dark">Favorite Farm Updates</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="notify_farm_updates" value="1" {{ !empty($preferences['notify_farm_updates']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.76rem;">Alerts when saved growers post weekly new arrivals.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="small fw-bold text-dark">Restock & Sold-Out Alerts</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="notify_restock_favorites" value="1" {{ !empty($preferences['notify_restock_favorites']) || !isset($preferences['notify_restock_favorites']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.76rem;">Get notified when previously sold-out items return to stock (SRS §1.6).</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="small fw-bold text-dark">Anonymous Reviews</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="display_review_anonymously" value="1" {{ !empty($preferences['display_review_anonymously']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.76rem;">Mask your real name as 'Verified Shopper' on product reviews.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="small fw-bold text-dark">Retain Order History</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="auto_save_orders" value="1" {{ !empty($preferences['auto_save_orders']) || !isset($preferences['auto_save_orders']) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.76rem;">Save item receipts and enable 1-click re-ordering.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Action Bar -->
        <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            <span class="text-muted small">
                <i class="bi bi-shield-check me-1 text-success"></i> Preferences are synchronized with your account and apply across checkout.
            </span>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                <button type="submit" class="btn btn-brand px-4 py-2">
                    <i class="bi bi-floppy me-1"></i> Save Account Preferences
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
