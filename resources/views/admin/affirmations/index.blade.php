@extends('layouts.admin')
@section('title', 'Daily Affirmations')
@section('page-title', 'Daily Affirmations')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Affirmations list --}}
    <div class="xl:col-span-2 space-y-3" id="affirmations-list">

        {{-- Explanation card --}}
        <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-green-800">
                These quotes appear on <strong>patient dashboards</strong> to provide daily motivation and mental health support.
                Only published affirmations with a past or no publish date are shown.
            </p>
        </div>

        @forelse($affirmations as $affirmation)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5" id="aff-row-{{ $affirmation->id }}">

            {{-- View mode --}}
            <div class="aff-view-{{ $affirmation->id }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-900 text-sm leading-relaxed italic">"{{ $affirmation->quote }}"</p>
                        @if($affirmation->author)
                        <p class="text-gray-500 text-xs mt-1">— {{ $affirmation->author }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="{{ $affirmation->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} text-xs px-2.5 py-1 rounded-full font-medium">
                                {{ $affirmation->is_published ? '✓ Published' : 'Draft' }}
                            </span>
                            @if($affirmation->publish_at)
                            <span class="text-gray-400 text-xs">{{ $affirmation->publish_at->format('M d, Y H:i') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button onclick="startEdit({{ $affirmation->id }}, {{ json_encode($affirmation->quote) }}, {{ json_encode($affirmation->author) }}, {{ $affirmation->is_published ? 'true' : 'false' }})"
                                class="text-green-600 hover:text-green-800 text-xs border border-green-200 hover:border-green-400 px-3 py-1.5 rounded-lg transition-colors">
                            Edit
                        </button>
                        <button onclick="deleteAffirmation({{ $affirmation->id }})"
                                class="text-red-400 hover:text-red-600 text-xs border border-red-200 hover:border-red-400 px-3 py-1.5 rounded-lg transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            {{-- Inline edit mode (hidden by default) --}}
            <div class="aff-edit-{{ $affirmation->id }} hidden space-y-3">
                <textarea id="edit-quote-{{ $affirmation->id }}" rows="3"
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 resize-none transition-colors"></textarea>
                <input type="text" id="edit-author-{{ $affirmation->id }}" placeholder="Author (optional)"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="edit-published-{{ $affirmation->id }}" class="w-4 h-4 accent-green-600">
                    <span class="text-sm text-gray-700">Published</span>
                </label>
                <div class="flex gap-2">
                    <button onclick="saveEdit({{ $affirmation->id }})"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                        Save
                    </button>
                    <button onclick="cancelEdit({{ $affirmation->id }})"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
            <p class="text-gray-500 text-sm">No affirmations yet. Add the first one using the form →</p>
        </div>
        @endforelse

        @if($affirmations->hasPages())
        <div>{{ $affirmations->links() }}</div>
        @endif
    </div>

    {{-- Add New Form --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 h-fit">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Add New Affirmation</h3>

        <div id="create-error" class="hidden mb-3 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-2.5 text-sm"></div>
        <div id="create-success" class="hidden mb-3 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-2.5 text-sm">Affirmation added!</div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Quote <span class="text-red-500">*</span></label>
                <textarea id="new-quote" rows="4" placeholder="Enter the affirmation text..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 resize-none transition-colors"></textarea>
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Author</label>
                <input type="text" id="new-author" placeholder="Optional..." value=""
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 transition-colors">
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Publish Date</label>
                <input type="datetime-local" id="new-publish-at"
                       value="{{ now()->format('Y-m-d\TH:i') }}"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="new-published" checked class="w-4 h-4 accent-green-600">
                <span class="text-sm text-gray-700">Publish immediately</span>
            </label>
            <button onclick="createAffirmation()"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-xl text-sm transition-colors">
                Add Affirmation
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Create ──────────────────────────────────────────────────────
function createAffirmation() {
    const quote     = document.getElementById('new-quote').value.trim();
    const author    = document.getElementById('new-author').value.trim();
    const publishAt = document.getElementById('new-publish-at').value;
    const published = document.getElementById('new-published').checked;
    const errDiv    = document.getElementById('create-error');
    const okDiv     = document.getElementById('create-success');

    if (!quote) {
        errDiv.textContent = 'Quote is required.';
        errDiv.classList.remove('hidden');
        return;
    }
    errDiv.classList.add('hidden');

    axios.post('{{ route("admin.affirmations.store") }}', {
        quote, author, publish_at: publishAt, is_published: published
    }).then(res => {
        if (res.data.success) {
            okDiv.classList.remove('hidden');
            document.getElementById('new-quote').value  = '';
            document.getElementById('new-author').value = '';
            setTimeout(() => { okDiv.classList.add('hidden'); window.location.reload(); }, 1500);
        }
    }).catch(err => {
        errDiv.textContent = err.response?.data?.message ?? 'Failed to create affirmation.';
        errDiv.classList.remove('hidden');
    });
}

// ── Edit ───────────────────────────────────────────────────────
function startEdit(id, quote, author, isPublished) {
    document.querySelector(`.aff-view-${id}`).classList.add('hidden');
    const editBlock = document.querySelector(`.aff-edit-${id}`);
    editBlock.classList.remove('hidden');
    document.getElementById(`edit-quote-${id}`).value     = quote;
    document.getElementById(`edit-author-${id}`).value    = author || '';
    document.getElementById(`edit-published-${id}`).checked = isPublished;
}

function cancelEdit(id) {
    document.querySelector(`.aff-edit-${id}`).classList.add('hidden');
    document.querySelector(`.aff-view-${id}`).classList.remove('hidden');
}

function saveEdit(id) {
    const quote     = document.getElementById(`edit-quote-${id}`).value.trim();
    const author    = document.getElementById(`edit-author-${id}`).value.trim();
    const published = document.getElementById(`edit-published-${id}`).checked;

    if (!quote) { alert('Quote cannot be empty.'); return; }

    axios.put(`/admin/affirmations/${id}`, { quote, author, is_published: published })
        .then(() => { window.location.reload(); })
        .catch(err => alert(err.response?.data?.message ?? 'Update failed.'));
}

// ── Delete ─────────────────────────────────────────────────────
function deleteAffirmation(id) {
    if (!confirm('Delete this affirmation?')) return;

    axios.delete(`/admin/affirmations/${id}`)
        .then(res => {
            if (res.data.success) {
                const row = document.getElementById(`aff-row-${id}`);
                row.style.transition = 'opacity 0.3s';
                row.style.opacity    = '0';
                setTimeout(() => row.remove(), 300);
            }
        })
        .catch(err => alert(err.response?.data?.message ?? 'Delete failed.'));
}
</script>
@endpush
