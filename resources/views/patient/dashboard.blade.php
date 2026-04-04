@extends('layouts.patient')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ID Verification Banner --}}
    @if(!auth()->user()->isDemo() && auth()->user()->id_verification_status !== 'approved')
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-xl shadow-sm mb-6 flex items-start sm:items-center justify-between flex-col sm:flex-row gap-4">
        <div class="flex items-center gap-3">
            <div class="bg-yellow-100 rounded-full p-2 text-yellow-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-yellow-800">
                    @if(auth()->user()->id_verification_status === 'pending')
                    Identity Verification Pending
                    @elseif(auth()->user()->id_verification_status === 'rejected')
                    Identity Verification Rejected
                    @else
                    Identity Verification Required
                    @endif
                </h3>
                <p class="text-sm text-yellow-700 mt-0.5">
                    @if(auth()->user()->id_verification_status === 'pending')
                    Your ID document is currently under review by our team.
                    @elseif(auth()->user()->id_verification_status === 'rejected')
                    Your previous ID submission was rejected. Please upload a clear, valid government ID.
                    @else
                    You need to verify your identity to book appointments, join communities, and send messages.
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('patient.id-verification.notice') }}" class="flex-shrink-0 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-yellow-300">
            @if(auth()->user()->id_verification_status === 'pending')
            View Status
            @else
            Verify Now
            @endif
        </a>
    </div>
    @endif

    {{-- Welcome Card --}}
    <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-3xl shadow-sm p-8 mb-6 relative overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-10 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full bg-white"></div>
        </div>
        <div class="flex items-center justify-between relative z-10">
            <div>
                <h2 class="text-3xl font-bold text-white mt-0.5">{{ $greeting }}, {{ auth()->user()->fname }}! 👋</h2>
                @if($todayAffirmation)
                <p class="text-green-100 text-sm mt-3 italic">"{{ $todayAffirmation->quote }}"</p>
                @if($todayAffirmation->author)
                <p class="text-green-200 text-xs mt-1 font-semibold">— {{ $todayAffirmation->author }}</p>
                @endif
                @endif
            </div>
            <div class="hidden md:flex w-24 h-24 rounded-full bg-white/20 border border-white/30 items-center justify-center backdrop-blur-sm">
                <span class="text-4xl font-black text-white">{{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}</span>
            </div>
        </div>
    </div>

    {{-- Prominent Find a Doctor Action --}}
    <div class="mb-6">
        <a href="{{ route('patient.doctors.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-medium inline-flex items-center gap-2 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            Find a Doctor
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $upcomingAppointments->count() }}</p>
                <p class="text-gray-500 text-sm">Upcoming Appointments</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $bookmarksCount }}</p>
                <p class="text-gray-500 text-sm">Saved Bookmarks</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $unreadNotifications }}</p>
                <p class="text-gray-500 text-sm">Unread Notifications</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Upcoming Appointments --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Upcoming Appointments</h3>
                <a href="{{ route('patient.appointments.index') }}" class="text-green-600 text-xs hover:text-green-700 transition-colors">View all →</a>
            </div>
            @forelse($upcomingAppointments as $appt)
            <div class="flex items-start gap-3 py-3 border-b border-gray-200 last:border-0">
                <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-xs font-bold text-green-700 flex-shrink-0">
                    {{ strtoupper(substr($appt->doctor->fname ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">Dr. {{ $appt->doctor->display_name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d') }} • {{ $appt->formatted_time }}</p>
                </div>
                <span class="{{ $appt->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-xs px-2 py-0.5 rounded-full capitalize">
                    {{ $appt->status }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-sm text-center py-4">No upcoming appointments</p>
            @endforelse
        </div>

        {{-- Recent Feed Posts --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Recent Posts</h3>
                <a href="{{ url('/feed') }}" class="text-green-600 text-xs hover:text-green-700 transition-colors">View feed →</a>
            </div>
            @forelse($recentPosts as $post)
            <div class="py-4 border-b border-gray-200 last:border-0">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-xs font-bold text-green-700">
                        {{ strtoupper(substr($post->user->fname ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $post->user->display_name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <p class="text-gray-700 text-sm line-clamp-2">{{ $post->text_content }}</p>
                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                    <span>❤️ {{ $post->likes->count() }}</span>
                    <span>💬 {{ $post->comments->count() }}</span>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm text-center py-8">No posts yet. Be the first to share!</p>
            @endforelse
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('patient.doctors.index') }}" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                Find a Doctor
            </a>
            <a href="{{ url('/feed') }}" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                Go to Feed
            </a>
            <button id="crisis-btn" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Crisis Support
            </button>
        </div>
    </div>
</div>

{{-- Crisis Modal --}}
<div id="crisis-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white border border-red-500/30 rounded-2xl shadow-sm border border-gray-100 p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-900 mb-2">🆘 Crisis Support</h3>
        <p class="text-gray-500 text-sm mb-4">Tell us what you're going through. Our team will reach out to you as quickly as possible.</p>
        <textarea id="crisis-desc" rows="4" placeholder="Describe what you're experiencing..." class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 resize-none"></textarea>
        <div id="crisis-error" class="hidden text-red-400 text-xs mt-1"></div>
        <div class="flex gap-3 mt-4">
            <button onclick="submitCrisis()" class="flex-1 bg-red-600 hover:bg-red-700 text-gray-900 py-2.5 rounded-lg text-sm font-medium transition-colors">Submit Report</button>
            <button onclick="document.getElementById('crisis-modal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-gray-900 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancel</button>
        </div>
        <div id="crisis-success" class="hidden mt-3 text-green-400 text-sm text-center"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('crisis-btn').addEventListener('click', () => {
    document.getElementById('crisis-modal').classList.remove('hidden');
});

function submitCrisis() {
    const desc = document.getElementById('crisis-desc').value.trim();
    const errDiv = document.getElementById('crisis-error');
    if (desc.length < 10) {
        errDiv.textContent = 'Please describe your situation (at least 10 characters).';
        errDiv.classList.remove('hidden');
        return;
    }
    errDiv.classList.add('hidden');

    axios.post('{{ route("patient.crisis.store") }}', { description: desc })
        .then(res => {
            document.getElementById('crisis-success').textContent = res.data.message;
            document.getElementById('crisis-success').classList.remove('hidden');
            document.getElementById('crisis-desc').value = '';
            setTimeout(() => document.getElementById('crisis-modal').classList.add('hidden'), 3000);
        })
        .catch(err => {
            errDiv.textContent = err.response?.data?.message ?? 'Something went wrong. Please try again.';
            errDiv.classList.remove('hidden');
        });
}
</script>
@endpush
