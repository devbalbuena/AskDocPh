@extends('layouts.doctor')
@section('title', 'Appointment Detail')
@section('page-title', 'Appointment Detail')

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('doctor.appointments.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 w-fit transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back
    </a>

    {{-- Detail card --}}
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Appointment</h2>
            <span class="
                @if($appointment->status==='confirmed')  bg-green-100 text-green-700
                @elseif($appointment->status==='pending') bg-yellow-100 text-yellow-700
                @elseif($appointment->status==='completed') bg-blue-100 text-blue-700
                @else bg-red-100 text-red-700 @endif
                text-sm px-4 py-1 rounded-full capitalize font-medium">
                {{ $appointment->status }}
            </span>
        </div>

        <div class="p-6 space-y-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center text-xl font-bold text-blue-300">
                    {{ strtoupper(substr($appointment->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 font-semibold text-lg">{{ $appointment->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-sm">{{ $appointment->patient->email ?? '' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50/60 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Date</p>
                    <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('D, M d Y') }}</p>
                </div>
                <div class="bg-gray-50/60 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Time</p>
                    <p class="text-gray-900 font-medium">{{ substr($appointment->start_time, 0, 5) }} – {{ substr($appointment->end_time, 0, 5) }}</p>
                </div>
            </div>

            <div class="bg-gray-50/60 rounded-xl p-4">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Reason</p>
                <p class="text-gray-900 text-sm">{{ $appointment->reason }}</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3 pt-2">
                @if($appointment->status === 'pending')
                <form method="POST" action="{{ route('doctor.appointments.confirm', $appointment) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-gray-900 text-sm px-5 py-2.5 rounded-lg transition-colors">✓ Confirm</button>
                </form>
                @endif
                @if($appointment->status === 'confirmed')
                <form method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-gray-900 text-sm px-5 py-2.5 rounded-lg transition-colors">✓ Mark Completed</button>
                </form>
                @endif
                @if(in_array($appointment->status, ['pending','confirmed']))
                <form method="POST" action="{{ route('doctor.appointments.cancel', $appointment) }}" onsubmit="return confirm('Cancel appointment?')">
                    @csrf
                    <button type="submit" class="bg-red-50 border border-red-500/30 text-red-400 hover:bg-red-600/40 text-sm px-5 py-2.5 rounded-lg transition-colors">Cancel</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
        <h3 class="text-base font-semibold text-gray-900">Appointment Notes</h3>
        @foreach($appointment->notes as $note)
        <div class="bg-gray-50/60 rounded-xl p-4 space-y-2 border border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">{{ $note->created_at->format('M d, Y H:i') }}</p>
                <span class="{{ $note->is_visible_to_patient ? 'text-green-400' : 'text-gray-500' }} text-xs">
                    {{ $note->is_visible_to_patient ? '👁 Visible to patient' : '🔒 Private' }}
                </span>
            </div>
            <p class="text-gray-700 text-sm">{{ $note->notes }}</p>
            @if($note->diagnosis)<p class="text-xs text-gray-500"><span class="text-gray-500">Diagnosis:</span> {{ $note->diagnosis }}</p>@endif
            @if($note->recommendations)<p class="text-xs text-gray-500"><span class="text-gray-500">Recommendations:</span> {{ $note->recommendations }}</p>@endif
        </div>
        @endforeach

        {{-- Add Note Form --}}
        <form method="POST" action="{{ route('doctor.appointments.notes.store', $appointment) }}" class="space-y-3 pt-2 border-t border-gray-200">
            @csrf
            <h4 class="text-sm font-medium text-gray-900">Add Note</h4>
            <textarea name="notes" rows="3" placeholder="Clinical notes..." class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 resize-none" required></textarea>
            <input type="text" name="diagnosis" placeholder="Diagnosis (optional)" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500">
            <input type="text" name="recommendations" placeholder="Recommendations (optional)" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500">
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="is_visible_to_patient" value="1" class="w-4 h-4 accent-purple-500">
                Visible to patient
            </label>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-gray-900 text-sm px-5 py-2.5 rounded-lg transition-colors">Save Note</button>
        </form>
    </div>
</div>
@endsection
