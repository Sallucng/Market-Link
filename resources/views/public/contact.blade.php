@extends('layouts.app')

@section('title', 'Contact Us — MarketLink Team')



@section('content')
<!-- Header -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-3 text-center">
        <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Get in Touch</span>
        <h1 class="heading-serif display-5 fw-bold text-dark mb-2">Contact the MarketLink Team</h1>
        <p class="lead text-muted col-lg-7 mx-auto">
            Have questions about local market schedules, vendor registration, or pickup coordination? We are here to support our community.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Info & Interactive Map (SRS §1.6) -->
            <div class="col-lg-6">
                <h3 class="heading-serif fw-bold text-dark mb-4">Team Headquarters & Coordination Center</h3>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-geo-alt-fill fs-5 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Central Coordination Office</h6>
                        <p class="text-secondary small mb-0">100 Central Square, Suite 400, Metropolis Plaza, NY 10001</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-telephone-fill fs-5 text-success"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Telephone Support</h6>
                        <p class="text-secondary small mb-0">+1 (555) 019-2831 (Mon - Sun, 07:00 AM - 05:00 PM)</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-envelope-fill fs-5 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Email Inquiries</h6>
                        <p class="text-secondary small mb-0">support@marketlink.local | info@marketlink.local</p>
                    </div>
                </div>

                <!-- Embedded Google Maps showing location (SRS §1.6) -->
                <div class="card card-custom p-2 bg-white border-0 shadow-sm mt-4">
                    <iframe 
                        title="MarketLink Headquarters on Google Maps"
                        src="https://maps.google.com/maps?q=100+Central+Square,+New+York,+NY+10001&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                        width="100%" 
                        height="380" 
                        style="border:0; border-radius: 12px;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                    <small class="text-muted text-center py-2 d-block">
                        <i class="bi bi-geo-alt text-danger me-1"></i> MarketLink HQ Location — 100 Central Square (Google Maps)
                    </small>
                </div>
            </div>

            <!-- Contact Inquiry Form -->
            <div class="col-lg-6">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100">
                    <h4 class="heading-serif fw-bold text-dark mb-3">Send Us a Direct Message</h4>
                    <p class="text-muted small mb-4">Leave your details and a team member will respond within 24 hours.</p>

                    <form onsubmit="event.preventDefault(); alert('Thank you for reaching out! Your message has been received.'); this.reset();">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Your Full Name</label>
                            <input type="text" class="form-control" placeholder="e.g. Emily Watson" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Email Address</label>
                                <input type="email" class="form-control" placeholder="emily@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Contact Phone</label>
                                <input type="tel" class="form-control" placeholder="+1 (555) 000-0000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Inquiry Type</label>
                            <select class="form-select">
                                <option>General Question</option>
                                <option>Farmer Stall Registration</option>
                                <option>Market Location Partnership</option>
                                <option>Order & Pickup Assistance</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Message</label>
                            <textarea rows="4" class="form-control" placeholder="Write your message here..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 py-2 rounded-pill">
                            <i class="bi bi-send me-1"></i> Submit Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


