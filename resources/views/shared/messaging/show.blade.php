@extends($layout)
@section('title', 'Chat with ' . ($otherParticipant?->display_name ?? 'Unknown'))
@section('page-title', 'Messages')

@section('content')
<div class="flex h-full -m-6" style="height: calc(100vh - 4rem);">

    {{-- ── LEFT PANEL ─────────────────────────────────────────── --}}
    <div class="w-80 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Messages</h2>
            <button onclick="openNewMessageModal()"
                    class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New
            </button>
        </div>
        <div class="px-4 py-3 border-b border-gray-100">
            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 gap-2">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input id="convo-search" type="text" placeholder="Search conversations…"
                       class="bg-transparent text-sm text-gray-700 placeholder-gray-400 focus:outline-none w-full"
                       oninput="filterConversations(this.value)">
            </div>
        </div>
        <div id="conversation-list" class="flex-1 overflow-y-auto">
            @forelse($conversationData as $convo)
            @php
                $other    = $convo['other_user'];
                $last     = $convo['last_message'];
                $isActive = $convo['id'] === $conversation->id;
                $initials = $other ? strtoupper(substr($other['name'], 0, 1)) : '?';
            @endphp
            <a href="{{ route('messages.show', $convo['id']) }}"
               class="convo-item flex items-center gap-3 px-4 py-3.5 border-b border-gray-50 transition-colors relative
                      {{ $isActive ? 'bg-green-50 border-l-4 border-l-green-600' : 'hover:bg-gray-50 border-l-4 border-l-transparent' }}"
               data-name="{{ strtolower($other['name'] ?? '') }}">
                <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-sm font-bold overflow-hidden
                    {{ ($other['role'] ?? '') === 'doctor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                    @if(!empty($other['avatar']))
                        <img src="{{ $other['avatar'] }}" class="w-10 h-10 object-cover" alt="{{ $other['name'] }}">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold {{ $isActive ? 'text-green-700' : 'text-gray-900' }} truncate">{{ $other['name'] ?? 'Unknown' }}</span>
                        @if($last)
                        <span class="text-xs text-gray-400 ml-2 flex-shrink-0">{{ $last['time'] }}</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-0.5">
                        <p class="text-xs text-gray-500 truncate">
                            @if($last){{ mb_substr($last['body'], 0, 45) }}@else<em>No messages yet</em>@endif
                        </p>
                        @if($convo['unread_count'] > 0)
                        <span class="ml-2 flex-shrink-0 w-2 h-2 rounded-full bg-green-500"></span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                <p class="text-sm text-gray-400">No conversations</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── RIGHT PANEL — Active Conversation ──────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 bg-white">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 py-3.5 bg-white border-b border-gray-100 flex-shrink-0">
            @if($otherParticipant)
            <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center text-sm font-bold overflow-hidden
                {{ $otherParticipant->role === 'doctor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                @if($otherParticipant->profile_photo)
                    <img src="{{ Storage::url($otherParticipant->profile_photo) }}" class="w-9 h-9 object-cover" alt="{{ $otherParticipant->display_name }}">
                @else
                    {{ strtoupper(substr($otherParticipant->fname, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ $otherParticipant->display_name }}</p>
                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full capitalize
                    {{ $otherParticipant->role === 'doctor' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                    {{ $otherParticipant->role }}
                </span>
            </div>
            <a href="{{ route('users.profile', $otherParticipant->username) }}"
               class="text-xs text-green-600 hover:text-green-700 font-medium transition-colors">
                View Profile →
            </a>
            @endif
        </div>

        {{-- Messages Area --}}
        <div id="messages-container"
             class="flex-1 overflow-y-auto px-5 py-4 space-y-3 flex flex-col"
             style="scroll-behavior: smooth;">

            @if($messages->isEmpty())
            <div class="flex-1 flex items-center justify-center">
                <p class="text-sm text-gray-400">No messages yet. Say hello! 👋</p>
            </div>
            @else
            @php $prevDate = null; @endphp
            @foreach($messages as $msg)
                {{-- Date separator --}}
                @php
                    $msgDate = $msg['created_date'];
                    $showDate = $msgDate !== $prevDate;
                    $prevDate = $msgDate;
                    $dateLabel = match(true) {
                        $msgDate === now()->toDateString()             => 'Today',
                        $msgDate === now()->subDay()->toDateString()   => 'Yesterday',
                        default                                        => \Carbon\Carbon::parse($msgDate)->format('M d, Y'),
                    };
                @endphp
                @if($showDate)
                <div class="flex items-center justify-center my-2">
                    <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">{{ $dateLabel }}</span>
                </div>
                @endif

                {{-- Message bubble --}}
                <div class="flex {{ $msg['is_own'] ? 'justify-end' : 'justify-start' }} gap-2">
                    @if(!$msg['is_own'])
                    <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold
                        {{ $msg['sender_role'] === 'doctor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                        @if($msg['sender_avatar'])
                            <img src="{{ $msg['sender_avatar'] }}" class="w-7 h-7 rounded-full object-cover">
                        @else
                            {{ strtoupper(substr($msg['sender_name'], 0, 1)) }}
                        @endif
                    </div>
                    @endif
                    <div class="max-w-xs sm:max-w-sm">
                        <div class="px-4 py-2.5 text-sm leading-relaxed
                            {{ $msg['is_own']
                                ? 'bg-green-600 text-white rounded-2xl rounded-br-sm'
                                : 'bg-gray-100 text-gray-800 rounded-2xl rounded-bl-sm' }}">
                            {{ $msg['body'] }}
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5 {{ $msg['is_own'] ? 'text-right' : 'text-left' }}">
                            {{ $msg['created_at'] }}
                        </p>
                    </div>
                </div>
            @endforeach
            @endif
        </div>

        {{-- Input Area --}}
        <div class="bg-white border-t border-gray-100 px-5 py-4 flex-shrink-0">
            <div class="flex items-end gap-3">
                <textarea id="msg-input"
                          class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800
                                 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500
                                 resize-none"
                          rows="1"
                          placeholder="Type a message…"
                          style="max-height: 120px; overflow-y: auto;"
                          oninput="autoResizeTextarea(this)"
                          onkeydown="handleEnter(event)"></textarea>
                <button id="send-btn"
                        onclick="sendMessage()"
                        class="flex-shrink-0 bg-green-600 hover:bg-green-700 text-white rounded-xl px-4 py-3
                               transition-colors flex items-center gap-1.5 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Send
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── NEW MESSAGE MODAL ───────────────────────────────────────── --}}
<div id="new-msg-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">New Message</h3>
            <button onclick="closeNewMessageModal()" class="text-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-5">
            <input id="user-search-input" type="text"
                   placeholder="Search for a patient or doctor..."
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                   oninput="searchUsers(this.value)">
            <div id="user-search-results" class="mt-3 space-y-1 max-h-72 overflow-y-auto">
                <p class="text-center text-sm text-gray-400 py-6">Type at least 2 characters to search</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CONVERSATION_ID = {{ $conversation->id }};
let lastMessageId     = {{ $messages->isNotEmpty() ? $messages->last()['id'] : 0 }};
let pollTimer         = null;
let isSending         = false;

// ── Scroll to bottom on load ───────────────────────────────────
window.addEventListener('load', () => {
    scrollToBottom();
    startPolling();
});

function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}

// ── Textarea auto-resize ───────────────────────────────────────
function autoResizeTextarea(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// ── Enter = send, Shift+Enter = newline ────────────────────────
function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

// ── Send Message ───────────────────────────────────────────────
function sendMessage() {
    const input = document.getElementById('msg-input');
    const body  = input.value.trim();
    if (!body || isSending) return;

    isSending = true;
    const btn = document.getElementById('send-btn');
    btn.disabled = true;

    axios.post(`/messages/${CONVERSATION_ID}/send`, { body })
        .then(res => {
            appendMessage(res.data);
            lastMessageId = res.data.id;
            input.value = '';
            input.style.height = 'auto';
            scrollToBottom();
        })
        .catch(err => {
            alert(err.response?.data?.message ?? 'Failed to send message.');
        })
        .finally(() => {
            isSending = false;
            btn.disabled = false;
            input.focus();
        });
}

// ── Append a message bubble ────────────────────────────────────
function appendMessage(msg) {
    const container = document.getElementById('messages-container');

    // Remove "no messages" empty state if present
    const empty = container.querySelector('.flex-1.flex.items-center');
    if (empty) empty.remove();

    const isOwn = msg.is_own;
    const initials = msg.sender_name ? msg.sender_name.charAt(0).toUpperCase() : '?';
    const avatarHtml = msg.sender_avatar
        ? `<img src="${msg.sender_avatar}" class="w-7 h-7 rounded-full object-cover">`
        : `<span>${initials}</span>`;

    const wrapper = document.createElement('div');
    wrapper.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} gap-2 msg-bubble`;

    wrapper.innerHTML = `
        ${!isOwn ? `<div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold bg-green-100 text-green-700 overflow-hidden">${avatarHtml}</div>` : ''}
        <div class="max-w-xs sm:max-w-sm">
            <div class="px-4 py-2.5 text-sm leading-relaxed ${isOwn ? 'bg-green-600 text-white rounded-2xl rounded-br-sm' : 'bg-gray-100 text-gray-800 rounded-2xl rounded-bl-sm'}">
                ${escapeHtml(msg.body)}
            </div>
            <p class="text-xs text-gray-400 mt-0.5 ${isOwn ? 'text-right' : 'text-left'}">${msg.created_at}</p>
        </div>
    `;
    container.appendChild(wrapper);
}

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
              .replace(/"/g,'&quot;').replace(/'/g,'&#039;').replace(/\n/g,'<br>');
}

// ── Polling ────────────────────────────────────────────────────
function startPolling() {
    pollTimer = setInterval(poll, 3000);
}
function stopPolling() {
    clearInterval(pollTimer);
}

function poll() {
    if (document.hidden) return;
    axios.get(`/messages/${CONVERSATION_ID}/poll`, { params: { after_id: lastMessageId } })
        .then(res => {
            res.data.forEach(msg => {
                if (!msg.is_own) {
                    appendMessage(msg);
                    scrollToBottom();
                }
                if (msg.id > lastMessageId) lastMessageId = msg.id;
            });
        })
        .catch(() => {});
}

document.addEventListener('visibilitychange', () => {
    if (document.hidden) { stopPolling(); } else { startPolling(); poll(); }
});

// ── Conversation Filter ────────────────────────────────────────
function filterConversations(q) {
    document.querySelectorAll('.convo-item').forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = (!q || name.includes(q.toLowerCase())) ? '' : 'none';
    });
}

// ── New Message Modal ──────────────────────────────────────────
function openNewMessageModal() {
    document.getElementById('new-msg-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('user-search-input').focus(), 100);
}
function closeNewMessageModal() {
    document.getElementById('new-msg-modal').classList.add('hidden');
    document.getElementById('user-search-input').value = '';
    document.getElementById('user-search-results').innerHTML =
        '<p class="text-center text-sm text-gray-400 py-6">Type at least 2 characters to search</p>';
}
document.getElementById('new-msg-modal').addEventListener('click', function(e) {
    if (e.target === this) closeNewMessageModal();
});

let searchTimeout = null;
function searchUsers(q) {
    clearTimeout(searchTimeout);
    const results = document.getElementById('user-search-results');
    if (q.length < 2) {
        results.innerHTML = '<p class="text-center text-sm text-gray-400 py-6">Type at least 2 characters to search</p>';
        return;
    }
    results.innerHTML = '<p class="text-center text-sm text-gray-400 py-4">Searching…</p>';
    searchTimeout = setTimeout(() => {
        axios.get('/users/search', { params: { q } })
            .then(res => {
                if (!res.data.length) {
                    results.innerHTML = '<p class="text-center text-sm text-gray-400 py-4">No users found.</p>';
                    return;
                }
                results.innerHTML = res.data.map(u => {
                    const initials = (u.fname.charAt(0) + u.lname.charAt(0)).toUpperCase();
                    const avatarHtml = u.profile_photo
                        ? `<img src="${u.profile_photo}" class="w-9 h-9 rounded-full object-cover">`
                        : `<span class="text-sm font-bold ${u.role==='doctor'?'text-blue-700':'text-green-700'}">${initials}</span>`;
                    const badgeCls = u.role === 'doctor' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600';
                    return `<button onclick="startConversation(${u.id})"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-left">
                        <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center overflow-hidden
                            ${u.role==='doctor'?'bg-blue-100':'bg-green-100'}">${avatarHtml}</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">${u.fname} ${u.lname}</p>
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full ${badgeCls} capitalize">${u.role}</span>
                        </div>
                    </button>`;
                }).join('');
            })
            .catch(() => { results.innerHTML = '<p class="text-center text-sm text-red-400 py-4">Search failed.</p>'; });
    }, 300);
}

function startConversation(recipientId) {
    axios.post('/messages/start', { recipient_id: recipientId })
        .then(res => { window.location.href = '/messages/' + res.data.conversation_id; })
        .catch(err => { alert(err.response?.data?.message ?? 'Could not start conversation.'); });
}
</script>
@endpush
