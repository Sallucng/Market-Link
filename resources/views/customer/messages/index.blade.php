@extends('layouts.app')

@section('title', 'Stall Messages & Inquiries — MarketLink')

@section('styles')
<style>
    /* Eliminates outer body scrollbar on Customer Messages and fits viewport height cleanly */
    html, body {
        overflow-y: hidden !important;
        height: 100vh;
    }
    body {
        display: flex;
        flex-direction: column;
        height: 100vh;
        max-height: 100vh;
    }
    #main-content {
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden !important;
        display: flex;
        flex-direction: column;
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }
    footer {
        display: none !important;
    }
    .customer-messages-container {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        min-height: 0;
        height: 100%;
        overflow: hidden;
        max-width: 1480px;
    }
    .customer-messages-card {
        flex: 1 1 auto;
        min-height: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04) !important;
        border-radius: 20px;
    }
    .customer-messages-card > .row {
        flex: 1 1 auto;
        height: 100%;
        min-height: 0;
    }
    .customer-threads-pane {
        height: 100%;
        display: flex;
        flex-direction: column;
        min-height: 0;
        background-color: #ffffff;
    }
    .customer-threads-list {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
        padding: 0.65rem;
    }
    .customer-chat-pane {
        height: 100%;
        display: flex;
        flex-direction: column;
        min-height: 0;
        background-color: #f8fafc;
    }
    .customer-chat-timeline {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
        background-color: #f8fafc;
        padding: 1.5rem;
    }

    /* Sleek Scrollbars */
    .customer-threads-list::-webkit-scrollbar,
    .customer-chat-timeline::-webkit-scrollbar {
        width: 6px;
    }
    .customer-threads-list::-webkit-scrollbar-track,
    .customer-chat-timeline::-webkit-scrollbar-track {
        background: transparent;
    }
    .customer-threads-list::-webkit-scrollbar-thumb,
    .customer-chat-timeline::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.12);
        border-radius: 10px;
    }
    .customer-threads-list::-webkit-scrollbar-thumb:hover,
    .customer-chat-timeline::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.22);
    }

    /* Conversation Tiles */
    .conversation-tile {
        border-radius: 12px;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        color: inherit;
        border: 1px solid transparent;
        display: block;
        margin-bottom: 6px;
        padding: 12px 14px;
    }
    .conversation-tile:hover {
        background-color: #f1f5f9;
        color: inherit;
    }
    .conversation-tile.active {
        background-color: #f0fdf4 !important;
        border-left: 3.5px solid #16a34a !important;
        border-color: #dcfce7;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
    }

    /* De-congested Chat Bubbles */
    .chat-bubble-user {
        background-color: #1b4332;
        color: #ffffff;
        border-radius: 18px 18px 4px 18px;
        padding: 10px 16px;
        max-width: 65%;
        box-shadow: 0 2px 6px rgba(27, 67, 50, 0.12);
        word-break: break-word;
    }
    .chat-bubble-peer {
        background-color: #ffffff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-radius: 18px 18px 18px 4px;
        padding: 10px 16px;
        max-width: 65%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        word-break: break-word;
    }
    .chat-input-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        padding: 5px 6px 5px 18px;
        transition: all 0.2s ease;
    }
    .chat-input-pill:focus-within {
        background: #ffffff;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }
</style>
@endsection

@section('content')
<div class="container customer-messages-container">
    <!-- Streamlined Header (Airy, Single Line, Zero Vertical Waste) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2.5 gap-2 flex-shrink-0">
        <div class="d-flex align-items-center gap-3">
            <h3 class="heading-serif fw-bold text-dark mb-0 fs-4">Stall Messages</h3>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill font-mono-meta small">
                <i class="bi bi-chat-dots-fill me-1"></i> {{ $conversations->count() }} {{ Str::plural('Conversation', $conversations->count()) }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-2xs">
                <i class="bi bi-arrow-left"></i>
                <span class="small fw-semibold">Dashboard</span>
            </a>
            <a href="{{ route('farmers.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-2xs">
                <i class="bi bi-shop"></i>
                <span class="small fw-semibold">Browse Growers</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 py-1.5 px-3 small mb-2 flex-shrink-0">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close py-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Chat Window Card -->
    <div class="card card-custom bg-white overflow-hidden customer-messages-card">
        <div class="row g-0 h-100">
            <!-- Left: Conversations Sidebar (Generous 35% width so names and times never wrap or clip) -->
            <div class="col-lg-5 col-xl-4 border-end customer-threads-pane">
                <div class="p-3 border-bottom bg-white flex-shrink-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark font-heading fs-6">Conversations</span>
                        <span class="badge bg-light text-muted border font-mono-meta">{{ $conversations->count() }}</span>
                    </div>
                    <div class="position-relative">
                        <i class="bi bi-search text-muted position-absolute start-0 ms-3 top-50 translate-middle-y" style="font-size: 0.8rem;"></i>
                        <input type="text" id="customerSearchThreads" 
                               class="form-control form-control-sm bg-light rounded-pill ps-5 pe-3 py-2 border-0 shadow-none" 
                               placeholder="Search stall, grower, or order..." style="font-size: 0.82rem;">
                    </div>
                </div>

                <div class="customer-threads-list" id="conversationsList">
                    @forelse($conversations as $conv)
                        @php
                            $unread = $conv->unreadCountFor(Auth::user());
                            $isActive = $activeConversation && $activeConversation->id === $conv->id;
                            $stallInitial = strtoupper(substr($conv->farmer->stall_name ?? 'F', 0, 1));
                        @endphp
                        <a href="{{ route('customer.messages.index', ['conversation_id' => $conv->id]) }}" 
                           class="conversation-tile {{ $isActive ? 'active' : '' }}"
                           data-stall="{{ strtolower($conv->farmer->stall_name ?? '') }}"
                           data-grower="{{ strtolower($conv->farmer->contact_person ?? '') }}"
                           data-order="{{ strtolower($conv->order->order_number ?? '') }}">
                            <div class="d-flex gap-2.5 align-items-start">
                                <!-- Avatar -->
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0 shadow-2xs" 
                                     style="width: 42px; height: 42px; background-color: {{ $isActive ? '#dcfce7' : '#f1f5f9' }}; color: {{ $isActive ? '#15803d' : '#475569' }}; font-size: 0.95rem;">
                                    {{ $stallInitial }}
                                </div>

                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-baseline mb-0.5">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.92rem;">
                                            {{ $conv->farmer->stall_name ?? 'Farmer Stall' }}
                                        </h6>
                                        <small class="text-muted font-mono-meta flex-shrink-0 ms-2" style="font-size: 0.68rem;">
                                            {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : $conv->created_at->diffForHumans(null, true) }}
                                        </small>
                                    </div>

                                    <div class="d-flex align-items-center gap-1.5 mb-1 text-muted small" style="font-size: 0.74rem;">
                                        <span class="text-truncate"><i class="bi bi-geo-alt text-success me-0.5"></i>{{ $conv->farmer->market->name ?? 'Local Market' }}</span>
                                    </div>

                                    @if($conv->order)
                                        <div class="mb-1">
                                            <span class="badge bg-light text-secondary border font-mono-meta px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                <i class="bi bi-receipt me-1"></i>#{{ $conv->order->order_number }}
                                            </span>
                                            <span class="badge {{ $conv->order->status === 'completed' ? 'bg-success-subtle text-success' : 'bg-light text-dark border' }} font-mono-meta px-1 py-0.5" style="font-size: 0.65rem;">
                                                {{ ucfirst($conv->order->status) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="small text-muted mb-0 text-truncate" style="font-size: 0.8rem; line-height: 1.3;">
                                            @if($conv->latestMessage)
                                                {{ $conv->latestMessage->sender_id === Auth::id() ? 'You: ' : '' }}{{ $conv->latestMessage->body }}
                                            @else
                                                <span class="fst-italic">Conversation started</span>
                                            @endif
                                        </p>
                                        @if($unread > 0)
                                            <span class="badge bg-danger rounded-pill font-mono-meta px-1.5 py-0.5 ms-1" style="font-size: 0.68rem;">{{ $unread }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-chat-square-text display-5 d-block mb-3 text-secondary opacity-50"></i>
                            <h6 class="fw-bold">No Messages Yet</h6>
                            <p class="small mb-3" style="font-size: 0.82rem;">You can message any farmer directly from their stall page or from your active pre-orders.</p>
                            <a href="{{ route('farmers.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Find a Farmer</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Active Chat View (col-lg-7 col-xl-8) -->
            <div class="col-lg-7 col-xl-8 customer-chat-pane">
                @if($activeConversation)
                    <!-- Chat Header -->
                    <div class="p-3 px-4 border-bottom bg-white d-flex justify-content-between align-items-center flex-shrink-0" style="min-height: 68px;">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $stallInitial = strtoupper(substr($activeConversation->farmer->stall_name ?? 'F', 0, 1));
                            @endphp
                            <div class="position-relative">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold shadow-2xs" 
                                     style="width: 44px; height: 44px; font-size: 1.05rem;">
                                    {{ $stallInitial }}
                                </div>
                                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold text-dark mb-0 fs-6">{{ $activeConversation->farmer->stall_name }}</h6>
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5 font-mono-meta" style="font-size: 0.68rem;">Verified Grower</span>
                                </div>
                                <div class="small text-muted mt-0.5" style="font-size: 0.78rem;">
                                    <span>Contact: <strong>{{ $activeConversation->farmer->contact_person }}</strong></span>
                                    @if($activeConversation->farmer->market)
                                        &bull; <span><i class="bi bi-geo-alt me-0.5 text-danger"></i>{{ $activeConversation->farmer->market->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('farmers.show', $activeConversation->farmer->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-2xs" style="font-size: 0.82rem;">
                                <i class="bi bi-shop"></i>
                                <span>Stall Profile</span>
                            </a>
                            @if($activeConversation->order)
                                <a href="{{ route('customer.orders.show', $activeConversation->order->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-2xs" style="font-size: 0.82rem;">
                                    <i class="bi bi-receipt"></i>
                                    <span>Order #{{ $activeConversation->order->order_number }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Messages Timeline (Airy, Spacious, Zero Congestion) -->
                    <div class="customer-chat-timeline" id="chatMessagesTimeline" style="scroll-behavior: smooth;">
                        @if($activeConversation->order)
                            <div class="text-center mb-4">
                                <div class="d-inline-flex align-items-center gap-2.5 px-4 py-1.5 bg-white rounded-pill shadow-2xs border border-light-subtle text-muted small">
                                    <i class="bi bi-box-seam text-success"></i>
                                    <span>Pre-Order <strong class="text-dark">#{{ $activeConversation->order->order_number }}</strong></span>
                                    <span class="text-muted-50">&bull;</span>
                                    <span>Pickup: <strong>{{ $activeConversation->order->pickup_date ? $activeConversation->order->pickup_date->format('M d, Y') : 'Scheduled' }}</strong></span>
                                </div>
                            </div>
                        @else
                            <div class="text-center mb-3">
                                <span class="badge bg-white text-muted border border-light-subtle shadow-2xs px-3 py-1 rounded-pill font-mono-meta small fw-normal">
                                    Direct Stall Inquiry
                                </span>
                            </div>
                        @endif

                        @forelse($activeConversation->messages as $msg)
                            @php $isMe = $msg->sender_id === Auth::id(); @endphp
                            <div class="d-flex mb-2.5 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}" data-message-id="{{ $msg->id }}">
                                <div class="{{ $isMe ? 'chat-bubble-user' : 'chat-bubble-peer' }}">
                                    <div class="message-text" style="font-size: 0.92rem; line-height: 1.5;">
                                        {!! nl2br(e($msg->body)) !!}
                                    </div>
                                    <div class="d-flex align-items-center justify-content-end gap-1 mt-1 {{ $isMe ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.67rem;">
                                        <span class="font-mono-meta">{{ $msg->created_at->format('g:i A') }}</span>
                                        @if($isMe)
                                            <i class="bi bi-check2-all" style="font-size: 0.8rem;"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted" id="noMessagesPlaceholder">
                                <i class="bi bi-chat-heart display-6 text-success opacity-50 mb-2"></i>
                                <h6 class="fw-bold mb-1">Say hello!</h6>
                                <p class="small mb-0">Ask about stall pickup location, fresh harvest readiness, or timing.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Chat Input Area (Clears Floating Controls) -->
                    <div class="p-3 px-4 bg-white border-top flex-shrink-0" style="padding-right: 5rem !important;">
                        <form id="chatSendForm" class="d-flex align-items-center chat-input-pill shadow-xs">
                            @csrf
                            <input type="text" id="chatMessageInput" class="form-control bg-transparent border-0 shadow-none px-2" 
                                   placeholder="Type a message to {{ $activeConversation->farmer->stall_name }}... (Press Enter to send)" 
                                   style="font-size: 0.92rem;"
                                   autocomplete="off" required>
                            <button type="submit" id="chatSendBtn" class="btn btn-success rounded-pill px-4 py-2 d-flex align-items-center gap-1.5 shadow-sm fw-semibold flex-shrink-0" style="font-size: 0.88rem; background-color: #1b4332; border-color: #1b4332;">
                                <span>Send</span> <i class="bi bi-send-fill ms-0.5"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 text-muted">
                        <i class="bi bi-chat-dots display-4 text-success opacity-50 mb-3"></i>
                        <h4 class="heading-serif fw-bold text-dark">Select a Conversation</h4>
                        <p class="small text-muted col-md-7 mb-3">Choose a stall thread from the left, or visit any farmer profile to send a direct pickup message.</p>
                        <a href="{{ route('farmers.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-4">Explore Local Stalls</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($activeConversation)
<script>
(function() {
    const conversationId = {{ $activeConversation->id }};
    const timeline = document.getElementById('chatMessagesTimeline');
    const sendForm = document.getElementById('chatSendForm');
    const msgInput = document.getElementById('chatMessageInput');
    const sendBtn = document.getElementById('chatSendBtn');
    let lastMessageId = {{ $activeConversation->messages->last()?->id ?? 0 }};

    function scrollToBottom() {
        if (timeline) {
            timeline.scrollTop = timeline.scrollHeight;
        }
    }
    scrollToBottom();

    // Instant AJAX Send
    if (sendForm) {
        sendForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const text = msgInput.value.trim();
            if (!text) return;

            sendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

            try {
                const res = await fetch("{{ route('customer.messages.send', $activeConversation->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ body: text })
                });

                const data = await res.json();
                if (data.success && data.message) {
                    msgInput.value = '';
                    appendMessage(data.message);
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                }
            } catch (err) {
                console.error('Send failed:', err);
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<span>Send</span> <i class="bi bi-send-fill ms-0.5"></i>';
                msgInput.focus();
            }
        });
    }

    function appendMessage(msg) {
        if (document.querySelector(`[data-message-id="${msg.id}"]`)) {
            return;
        }

        const placeholder = document.getElementById('noMessagesPlaceholder');
        if (placeholder) placeholder.remove();

        const isMe = msg.is_me;
        const div = document.createElement('div');
        div.className = `d-flex mb-2.5 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;
        div.setAttribute('data-message-id', msg.id);

        const checkmark = isMe ? '<i class="bi bi-check2-all" style="font-size: 0.8rem;"></i>' : '';
        const metaColor = isMe ? 'text-white-50' : 'text-muted';

        div.innerHTML = `
            <div class="${isMe ? 'chat-bubble-user' : 'chat-bubble-peer'}">
                <div class="message-text" style="font-size: 0.92rem; line-height: 1.5;">
                    ${escapeHtml(msg.body).replace(/\n/g, '<br>')}
                </div>
                <div class="d-flex align-items-center justify-content-end gap-1 mt-1 ${metaColor}" style="font-size: 0.67rem;">
                    <span class="font-mono-meta">${msg.created_at_time || 'Just now'}</span>
                    ${checkmark}
                </div>
            </div>
        `;
        timeline.appendChild(div);
        scrollToBottom();
    }

    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    // Auto-Poll for new messages every 3.5s
    setInterval(async () => {
        try {
            const url = `{{ url('/customer/messages') }}/${conversationId}/poll?after_id=${lastMessageId}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (res.ok) {
                const data = await res.json();
                if (data.success && data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        appendMessage(msg);
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                }
            }
        } catch (e) {
            // silent catch on background poll
        }
    }, 3500);

    // Live search filter on conversation list
    const threadSearchInput = document.getElementById('customerSearchThreads');
    if (threadSearchInput) {
        threadSearchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#conversationsList .conversation-tile').forEach(tile => {
                const stall = tile.getAttribute('data-stall') || '';
                const grower = tile.getAttribute('data-grower') || '';
                const order = tile.getAttribute('data-order') || '';
                if (!q || stall.includes(q) || grower.includes(q) || order.includes(q)) {
                    tile.style.display = 'block';
                } else {
                    tile.style.display = 'none';
                }
            });
        });
    }
})();
</script>
@endif
@endsection
