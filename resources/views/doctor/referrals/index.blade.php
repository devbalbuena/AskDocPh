@extends('layouts.doctor')
@section('title', 'Patient Referrals')
@section('page-title', 'Referrals')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Tab Header --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button id="tab-received" onclick="switchTab('received')"
                    class="tab-btn flex-1 py-4 text-sm font-semibold text-center transition-colors border-b-2 border-indigo-600 text-indigo-700 bg-indigo-50">
                📥 Received
                @if($received->where('status','pending')->count())
                <span class="ml-1.5 bg-indigo-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $received->where('status','pending')->count() }}</span>
                @endif
            </button>
            <button id="tab-sent" onclick="switchTab('sent')"
                    class="tab-btn flex-1 py-4 text-sm font-semibold text-center transition-colors border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                📤 Sent
            </button>
        </div>

        {{-- Received Tab --}}
        <div id="panel-received" class="p-6">
            @forelse($received as $ref)
            <div class="border border-gray-100 rounded-2xl p-5 mb-4 last:mb-0 hover:border-indigo-200 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            <span class="text-sm font-semibold text-gray-900">
                                Patient: {{ $ref->patient->display_name ?? 'Unknown' }}
                            </span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
                                {{ $ref->status === 'pending' ? 'bg-yellow-100 text-yellow-700'
                                : ($ref->status === 'accepted' ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($ref->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">From: <span class="font-medium text-gray-700">Dr. {{ $ref->referringDoctor->display_name ?? 'Unknown' }}</span></p>
                        <p class="text-sm text-gray-700 mb-1"><span class="font-medium">Reason:</span> {{ $ref->reason }}</p>
                        @if($ref->message)
                        <p class="text-sm text-gray-500 italic mt-1 bg-gray-50 rounded-lg px-3 py-2">{{ $ref->message }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $ref->created_at->diffForHumans() }}</p>
                    </div>

                    @if($ref->status === 'pending')
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        <button onclick="respondReferral({{ $ref->id }}, 'accept', this)"
                                class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                            ✓ Accept
                        </button>
                        <button onclick="respondReferral({{ $ref->id }}, 'decline', this)"
                                class="bg-white border border-red-200 text-red-500 hover:bg-red-50 text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                            ✕ Decline
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 text-sm font-medium">No referrals received yet</p>
            </div>
            @endforelse
        </div>

        {{-- Sent Tab --}}
        <div id="panel-sent" class="p-6 hidden">
            @forelse($sent as $ref)
            <div class="border border-gray-100 rounded-2xl p-5 mb-4 last:mb-0">
                <div class="flex items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            <span class="text-sm font-semibold text-gray-900">
                                Patient: {{ $ref->patient->display_name ?? 'Unknown' }}
                            </span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
                                {{ $ref->status === 'pending' ? 'bg-yellow-100 text-yellow-700'
                                : ($ref->status === 'accepted' ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($ref->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">Referred to: <span class="font-medium text-gray-700">Dr. {{ $ref->referredToDoctor->display_name ?? 'Unknown' }}</span></p>
                        <p class="text-sm text-gray-700 mb-1"><span class="font-medium">Reason:</span> {{ $ref->reason }}</p>
                        @if($ref->message)
                        <p class="text-sm text-gray-500 italic mt-1 bg-gray-50 rounded-lg px-3 py-2">{{ $ref->message }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $ref->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <p class="text-gray-500 text-sm font-medium">No referrals sent yet</p>
                <p class="text-gray-400 text-xs mt-1">Refer a patient from any completed appointment</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Toast notification --}}
<div id="referral-toast" class="hidden fixed bottom-6 right-6 z-50 bg-green-600 text-white text-sm px-5 py-3 rounded-xl shadow-xl flex items-center gap-2 max-w-xs">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    <span id="referral-toast-msg"></span>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    // Panels
    document.getElementById('panel-received').classList.toggle('hidden', tab !== 'received');
    document.getElementById('panel-sent').classList.toggle('hidden', tab !== 'sent');

    // Tab buttons
    const active   = 'border-b-2 border-indigo-600 text-indigo-700 bg-indigo-50';
    const inactive = 'border-b-2 border-transparent text-gray-500 hover:text-gray-700';
    document.getElementById('tab-received').className = 'tab-btn flex-1 py-4 text-sm font-semibold text-center transition-colors ' + (tab === 'received' ? active : inactive);
    document.getElementById('tab-sent').className     = 'tab-btn flex-1 py-4 text-sm font-semibold text-center transition-colors ' + (tab === 'sent' ? active : inactive);
}

function respondReferral(id, action, btn) {
    const orig = btn.textContent;
    btn.disabled    = true;
    btn.textContent = '...';

    axios.post(`/doctor/referrals/${id}/${action}`)
        .then(res => {
            showToast(action === 'accept' ? 'Referral accepted!' : 'Referral declined.');
            // Fade out the card
            const card = btn.closest('.border');
            if (card) { card.style.opacity = '0.4'; card.style.pointerEvents = 'none'; }
            // Remove action buttons
            btn.parentElement?.remove();
        })
        .catch(err => {
            alert(err.response?.data?.message ?? 'Could not process referral.');
            btn.disabled    = false;
            btn.textContent = orig;
        });
}

function showToast(msg) {
    const toast = document.getElementById('referral-toast');
    document.getElementById('referral-toast-msg').textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}
</script>
@endpush
@endsection
