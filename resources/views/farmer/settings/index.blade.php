@extends('layouts.farmer')

@section('title', 'Stall Configurations & Operational Policies')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono-meta">
            <li class="breadcrumb-item"><a href="{{ route('farmer.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active text-success" aria-current="page">Stall Settings</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark" style="letter-spacing: -0.025em;">Stall Operations & Configurations</h1>
            <p class="text-muted small mb-0">Customize pre-order deadlines, automated acceptance, inventory alerts, and stall vacation status.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge {{ ($settings['stall_open'] ?? true) ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ ($settings['stall_open'] ?? true) ? 'text-success' : 'text-secondary' }} border px-3 py-2 rounded-pill font-mono-meta">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i> {{ ($settings['stall_open'] ?? true) ? 'Stall Open & Accepting Pre-Orders' : 'Stall Paused' }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('farmer.settings.update') }}" method="POST" id="farmerSettingsForm">
        @csrf

        <div class="row g-4">
            <!-- CARD 1: Pre-Order Rules & Deadlines -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-success bg-opacity-10 p-2.5 text-success d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Pre-Order Rules & Cutoff</h5>
                            <small class="text-muted">Set when order books close before market day</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Order Cutoff Deadline</label>
                        <select name="cutoff_hours" class="form-select">
                            <option value="1" {{ ($farmer->cutoff_hours == 1) ? 'selected' : '' }}>1 Hour Before Market Opens</option>
                            <option value="2" {{ ($farmer->cutoff_hours == 2) ? 'selected' : '' }}>2 Hours Before Market Opens (Recommended)</option>
                            <option value="4" {{ ($farmer->cutoff_hours == 4) ? 'selected' : '' }}>4 Hours Before Market Opens</option>
                            <option value="12" {{ ($farmer->cutoff_hours == 12) ? 'selected' : '' }}>12 Hours Before (Evening Prior)</option>
                            <option value="24" {{ ($farmer->cutoff_hours == 24) ? 'selected' : '' }}>24 Hours Before (1 Full Day Ahead)</option>
                            <option value="48" {{ ($farmer->cutoff_hours == 48) ? 'selected' : '' }}>48 Hours Before (2 Days Ahead)</option>
                        </select>
                        <small class="text-muted" style="font-size: 0.78rem;">Orders submitted after this cutoff are locked until the next market date.</small>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="autoAccept">Auto-Accept Pre-Orders</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Automatically advance incoming reservations from 'Placed' to 'Accepted'.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="autoAccept" name="auto_accept_orders" value="1" {{ !empty($settings['auto_accept_orders']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Max Daily Pre-Orders Capacity</label>
                        <div class="input-group">
                            <input type="number" name="max_daily_orders" class="form-control" value="{{ $settings['max_daily_orders'] ?? 50 }}" min="5" max="500">
                            <span class="input-group-text bg-light text-muted small">Orders / Day</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Caps simultaneous pre-orders per market day to prevent stall packing bottlenecks.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Stall Minimum Reservation ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">$</span>
                            <input type="number" step="0.50" name="min_order_amount" class="form-control" value="{{ $settings['min_order_amount'] ?? 0 }}" min="0">
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Optional minimum basket amount before customer can pre-order from your stall (0 to disable).</small>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="allowNotes">Allow Customer Packing Notes</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Permits shoppers to add ripe preference notes (e.g. 'greener bananas', 'firm tomatoes').</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="allowNotes" name="allow_customer_notes" value="1" {{ !empty($settings['allow_customer_notes']) || !isset($settings['allow_customer_notes']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="allowSubs">Allow Produce Substitutions</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Customer permits you to substitute equal/higher quality harvest if an item is picked out.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="allowSubs" name="allow_substitutions" value="1" {{ !empty($settings['allow_substitutions']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Eco-Packaging & Bag Policy Note</label>
                        <textarea name="eco_packaging_note" class="form-control form-control-sm" rows="2" placeholder="e.g. We pack in recyclable kraft cartons. Please bring your reusable market tote!">{{ $settings['eco_packaging_note'] ?? 'We pack in recyclable kraft cartons. Please bring your reusable market tote!' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CARD 2: Pickup Windows & Intervals -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-2.5 text-primary d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-calendar-range fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Stall Pickup Time Windows</h5>
                            <small class="text-muted">Collection time slots presented to customers during checkout</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Configured Pickup Slots</label>
                        <input type="text" name="pickup_time_windows" class="form-control" value="{{ $farmer->pickup_time_windows }}">
                        <small class="text-muted" style="font-size: 0.78rem;">Comma-separated pickup intervals offered at stall checkout.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Operating Days</label>
                        <div class="p-2.5 bg-light rounded-3 border d-flex flex-wrap gap-2">
                            @foreach(['Saturday', 'Sunday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                <span class="badge {{ str_contains($farmer->operating_days, $day) ? 'bg-success text-white' : 'bg-white text-muted border' }} py-1 px-2.5">
                                    {{ $day }}
                                </span>
                            @endforeach
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Manage operating days in Stall Profile.</small>
                    </div>

                    <div class="p-3 rounded-3 bg-light border">
                        <div class="small fw-bold text-dark mb-1"><i class="bi bi-lightning-charge text-warning me-1"></i> Quick Interval Templates</div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelector('input[name=pickup_time_windows]').value = '08:00 AM - 10:00 AM, 10:30 AM - 12:30 PM, 01:00 PM - 03:00 PM'">
                                Standard 3-Slot
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelector('input[name=pickup_time_windows]').value = '08:30 AM - 11:30 AM, 12:00 PM - 03:00 PM'">
                                Morning / Afternoon
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelector('input[name=pickup_time_windows]').value = '09:00 AM - 01:00 PM'">
                                Extended Morning
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: Inventory Alerts & Notifications -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-amber-500 bg-opacity-10 p-2.5 text-warning d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fef3c7;">
                            <i class="bi bi-bell fs-5 text-warning"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Inventory & Notification Triggers</h5>
                            <small class="text-muted">Alert preferences when items run low or orders arrive</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Low-Stock Alert Level</label>
                        <div class="input-group">
                            <input type="number" name="low_stock_threshold" class="form-control" value="{{ $settings['low_stock_threshold'] ?? 5 }}" min="1" max="50">
                            <span class="input-group-text bg-light text-muted small">Units remaining</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">Highlights harvest products on your stock table with an amber alert tag.</small>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="autoSoldOut">Auto-Flag as Sold Out</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Immediately mark items as 'Sold Out' on market storefront when quantity hits zero.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="autoSoldOut" name="auto_sold_out" value="1" {{ !empty($settings['auto_sold_out']) || !isset($settings['auto_sold_out']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="emailNotif">Email Alert on New Pre-Orders</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Receive an instant notification to {{ Auth::user()->email }} whenever a customer reserves items.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="emailNotif" name="email_notifications" value="1" {{ !empty($settings['email_notifications']) || !isset($settings['email_notifications']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="smsNotif">SMS Customer Arrival Alerts</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Receive mobile alerts when shoppers check in at the market plaza.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="smsNotif" name="sms_notifications" value="1" {{ !empty($settings['sms_notifications']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-0 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="soundNotif">Audio Chime on New Orders</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Play a distinct audio bell notification when an incoming pre-order appears.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="soundNotif" name="order_alert_sound" value="1" {{ !empty($settings['order_alert_sound']) || !isset($settings['order_alert_sound']) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 4: Stall Status & Vacation / Holiday Mode -->
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-3 bg-teal-500 bg-opacity-10 p-2.5 text-teal d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #e0f2fe;">
                            <i class="bi bi-airplane fs-5 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Stall Status & Holiday Mode</h5>
                            <small class="text-muted">Pause reservations during harvests, holidays, or inclement weather</small>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="stallOpen">Accepting Pre-Orders (Stall Open)</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Disable to temporarily hide 'Add to Cart' buttons across all your products.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="stallOpen" name="stall_open" value="1" {{ !empty($settings['stall_open']) || !isset($settings['stall_open']) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3 bg-amber-50 border border-warning-subtle">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-2">
                            <div>
                                <label class="form-check-label fw-semibold small text-dark mb-0 d-block" for="vacationMode">Stall Holiday / Seasonal Break Mode</label>
                                <span class="text-muted" style="font-size: 0.75rem;">Displays a friendly vacation banner on your stall page.</span>
                            </div>
                            <input class="form-check-input ms-2" type="checkbox" role="switch" id="vacationMode" name="vacation_mode" value="1" {{ !empty($settings['vacation_mode']) ? 'checked' : '' }}>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-semibold text-dark mb-1">Expected Return Date</label>
                            <input type="date" name="vacation_return_date" class="form-control form-control-sm" value="{{ $settings['vacation_return_date'] ?? '' }}">
                        </div>

                        <div>
                            <label class="form-label small fw-semibold text-dark mb-1">Customer Notice Message</label>
                            <textarea name="vacation_notice" class="form-control form-control-sm" rows="2" placeholder="e.g. Harvesting our autumn honeycrisp apples! Orders resume next Saturday.">{{ $settings['vacation_notice'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Action Bar -->
        <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i> Stall policies apply directly to all customer pre-order checkouts and stock displays.
            </span>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('farmer.dashboard') }}" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                <button type="submit" class="btn btn-brand px-4 py-2">
                    <i class="bi bi-floppy me-1"></i> Save Stall Configurations
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
