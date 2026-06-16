@extends($layout)
@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')
<div class="flex h-full -m-6" style="height: calc(100vh - 4rem);">

    {{-- ── LEFT PANEL ─────────────────────────────────────────── --}}
    <div class="w-80 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col">

        {{-- Header --}}
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

        {{-- Search existing conversations --}}
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

        {{-- Conversation list --}}
        <div id="conversation-list" class="flex-1 overflow-y-auto">
            @forelse($conversationData as $convo)
            @php
                $other = $convo['other_user'];
                $last  = $convo['last_message'];
                $initials = $other ? strtoupper(substr($other['name'], 0, 1)) : '?';
            @endphp
            <a href="{{ route('messages.show', $convo['id']) }}"
               class="convo-item flex items-center gap-3 px-4 py-3.5 hover:bg-gray-50 border-b border-gray-50 transition-colors relative"
               data-name="{{ strtolower($other['name'] ?? '') }}">

                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-sm font-bold
                    {{ ($other['role'] ?? '') === 'doctor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                    @if(!empty($other['avatar']))
                        <img src="{{ $other['avatar'] }}" class="w-10 h-10 rounded-full object-cover" alt="{{ $other['name'] }}">
                    @else
                        {{ $initials }}
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-900 truncate">{{ $other['name'] ?? 'Unknown' }}</span>
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
                    <div>
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full capitalize inline-block mt-0.5
                            {{ ($other['role'] ?? '') === 'doctor' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                            {{ $other['role'] ?? '' }}
                        </span>
                        @if(!empty($other['is_verified_doctor']))
                        <span class="inline-flex items-center gap-0.5 bg-blue-100 text-blue-700 text-[10px] px-1.5 py-0.5 rounded-full font-semibold ml-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-600">No conversations yet</p>
                <p class="text-xs text-gray-400 mt-1">Start a new message to connect</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── RIGHT PANEL (empty index state) ────────────────────── --}}
    <div class="flex-1 flex flex-col items-center justify-center bg-gray-50">
        <div class="text-center">
            <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">Select a conversation</h3>
            <p class="text-sm text-gray-400 mt-1">or start a new one</p>
            <button onclick="openNewMessageModal()"
                    class="mt-5 inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Message
            </button>
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
            <div class="relative">
                <input id="user-search-input" type="text"
                       placeholder="Search for a patient or doctor..."
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                       oninput="searchUsers(this.value)">
            </div>
            <div id="user-search-results" class="mt-3 space-y-1 max-h-72 overflow-y-auto">
                <p class="text-center text-sm text-gray-400 py-6">Type at least 2 characters to search</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Conversation filter ────────────────────────────────────────
function filterConversations(q) {
    const items = document.querySelectorAll('.convo-item');
    q = q.toLowerCase().trim();
    items.forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = (!q || name.includes(q)) ? '' : 'none';
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

// Close modal on backdrop click
document.getElementById('new-msg-modal').addEventListener('click', function(e) {
    if (e.target === this) closeNewMessageModal();
});

// ── User Search ────────────────────────────────────────────────
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
                        ? `<img src="${u.profile_photo}" class="w-9 h-9 rounded-full object-cover" alt="${u.fname}">`
                        : `<span class="text-sm font-bold ${u.role === 'doctor' ? 'text-blue-700' : 'text-green-700'}">${initials}</span>`;
                    const badgeClass = u.role === 'doctor' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600';
                    return `<button onclick="startConversation(${u.id})"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-left">
                        <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center
                            ${u.role === 'doctor' ? 'bg-blue-100' : 'bg-green-100'}">${avatarHtml}</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">${u.fname} ${u.lname}</p>
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full ${badgeClass} capitalize">${u.role}</span>
                        </div>
                    </button>`;
                }).join('');
            })
            .catch(() => {
                results.innerHTML = '<p class="text-center text-sm text-red-400 py-4">Search failed.</p>';
            });
    }, 300);
}

// ── Start Conversation ─────────────────────────────────────────
function startConversation(recipientId) {
    axios.post('/messages/start', { recipient_id: recipientId })
        .then(res => {
            window.location.href = '/messages/' + res.data.conversation_id;
        })
        .catch(err => {
            const msg = err.response?.data?.message ?? 'Could not start conversation.';
            alert(msg);
        });
}
</script>
@endpush
