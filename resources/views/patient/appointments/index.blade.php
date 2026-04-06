@extends('layouts.patient')
@section('title', 'My Appointments')
@section('page-title', 'My Appointments')

@section('content')
<div class="space-y-6">
    {{-- Tabs --}}
    <div class="flex gap-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-1 w-fit">
        <button id="tab-upcoming" onclick="switchTab('upcoming')" class="tab-btn px-5 py-2 rounded-xl text-sm font-medium transition-all bg-green-600 text-white">Upcoming</button>
        <button id="tab-past"     onclick="switchTab('past')"     class="tab-btn px-5 py-2 rounded-xl text-sm font-medium transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50">Past</button>
    </div>

    {{-- Upcoming --}}
    <div id="panel-upcoming" class="space-y-3">
        @forelse($upcoming as $appt)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-green-700/30 rounded-xl flex items-center justify-center text-green-700 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($appt->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 dark:text-gray-100 font-semibold">Dr. {{ $appt->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }} • {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($appt->end_time)->format('g:i A') }}</p>
                    <p class="text-gray-500 text-xs mt-1 capitalize">{{ $appt->type === 'in_person' ? 'In-Person' : 'Online / Video Call' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-xs px-3 py-1 rounded-full capitalize font-medium flex items-center">
                    {{ $appt->status }}
                </span>
                @if($appt->status === 'confirmed' && \Carbon\Carbon::parse($appt->appointment_date)->isToday())
                    <span class="appointment-countdown bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap" data-datetime="{{ \Carbon\Carbon::parse($appt->appointment_date->format('Y-m-d') . ' ' . $appt->start_time)->toISOString() }}"></span>
                @endif
                <a href="{{ route('patient.appointments.show', $appt) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">View</a>
                @if(in_array($appt->status, ['pending', 'confirmed']))
                <form method="POST" action="{{ route('patient.appointments.cancel', $appt) }}"
                      onsubmit="return confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')">
                    @csrf
                    <button type="submit" class="bg-red-50 text-red-600 border border-red-200 text-sm px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">Cancel</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-10 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm">No upcoming appointments.</p>
            <a href="{{ route('patient.doctors.index') }}" class="mt-3 inline-block bg-green-600 hover:bg-green-700 text-gray-900 text-sm px-5 py-2.5 rounded-lg transition-colors">Find a Doctor</a>
        </div>
        @endforelse
    </div>

    {{-- Past --}}
    <div id="panel-past" class="space-y-3 hidden">
        @forelse($past as $appt)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 opacity-80">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center text-gray-500 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($appt->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 dark:text-gray-100 font-semibold">Dr. {{ $appt->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }} text-xs px-3 py-1 rounded-full capitalize font-medium">
                    {{ $appt->status }}
                </span>
                <a href="{{ route('patient.appointments.show', $appt) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">View</a>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-10 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm">No past appointments.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    ['upcoming','past'].forEach(t => {
        document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
        const btn = document.getElementById('tab-' + t);
        btn.classList.toggle('bg-green-600', t === tab);
        btn.classList.toggle('text-white', t === tab);
        btn.classList.toggle('text-gray-500', t !== tab);
    });
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
