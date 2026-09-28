@extends('layouts.farmer')

@section('title', 'Customer Inquiries & Order Messages — Farmer Portal')

@section('styles')
<style>
    /* Eliminates outer body scrollbar on Farmer Messages and fits viewport height cleanly */
    html, body {
        overflow-y: hidden !important;
        height: 100vh;
    }
    .farmer-main-wrapper {
        height: 100vh;
        max-height: 100vh;
        overflow: hidden !important;
        display: flex;
        flex-direction: column;
    }
    .farmer-main-wrapper main {
        flex: 1 1 auto;
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
        padding-left: 1.75rem !important;
        padding-right: 1.75rem !important;
        overflow: hidden !important;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .farmer-main-wrapper footer {
        display: none !important;
    }
    .farmer-messages-container {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        min-height: 0;
        height: 100%;
        overflow: hidden;
    }
    .farmer-messages-card {
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
    .farmer-messages-card > .row {
        flex: 1 1 auto;
        height: 100%;
        min-height: 0;
    }
    .farmer-threads-pane {
        height: 100%;
        display: flex;
        flex-direction: column;
        min-height: 0;
        background-color: #ffffff;
    }
    .farmer-threads-list {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
        padding: 0.65rem;
    }
    .farmer-chat-pane {
        height: 100%;
        display: flex;
        flex-direction: column;
        min-height: 0;
        background-color: #f8fafc;
    }
    .farmer-chat-timeline {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
        background-color: #f8fafc;
        padding: 1.5rem;
    }
    
    /* Sleek Scrollbars */
    .farmer-threads-list::-webkit-scrollbar,
    .farmer-chat-timeline::-webkit-scrollbar {
        width: 6px;
    }
    .farmer-threads-list::-webkit-scrollbar-track,
    .farmer-chat-timeline::-webkit-scrollbar-track {
        background: transparent;
    }
    .farmer-threads-list::-webkit-scrollbar-thumb,
    .farmer-chat-timeline::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.12);
        border-radius: 10px;
    }
    .farmer-threads-list::-webkit-scrollbar-thumb:hover,
    .farmer-chat-timeline::-webkit-scrollbar-thumb:hover {
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

    /* De-congested Modern Chat Bubbles */
    .chat-bubble-farmer {
        background: #1b4332;
        color: #ffffff;
        border-radius: 18px 18px 4px 18px;
        padding: 10px 16px;
        max-width: 65%;
        box-shadow: 0 2px 6px rgba(27, 67, 50, 0.12);
        word-break: break-word;
    }
    .chat-bubble-customer {
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
<div class="farmer-messages-container">
    <!-- Streamlined Header (Airy, Uncluttered, Maximum Vertical Space for Chat) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 flex-shrink-0">
        <div class="d-flex align-items-center gap-3">
            <h2 class="heading-serif fw-bold text-dark mb-0 fs-3">Shopper Messages</h2>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill font-mono-meta small">
                <i class="bi bi-chat-dots-fill me-1"></i> {{ $conversations->count() }} {{ Str::plural('Thread', $conversations->count()) }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('farmer.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-2xs">
                <i class="bi bi-arrow-left"></i>
                <span class="small fw-semibold">Back to Dashboard</span>
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
    <div class="card card-custom bg-white overflow-hidden farmer-messages-card">
        <div class="row g-0 h-100">
            <!-- Left: Conversations Sidebar -->
            <div class="col-lg-4 col-xl-3 border-end farmer-threads-pane">
                <!-- Search & Header Bar -->
                <div class="p-3 border-bottom bg-white flex-shrink-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark font-heading fs-6">Inbox</span>
                        <span class="badge bg-light text-muted border font-mono-meta">{{ $conversations->count() }}</span>
                    </div>
                    <div class="position-relative">
                        <i class="bi bi-search text-muted position-absolute start-0 ms-3 top-50 translate-middle-y" style="font-size: 0.8rem;"></i>
                        <input type="text" id="farmerSearchThreads" 
                               class="form-control form-control-sm bg-light rounded-pill ps-5 pe-3 py-2 border-0 shadow-none" 
                               placeholder="Filter customer or order..." style="font-size: 0.82rem;">
                    </div>
                </div>

                <!-- Conversation Tiles -->
                <div class="farmer-threads-list" id="farmerConversationsList">
                    @forelse($conversations as $conv)
                        @php
                            $unread = $conv->unreadCountFor(Auth::user());
                            $isActive = $activeConversation && $activeConversation->id === $conv->id;
                            $customerInitial = strtoupper(substr($conv->customer->name ?? 'C', 0, 1));
                        @endphp
                        <a href="{{ route('farmer.messages.index', ['conversation_id' => $conv->id]) }}" 
                           class="conversation-tile {{ $isActive ? 'active' : '' }}"
                           data-customer="{{ strtolower($conv->customer->name ?? '') }}"
                           data-order="{{ strtolower($conv->order->order_number ?? '') }}">
                            <div class="d-flex gap-2.5 align-items-start">
                                <!-- Avatar -->
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0 shadow-2xs" 
                                     style="width: 40px; height: 40px; background-color: {{ $isActive ? '#dcfce7' : '#f1f5f9' }}; color: {{ $isActive ? '#15803d' : '#475569' }}; font-size: 0.95rem;">
                                    {{ $customerInitial }}
                                </div>

                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-baseline mb-0.5">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.92rem;">
                                            {{ $conv->customer->name ?? 'Customer' }}
                                        </h6>
                                        <small class="text-muted font-mono-meta flex-shrink-0 ms-1" style="font-size: 0.68rem;">
                                            {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : $conv->created_at->diffForHumans(null, true) }}
                                        </small>
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
                                                <span class="fst-italic">New conversation</span>
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
                            <i class="bi bi-inbox display-6 d-block mb-2 text-secondary opacity-50"></i>
                            <h6 class="fw-bold mb-1">No Messages Yet</h6>
                            <p class="small mb-0" style="font-size: 0.82rem;">When shoppers send inquiries or ask questions about pre-orders, they will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Active Chat Window -->
            <div class="col-lg-8 col-xl-9 farmer-chat-pane">
                @if($activeConversation)
                    <!-- Top Chat Bar -->
                    <div class="p-3 px-4 border-bottom bg-white d-flex justify-content-between align-items-center flex-shrink-0" style="min-height: 68px;">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $activeInitial = strtoupper(substr($activeConversation->customer->name ?? 'C', 0, 1));
                            @endphp
                            <div class="position-relative">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold shadow-2xs" 
                                     style="width: 44px; height: 44px; font-size: 1.05rem;">
                                    {{ $activeInitial }}
                                </div>
                                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold text-dark mb-0 fs-6">{{ $activeConversation->customer->name }}</h6>
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 font-mono-meta" style="font-size: 0.68rem;">Shopper</span>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2 small text-muted mt-0.5" style="font-size: 0.78rem;">
                                    <span><i class="bi bi-envelope me-1"></i><a href="mailto:{{ $activeConversation->customer->email }}" class="text-decoration-none text-muted">{{ $activeConversation->customer->email }}</a></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if($activeConversation->order)
                                <a href="{{ route('farmer.orders.show', $activeConversation->order->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-2xs" style="font-size: 0.82rem;">
                                    <i class="bi bi-receipt"></i>
                                    <span>Order #{{ $activeConversation->order->order_number }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Messages Timeline (Airy, Spacious, Zero-Congestion Bubbles) -->
                    <div class="farmer-chat-timeline" id="farmerChatTimeline" style="scroll-behavior: smooth;">
                        @if($activeConversation->order)
                            <div class="text-center mb-4">
                                <div class="d-inline-flex align-items-center gap-2.5 px-4 py-1.5 bg-white rounded-pill shadow-2xs border border-light-subtle text-muted small">
                                    <i class="bi bi-box-seam text-success"></i>
                                    <span>Pre-Order <strong class="text-dark">#{{ $activeConversation->order->order_number }}</strong></span>
                                    <span class="text-muted-50">&bull;</span>
                                    <span class="fw-semibold text-success">${{ number_format($activeConversation->order->total_amount, 2) }}</span>
                                    <span class="text-muted-50">&bull;</span>
                                    <span class="badge {{ $activeConversation->order->status === 'completed' ? 'bg-success-subtle text-success' : 'bg-light text-dark border' }} rounded-pill py-0.5 px-2">
                                        {{ ucfirst($activeConversation->order->status) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="text-center mb-3">
                                <span class="badge bg-white text-muted border border-light-subtle shadow-2xs px-3 py-1 rounded-pill font-mono-meta small fw-normal">
                                    Conversation Started
                                </span>
                            </div>
                        @endif

                        @forelse($activeConversation->messages as $msg)
                            @php $isMe = $msg->sender_id === Auth::id(); @endphp
                            <div class="d-flex mb-2.5 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}" data-message-id="{{ $msg->id }}">
                                <div class="{{ $isMe ? 'chat-bubble-farmer' : 'chat-bubble-customer' }}">
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
                            <div class="text-center py-5 text-muted" id="noFarmerMessagesPlaceholder">
                                <i class="bi bi-chat-dots display-5 text-success opacity-50 mb-3"></i>
                                <h6 class="fw-bold">No Messages Yet</h6>
                                <p class="small mb-0">Respond to this customer inquiry or coordinate pickup readiness.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Clean, Spacious Floating Input Bar (Clears Floating Controls) -->
                    <div class="p-3 px-4 bg-white border-top flex-shrink-0" style="padding-right: 5rem !important;">
                        <form id="farmerChatSendForm" class="d-flex align-items-center chat-input-pill shadow-xs">
                            @csrf
                            <input type="text" id="farmerChatMessageInput" class="form-control bg-transparent border-0 shadow-none px-2" 
                                   placeholder="Type a message to {{ $activeConversation->customer->name }}... (Press Enter to send)" 
                                   style="font-size: 0.92rem;"
                                   autocomplete="off" required>
                            <button type="submit" id="farmerChatSendBtn" class="btn btn-success rounded-pill px-4 py-2 d-flex align-items-center gap-1.5 shadow-sm fw-semibold flex-shrink-0" style="font-size: 0.88rem; background-color: #1b4332; border-color: #1b4332;">
                                <span>Send</span> <i class="bi bi-send-fill ms-0.5"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 text-muted">
                        <i class="bi bi-chat-left-dots display-3 text-success opacity-40 mb-3"></i>
                        <h4 class="heading-serif fw-bold text-dark">Select a Customer Conversation</h4>
                        <p class="text-muted col-md-6" style="font-size: 0.92rem;">Choose an inquiry or order discussion from the list to reply directly to your customer.</p>
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
    const timeline = document.getElementById('farmerChatTimeline');
    const sendForm = document.getElementById('farmerChatSendForm');
    const msgInput = document.getElementById('farmerChatMessageInput');
    const sendBtn = document.getElementById('farmerChatSendBtn');
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
                const res = await fetch("{{ route('farmer.messages.send', $activeConversation->id) }}", {
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
                console.error('Farmer send failed:', err);
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

        const placeholder = document.getElementById('noFarmerMessagesPlaceholder');
        if (placeholder) placeholder.remove();

        const isMe = msg.is_me;
        const div = document.createElement('div');
        div.className = `d-flex mb-2.5 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;
        div.setAttribute('data-message-id', msg.id);

        const checkmark = isMe ? '<i class="bi bi-check2-all" style="font-size: 0.8rem;"></i>' : '';
        const metaColor = isMe ? 'text-white-50' : 'text-muted';

        div.innerHTML = `
            <div class="${isMe ? 'chat-bubble-farmer' : 'chat-bubble-customer'}">
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

    // Auto-Poll for new customer replies every 3.5s
    setInterval(async () => {
        try {
            const url = `{{ url('/farmer/messages') }}/${conversationId}/poll?after_id=${lastMessageId}`;
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
            // silent catch
        }
    }, 3500);

    // Live search filter on conversation list
    const farmerThreadSearchInput = document.getElementById('farmerSearchThreads');
    if (farmerThreadSearchInput) {
        farmerThreadSearchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#farmerConversationsList .conversation-tile').forEach(tile => {
                const cust = tile.getAttribute('data-customer') || '';
                const order = tile.getAttribute('data-order') || '';
                if (!q || cust.includes(q) || order.includes(q)) {
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
