@extends('layouts.app')

@section('title', __('Frequently Asked Questions') . ' — MarketLink')

@section('content')
<!-- Hero Header -->
<div class="py-5" style="background: linear-gradient(135deg, #0b2116 0%, #1b4332 100%); position: relative; overflow: hidden;">
    <div class="container py-4 text-center position-relative" style="z-index: 2;">
        <span class="badge rounded-pill px-3 py-1.5 mb-3 text-uppercase font-mono-meta" style="background: rgba(255, 255, 255, 0.15); color: #d8f3dc; letter-spacing: 0.08em; border: 1px solid rgba(255, 255, 255, 0.25);">
            <i class="bi bi-question-circle me-1"></i> {{ __('Help & Documentation') }}
        </span>
        <h1 class="heading-serif display-5 fw-bold text-white mb-3">
            {{ __('Frequently Asked Questions') }}
        </h1>
        <p class="text-white-50 col-lg-7 mx-auto fs-6 mb-4">
            {{ __('Everything you need to know about farm harvest pre-orders, market stall pickups, grower verification, and community policies.') }}
        </p>

        <!-- Live FAQ Search Bar -->
        <div class="col-lg-6 col-md-8 mx-auto">
            <div class="input-group input-group-lg shadow-lg" style="border-radius: 999px; overflow: hidden; background: #ffffff;">
                <span class="input-group-text bg-transparent border-0 text-success ps-4">
                    <i class="bi bi-search fs-5"></i>
                </span>
                <input type="text" id="faqSearchInput" class="form-control border-0 shadow-none fs-6 py-3" placeholder="{{ __('Type your question (e.g. pickup, payment, cutoff, farmer)...') }}" autocomplete="off">
                <button type="button" id="faqSearchClear" class="btn btn-link text-muted pe-4 text-decoration-none d-none" onclick="clearFaqSearch()">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Content Section -->
<div class="container py-5">
    <!-- Category Filter Pills -->
    <div class="d-flex flex-wrap justify-content-center gap-2 mb-5" id="faqCategoryFilters">
        <button type="button" class="btn btn-sm btn-brand rounded-pill px-3 py-1.5 active" data-filter="all">
            <i class="bi bi-grid-fill me-1"></i> {{ __('All Questions') }}
        </button>
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-filter="orders">
            <i class="bi bi-basket me-1 text-success"></i> {{ __('Pre-Orders & Pickup') }}
        </button>
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-filter="farmers">
            <i class="bi bi-shop me-1 text-success"></i> {{ __('Farmers & Stalls') }}
        </button>
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-filter="safety">
            <i class="bi bi-shield-check me-1 text-success"></i> {{ __('Safety & Moderation') }}
        </button>
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-filter="emails">
            <i class="bi bi-envelope-check me-1 text-success"></i> {{ __('Emails & Notifications') }}
        </button>
    </div>

    <!-- Accordion List -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="accordion accordion-flush d-flex flex-column gap-3" id="mainFaqAccordion">
                @foreach($faqs as $index => $faq)
                    <div class="accordion-item bg-white rounded-3 shadow-sm border faq-item" data-category="{{ $faq['category'] }}" style="border-radius: 14px !important; overflow: hidden; border-color: var(--border-hairline) !important;">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button fw-bold text-dark collapsed px-4 py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}" style="font-size: 1.02rem;">
                                <span class="p-2 rounded-2 me-3 d-inline-flex align-items-center justify-content-center bg-light text-success" style="width:34px; height:34px;">
                                    <i class="bi {{ $faq['icon'] }}"></i>
                                </span>
                                <span class="faq-question-text">{{ $faq['question'] }}</span>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#mainFaqAccordion">
                            <div class="accordion-body px-4 py-3 text-secondary faq-answer-text" style="line-height: 1.7; font-size: 0.95rem; border-top: 1px solid var(--border-hairline); background: #fafbfa;">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty Search State -->
            <div id="faqNoResults" class="text-center py-5 d-none">
                <i class="bi bi-search text-muted fs-1 mb-3 d-block"></i>
                <h4 class="fw-bold text-dark mb-1">{{ __('No matching questions found') }}</h4>
                <p class="text-muted small mb-3">{{ __('Try searching with another keyword or explore our categories.') }}</p>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="clearFaqSearch()">
                    {{ __('View all questions') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Help & Support Card -->
    <div class="row justify-content-center mt-5 pt-3">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm p-4 text-center rounded-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0 !important;">
                <div class="d-inline-flex align-items-center justify-content-center bg-white text-success rounded-circle shadow-sm mb-3 mx-auto" style="width: 52px; height: 52px;">
                    <i class="bi bi-chat-heart fs-4"></i>
                </div>
                <h3 class="heading-serif fw-bold text-dark mb-2">{{ __('Still Have Questions?') }}</h3>
                <p class="text-secondary small col-md-8 mx-auto mb-4" style="line-height: 1.6;">
                    {{ __('Our community team is here to help you navigate local markets, connect with family growers, or resolve orders.') }}
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('contact') }}" class="btn btn-brand rounded-pill px-4 py-2">
                        <i class="bi bi-envelope me-1"></i> {{ __('Contact Community Team') }}
                    </a>
                    <button type="button" class="btn btn-light border rounded-pill px-4 py-2 text-dark fw-semibold" onclick="document.getElementById('ai-toggle-btn')?.click()">
                        <i class="bi bi-robot text-success me-1"></i> {{ __('Ask AI Assistant') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('faqSearchInput');
    const clearBtn = document.getElementById('faqSearchClear');
    const filterButtons = document.querySelectorAll('#faqCategoryFilters button');
    const faqItems = document.querySelectorAll('.faq-item');
    const noResults = document.getElementById('faqNoResults');
    let currentCategory = 'all';

    function filterFaqs() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        faqItems.forEach(item => {
            const category = item.getAttribute('data-category');
            const qText = item.querySelector('.faq-question-text').textContent.toLowerCase();
            const aText = item.querySelector('.faq-answer-text').textContent.toLowerCase();

            const matchesCategory = (currentCategory === 'all' || category === currentCategory);
            const matchesQuery = (query === '' || qText.includes(query) || aText.includes(query));

            if (matchesCategory && matchesQuery) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }

        if (query.length > 0) {
            clearBtn.classList.remove('d-none');
        } else {
            clearBtn.classList.add('d-none');
        }
    }

    searchInput.addEventListener('input', filterFaqs);

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => {
                b.classList.remove('btn-brand', 'active');
                b.classList.add('btn-light', 'border');
            });
            this.classList.remove('btn-light', 'border');
            this.classList.add('btn-brand', 'active');

            currentCategory = this.getAttribute('data-filter');
            filterFaqs();
        });
    });

    window.clearFaqSearch = function () {
        searchInput.value = '';
        currentCategory = 'all';
        filterButtons.forEach(b => {
            if (b.getAttribute('data-filter') === 'all') {
                b.classList.remove('btn-light', 'border');
                b.classList.add('btn-brand', 'active');
            } else {
                b.classList.remove('btn-brand', 'active');
                b.classList.add('btn-light', 'border');
            }
        });
        filterFaqs();
        searchInput.focus();
    };
});
</script>
@endsection
