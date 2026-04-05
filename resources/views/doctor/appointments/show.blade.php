@extends('layouts.doctor')
@section('title', 'Appointment Detail')
@section('page-title', 'Appointment Detail')

@section('content')
@php
    $typeLabel = $appointment->type === 'in_person' ? 'In-Person' : 'Online / Video Call';
    $startFmt  = \Carbon\Carbon::parse($appointment->start_time)->format('g:i A');
    $endFmt    = \Carbon\Carbon::parse($appointment->end_time)->format('g:i A');
@endphp
<div class="max-w-2xl space-y-5">
    <a href="{{ route('doctor.appointments.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 w-fit transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Appointments
    </a>

    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- Detail card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Appointment Details</h2>
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
            {{-- Patient --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center text-xl font-bold text-green-600">
                    {{ strtoupper(substr($appointment->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 font-semibold text-lg">{{ $appointment->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-400 text-sm">{{ $appointment->patient->email ?? '' }}</p>
                </div>
            </div>

            {{-- Info grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Date</p>
                    <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('D, M d Y') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Time</p>
                    <p class="text-gray-900 font-medium">{{ $startFmt }} – {{ $endFmt }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Type</p>
                    <p class="text-gray-900 font-medium">{{ $typeLabel }}</p>
                </div>
                @if($appointment->meeting_link)
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Meeting Link</p>
                    <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-green-600 text-sm hover:underline break-all">Join Call</a>
                </div>
                @endif
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Reason</p>
                <p class="text-gray-900 text-sm">{{ $appointment->reason }}</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3 pt-2">
                @if($appointment->status === 'pending')
                <form method="POST" action="{{ route('doctor.appointments.confirm', $appointment) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2.5 rounded-lg transition-colors">✓ Confirm</button>
                </form>
                @endif
                @if($appointment->status === 'confirmed')
                <form method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2.5 rounded-lg transition-colors">✓ Mark Completed</button>
                </form>
                @endif
                @if(in_array($appointment->status, ['pending','confirmed']))
                <form method="POST" action="{{ route('doctor.appointments.cancel', $appointment) }}" onsubmit="return confirm('Cancel this appointment?')">
                    @csrf
                    <button type="submit" class="bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 text-sm px-5 py-2.5 rounded-lg transition-colors">Cancel Appointment</button>
                </form>
                @endif
                @if($appointment->status === 'completed')
                <button onclick="document.getElementById('refer-modal').style.display='flex'"
                        class="inline-flex items-center gap-2 bg-indigo-50 border border-indigo-300 text-indigo-600 hover:bg-indigo-100 text-sm px-5 py-2.5 rounded-lg transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Refer Patient
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <h3 class="text-base font-semibold text-gray-900">Appointment Notes</h3>

        @forelse($appointment->notes as $note)
        <div class="bg-gray-50 rounded-xl p-4 space-y-2 border border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-400">{{ $note->created_at->format('M d, Y g:i A') }}</p>
                <span class="{{ $note->is_visible_to_patient ? 'text-green-600' : 'text-gray-400' }} text-xs">
                    {{ $note->is_visible_to_patient ? '👁 Visible to patient' : '🔒 Private' }}
                </span>
            </div>
            <p class="text-gray-700 text-sm">{{ $note->notes }}</p>
            @if($note->diagnosis)
            <p class="text-xs text-gray-400"><span class="font-medium text-gray-500">Diagnosis:</span> {{ $note->diagnosis }}</p>
            @endif
            @if($note->recommendations)
            <p class="text-xs text-gray-400"><span class="font-medium text-gray-500">Recommendations:</span> {{ $note->recommendations }}</p>
            @endif
        </div>
        @empty
        <p class="text-gray-400 text-sm italic">No notes added yet.</p>
        @endforelse

        {{-- Add Note Form --}}
        <form method="POST" action="{{ route('doctor.appointments.notes.store', $appointment) }}" class="space-y-3 pt-3 border-t border-gray-100">
            @csrf
            <h4 class="text-sm font-medium text-gray-900">Add Note</h4>
            <textarea name="notes" rows="3" placeholder="Clinical notes…"
                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 resize-none" required></textarea>
            <input type="text" name="diagnosis" placeholder="Diagnosis (optional)"
                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500">
            <input type="text" name="recommendations" placeholder="Recommendations (optional)"
                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500">
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="is_visible_to_patient" value="1" class="w-4 h-4 accent-green-600">
                Visible to patient
            </label>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2.5 rounded-lg transition-colors">Save Note</button>
        </form>
    </div>
</div>

{{-- Refer Patient Modal --}}
<div id="refer-modal" style="display:none"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Refer Patient to Another Doctor</h3>
            <button onclick="document.getElementById('refer-modal').style.display='none'"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none font-bold">&times;</button>
        </div>

        <div class="px-6 py-5 space-y-4">
            {{-- Patient info --}}
            <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm">
                <span class="text-gray-500">Referring patient:</span>
                <span class="font-semibold text-gray-900 ml-1">{{ $appointment->patient->display_name ?? 'Unknown' }}</span>
            </div>

            {{-- Target doctor --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Refer to Doctor <span class="text-red-500">*</span></label>
                <select id="refer-doctor"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-indigo-500">
                    <option value="">Select a doctor...</option>
                    @foreach($verifiedDoctors as $doc)
                        @php
                            $spec = json_decode($doc->bio ?? '{}', true)['specialization'] ?? null;
                        @endphp
                        <option value="{{ $doc->id }}">
                            Dr. {{ $doc->lname }}, {{ $doc->fname }}{{ $spec ? ' — ' . $spec : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Reason --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Reason <span class="text-red-500">*</span></label>
                <input type="text" id="refer-reason" maxlength="255"
                       placeholder="e.g. Needs specialist evaluation for anxiety..."
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-indigo-500">
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Additional Notes <span class="text-gray-400">(optional)</span></label>
                <textarea id="refer-message" rows="3" maxlength="1000"
                          placeholder="Background context, clinical notes..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-indigo-500 resize-none"></textarea>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
            <button onclick="document.getElementById('refer-modal').style.display='none'"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition-colors">Cancel</button>
            <button id="refer-submit-btn" onclick="submitReferral({{ $appointment->id }})"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">Send Referral</button>
        </div>
    </div>
</div>

<script>
function submitReferral(appointmentId) {
    const doctorId = document.getElementById('refer-doctor').value;
    const reason   = document.getElementById('refer-reason').value.trim();
    const message  = document.getElementById('refer-message').value.trim();
    const btn      = document.getElementById('refer-submit-btn');

    if (!doctorId) { alert('Please select a doctor.'); return; }
    if (!reason)   { alert('Please enter a reason.'); return; }

    btn.disabled    = true;
    btn.textContent = 'Sending...';

    axios.post('{{ route("doctor.referrals.store") }}', {
        appointment_id:        appointmentId,
        referred_to_doctor_id: doctorId,
        reason:                reason,
        message:               message || null,
    }).then(res => {
        document.getElementById('refer-modal').style.display = 'none';
        // Show confirmation inline
        const flash = document.createElement('div');
        flash.className = 'bg-indigo-50 border border-indigo-200 text-indigo-700 text-sm px-4 py-3 rounded-lg mt-4';
        flash.textContent = '✓ ' + res.data.message;
        document.querySelector('.max-w-2xl').prepend(flash);
        setTimeout(() => flash.remove(), 5000);
    }).catch(err => {
        alert(err.response?.data?.message ?? 'Failed to send referral.');
        btn.disabled    = false;
        btn.textContent = 'Send Referral';
    });
}
</script>
@endsection
