@extends($layout)
@section('title', ($profileUser->display_name ?? $profileUser->username) . ' — Profile')
@section('page-title', 'Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- ── Profile Header Card ─────────────────────────── --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Cover banner --}}
        @if($profileUser->cover_photo)
        <div class="h-36 w-full bg-cover bg-center"
             style="background-image: url('{{ Storage::url($profileUser->cover_photo) }}')">
        </div>
        @else
        <div class="h-36 bg-gradient-to-br from-green-700 to-emerald-500"></div>
        @endif

        {{-- Avatar + actions row --}}
        <div class="px-6 pb-5">
            <div class="flex items-end justify-between -mt-10 mb-4">

                {{-- Avatar circle --}}
                <div class="w-20 h-20 rounded-full border-4 border-white shadow-md flex-shrink-0 overflow-hidden
                            bg-green-100 flex items-center justify-center">
                    @if($profileUser->profile_photo)
                    <img src="{{ Storage::url($profileUser->profile_photo) }}"
                         alt="{{ $profileUser->display_name }}"
                         class="w-full h-full object-cover">
                    @else
                    <span class="text-2xl font-bold text-green-700">
                        {{ strtoupper(substr($profileUser->fname, 0, 1)) }}{{ strtoupper(substr($profileUser->lname, 0, 1)) }}
                    </span>
                    @endif
                </div>

                {{-- Action button --}}
                <div class="mt-10">
                    @if($isOwnProfile)
                    {{-- Own profile: show Edit link --}}
                    <a href="{{ route('profile.edit') }}"
                       class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828A2 2 0 0110 16.414H8v-2a2 2 0 01.586-1.414z"/>
                        </svg>
                        Edit Profile
                    </a>
                    @else
                    {{-- Other user: Follow / Unfollow button --}}
                    <button id="follow-btn"
                            onclick="toggleFollow()"
                            class="{{ $isFollowing
                                ? 'bg-gray-200 text-gray-700 hover:bg-red-100 hover:text-red-600 hover:border-red-300'
                                : 'bg-green-600 text-white hover:bg-green-700' }}
                                   border border-transparent text-sm font-semibold px-5 py-2 rounded-xl transition-all duration-150"
                            data-following="{{ $isFollowing ? 'true' : 'false' }}">
                        {{ $isFollowing ? 'Following' : 'Follow' }}
                    </button>

                    {{-- Book Appointment: only visible to patients viewing a doctor profile --}}
                    @if(auth()->user()->role === 'patient' && $profileUser->role === 'doctor')
                    <a href="{{ route('patient.doctors.schedule', $profileUser->id) }}"
                       class="ml-2 inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Book Appointment
                    </a>
                    @endif

                    {{-- Message Button: hidden if admin viewing or viewing admin --}}
                    @if(auth()->user()->role !== 'admin' && $profileUser->role !== 'admin')
                    <button onclick="startConversation({{ $profileUser->id }})"
                            class="ml-2 inline-flex items-center gap-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Message
                    </button>
                    @endif
                    @endif
                </div>
            </div>

            {{-- Name, username, role badge --}}
            <div class="mb-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-gray-900">{{ $profileUser->display_name }}</h1>
                    {{-- Role badge --}}
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full capitalize
                        {{ $profileUser->role === 'doctor' ? 'bg-blue-100 text-blue-700'
                         : ($profileUser->role === 'admin' ? 'bg-purple-100 text-purple-700'
                         : 'bg-green-100 text-green-700') }}">
                        {{ $profileUser->role }}
                    </span>
                    @if($profileUser->role === 'doctor' && $doctorAverageRating > 0)
                    <span class="flex items-center gap-1 text-xs font-bold text-yellow-600 bg-yellow-50 border border-yellow-200 px-2 py-0.5 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        {{ number_format($doctorAverageRating, 1) }}
                    </span>
                    @endif
                </div>
                @if($profileUser->username)
                <p class="text-gray-400 text-sm mt-0.5">@{{ $profileUser->username }}</p>
                @endif

                {{-- Doctor professional titles --}}
                @if($profileUser->role === 'doctor' && count($professionalTitles))
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach($professionalTitles as $title)
                    <span class="text-xs text-gray-500 bg-gray-100 border border-gray-200 px-2.5 py-1 rounded-full">
                        {{ $title }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Bio --}}
            @if($profileUser->bio)
            <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ $profileUser->bio }}</p>
            @endif

            {{-- Followers / Following counts --}}
            <div class="flex items-center gap-5 text-sm">
                <button onclick="loadFollowers()"
                        class="flex flex-col items-center hover:text-green-700 transition-colors cursor-pointer">
                    <span id="followers-count" class="text-lg font-bold text-gray-900">{{ $followersCount }}</span>
                    <span class="text-xs text-gray-500">Followers</span>
                </button>
                <button onclick="loadFollowing()"
                        class="flex flex-col items-center hover:text-green-700 transition-colors cursor-pointer">
                    <span class="text-lg font-bold text-gray-900">{{ $followingCount }}</span>
                    <span class="text-xs text-gray-500">Following</span>
                </button>
                <div class="flex flex-col items-center">
                    <span class="text-lg font-bold text-gray-900">{{ $posts->total() }}</span>
                    <span class="text-xs text-gray-500">Posts</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Followers / Following Modal ──────────────────── --}}
    <div id="social-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[70vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 id="modal-title" class="text-base font-semibold text-gray-900">Followers</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="modal-list" class="flex-1 overflow-y-auto divide-y divide-gray-100 p-2">
                <p class="text-center text-gray-400 text-sm py-8">Loading...</p>
            </div>
        </div>
    </div>

    {{-- ── Doctor Reviews (if doctor) ────────────────────── --}}
    @if($profileUser->role === 'doctor')
        @include('shared.profile._reviews')
    @endif

    {{-- ── Posts Feed ───────────────────────────────────── --}}
    <div class="mt-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Posts</h2>

        @if($posts->count())
        <div class="space-y-4">
            @foreach($posts as $post)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">

                {{-- Post header --}}
                <div class="flex items-center gap-3 mb-3">
                    <a href="{{ url('/users/' . $profileUser->username) }}"
                       class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0 hover:ring-2 hover:ring-green-400 transition">
                        {{ strtoupper(substr($profileUser->fname ?? 'U', 0, 1)) }}
                    </a>
                    <div>
                        <a href="{{ url('/users/' . $profileUser->username) }}"
                           class="text-sm font-semibold text-gray-900 hover:text-green-700 transition-colors">
                            {{ $profileUser->display_name }}
                        </a>
                        <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Post body --}}
                @if($post->text_content)
                <p class="text-sm text-gray-800 leading-relaxed mb-3">{{ $post->text_content }}</p>
                @endif

                {{-- Post media --}}
                @if($post->media->count())
                <div class="flex gap-2 flex-wrap mb-3">
                    @foreach($post->media as $media)
                    @if($media->media_type === 'video')
                    <video src="{{ Storage::url($media->path) }}" class="rounded-xl max-h-64 max-w-full object-cover" controls></video>
                    @else
                    <img src="{{ Storage::url($media->path) }}" class="rounded-xl max-h-64 max-w-full object-cover" alt="Post image">
                    @endif
                    @endforeach
                </div>
                @endif

                {{-- Like count --}}
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100 text-xs text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                        {{ $post->likes->count() }} likes
                    </span>
                    <span>{{ $post->comments->count() }} comments</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
        <div class="mt-4 bg-white border border-gray-200 rounded-xl px-5 py-4 shadow-sm">
            {{ $posts->links() }}
        </div>
        @endif

        @else
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center shadow-sm">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-medium text-sm">No posts yet</p>
        </div>
        @endif
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Follow / Unfollow ────────────────────────────────
    window.toggleFollow = function () {
        const btn      = document.getElementById('follow-btn');
        const isFollow = btn.dataset.following === 'true';
        btn.disabled   = true;

        axios.post('{{ route("users.follow", $profileUser->id) }}')
            .then(res => {
                if (!res.data.success) return;

                const nowFollowing = res.data.following;
                btn.dataset.following = nowFollowing ? 'true' : 'false';

                // Update button appearance
                btn.className = nowFollowing
                    ? 'border border-transparent bg-gray-200 text-gray-700 hover:bg-red-100 hover:text-red-600 hover:border-red-300 text-sm font-semibold px-5 py-2 rounded-xl transition-all duration-150'
                    : 'border border-transparent bg-green-600 text-white hover:bg-green-700 text-sm font-semibold px-5 py-2 rounded-xl transition-all duration-150';
                btn.textContent = nowFollowing ? 'Following' : 'Follow';

                // Update follower count
                const countEl = document.getElementById('followers-count');
                if (countEl) countEl.textContent = res.data.followers_count;
            })
            .catch(err => alert(err.response?.data?.message ?? 'Failed to update follow status.'))
            .finally(() => { btn.disabled = false; });
    };

    // ── Followers / Following modal ───────────────────────
    window.loadFollowers = function () {
        document.getElementById('modal-title').textContent = 'Followers';
        openModal('{{ route("users.followers", $profileUser->id) }}');
    };

    window.loadFollowing = function () {
        document.getElementById('modal-title').textContent = 'Following';
        openModal('{{ route("users.following", $profileUser->id) }}');
    };

    function openModal(url) {
        const modal = document.getElementById('social-modal');
        const list  = document.getElementById('modal-list');
        modal.classList.remove('hidden');
        list.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">Loading...</p>';

        axios.get(url).then(res => {
            if (!res.data.data.length) {
                list.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">Nobody here yet.</p>';
                return;
            }
            list.innerHTML = res.data.data.map(u => `
                <a href="${u.profile_url}"
                   class="flex items-center gap-3 px-3 py-3 hover:bg-gray-50 rounded-xl transition-colors">
                    <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                        ${u.display_name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">${u.display_name}</p>
                        <p class="text-xs text-gray-400 capitalize">${u.role}</p>
                    </div>
                </a>
            `).join('');
        }).catch(() => {
            list.innerHTML = '<p class="text-center text-red-400 text-sm py-8">Failed to load users.</p>';
        });
    }

    window.closeModal = function () {
        document.getElementById('social-modal').classList.add('hidden');
    };

    // Close modal on backdrop click
    document.getElementById('social-modal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
});
</script>
