@extends('layouts.doctor')
@section('title', 'My Schedule')
@section('page-title', 'My Schedule')

@section('content')
<div class="space-y-6">

    {{-- Main grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ── Left: Current Slots ── --}}
        <div class="xl:col-span-2 space-y-3">
            <h3 class="text-base font-semibold text-gray-900">Current Availability Slots</h3>
            @php $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']; @endphp

            <div id="slots-container" class="space-y-3">
            @foreach($days as $day)
            @php $daySlots = $schedules->where('day_of_week', $day); @endphp
            @if($daySlots->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" data-day="{{ $day }}">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/60">
                    <p class="text-sm font-medium text-gray-900 capitalize">{{ $day }}</p>
                </div>
                @foreach($daySlots as $slot)
                @php
                    $startFmt = \Carbon\Carbon::parse($slot->start_time)->format('g:i A');
                    $endFmt   = \Carbon\Carbon::parse($slot->end_time)->format('g:i A');
                @endphp
                <div id="slot-row-{{ $slot->id }}" class="border-b border-gray-200/50 last:border-0">
                    {{-- Display row --}}
                    <div class="slot-display flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="{{ $slot->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} text-xs px-2 py-0.5 rounded-full">
                                {{ $slot->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                            <span class="text-gray-900 text-sm font-medium">{{ $startFmt }} – {{ $endFmt }}</span>
                            <span class="text-gray-400 text-xs">{{ $slot->slot_duration_minutes }}min slots</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="openEditForm({{ $slot->id }}, '{{ $slot->day_of_week }}', '{{ $slot->start_time }}', '{{ $slot->end_time }}', {{ $slot->slot_duration_minutes }}, {{ $slot->is_available ? 'true' : 'false' }})"
                                class="bg-white border border-green-600 text-green-600 text-sm px-3 py-1 rounded-lg hover:bg-green-50 transition-colors">
                                Edit
                            </button>
                            <button onclick="removeSlot({{ $slot->id }}, this)"
                                class="text-red-400 hover:text-red-600 text-sm px-3 py-1 rounded-lg hover:bg-red-50 transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>
                    {{-- Inline edit form (hidden initially) --}}
                    <div class="slot-edit hidden px-4 py-4 bg-green-50/40 border-t border-green-100 space-y-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Editing: <span class="capitalize text-gray-700">{{ $slot->day_of_week }}</span></p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Start Time</label>
                                <input type="time" class="edit-start w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-green-500" value="{{ $slot->start_time }}">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">End Time</label>
                                <input type="time" class="edit-end w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-green-500" value="{{ $slot->end_time }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Slot Duration</label>
                                <select class="edit-duration w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                                    <option value="15" {{ ($slot->slot_duration_minutes % 15 === 0 && $slot->slot_duration_minutes <= 15) ? 'selected' : '' }}>15 min</option>
                                    <option value="30" {{ ($slot->slot_duration_minutes >= 30 && $slot->slot_duration_minutes < 45) ? 'selected' : '' }}>30 min</option>
                                    <option value="45" {{ ($slot->slot_duration_minutes >= 45 && $slot->slot_duration_minutes < 60) ? 'selected' : '' }}>45 min</option>
                                    <option value="60" {{ $slot->slot_duration_minutes >= 60 ? 'selected' : '' }}>60 min</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Buffer Time</label>
                                <select class="edit-buffer w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                                    <option value="0">None</option>
                                    <option value="5">+ 5 min buffer</option>
                                    <option value="10">+ 10 min buffer</option>
                                    <option value="15">+ 15 min buffer</option>
                                    <option value="30">+ 30 min buffer</option>
                                </select>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" class="edit-available w-4 h-4 accent-green-600" {{ $slot->is_available ? 'checked' : '' }}>
                            Mark as Available
                        </label>
                        <p class="edit-error text-red-500 text-xs hidden"></p>
                        <div class="flex gap-2">
                            <button onclick="saveEdit({{ $slot->id }}, this)"
                                class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-1.5 rounded-lg transition-colors">
                                Save
                            </button>
                            <button onclick="cancelEdit({{ $slot->id }})"
                                class="bg-white border border-gray-300 text-gray-600 text-sm px-4 py-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @endforeach
            </div>

            @if($schedules->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
                <p class="text-gray-500 text-sm">No schedule slots yet. Add your availability using the form.</p>
            </div>
            @endif

            {{-- Global success / error toast (JS-injected) --}}
            <div id="schedule-toast" class="hidden text-sm px-4 py-3 rounded-lg"></div>
        </div>

        {{-- ── Right: Add Slot Form ── --}}
        <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 h-fit">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Add Availability Slot</h3>
                <div class="space-y-4" id="add-slot-form">
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Day</label>
                        <select id="add-day" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                            @foreach($days as $day)
                            <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Start Time</label>
                            <input type="time" id="add-start" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">End Time</label>
                            <input type="time" id="add-end" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Slot Duration</label>
                        <select id="add-duration" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                            <option value="15">15 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Buffer Time</label>
                        <select id="add-buffer" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                            <option value="0">None</option>
                            <option value="5">5 min buffer</option>
                            <option value="10">10 min buffer</option>
                            <option value="15">15 min buffer</option>
                            <option value="30">30 min buffer</option>
                        </select>
                        <p id="add-duration-preview" class="text-xs text-gray-400 mt-1"></p>
                    </div>

                    {{-- Recurring / Specific date toggle --}}
                    <div class="border border-gray-200 rounded-xl p-3 space-y-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" id="add-recurring" checked class="w-4 h-4 accent-green-600">
                            <span>Recurring weekly</span>
                        </label>
                        <div id="specific-date-wrap" class="hidden">
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1.5">Specific Date</label>
                            <input type="date" id="add-specific-date" min="{{ date('Y-m-d') }}"
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-green-500">
                            <p class="text-xs text-gray-400 mt-1">Day of week will be auto-filled from the selected date.</p>
                        </div>
                    </div>

                    <p id="add-slot-error" class="text-red-500 text-xs hidden"></p>
                    <button id="add-slot-btn" onclick="addSlot()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-xl text-sm transition-colors">
                        Add Slot
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Block Off Dates ── --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
        <div>
            <h3 class="text-base font-semibold text-gray-900">Block Off Dates</h3>
            <p class="text-xs text-gray-400 mt-0.5">Mark specific dates when you are unavailable.</p>
        </div>

        {{-- Warning note --}}
        <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-amber-700 text-xs">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.293 4.293a1 1 0 011.414 0l7 7a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7a1 1 0 010-1.414l7-7z"/></svg>
            <span><strong>Note:</strong> Blocked dates are temporary and will reset on server restart. A permanent solution will be added soon.</span>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <input type="date" id="block-date-input" min="{{ date('Y-m-d') }}"
                class="bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-green-500">
            <input type="text" id="block-reason-input" placeholder="Reason (optional)"
                class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-green-500">
            <button onclick="blockDate()"
                class="bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2 rounded-lg transition-colors shrink-0">
                Block Date
            </button>
        </div>

        {{-- Blocked dates list --}}
        <div id="blocked-list" class="space-y-2">
            @forelse($blockedDates as $bd)
            <div id="blocked-entry-{{ $bd['id'] }}" data-date="{{ $bd['date'] }}" class="flex items-center justify-between bg-red-50 border border-red-100 rounded-lg px-4 py-2.5">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($bd['date'])->format('D, M d Y') }}</p>
                    @if($bd['reason'])
                    <p class="text-xs text-gray-400">{{ $bd['reason'] }}</p>
                    @endif
                </div>
                <button onclick="unblockDate('{{ $bd['date'] }}', '{{ $bd['id'] }}', this)"
                    class="text-red-400 hover:text-red-600 text-xs px-3 py-1 rounded-lg hover:bg-red-100 transition-colors">
                    Remove
                </button>
            </div>
            @empty
            <p id="blocked-empty" class="text-gray-400 text-sm italic">No dates blocked yet.</p>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Recurring / Specific date toggle ─────────────────────────────────────────
const recurringChk  = document.getElementById('add-recurring');
const specificWrap  = document.getElementById('specific-date-wrap');
const specificInput = document.getElementById('add-specific-date');
const daySelect     = document.getElementById('add-day');
const dayNames      = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];

recurringChk.addEventListener('change', () => {
    if (recurringChk.checked) {
        specificWrap.classList.add('hidden');
        specificInput.value = '';
    } else {
        specificWrap.classList.remove('hidden');
    }
});

specificInput.addEventListener('change', () => {
    if (specificInput.value) {
        const d = new Date(specificInput.value + 'T00:00:00');
        daySelect.value = dayNames[d.getDay()];
    }
});

// ── Duration preview ───────────────────────────────────────────────────────────
function updateDurationPreview() {
    const dur = parseInt(document.getElementById('add-duration').value) || 0;
    const buf = parseInt(document.getElementById('add-buffer').value) || 0;
    const p   = document.getElementById('add-duration-preview');
    if (buf > 0) {
        p.textContent = `${dur} min appointment + ${buf} min buffer = ${dur + buf} min total`;
    } else {
        p.textContent = '';
    }
}
document.getElementById('add-duration').addEventListener('change', updateDurationPreview);
document.getElementById('add-buffer').addEventListener('change', updateDurationPreview);

// ── Toast helper ───────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.getElementById('schedule-toast');
    t.textContent = msg;
    t.className = `text-sm px-4 py-3 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-600 border border-red-200'}`;
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 4000);
}

// ── Add Slot ───────────────────────────────────────────────────────────────────
function addSlot() {
    const day      = document.getElementById('add-day').value;
    const start    = document.getElementById('add-start').value;
    const end      = document.getElementById('add-end').value;
    const duration = document.getElementById('add-duration').value;
    const buffer   = document.getElementById('add-buffer').value;
    const specific = document.getElementById('add-specific-date').value; // may be empty
    const errEl    = document.getElementById('add-slot-error');
    const btn      = document.getElementById('add-slot-btn');

    errEl.classList.add('hidden');

    if (!start || !end) {
        errEl.textContent = 'Please fill in start and end times.';
        errEl.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Adding…';

    axios.post('{{ route("doctor.schedule.store") }}', {
        day_of_week:           day,
        start_time:            start,
        end_time:              end,
        slot_duration_minutes: duration,
        buffer_minutes:        buffer,
        _token:                CSRF,
    }).then(res => {
        showToast(res.data.message);
        // Reload page to refresh slot list
        window.location.reload();
    }).catch(err => {
        const msg = err.response?.data?.message || err.response?.data?.errors?.start_time?.[0] || 'Failed to add slot.';
        errEl.textContent = msg;
        errEl.classList.remove('hidden');
    }).finally(() => {
        btn.disabled = false;
        btn.textContent = 'Add Slot';
    });
}

// ── Edit helpers ───────────────────────────────────────────────────────────────
function openEditForm(id, day, start, end, duration, available) {
    const row     = document.getElementById(`slot-row-${id}`);
    const display = row.querySelector('.slot-display');
    const editEl  = row.querySelector('.slot-edit');
    display.classList.add('hidden');
    editEl.classList.remove('hidden');

    // Set current values
    editEl.querySelector('.edit-start').value     = start;
    editEl.querySelector('.edit-end').value       = end;
    editEl.querySelector('.edit-available').checked = available;
    setClosestDuration(editEl.querySelector('.edit-duration'), duration);
    editEl.querySelector('.edit-buffer').value    = '0';
    editEl.querySelector('.edit-error').classList.add('hidden');
}

function setClosestDuration(select, total) {
    const opts = [15, 30, 45, 60];
    let best = opts.reduce((a, b) => Math.abs(b - total) < Math.abs(a - total) ? b : a);
    select.value = best;
}

function cancelEdit(id) {
    const row     = document.getElementById(`slot-row-${id}`);
    const display = row.querySelector('.slot-display');
    const editEl  = row.querySelector('.slot-edit');
    display.classList.remove('hidden');
    editEl.classList.add('hidden');
}

function saveEdit(id, saveBtn) {
    const row      = document.getElementById(`slot-row-${id}`);
    const editEl   = row.querySelector('.slot-edit');
    const start    = editEl.querySelector('.edit-start').value;
    const end      = editEl.querySelector('.edit-end').value;
    const duration = editEl.querySelector('.edit-duration').value;
    const buffer   = editEl.querySelector('.edit-buffer').value;
    const avail    = editEl.querySelector('.edit-available').checked ? 1 : 0;
    const errEl    = editEl.querySelector('.edit-error');

    errEl.classList.add('hidden');
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving…';

    axios.put(`/doctor/schedule/${id}`, {
        start_time:            start,
        end_time:              end,
        slot_duration_minutes: duration,
        buffer_minutes:        buffer,
        is_available:          avail,
        _token:                CSRF,
    }).then(res => {
        showToast(res.data.message);
        window.location.reload();
    }).catch(err => {
        const msg = err.response?.data?.message || err.response?.data?.errors?.start_time?.[0] || 'Failed to update slot.';
        errEl.textContent = msg;
        errEl.classList.remove('hidden');
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save';
    });
}

// ── Remove Slot ────────────────────────────────────────────────────────────────
function removeSlot(id, btn) {
    if (!confirm('Remove this slot?')) return;
    btn.disabled = true;

    axios.delete(`/doctor/schedule/${id}`, {
        data: { _token: CSRF }
    }).then(() => {
        const row = document.getElementById(`slot-row-${id}`);
        row.remove();
        showToast('Slot removed.');
    }).catch(() => {
        showToast('Failed to remove slot.', 'error');
        btn.disabled = false;
    });
}

function blockDate() {
    const date   = document.getElementById('block-date-input').value;
    const reason = document.getElementById('block-reason-input').value;

    if (!date) { alert('Please select a date.'); return; }

    axios.post('{{ route("doctor.schedule.blockDate") }}', {
        date, reason, _token: CSRF,
    }).then(res => {
        const entry = res.data.entry;
        // Remove empty placeholder
        const empty = document.getElementById('blocked-empty');
        if (empty) empty.remove();
        // Append new entry
        const list = document.getElementById('blocked-list');
        const dateLabel = new Date(entry.date + 'T00:00:00').toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', year:'numeric' });
        list.insertAdjacentHTML('beforeend', `
            <div id="blocked-entry-${entry.id}" data-date="${entry.date}" class="flex items-center justify-between bg-red-50 border border-red-100 rounded-lg px-4 py-2.5">
                <div>
                    <p class="text-sm font-medium text-gray-900">${dateLabel}</p>
                    ${entry.reason ? `<p class="text-xs text-gray-400">${entry.reason}</p>` : ''}
                </div>
                <button onclick="unblockDate('${entry.date}', '${entry.id}', this)"
                    class="text-red-400 hover:text-red-600 text-xs px-3 py-1 rounded-lg hover:bg-red-100 transition-colors">
                    Remove
                </button>
            </div>
        `);
        document.getElementById('block-date-input').value   = '';
        document.getElementById('block-reason-input').value = '';
        showToast('Date blocked.');
    }).catch(err => {
        const msg = err.response?.data?.errors?.date?.[0] || 'Failed to block date.';
        showToast(msg, 'error');
    });
}

function unblockDate(date, id, btn) {
    btn.disabled = true;
    axios.delete('{{ route("doctor.schedule.unblockDate") }}', {
        data: { date, _token: CSRF },
    }).then(() => {
        document.getElementById(`blocked-entry-${id}`).remove();
        showToast('Date unblocked.');
    }).catch(() => {
        showToast('Failed to remove blocked date.', 'error');
        btn.disabled = false;
    });
}
</script>
@endpush
@endsection
