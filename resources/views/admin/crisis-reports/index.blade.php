@extends('layouts.admin')
@section('title', 'Crisis Reports')
@section('page-title', 'Crisis Reports')

@section('content')
<div class="space-y-5">
    {{-- Status Filter --}}
    <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 w-fit flex-wrap">
        @foreach(['all'=>'All','pending'=>'Pending','responding'=>'Responding','resolved'=>'Resolved'] as $val => $label)
        <a href="{{ route('admin.crisis.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all
                  {{ $status === $val ? 'bg-green-600 text-white' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Patient</th>
                        <th class="text-left px-5 py-3">Description</th>
                        <th class="text-left px-5 py-3">Submitted</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="border-b border-gray-200/50 hover:bg-gray-50 transition-colors" id="crisis-row-{{ $report->id }}">
                        <td class="px-5 py-4">
                            <p class="text-gray-900 font-medium">{{ $report->user->display_name ?? 'Unknown' }}</p>
                            <p class="text-gray-400 text-xs">{{ $report->user->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-500 max-w-xs">
                            <p class="line-clamp-2 text-sm">{{ $report->description }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">
                            {{ $report->created_at->format('M d, Y') }}<br>
                            <span class="text-gray-400">{{ $report->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span id="status-badge-{{ $report->id }}"
                                  class="{{ match($report->status) {
                                      'pending'    => 'bg-red-100 text-red-700 font-semibold',
                                      'responding' => 'bg-orange-100 text-orange-700',
                                      'resolved'   => 'bg-green-100 text-green-700',
                                      default      => 'bg-gray-100 text-gray-500',
                                  } }} inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full capitalize">
                                @if($report->status === 'pending')
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse flex-shrink-0"></span>
                                @endif
                                {{ $report->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2" id="actions-{{ $report->id }}">
                                @if($report->status === 'pending')
                                <button onclick="crisisAction({{ $report->id }}, 'respond')"
                                        id="respond-btn-{{ $report->id }}"
                                        class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                                    Respond Now
                                </button>
                                @elseif($report->status === 'responding')
                                <button onclick="crisisAction({{ $report->id }}, 'resolve')"
                                        id="resolve-btn-{{ $report->id }}"
                                        class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                    Mark Resolved
                                </button>
                                @elseif($report->status === 'resolved')
                                <button onclick="openCrisisModal({{ $report->id }})"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                    View Details
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Embed report data as JSON for the modal --}}
                    @if($report->status === 'resolved')
                    <script>
                    window.__crisisData = window.__crisisData || {};
                    window.__crisisData[{{ $report->id }}] = {
                        patient:      @json($report->user->display_name ?? 'Unknown'),
                        description:  @json($report->description),
                        respondedBy:  @json($report->respondedBy->display_name ?? '—'),
                        respondedAt:  @json($report->responded_at?->format('M d, Y H:i') ?? '—'),
                        resolvedAt:   @json($report->resolved_at?->format('M d, Y H:i') ?? '—'),
                    };
                    </script>
                    @endif
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500 font-medium">No crisis reports found</p>
                            <p class="text-gray-400 text-sm mt-1">
                                {{ $status !== 'all' ? "No $status reports at this time." : 'All clear!' }}
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $reports->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

{{-- View Details Modal --}}
<div id="crisis-modal" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-20 px-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCrisisModal()"></div>
    {{-- Panel --}}
    <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                <h3 class="text-base font-semibold text-gray-900">Crisis Report Details</h3>
            </div>
            <button onclick="closeCrisisModal()" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors text-lg leading-none">
                &times;
            </button>
        </div>
        {{-- Body --}}
        <div class="px-6 py-5 space-y-4" id="crisis-modal-body">
            {{-- Populated by JS --}}
        </div>
        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button onclick="closeCrisisModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.__crisisData = window.__crisisData || {};

function crisisAction(id, action) {
    const url = `/admin/crisis-reports/${id}/${action}`;

    axios.post(url)
        .then(res => {
            if (!res.data.success) return;
            const newStatus = res.data.status;

            // Update the status badge
            const badge = document.getElementById(`status-badge-${id}`);
            if (newStatus === 'responding') {
                badge.className = 'inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full capitalize bg-orange-100 text-orange-700';
                badge.textContent = 'responding';
            } else if (newStatus === 'resolved') {
                badge.className = 'inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full capitalize bg-green-100 text-green-700';
                badge.textContent = 'resolved';
            }

            // Update action buttons
            const actionsDiv = document.getElementById(`actions-${id}`);
            if (newStatus === 'responding') {
                actionsDiv.innerHTML = `
                    <button onclick="crisisAction(${id}, 'resolve')"
                            class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                        Mark Resolved
                    </button>`;
            } else if (newStatus === 'resolved') {
                actionsDiv.innerHTML = `
                    <button onclick="openCrisisModal(${id})"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                        View Details
                    </button>`;
            }
        })
        .catch(err => alert(err.response?.data?.message ?? 'Action failed. Please try again.'));
}

function openCrisisModal(id) {
    const data = window.__crisisData[id];
    if (!data) {
        alert('Details not available. Please refresh the page.');
        return;
    }

    const body = document.getElementById('crisis-modal-body');
    body.innerHTML = `
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Patient</p>
            <p class="text-gray-900 font-medium">${escHtml(data.patient)}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Description</p>
            <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">${escHtml(data.description)}</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Responded By</p>
                <p class="text-gray-700 text-sm">${escHtml(data.respondedBy)}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Responded At</p>
                <p class="text-gray-700 text-sm">${escHtml(data.respondedAt)}</p>
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Resolved At</p>
            <p class="text-gray-700 text-sm">${escHtml(data.resolvedAt)}</p>
        </div>
    `;

    document.getElementById('crisis-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCrisisModal() {
    document.getElementById('crisis-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

// Close modal on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeCrisisModal();
});
</script>
@endpush
