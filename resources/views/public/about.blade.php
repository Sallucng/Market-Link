@extends('layouts.app')

@section('title', 'About Us — MarketLink')

@section('content')
<!-- Hero Section -->
<section class="py-5 bg-white border-bottom position-relative overflow-hidden">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge badge-pastel-green px-3 py-1 rounded-pill mb-3">
                    <i class="bi bi-flower2 me-1"></i> Cultivating Direct Community Commerce
                </span>
                <h1 class="heading-serif display-4 fw-bold text-dark mb-3" style="letter-spacing: -0.02em; line-height: 1.15;">
                    Bridging the Distance From <span class="text-success">Soil to Stall</span>.
                </h1>
                <p class="lead text-secondary mb-4" style="line-height: 1.6; font-size: 1.15rem;">
                    MarketLink was founded on a simple conviction: local food systems thrive when neighborhood growers and conscious shoppers can connect with total clarity, guaranteed harvest availability, and zero middleman friction.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('markets.index') }}" class="btn btn-brand px-4 py-2">
                        Explore Local Markets <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-brand-outline px-4 py-2">
                        Browse Farm Products
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80" 
                         alt="Fresh harvest in wooden crates" 
                         class="img-fluid rounded-4 shadow-lg w-100" 
                         style="object-fit: cover; max-height: 420px;">
                    <div class="card card-custom p-3 bg-white position-absolute bottom-0 start-0 m-3 shadow border-0 d-none d-sm-flex flex-row align-items-center gap-3" style="max-width: 280px;">
                        <div class="p-2 bg-success text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">100% In-Person</div>
                            <small class="text-muted" style="font-size: 0.78rem;">Settled directly with growers at market pickup</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Metrics Strip -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row row-cols-2 row-cols-md-4 g-4 text-center">
            <div class="col">
                <div class="p-2">
                    <div class="heading-serif display-6 fw-bold text-success mb-1">100%</div>
                    <div class="small fw-semibold text-dark">Direct Stall Settlement</div>
                    <small class="text-muted" style="font-size: 0.75rem;">$0 platform commissions taken</small>
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <div class="heading-serif display-6 fw-bold text-success mb-1">0%</div>
                    <div class="small fw-semibold text-dark">Harvest Spoilage</div>
                    <small class="text-muted" style="font-size: 0.75rem;">Harvested against confirmed reservations</small>
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <div class="heading-serif display-6 fw-bold text-success mb-1">15+</div>
                    <div class="small fw-semibold text-dark">Community Markets</div>
                    <small class="text-muted" style="font-size: 0.75rem;">Geocoded with OpenStreetMap pins</small>
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <div class="heading-serif display-6 fw-bold text-success mb-1">40+</div>
                    <div class="small fw-semibold text-dark">Family Farm Stalls</div>
                    <small class="text-muted" style="font-size: 0.75rem;">Independently owned local growers</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Origin Story & UX Problem Space -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-2">
                <span class="badge badge-pastel-amber px-3 py-1 rounded-pill mb-2">The Communication Gap</span>
                <h2 class="heading-serif fw-bold text-dark mb-3">Why Traditional Farmers Markets Needed a Digital Bridge</h2>
                <p class="text-secondary" style="line-height: 1.7;">
                    Local farmers markets are booming worldwide as communities rediscover the nutrition, flavor, and ecological responsibility of buying directly from family growers. Yet, until MarketLink, the interaction relied entirely on guesswork.
                </p>
                <div class="card card-custom p-3 bg-light border-0 mb-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-danger mt-1"><i class="bi bi-x-circle-fill fs-5"></i></div>
                        <div>
                            <strong class="text-dark small d-block mb-1">The Shopper’s Frustration</strong>
                            <p class="text-secondary small mb-0">
                                Shoppers travel long distances across town on Saturday mornings, only to discover that prized heirloom tomatoes or organic honey sold out within the first twenty minutes—or that their favorite vendor took the week off.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card card-custom p-3 bg-light border-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-danger mt-1"><i class="bi bi-exclamation-triangle-fill fs-5"></i></div>
                        <div>
                            <strong class="text-dark small d-block mb-1">The Grower’s Dilemma</strong>
                            <p class="text-secondary small mb-0">
                                Farmers wake at dawn on harvest day having to estimate consumer demand in advance. Unsold perishable stock spoils before the next market, while under-harvesting leaves regular patrons empty-handed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="card card-custom p-4 bg-brand-light border-0 shadow-sm">
                    <span class="badge badge-pastel-green px-3 py-1 rounded-pill mb-3 w-auto align-self-start">The MarketLink Solution</span>
                    <h3 class="heading-serif fw-bold text-dark mb-3">Predictable Harvesting. Guaranteed Collection.</h3>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;">
                        MarketLink replaces chalkboards and paper flyers with a real-time weekly inventory catalog, interactive map pins, and structured pre-order reservations. 
                    </p>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center gap-2 mb-3 text-dark small">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>Advance Visibility:</strong> Know exactly who is attending and what stock they have before stepping out of the house.</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 text-dark small">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>Zero Risk of Sell-Out:</strong> Reserve what you need with guaranteed holding at the stall.</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 text-dark small">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>Preparation Cutoffs:</strong> Farmers configure preparation hours so harvest schedules remain orderly and calm.</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 text-dark small">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>100% In-Person Connection:</strong> Reconnect with growers face-to-face; inspect your basket, exchange pleasantries, and pay at the stall.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The MarketLink Manifesto (4 Core Pillars) -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Our Operating Philosophy</span>
            <h2 class="heading-serif fw-bold text-dark mb-2">The Four Pillars of MarketLink</h2>
            <p class="text-muted col-lg-7 mx-auto small">Every feature we engineer is measured against our core commitment to community growers and conscious shoppers.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100 text-center">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-calendar2-week fs-4 text-success"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Harvest on Demand</h5>
                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                        Growers harvest against actual pre-order volumes. Perishable leafy greens and delicate berries stay on the vine until reserved.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100 text-center">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-cash-coin fs-4 text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Direct Settlement</h5>
                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                        Strictly pay-at-pickup. We do not extract transaction cuts, credit card processing surcharges, or delivery markup fees from small farms.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100 text-center">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-geo-alt fs-4 text-danger"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Open Geolocation</h5>
                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                        Integrated OpenStreetMap and Leaflet markers pin exact market plazas, stall alleys, and walking directions without proprietary paywalls.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card card-custom p-4 bg-white border-0 shadow-sm h-100 text-center">
                    <div class="p-3 bg-brand-light text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-star-fill fs-4 text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Verified Feedback</h5>
                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                        Community ratings and comments are unlocked only after a customer completes pickup at the stall, ensuring zero bot manipulation or fake reviews.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dual Experience Workflow (How it Works for Both Sides) -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge badge-pastel-green px-3 py-1 rounded-pill mb-2">User Experience Flow</span>
            <h2 class="heading-serif fw-bold text-dark mb-2">Designed for Real Market Days</h2>
            <p class="text-muted col-lg-7 mx-auto small">Whether you are shopping for your household or managing a multi-acre organic homestead, MarketLink adapts to your workflow.</p>
        </div>

        <div class="row g-4">
            <!-- Customer Journey -->
            <div class="col-lg-6">
                <div class="card card-custom p-4 bg-light border-0 h-100">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-success rounded-pill px-3 py-1">For Shoppers</span>
                        <h4 class="heading-serif fw-bold text-dark mb-0">The Weekend Routine</h4>
                    </div>
                    <div class="position-relative ps-4 border-start border-2 border-success ms-2 my-2">
                        <div class="mb-4 position-relative">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-success border border-white rounded-circle" style="left: -17px !important;"></span>
                            <h6 class="fw-bold text-dark mb-1">1. Discover Markets & Stalls</h6>
                            <p class="text-secondary small mb-0">Filter by location and operating day. Browse attending farmers, bio details, and stall positions on the map.</p>
                        </div>
                        <div class="mb-4 position-relative">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-success border border-white rounded-circle" style="left: -17px !important;"></span>
                            <h6 class="fw-bold text-dark mb-1">2. Reserve Fresh Harvest</h6>
                            <p class="text-secondary small mb-0">Add seasonal products to your pickup cart. Select your preferred date and time slot within the farmer’s window.</p>
                        </div>
                        <div class="position-relative">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-success border border-white rounded-circle" style="left: -17px !important;"></span>
                            <h6 class="fw-bold text-dark mb-1">3. Stroll, Collect & Pay at Stall</h6>
                            <p class="text-secondary small mb-0">Arrive at the market with your ready-for-pickup alert. Settle payment directly in person with the grower and take home farm-fresh food.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Farmer Journey -->
            <div class="col-lg-6">
                <div class="card card-custom p-4 bg-light border-0 h-100">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-pill px-3 py-1">For Farmers</span>
                        <h4 class="heading-serif fw-bold text-dark mb-0">The Vendor Workflow</h4>
                    </div>
                    <div class="position-relative ps-4 border-start border-2 border-primary ms-2 my-2">
                        <div class="mb-4 position-relative">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-white rounded-circle" style="left: -17px !important;"></span>
                            <h6 class="fw-bold text-dark mb-1">1. Set Weekly Stock Templates</h6>
                            <p class="text-secondary small mb-0">Define recurring weekly replenishment baselines. Adjust available quantities as harvest cycles fluctuate with a single click.</p>
                        </div>
                        <div class="mb-4 position-relative">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-white rounded-circle" style="left: -17px !important;"></span>
                            <h6 class="fw-bold text-dark mb-1">2. Manage Cutoff Hours & Orders</h6>
                            <p class="text-secondary small mb-0">Accept incoming pre-orders. When the cutoff time hits, order books lock so you can harvest, wash, and crate items without last-minute chaos.</p>
                        </div>
                        <div class="position-relative">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-white rounded-circle" style="left: -17px !important;"></span>
                            <h6 class="fw-bold text-dark mb-1">3. Mark Ready & Build Customer Loyalty</h6>
                            <p class="text-secondary small mb-0">Flag crates as ready for pickup. Receive cash or card in person, build personal rapport, and respond to verified customer feedback.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Team Behind MarketLink (SRS §1.6 & §1.9) -->
<section class="py-5 bg-white border-top">
    <div class="container py-2">
        <div class="text-center mb-5">
            <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Platform Architects & Stewards</span>
            <h2 class="heading-serif fw-bold text-dark mb-2">The Team Behind MarketLink</h2>
            <p class="text-muted col-lg-7 mx-auto small">Dedicated engineers, agricultural liaisons, and designers passionate about local food security, farmer prosperity, and community resilience.</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80" 
                         alt="Elena Rostova" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 84px; height: 84px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">Elena Rostova</h6>
                    <small class="text-success fw-semibold d-block mb-2">Platform Lead & Architect</small>
                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                        Directs technical infrastructure, state machines, atomic stock allocation, and database schema integrity.
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80" 
                         alt="Marcus Vance" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 84px; height: 84px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">Marcus Vance</h6>
                    <small class="text-success fw-semibold d-block mb-2">Grower Community Liaison</small>
                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                        Maintains direct partnerships with agricultural associations and coordinates physical stall onboardings.
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80" 
                         alt="Sarah Chen" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 84px; height: 84px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">Sarah Chen</h6>
                    <small class="text-success fw-semibold d-block mb-2">Lead UI/UX & Accessibility</small>
                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                        Specializes in WCAG AA accessibility, mobile responsive layouts, and intuitive multi-device vendor interfaces.
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="card card-custom p-4 bg-light border-0 shadow-sm text-center h-100">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80" 
                         alt="David Kim" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 84px; height: 84px; object-fit: cover;">
                    <h6 class="fw-bold text-dark mb-1">David Kim</h6>
                    <small class="text-success fw-semibold d-block mb-2">Operations & Support Steward</small>
                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                        Oversees community dispute resolution, in-app notification dispatch, and weekend pickup verification pipelines.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Frequently Asked Questions / Trust Badges -->
<section class="py-5 bg-light border-top">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge badge-pastel-amber px-3 py-1 rounded-pill mb-2">Common Inquiries</span>
            <h2 class="heading-serif fw-bold text-dark mb-2">Frequently Asked Questions</h2>
            <p class="text-muted col-lg-7 mx-auto small">Understanding the design choices and community policies that govern MarketLink.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item bg-white rounded-3 shadow-sm mb-3 border-0">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-bold text-dark rounded-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                Why is payment settled in person at pickup rather than through an online gateway?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                In strict compliance with the platform’s foundational ethos (and SRS §1.5), pre-orders reserve inventory online without charging payment gateway fees. Traditional online payment processors siphon 2.9% + 30¢ from small family growers. By keeping payment directly between customer and farmer at the stall (via cash or the farmer's stall terminal), 100% of the money stays with the grower.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item bg-white rounded-3 shadow-sm mb-3 border-0">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button fw-bold text-dark rounded-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                How does the pre-order cutoff window work?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                Each farmer specifies an order cutoff window (e.g., 2, 4, or 12 hours prior to the market's opening). Once the cutoff time passes, orders cannot be cancelled or modified by shoppers. This guarantees that farmers can harvest, wash, pack, and transport your items knowing the order is locked in.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item bg-white rounded-3 shadow-sm mb-3 border-0">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button fw-bold text-dark rounded-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                How are farmer stall locations mapped?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                MarketLink uses OpenStreetMap with Leaflet.js. Farmers provide their exact stall coordinates, address, and physical booth identifiers (e.g., "Stall #12, North Corridor"). Customers can open interactive route maps on mobile or desktop to navigate straight to the pickup point.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item bg-white rounded-3 shadow-sm border-0">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button fw-bold text-dark rounded-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                Who can leave reviews and ratings on the platform?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                Only authenticated customers who have actually placed an order that was marked <strong>Completed</strong> by the grower upon pickup are permitted to submit a review and star rating. This prevents astroturfing and guarantees 100% verified, authentic customer feedback.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Community Commitment CTA Banner -->
<section class="py-5 bg-brand-light border-top">
    <div class="container text-center py-2">
        <span class="badge badge-brand px-3 py-1 rounded-pill mb-2">Strengthen Your Local Food System</span>
        <h3 class="heading-serif display-6 fw-bold text-dark mb-3">Ready to Connect With Your Local Growers?</h3>
        <p class="text-secondary col-lg-7 mx-auto mb-4" style="line-height: 1.7;">
            Discover fresh harvest schedules, find nearby weekend markets, and reserve products directly with the families who grow them.
        </p>
        <div class="d-inline-flex flex-wrap gap-3 justify-content-center">
            <a href="{{ route('markets.index') }}" class="btn btn-brand rounded-pill px-4 py-2">
                <i class="bi bi-geo-alt me-1"></i> Explore Nearby Markets
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-brand-outline rounded-pill px-4 py-2">
                <i class="bi bi-basket me-1"></i> Browse Fresh Products
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-dark rounded-pill px-4 py-2">
                    Register Customer Account
                </a>
            @endguest
        </div>
    </div>
</section>
@endsection
