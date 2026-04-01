@extends('layouts.patient')
@section('title', 'Book with Dr. '.$doctor->display_name)
@section('page-title', 'Book Appointment')

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('patient.doctors.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Doctors
    </a>

    {{-- Doctor header --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-16 h-16 rounded-xl bg-green-100 flex items-center justify-center text-2xl font-bold text-green-700 flex-shrink-0">
            {{ strtoupper(substr($doctor->fname, 0, 1)) }}{{ strtoupper(substr($doctor->lname, 0, 1)) }}
        </div>
        <div>
            <p class="text-lg font-bold text-gray-900">Dr. {{ $doctor->display_name }}</p>
            <p class="text-gray-500 text-sm">{{ $doctor->bio ?? 'Mental Health Professional' }}</p>
        </div>
    </div>

    {{-- Availability --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Weekly Availability</h3>
        @if($schedules->isEmpty())
        <p class="text-gray-500 text-sm">No available slots at this time.</p>
        @else
        <div class="space-y-3">
            @foreach($schedules as $day => $slots)
            <div class="flex items-start gap-3">
                <p class="text-gray-500 text-sm w-24 capitalize pt-1.5">{{ $day }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($slots as $slot)
                    <button type="button"
                        data-start="{{ $slot->start_time }}"
                        data-end="{{ $slot->end_time }}"
                        data-schedule-id="{{ $slot->id }}"
                        id="slot-{{ $slot->id }}"
                        class="slot-btn px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-xs hover:border-green-500 hover:text-green-600 transition-all">
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
    <form id="booking-form" method="POST" action="{{ route('patient.appointments.store') }}" class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
        @csrf
        <h3 class="text-base font-semibold text-gray-900">Booking Details</h3>

        <input type="hidden" name="start_time" id="start_time">
        <input type="hidden" name="end_time" id="end_time">
        <input type="hidden" name="schedule_id" id="schedule_id">
        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

        <div id="selected-slot-display" class="hidden bg-green-50 border border-green-200 rounded-lg px-4 py-2.5 text-green-700 text-sm"></div>

        <div>
            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Appointment Date</label>
            <input type="date" name="appointment_date" min="{{ today()->toDateString() }}"
                   class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
            @error('appointment_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Consultation Type</label>
            <select name="type" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                <option value="online">Online (Video Call)</option>
                <option value="in_person">In-Person</option>
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Reason for Visit</label>
            <textarea name="reason" rows="3" placeholder="Briefly describe what you'd like to discuss..."
                      class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 resize-none"></textarea>
            @error('reason')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 text-red-400 text-sm">
            @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
        </div>
        @endif

        <button type="submit" id="submit-btn" disabled
                class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-900 font-medium py-3 rounded-xl transition-colors">
            Confirm Booking
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.slot-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('start_time').value = this.dataset.start;
        document.getElementById('end_time').value = this.dataset.end;
        document.getElementById('schedule_id').value = this.dataset.scheduleId;
        
        document.querySelectorAll('.slot-btn').forEach(b => {
             b.classList.remove('bg-green-600', 'text-gray-900');
             b.classList.add('text-gray-700');
        });
        
        this.classList.remove('text-gray-700');
        this.classList.add('bg-green-600', 'text-gray-900');
        
        document.getElementById('submit-btn').disabled = false;
        
        const display = document.getElementById('selected-slot-display');
        display.textContent = `Selected: ${this.dataset.start} - ${this.dataset.end}`;
        display.classList.remove('hidden');
    });
});
</script>
@endpush
