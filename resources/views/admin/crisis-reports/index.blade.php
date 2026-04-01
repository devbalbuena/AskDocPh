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
                                      'pending'    => 'bg-yellow-100 text-yellow-700',
                                      'responding' => 'bg-blue-100 text-blue-700',
                                      'resolved'   => 'bg-green-100 text-green-700',
                                      default      => 'bg-gray-100 text-gray-500',
                                  } }} text-xs px-2.5 py-1 rounded-full capitalize font-medium">
                                {{ $report->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2" id="actions-{{ $report->id }}">
                                @if($report->status === 'pending')
                                <button onclick="crisisAction({{ $report->id }}, 'respond')"
                                        id="respond-btn-{{ $report->id }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                    Respond
                                </button>
                                @endif
                                @if(in_array($report->status, ['pending','responding']))
                                <button onclick="crisisAction({{ $report->id }}, 'resolve')"
                                        id="resolve-btn-{{ $report->id }}"
                                        class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                    Resolve
                                </button>
                                @endif
                                @if($report->status === 'resolved')
                                <span class="text-xs text-gray-400 italic">Resolved</span>
                                @endif
                            </div>
                        </td>
                    </tr>
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
@endsection

@push('scripts')
<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function crisisAction(id, action) {
    const url = `/admin/crisis-reports/${id}/${action}`;

    axios.post(url)
        .then(res => {
            if (!res.data.success) return;
            const newStatus = res.data.status;

            // Update the status badge
            const badge = document.getElementById(`status-badge-${id}`);
            badge.textContent = newStatus;
            badge.className = 'text-xs px-2.5 py-1 rounded-full capitalize font-medium '
                + (newStatus === 'resolved'   ? 'bg-green-100 text-green-700'
                :  newStatus === 'responding' ? 'bg-blue-100 text-blue-700'
                :                               'bg-yellow-100 text-yellow-700');

            // Update the action buttons
            const actionsDiv = document.getElementById(`actions-${id}`);
            if (newStatus === 'responding') {
                // Remove Respond button, keep Resolve
                const respondBtn = document.getElementById(`respond-btn-${id}`);
                if (respondBtn) respondBtn.remove();
            } else if (newStatus === 'resolved') {
                // Remove all buttons, show resolved text
                actionsDiv.innerHTML = '<span class="text-xs text-gray-400 italic">Resolved</span>';
            }
        })
        .catch(err => alert(err.response?.data?.message ?? 'Action failed. Please try again.'));
}
</script>
@endpush
