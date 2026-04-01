@extends('layouts.patient')
@section('title', 'Book with Dr. '.$doctor->display_name)
@section('page-title', 'Book Appointment')

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('patient.doctors.index') }}" class="flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Doctors
    </a>

    {{-- Doctor header --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-16 h-16 rounded-xl bg-purple-600/30 flex items-center justify-center text-2xl font-bold text-purple-300 flex-shrink-0">
            {{ strtoupper(substr($doctor->fname, 0, 1)) }}{{ strtoupper(substr($doctor->lname, 0, 1)) }}
        </div>
        <div>
            <p class="text-lg font-bold text-white">Dr. {{ $doctor->display_name }}</p>
            <p class="text-gray-400 text-sm">{{ $doctor->bio ?? 'Mental Health Professional' }}</p>
        </div>
    </div>

    {{-- Availability --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <h3 class="text-base font-semibold text-white mb-4">Weekly Availability</h3>
        @if($schedules->isEmpty())
        <p class="text-gray-500 text-sm">No available slots at this time.</p>
        @else
        <div class="space-y-3">
            @foreach($schedules as $day => $slots)
            <div class="flex items-start gap-3">
                <p class="text-gray-400 text-sm w-24 capitalize pt-1.5">{{ $day }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($slots as $slot)
                    <button type="button"
                        onclick="selectSlot('{{ $slot->id }}', '{{ $day }}', '{{ substr($slot->start_time, 0, 5) }}', '{{ substr($slot->end_time, 0, 5) }}')"
                        id="slot-{{ $slot->id }}"
                        class="slot-btn px-3 py-1.5 rounded-lg border border-gray-600 text-gray-300 text-xs hover:border-purple-500 hover:text-purple-400 transition-all">
                        {{ substr($slot->start_time, 0, 5) }} – {{ substr($slot->end_time, 0, 5) }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Booking Form --}}
    <form id="booking-form" method="POST" action="{{ route('patient.appointments.store') }}" class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-4">
        @csrf
        <h3 class="text-base font-semibold text-white">Booking Details</h3>

        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
        <input type="hidden" name="schedule_id" id="selected_schedule_id" value="">
        <input type="hidden" name="start_time" id="selected_start" value="">
        <input type="hidden" name="end_time" id="selected_end" value="">

        <div id="selected-slot-display" class="hidden bg-purple-600/10 border border-purple-500/30 rounded-lg px-4 py-2.5 text-purple-300 text-sm"></div>

        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Appointment Date</label>
            <input type="date" name="appointment_date" min="{{ today()->toDateString() }}"
                   class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
            @error('appointment_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Consultation Type</label>
            <select name="type" class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
                <option value="online">Online (Video Call)</option>
                <option value="in_person">In-Person</option>
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Reason for Visit</label>
            <textarea name="reason" rows="3" placeholder="Briefly describe what you'd like to discuss..."
                      class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 resize-none"></textarea>
            @error('reason')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 text-red-400 text-sm">
            @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
        </div>
        @endif

        <button type="submit" id="submit-btn" disabled
                class="w-full bg-purple-600 hover:bg-purple-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium py-3 rounded-xl transition-colors">
            Confirm Booking
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectSlot(id, day, start, end) {
    document.querySelectorAll('.slot-btn').forEach(b => {
        b.classList.remove('border-purple-500', 'text-purple-400', 'bg-purple-600/10');
    });
    const btn = document.getElementById('slot-' + id);
    btn.classList.add('border-purple-500', 'text-purple-400', 'bg-purple-600/10');

    document.getElementById('selected_schedule_id').value = id;
    document.getElementById('selected_start').value = start + ':00';
    document.getElementById('selected_end').value = end + ':00';

    const display = document.getElementById('selected-slot-display');
    display.textContent = `Selected: ${day.charAt(0).toUpperCase() + day.slice(1)} ${start} – ${end}`;
    display.classList.remove('hidden');
    document.getElementById('submit-btn').disabled = false;
}
</script>
@endpush
