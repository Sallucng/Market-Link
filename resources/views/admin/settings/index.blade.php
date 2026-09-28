@extends('layouts.admin')

@section('title', 'Platform Settings & Configurations')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono-meta">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active text-success" aria-current="page">System Settings</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark" style="letter-spacing: -0.025em;">Platform Settings & Configurations</h1>
            <p class="text-muted small mb-0">Configure operational parameters, pre-order constraints, vendor policies, and system-wide alerts.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill font-mono-meta">
                <i class="bi bi-shield-check me-1"></i> SRS §1.6 Compliant
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" id="platformSettingsForm">
        @csrf

        <div class="row g-4">
            <!-- CARD 1: Market & Pre-Order Rules -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-success bg-opacity-10 p-2.5 text-success d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Market & Pre-Order Rules</h5>
                            <small class="text-muted">Order cutoff deadlines and reservation boundaries</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Default Pre-Order Cutoff (Hours)</label>
                        <div class="input-group">
                            <input type="number" name="default_cutoff_hours" class="form-control" value="{{ $settings['default_cutoff_hours'] ?? 2 }}" min="1" max="72">
                            <span class="input-group-text bg-light text-muted small">Hours before market</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Platform-wide fallback cutoff time for farmer pre-order collection windows.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Advance Pre-Order Window (Days)</label>
                        <div class="input-group">
                            <input type="number" name="max_preorder_days" class="form-control" value="{{ $settings['max_preorder_days'] ?? 7 }}" min="1" max="30">
                            <span class="input-group-text bg-light text-muted small">Days ahead</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">How many days before market day shoppers may submit order reservations.</small>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="allowSameDay">Allow Same-Day Morning Pre-Orders</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Permits early morning orders before market opening cutoff.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="allowSameDay" name="allow_same_day_orders" value="1" {{ !empty($settings['allow_same_day_orders']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Minimum Platform Pre-Order Value ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">$</span>
                            <input type="number" step="0.50" name="min_platform_order" class="form-control" value="{{ $settings['min_platform_order'] ?? 0 }}" min="0">
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Set to 0 to disable minimum reservation cart limits.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Customer Order Cancellation Grace Period</label>
                        <div class="input-group">
                            <input type="number" name="order_cancellation_grace_hours" class="form-control" value="{{ $settings['order_cancellation_grace_hours'] ?? 4 }}" min="1" max="48">
                            <span class="input-group-text bg-light text-muted small">Hours before collection</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Window during which customers can self-cancel or modify pre-orders before stall packing.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Max Active Products Per Stall</label>
                        <div class="input-group">
                            <input type="number" name="max_active_listings_per_farmer" class="form-control" value="{{ $settings['max_active_listings_per_farmer'] ?? 50 }}" min="5" max="500">
                            <span class="input-group-text bg-light text-muted small">Items cap</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Listing cap to maintain high quality and prevent vendor clutter.</small>
                    </div>
                </div>
            </div>

            <!-- CARD 2: System Alerts & Automation -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-amber-500 bg-opacity-10 p-2.5 text-warning d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fef3c7;">
                            <i class="bi bi-bell fs-5 text-warning"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">System Alerts & Automation</h5>
                            <small class="text-muted">In-app notifications and threshold triggers</small>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="inAppNotif">In-App Notifications Engine</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Generate persistent in-app notifications for order updates and announcements.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="inAppNotif" name="enable_in_app_notifications" value="1" {{ !empty($settings['enable_in_app_notifications']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Customer Pickup Reminder Window</label>
                        <div class="input-group">
                            <input type="number" name="pickup_reminder_lead_hours" class="form-control" value="{{ $settings['pickup_reminder_lead_hours'] ?? 2 }}" min="1" max="24">
                            <span class="input-group-text bg-light text-muted small">Hours before pickup</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Lead time before customer collection slot when pickup reminder alert triggers.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Low-Stock Alert Threshold (Default)</label>
                        <div class="input-group">
                            <input type="number" name="low_stock_threshold_default" class="form-control" value="{{ $settings['low_stock_threshold_default'] ?? 5 }}" min="1" max="50">
                            <span class="input-group-text bg-light text-muted small">Units remaining</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Highlight items with amber badges when remaining quantity reaches this level.</small>
                    </div>

                    <div class="mb-0 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="adminDigest">Daily Admin Operations Digest</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Bundle pending reviews and vendor registrations in the admin dashboard summary.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="adminDigest" name="admin_email_alerts" value="1" {{ !empty($settings['admin_email_alerts']) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: Grower & Vendor Policies -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-2.5 text-primary d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-people fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Grower & Vendor Policies</h5>
                            <small class="text-muted">Onboarding verification and inventory compliance</small>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="autoApprove">Auto-Approve New Growers</label>
                                <span class="text-muted" style="font-size: 0.75rem;">If enabled, newly registered farmers are published immediately without manual admin approval.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="autoApprove" name="auto_approve_farmers" value="1" {{ !empty($settings['auto_approve_farmers']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="requireGps">Require Leaflet GPS Map Pin</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Growers must pin their stall location before showing up on the market map view.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="requireGps" name="require_stall_coordinates" value="1" {{ !empty($settings['require_stall_coordinates']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="allowCancel">Allow Farmers to Cancel Orders</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Enables farmers to cancel pre-orders with notice in cases of unexpected harvest shortages.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="allowCancel" name="allow_farmer_order_cancellation" value="1" {{ !empty($settings['allow_farmer_order_cancellation']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="stockTracking">Enforce Real-Time Stock Tracking</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Decrement inventory on order submission to prevent overselling.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="stockTracking" name="require_stock_tracking" value="1" {{ !empty($settings['require_stock_tracking']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-0 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="autoReviews">Auto-Publish Customer Reviews</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Immediately publish verified reviews. If disabled, reviews enter the moderation queue.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="autoReviews" name="auto_publish_reviews" value="1" {{ !empty($settings['auto_publish_reviews']) || !isset($settings['auto_publish_reviews']) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 4: Storefront Appearance & Alerts -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-danger bg-opacity-10 p-2.5 text-danger d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-broadcast fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Storefront Appearance & Alerts</h5>
                            <small class="text-muted">Branding, maintenance mode, currency, and AI features</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Platform Brand Title</label>
                        <input type="text" name="site_title" class="form-control" value="{{ $settings['site_title'] ?? 'MarketLink — Local Farmers Network' }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-dark">Public Support Email</label>
                            <input type="email" name="support_email" class="form-control" value="{{ $settings['support_email'] ?? 'support@marketlink.org' }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-dark">Inquiries Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '+1 (555) 345-6789' }}">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-dark">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] ?? '$' }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-dark">Local Market Tax (%)</label>
                            <input type="number" step="0.01" name="tax_rate_percent" class="form-control" value="{{ $settings['tax_rate_percent'] ?? '0.00' }}">
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="aiChatbotToggle">Enable AI Shopping Assistant</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Display floating AI chatbot widget on discovery pages (SRS §1.6).</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="aiChatbotToggle" name="enable_ai_chatbot" value="1" {{ !empty($settings['enable_ai_chatbot']) || !isset($settings['enable_ai_chatbot']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="maintMode">Platform Maintenance Advisory</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Show a subtle notification ribbon across headers informing users of scheduled maintenance.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="maintMode" name="maintenance_mode" value="1" {{ !empty($settings['maintenance_mode']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-danger bg-opacity-10 border border-danger-subtle">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-2">
                            <div>
                                <label class="form-check-label fw-semibold small text-danger mb-0 d-block" for="emergencyBroadcast">Emergency Market Broadcast</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Broadcast an urgent banner on all public storefront pages (weather, market relocated, etc.)</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="emergencyBroadcast" name="emergency_broadcast_active" value="1" {{ !empty($settings['emergency_broadcast_active']) ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label class="form-label small fw-semibold text-dark mb-1">Broadcast Message Text</label>
                            <textarea name="emergency_broadcast_message" class="form-control form-control-sm" rows="2">{{ $settings['emergency_broadcast_message'] ?? 'Weather Notice: Saturday outdoor stalls are moved inside Pavilion B due to light rain.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Action Bar -->
        <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i> Changes take effect immediately across all client sessions and pre-order validation flows.
            </span>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                <button type="submit" class="btn btn-brand px-4 py-2">
                    <i class="bi bi-floppy me-1"></i> Save Platform Configurations
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
