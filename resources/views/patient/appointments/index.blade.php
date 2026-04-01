@extends('layouts.patient')
@section('title', 'My Appointments')
@section('page-title', 'My Appointments')

@section('content')
<div class="space-y-6">
    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-800 border border-gray-700 rounded-xl p-1 w-fit">
        <button id="tab-upcoming" onclick="switchTab('upcoming')" class="tab-btn px-5 py-2 rounded-lg text-sm font-medium transition-all bg-purple-600 text-white">Upcoming</button>
        <button id="tab-past"     onclick="switchTab('past')"     class="tab-btn px-5 py-2 rounded-lg text-sm font-medium transition-all text-gray-400 hover:text-white">Past</button>
    </div>

    {{-- Upcoming --}}
    <div id="panel-upcoming" class="space-y-3">
        @forelse($upcoming as $appt)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-purple-700/30 rounded-xl flex items-center justify-center text-purple-300 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($appt->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-white font-semibold">Dr. {{ $appt->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-400 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }} • {{ substr($appt->start_time, 0, 5) }}–{{ substr($appt->end_time, 0, 5) }}</p>
                    <p class="text-gray-500 text-xs mt-1 capitalize">{{ $appt->type }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'confirmed' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }} text-xs px-3 py-1 rounded-full capitalize font-medium">
                    {{ $appt->status }}
                </span>
                <a href="{{ route('patient.appointments.show', $appt) }}" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">View</a>
            </div>
        </div>
        @empty
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-10 text-center">
            <p class="text-gray-400 text-sm">No upcoming appointments.</p>
            <a href="{{ route('patient.doctors.index') }}" class="mt-3 inline-block bg-purple-600 hover:bg-purple-700 text-white text-sm px-5 py-2.5 rounded-lg transition-colors">Find a Doctor</a>
        </div>
        @endforelse
    </div>

    {{-- Past --}}
    <div id="panel-past" class="space-y-3 hidden">
        @forelse($past as $appt)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 opacity-80">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center text-gray-400 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($appt->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-white font-semibold">Dr. {{ $appt->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-400 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'completed' ? 'bg-blue-500/20 text-blue-400' : 'bg-red-500/20 text-red-400' }} text-xs px-3 py-1 rounded-full capitalize font-medium">
                    {{ $appt->status }}
                </span>
                <a href="{{ route('patient.appointments.show', $appt) }}" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">View</a>
            </div>
        </div>
        @empty
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-10 text-center">
            <p class="text-gray-400 text-sm">No past appointments.</p>
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
        btn.classList.toggle('bg-purple-600', t === tab);
        btn.classList.toggle('text-white', t === tab);
        btn.classList.toggle('text-gray-400', t !== tab);
    });
}
</script>
@endpush
