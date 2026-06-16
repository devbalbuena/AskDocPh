@extends('layouts.admin')
@section('title', 'Broadcast Announcements')
@section('page-title', 'Announcements')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Create Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    New Announcement
                </h3>

                <div class="space-y-4">
                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="ann-title" maxlength="100"
                               placeholder="e.g. System Maintenance"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    {{-- Message --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Message <span class="text-red-500">*</span></label>
                        <textarea id="ann-message" rows="3" maxlength="500"
                                  placeholder="Broadcast message to all users..."
                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"></textarea>
                    </div>

                    {{-- Type Selector --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Type <span class="text-red-500">*</span></label>
                        <input type="hidden" id="ann-type" value="info">
                        <div class="flex gap-2">
                            <button type="button" onclick="selectType('info', this)"
                                    data-type="info"
                                    class="type-pill flex-1 py-2 rounded-xl text-sm font-semibold border-2 transition-all bg-blue-600 border-blue-600 text-white">
                                ℹ Info
                            </button>
                            <button type="button" onclick="selectType('warning', this)"
                                    data-type="warning"
                                    class="type-pill flex-1 py-2 rounded-xl text-sm font-semibold border-2 transition-all bg-white border-yellow-400 text-yellow-700 hover:bg-yellow-50">
                                ⚠ Warning
                            </button>
                            <button type="button" onclick="selectType('urgent', this)"
                                    data-type="urgent"
                                    class="type-pill flex-1 py-2 rounded-xl text-sm font-semibold border-2 transition-all bg-white border-red-400 text-red-600 hover:bg-red-50">
                                🚨 Urgent
                            </button>
                        </div>
                    </div>

                    {{-- Expires At --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Expires <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="datetime-local" id="ann-expires"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500">
                    </div>

                    {{-- Submit --}}
                    <button id="ann-submit-btn" onclick="publishAnnouncement()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors mt-2">
                        Publish Announcement
                    </button>
                </div>
            </div>
        </div>

        {{-- Active Announcements Table --}}
        <div class="lg:col-span-3">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Active Announcements</h3>
                    <span id="ann-count-badge" class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-medium">{{ $announcements->count() }} total</span>
                </div>

                <div id="announcements-table">
                    @forelse($announcements as $ann)
                    <div class="announcement-row px-6 py-4 border-b border-gray-100 last:border-b-0 flex items-start gap-4" id="ann-row-{{ $ann->id }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="font-semibold text-sm text-gray-900">{{ $ann->title }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $ann->type === 'urgent' ? 'bg-red-100 text-red-700'
                                    : ($ann->type === 'warning' ? 'bg-yellow-100 text-yellow-700'
                                    : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($ann->type) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mb-1.5">{{ $ann->message }}</p>
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                @if($ann->expires_at)
                                <span>Expires {{ $ann->expires_at->diffForHumans() }}</span>
                                @else
                                <span>No expiry</span>
                                @endif
                                <span>·</span>
                                <span>{{ $ann->dismissals_count }} user{{ $ann->dismissals_count !== 1 ? 's' : '' }} dismissed</span>
                            </div>
                        </div>
                        <button onclick="deleteAnnouncement({{ $ann->id }}, this)"
                                class="flex-shrink-0 text-gray-300 hover:text-red-500 transition-colors p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    @empty
                    <div id="ann-empty" class="px-6 py-16 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                        <p class="text-sm text-gray-500 font-medium">No announcements yet</p>
                        <p class="text-xs text-gray-400 mt-1">Create one using the form on the left</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="ann-toast" class="hidden fixed bottom-6 right-6 z-50 bg-green-600 text-white text-sm px-5 py-3 rounded-xl shadow-xl flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <span id="ann-toast-msg"></span>
</div>

@push('scripts')
<script>
// ── Type pill selection ───────────────────────────────────────
const typeStyles = {
    info:    { active: 'bg-blue-600 border-blue-600 text-white',    inactive: 'bg-white border-blue-400 text-blue-700 hover:bg-blue-50' },
    warning: { active: 'bg-yellow-500 border-yellow-500 text-white', inactive: 'bg-white border-yellow-400 text-yellow-700 hover:bg-yellow-50' },
    urgent:  { active: 'bg-red-600 border-red-600 text-white',      inactive: 'bg-white border-red-400 text-red-600 hover:bg-red-50' },
};

function selectType(type, el) {
    document.getElementById('ann-type').value = type;
    document.querySelectorAll('.type-pill').forEach(btn => {
        const t = btn.dataset.type;
        btn.className = 'type-pill flex-1 py-2 rounded-xl text-sm font-semibold border-2 transition-all ' +
            (t === type ? typeStyles[t].active : typeStyles[t].inactive);
    });
}

// ── Publish ───────────────────────────────────────────────────
function publishAnnouncement() {
    const title   = document.getElementById('ann-title').value.trim();
    const message = document.getElementById('ann-message').value.trim();
    const type    = document.getElementById('ann-type').value;
    const expires = document.getElementById('ann-expires').value;
    const btn     = document.getElementById('ann-submit-btn');

    if (!title)   { alert('Please enter a title.'); return; }
    if (!message) { alert('Please enter a message.'); return; }

    btn.disabled    = true;
    btn.textContent = 'Publishing...';

    axios.post('{{ route("admin.announcements.store") }}', {
        title, message, type,
        expires_at: expires || null,
    }).then(res => {
        showToast('Announcement published!');

        // Remove empty state
        const empty = document.getElementById('ann-empty');
        if (empty) empty.remove();

        // Prepend new row
        const ann  = res.data.announcement;
        const badge = { info: 'bg-blue-100 text-blue-700', warning: 'bg-yellow-100 text-yellow-700', urgent: 'bg-red-100 text-red-700' }[ann.type];
        const row   = document.createElement('div');
        row.id        = 'ann-row-' + ann.id;
        row.className = 'announcement-row px-6 py-4 border-b border-gray-100 flex items-start gap-4';
        row.innerHTML = `
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span class="font-semibold text-sm text-gray-900">${ann.title}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium ${badge}">${ann.type.charAt(0).toUpperCase() + ann.type.slice(1)}</span>
                </div>
                <p class="text-xs text-gray-500 mb-1.5">${ann.message}</p>
                <div class="flex items-center gap-3 text-xs text-gray-400">
                    <span>${ann.expires_at ? 'Expires ' + ann.expires_at : 'No expiry'}</span>
                    <span>·</span><span>0 users dismissed</span>
                </div>
            </div>
            <button onclick="deleteAnnouncement(${ann.id}, this)" class="flex-shrink-0 text-gray-300 hover:text-red-500 transition-colors p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>`;
        document.getElementById('announcements-table').prepend(row);

        // Update count badge
        const badge2 = document.getElementById('ann-count-badge');
        const count  = document.querySelectorAll('.announcement-row').length;
        badge2.textContent = count + ' total';

        // Reset form
        document.getElementById('ann-title').value   = '';
        document.getElementById('ann-message').value = '';
        document.getElementById('ann-expires').value = '';
        selectType('info', document.querySelector('[data-type="info"]'));
    }).catch(err => {
        alert('Error: ' + JSON.stringify(err.response?.data ?? 'Unknown error'));
    }).finally(() => {
        btn.disabled    = false;
        btn.textContent = 'Publish Announcement';
    });
}

// ── Delete ────────────────────────────────────────────────────
function deleteAnnouncement(id, btn) {
    if (!confirm('Delete this announcement? All users will stop seeing it immediately.')) return;

    axios.delete('/admin/announcements/' + id)
        .then(() => {
            document.getElementById('ann-row-' + id)?.remove();
            const count = document.querySelectorAll('.announcement-row').length;
            document.getElementById('ann-count-badge').textContent = count + ' total';
            if (count === 0) {
                document.getElementById('announcements-table').innerHTML =
                    `<div id="ann-empty" class="px-6 py-16 text-center"><p class="text-sm text-gray-500">No announcements yet</p></div>`;
            }
            showToast('Announcement deleted.');
        })
        .catch(() => alert('Could not delete announcement.'));
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(msg) {
    const toast = document.getElementById('ann-toast');
    document.getElementById('ann-toast-msg').textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}
</script>
@endpush
@endsection
