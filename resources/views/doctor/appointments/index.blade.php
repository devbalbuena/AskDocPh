@extends('layouts.doctor')
@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
<div class="space-y-5">
    {{-- Status Filter Tabs --}}
    <div class="flex gap-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-1 w-fit flex-wrap">
        @foreach(['all'=>'All','pending'=>'Pending','confirmed'=>'Confirmed','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $label)
        <a href="{{ route('doctor.appointments.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $status === $val ? 'bg-green-600 text-white' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Appointments List --}}
    <div class="space-y-3" id="appointments-list">
        @forelse($appointments as $appt)
        @php
            $typeLabel = $appt->type === 'in_person' ? 'In-Person' : 'Online / Video Call';
            $startFmt  = \Carbon\Carbon::parse($appt->start_time)->format('g:i A');
            $endFmt    = \Carbon\Carbon::parse($appt->end_time)->format('g:i A');
        @endphp
        <div id="appt-card-{{ $appt->id }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-600 flex-shrink-0">
                    {{ strtoupper(substr($appt->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $appt->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">
                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }} •
                        {{ $startFmt }} – {{ $endFmt }}
                    </p>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $typeLabel }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap justify-end">
                {{-- Status badge --}}
                <span id="badge-{{ $appt->id }}" class="
                    @if($appt->status==='pending')   bg-yellow-100 text-yellow-700
                    @elseif($appt->status==='confirmed') bg-green-100 text-green-700
                    @elseif($appt->status==='completed') bg-blue-100 text-blue-700
                    @else bg-red-100 text-red-700 @endif
                    text-xs px-3 py-1 rounded-full capitalize font-medium">
                    {{ $appt->status }}
                </span>

                @if($appt->status === 'confirmed' && \Carbon\Carbon::parse($appt->appointment_date)->isToday())
                    <span class="appointment-countdown bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap" data-datetime="{{ \Carbon\Carbon::parse($appt->appointment_date->format('Y-m-d') . ' ' . $appt->start_time)->toISOString() }}"></span>
                @endif

                {{-- Quick Accept/Decline for pending only --}}
                @if($appt->status === 'pending')
                <div id="quick-actions-{{ $appt->id }}" class="flex gap-2">
                    <button onclick="quickAccept({{ $appt->id }}, '{{ route('doctor.appointments.confirm', $appt) }}')"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded-lg transition-colors">
                        Accept
                    </button>
                    <button onclick="quickDecline({{ $appt->id }}, '{{ route('doctor.appointments.cancel', $appt) }}')"
                        class="bg-red-100 hover:bg-red-200 text-red-600 text-sm px-3 py-1 rounded-lg transition-colors">
                        Decline
                    </button>
                </div>
                @endif

                <a href="{{ route('doctor.appointments.show', $appt) }}"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                    View
                </a>
            </div>
        </div>
        @empty
        {{-- Empty state --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
            <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-700 font-medium text-base">No appointments found</p>
            <p class="text-gray-400 text-sm mt-1">
                @if($status === 'pending')   You have no pending appointments.
                @elseif($status === 'confirmed') You have no confirmed appointments.
                @elseif($status === 'completed') You have no completed appointments.
                @elseif($status === 'cancelled') You have no cancelled appointments.
                @else No appointments have been booked yet.
                @endif
            </p>
        </div>
        @endforelse
    </div>

    @if($appointments->hasPages())
    <div>{{ $appointments->withQueryString()->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function quickAccept(id, url) {
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.textContent = '…';
    axios.post(url, { _token: CSRF }, {
        headers: { 'Accept': 'application/json' }
    }).then(() => {
        updateCardStatus(id, 'confirmed');
    }).catch(err => {
        alert(err.response?.data?.message || 'Failed to accept appointment.');
        btn.disabled = false;
        btn.textContent = 'Accept';
    });
}

function quickDecline(id, url) {
    if (!confirm('Decline this appointment?')) return;
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.textContent = '…';
    axios.post(url, { _token: CSRF }, {
        headers: { 'Accept': 'application/json' }
    }).then(() => {
        updateCardStatus(id, 'cancelled');
    }).catch(err => {
        alert(err.response?.data?.message || 'Failed to decline appointment.');
        btn.disabled = false;
        btn.textContent = 'Decline';
    });
}

function updateCardStatus(id, newStatus) {
    // Update badge
    const badge = document.getElementById(`badge-${id}`);
    if (badge) {
        const classes = {
            confirmed: 'bg-green-100 text-green-700',
            cancelled:  'bg-red-100 text-red-700',
            pending:    'bg-yellow-100 text-yellow-700',
            completed:  'bg-blue-100 text-blue-700',
        };
        badge.className = `${classes[newStatus] || ''} text-xs px-3 py-1 rounded-full capitalize font-medium`;
        badge.textContent = newStatus;
    }
    // Hide quick-action buttons
    const actions = document.getElementById(`quick-actions-${id}`);
    if (actions) actions.remove();
}

function updateCountdowns() {
    document.querySelectorAll('.appointment-countdown').forEach(el => {
        const apptTime = new Date(el.dataset.datetime);
        const now = new Date();
        const diff = apptTime - now;
        if (diff > 0) {
            const hours = Math.floor(diff / 3600000);
            const mins = Math.floor((diff % 3600000) / 60000);
            el.textContent = hours > 0 ? 'In ' + hours + 'h ' + mins + 'm' : 'In ' + mins + 'm';
        } else {
            el.textContent = 'Now';
            el.classList.remove('bg-green-100', 'text-green-700');
            el.classList.add('bg-red-100', 'text-red-600');
        }
    });
}
if(document.querySelectorAll('.appointment-countdown').length > 0) {
    updateCountdowns();
    setInterval(updateCountdowns, 60000);
}
</script>
@endpush
@endsection
