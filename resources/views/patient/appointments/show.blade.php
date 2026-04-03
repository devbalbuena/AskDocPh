@extends('layouts.patient')
@section('title', 'Appointment Detail')
@section('page-title', 'Appointment Detail')

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('patient.appointments.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Appointments
    </a>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
        {{-- Status header --}}
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Appointment</h2>
            <span class="
                @if($appointment->status === 'confirmed')  bg-green-100 text-green-700
                @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-700
                @elseif($appointment->status === 'completed') bg-blue-100 text-blue-700
                @else bg-red-100 text-red-700 @endif
                text-sm px-4 py-1 rounded-full capitalize font-medium">
                {{ $appointment->status }}
            </span>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center text-xl font-bold text-green-700 flex-shrink-0">
                    {{ strtoupper(substr($appointment->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 font-semibold text-lg">Dr. {{ $appointment->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-sm capitalize">{{ $appointment->type === 'in_person' ? 'In-Person' : 'Online / Video Call' }} Consultation</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div class="bg-gray-50/60 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Date</p>
                    <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('D, M d Y') }}</p>
                </div>
                <div class="bg-gray-50/60 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Time</p>
                    <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}</p>
                </div>
            </div>

            <div class="bg-gray-50/60 rounded-xl p-4">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Reason</p>
                <p class="text-gray-900 text-sm">{{ ucfirst($appointment->reason ?? '') }}</p>
            </div>

            @if($appointment->meeting_link && $appointment->status === 'confirmed')
            <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                <p class="text-xs text-blue-400 uppercase tracking-wider mb-1">Meeting Link</p>
                <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-300 text-sm hover:text-blue-200 break-all">{{ $appointment->meeting_link }}</a>
            </div>
            @endif

            {{-- Doctor Notes (patient-visible only) --}}
            @if($appointment->notes->isNotEmpty())
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Doctor's Notes</h3>
                @foreach($appointment->notes as $note)
                <div class="bg-gray-50/60 rounded-xl p-4 space-y-2">
                    <p class="text-gray-700 text-sm">{{ $note->notes }}</p>
                    @if($note->diagnosis)
                    <div><p class="text-xs text-gray-500">Diagnosis:</p><p class="text-gray-700 text-sm">{{ $note->diagnosis }}</p></div>
                    @endif
                    @if($note->recommendations)
                    <div><p class="text-xs text-gray-500">Recommendations:</p><p class="text-gray-700 text-sm">{{ $note->recommendations }}</p></div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Cancel button --}}
        @if(in_array($appointment->status, ['pending', 'confirmed']))
        <div class="px-6 pb-6">
            <form method="POST" action="{{ route('patient.appointments.cancel', $appointment) }}"
                  onsubmit="return confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')">
                @csrf
                <button type="submit" class="w-full bg-red-50 hover:bg-red-600/40 border border-red-500/30 text-red-400 py-3 rounded-xl text-sm font-medium transition-colors">
                    Cancel Appointment
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
