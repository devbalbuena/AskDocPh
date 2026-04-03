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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-16 h-16 rounded-xl bg-green-100 flex items-center justify-center text-2xl font-bold text-green-700 flex-shrink-0 overflow-hidden">
            @if($doctor->profile_photo)
                <img src="{{ Storage::url($doctor->profile_photo) }}" class="w-full h-full object-cover">
            @else
                {{ strtoupper(substr($doctor->fname, 0, 1)) }}{{ strtoupper(substr($doctor->lname, 0, 1)) }}
            @endif
        </div>
        <div>
            <p class="text-lg font-bold text-gray-900">Dr. {{ $doctor->display_name }}</p>
            @php
            $professional = json_decode($doctor->bio ?? '{}', true) ?? [];
            @endphp
            @if(!empty($professional['specialization']))
            <p class="text-gray-500 text-sm mt-1">
                {{ $professional['specialization'] }}
            </p>
            @endif
        </div>
    </div>

    {{-- Availability --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
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
                    <div class="px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 text-xs">
                        {{ $slot['formatted'] }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Booking Form --}}
    <form id="booking-form" method="POST" action="{{ route('patient.appointments.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        <h3 class="text-base font-semibold text-gray-900">Booking Details</h3>

        <input type="hidden" name="start_time" id="start_time">
        <input type="hidden" name="end_time" id="end_time">
        <input type="hidden" name="schedule_id" id="schedule_id">
        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

        <div id="selected-slot-display" class="hidden bg-green-50 border border-green-200 rounded-lg px-4 py-2.5 text-green-700 text-sm"></div>

        <div>
            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Appointment Date</label>
            <input type="date" id="appointment_date" name="appointment_date" min="{{ today()->toDateString() }}"
                   class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
            @error('appointment_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            <div id="slots-container" class="mt-3 flex flex-wrap gap-2"></div>
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
                class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed text-white shadow-sm font-medium py-3.5 rounded-xl transition-colors">
            Confirm Booking
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
const schedules = @json($schedules);
const availableDays = Object.keys(schedules);

const dateInput = document.getElementById('appointment_date');
const slotsContainer = document.getElementById('slots-container');

const today = new Date();
today.setHours(0, 0, 0, 0);

if (dateInput) {
    dateInput.addEventListener('change', function() {
        slotsContainer.innerHTML = '';
        document.getElementById('start_time').value = '';
        document.getElementById('end_time').value = '';
        document.getElementById('schedule_id').value = '';
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('selected-slot-display').classList.add('hidden');

        if (!this.value) return;

        const [year, month, day] = this.value.split('-');
        const date = new Date(year, month - 1, day);
        
        if (date < today) {
            alert('Please select a future date.');
            this.value = '';
            return;
        }

        const days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        const dayName = days[date.getDay()];

        if (!availableDays.includes(dayName)) {
            this.value = '';
            alert('Doctor is not available on this day. Please select a different date.');
            return;
        }

        showSlotsForDay(dayName);
    });
}

function showSlotsForDay(dayName) {
    const slots = schedules[dayName];
    if (!slots || slots.length === 0) return;

    slots.forEach(slot => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'slot-btn px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs hover:border-green-500 hover:text-green-600 transition-all';
        btn.textContent = slot.formatted;
        btn.dataset.start = slot.start_time;
        btn.dataset.end = slot.end_time;
        btn.dataset.scheduleId = slot.id;

        btn.addEventListener('click', function() {
            document.getElementById('start_time').value = this.dataset.start;
            document.getElementById('end_time').value = this.dataset.end;
            document.getElementById('schedule_id').value = this.dataset.scheduleId;
            
            document.querySelectorAll('#slots-container .slot-btn').forEach(b => {
                 b.classList.remove('bg-green-600', 'text-white', 'border-green-600');
                 b.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
            });
            
            this.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
            this.classList.add('bg-green-600', 'text-white', 'border-green-600');
            
            document.getElementById('submit-btn').disabled = false;
            
            const display = document.getElementById('selected-slot-display');
            display.textContent = `Selected: ${this.textContent}`;
            display.classList.remove('hidden');
        });

        slotsContainer.appendChild(btn);
    });
}
</script>
@endpush
