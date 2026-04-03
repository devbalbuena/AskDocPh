@extends('layouts.patient')
@section('title', 'My Appointments')
@section('page-title', 'My Appointments')

@section('content')
<div class="space-y-6">
    {{-- Tabs --}}
    <div class="flex gap-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-1 w-fit">
        <button id="tab-upcoming" onclick="switchTab('upcoming')" class="tab-btn px-5 py-2 rounded-xl text-sm font-medium transition-all bg-green-600 text-white">Upcoming</button>
        <button id="tab-past"     onclick="switchTab('past')"     class="tab-btn px-5 py-2 rounded-xl text-sm font-medium transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50">Past</button>
    </div>

    {{-- Upcoming --}}
    <div id="panel-upcoming" class="space-y-3">
        @forelse($upcoming as $appt)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-green-700/30 rounded-xl flex items-center justify-center text-green-700 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($appt->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 font-semibold">Dr. {{ $appt->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }} • {{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($appt->end_time)->format('g:i A') }}</p>
                    <p class="text-gray-500 text-xs mt-1 capitalize">{{ $appt->type === 'in_person' ? 'In-Person' : 'Online / Video Call' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-xs px-3 py-1 rounded-full capitalize font-medium">
                    {{ $appt->status }}
                </span>
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
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <p class="text-gray-500 text-sm">No upcoming appointments.</p>
            <a href="{{ route('patient.doctors.index') }}" class="mt-3 inline-block bg-green-600 hover:bg-green-700 text-gray-900 text-sm px-5 py-2.5 rounded-lg transition-colors">Find a Doctor</a>
        </div>
        @endforelse
    </div>

    {{-- Past --}}
    <div id="panel-past" class="space-y-3 hidden">
        @forelse($past as $appt)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 opacity-80">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center text-gray-500 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($appt->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 font-semibold">Dr. {{ $appt->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }}</p>
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
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <p class="text-gray-500 text-sm">No past appointments.</p>
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
</script>
@endpush
