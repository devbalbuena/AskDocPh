@extends('layouts.patient')
@section('title', 'Appointment Detail')
@section('page-title', 'Appointment Detail')

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('patient.appointments.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Appointments
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Status header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
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
            <div class="border-t border-gray-100 pt-6 mt-2">
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

            {{-- Review section — only for completed appointments --}}
            @if($appointment->status === 'completed')
            <div class="border-t border-gray-100 pt-6 mt-2">
                <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Rate Your Doctor
                </h3>

                @if($existingReview)
                {{-- Already reviewed --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center gap-1.5 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $existingReview->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                        <span class="text-sm text-green-700 font-semibold ml-1">Review submitted — thank you!</span>
                    </div>
                    @if($existingReview->review_text)
                        <p class="text-sm text-gray-700 italic">"{{ $existingReview->review_text }}"</p>
                    @endif
                </div>
                @else
                {{-- Review form --}}
                <div id="review-form-wrap" class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wider">Your Rating</p>
                        <div class="flex items-center gap-2" id="star-picker">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="selectStar({{ $i }})"
                                    data-star="{{ $i }}"
                                    class="star-btn text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </button>
                            @endfor
                        </div>
                        <p id="rating-label" class="text-xs text-gray-400 mt-1.5">Click a star to rate</p>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 mb-1 font-medium uppercase tracking-wider block">Written Review (optional)</label>
                        <textarea id="review-text" rows="3" maxlength="2000" placeholder="Share your experience with this doctor..."
                                  class="w-full border border-gray-200 bg-white rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 resize-none transition"></textarea>
                    </div>

                    <p id="review-error" class="text-sm text-red-600 hidden"></p>

                    <div class="flex justify-end">
                        <button id="review-submit-btn" onclick="submitReview({{ $appointment->id }})"
                                class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Submit Review
                        </button>
                    </div>
                </div>

                {{-- Post-submit thank you state (shown via JS) --}}
                <div id="review-thankyou" class="hidden bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-semibold">Review submitted — thank you for your feedback!</span>
                    </div>
                </div>
                @endif
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

@push('scripts')
<script>
// ── Star Rating Picker ────────────────────────────────────────────
let selectedRating = 0;
const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

function selectStar(rating) {
    selectedRating = rating;
    document.querySelectorAll('.star-btn').forEach((btn) => {
        const star = parseInt(btn.dataset.star);
        btn.classList.toggle('text-yellow-400', star <= rating);
        btn.classList.toggle('text-gray-300', star > rating);
    });
    const label = document.getElementById('rating-label');
    if (label) label.textContent = `${rating} / 5 — ${ratingLabels[rating]}`;
}

// ── Submit Review ─────────────────────────────────────────────────
function submitReview(appointmentId) {
    const errorEl   = document.getElementById('review-error');
    const submitBtn = document.getElementById('review-submit-btn');

    errorEl.classList.add('hidden');

    if (selectedRating === 0) {
        errorEl.textContent = 'Please select a star rating before submitting.';
        errorEl.classList.remove('hidden');
        return;
    }

    const reviewText = document.getElementById('review-text')?.value ?? '';

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    axios.post('/doctor-reviews', {
        appointment_id : appointmentId,
        rating         : selectedRating,
        review_text    : reviewText,
    })
    .then(() => {
        document.getElementById('review-form-wrap').classList.add('hidden');
        document.getElementById('review-thankyou').classList.remove('hidden');
    })
    .catch(err => {
        const msg = err.response?.data?.message ?? 'Failed to submit review. Please try again.';
        errorEl.textContent = msg;
        errorEl.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> Submit Review';
    });
}
</script>
@endpush
