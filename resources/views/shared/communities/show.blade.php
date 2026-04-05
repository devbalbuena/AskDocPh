@extends($layout)
@section('title', $group->name . ' - Community')
@section('page-title', 'Community')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Cover & Header Info --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        @if($group->cover_photo)
        <div class="h-48 md:h-64 w-full bg-cover bg-center relative" style="background-image: url('{{ Storage::url($group->cover_photo) }}')">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        </div>
        @else
        <div class="h-48 md:h-64 bg-gradient-to-br from-green-600 to-emerald-800 relative">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDUiLz4KPHBhdGggZD0iTTAgMEw4IDhNOCAwTDAgOCIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wNSIvPgo8L3N2Zz4=')] opacity-20"></div>
        </div>
        @endif

        <div class="px-6 py-5 relative">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h1>
                        @if($group->visibility === 'private')
                        <span class="bg-gray-100 text-gray-700 text-xs font-bold px-2 py-0.5 rounded uppercase tracking-wider flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Private
                        </span>
                        @endif
                    </div>
                    <p class="text-gray-600 mb-4 max-w-3xl">{{ $group->description ?? 'No description provided.' }}</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500 font-medium">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            {{ number_format($membersCount) }} Members
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Created {{ $group->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="flex-shrink-0 flex items-center gap-3">
                    @if($membership)
                        @if($group->creator_id === auth()->id())
                            <span class="bg-blue-50 text-blue-700 text-sm font-semibold px-4 py-2 rounded-xl border border-blue-200 cursor-default">Admin</span>
                        @else
                            <button onclick="leaveGroup()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-red-600 hover:border-red-300 text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                                Leave Group
                            </button>
                        @endif
                        <button onclick="document.getElementById('post-input').focus()" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828A2 2 0 0110 16.414H8v-2a2 2 0 01.586-1.414z"/></svg>
                            Write Post
                        </button>
                        @if($isMember)
                        <button onclick="document.getElementById('create-poll-modal').style.display='flex'" class="inline-flex items-center gap-2 bg-white border border-purple-500 text-purple-600 hover:bg-purple-50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Create Poll
                        </button>
                        @endif
                    @else
                        <button onclick="joinGroup()" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                            Join Community
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Feed --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Create Post Box --}}
            @if($membership)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex-shrink-0 overflow-hidden flex items-center justify-center text-green-700 font-bold">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ Storage::url(auth()->user()->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1">
                        <textarea id="post-input" rows="2" placeholder="Share something with the community..."
                                  class="w-full border-none bg-gray-50 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:bg-white transition resize-none"></textarea>
                        <div class="mt-3 flex justify-end">
                            <button id="submit-post-btn" onclick="submitPost()" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Post
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($group->visibility === 'private')
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Private Community</h3>
                <p class="text-gray-500 text-sm mb-4">Join this group to participate and view posts.</p>
                <button onclick="joinGroup()" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-sm">Join to View</button>
            </div>
            @endif

            {{-- Posts List --}}
            @if($membership || $group->visibility === 'public')
                <div id="posts-container" class="space-y-6">
                    @forelse($posts as $post)
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <a href="{{ route('users.profile', $post->user->username) }}" class="w-10 h-10 rounded-full bg-green-100 flex-shrink-0 overflow-hidden flex items-center justify-center text-green-700 font-bold hover:ring-2 ring-green-500 transition">
                                @if($post->user->profile_photo)
                                    <img src="{{ Storage::url($post->user->profile_photo) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($post->user->fname, 0, 1)) }}
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('users.profile', $post->user->username) }}" class="text-sm font-semibold text-gray-900 hover:text-green-600 transition">{{ $post->user->display_name }}</a>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span class="capitalize {{ $post->user->role === 'doctor' ? 'text-blue-600' : 'text-gray-500' }}">{{ $post->user->role }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $post->text_content }}</div>
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-6">
                            <span class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>
                                {{ $post->likes->count() }} Likes
                            </span>
                            <span class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                {{ $post->comments->count() }} Comments
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-50 border border-gray-200 border-dashed rounded-2xl p-10 text-center">
                        <p class="text-sm text-gray-500 mb-2">No posts in this community yet.</p>
                        @if($membership)
                            <p class="text-xs text-gray-400">Be the first to share something!</p>
                        @endif
                    </div>
                    @endforelse
                </div>
                
                <div class="mt-6">
                    {{ $posts->links() }}
                </div>

                {{-- Polls Section --}}
                @if($polls->count() > 0)
                <div class="mt-6 space-y-4">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Community Polls</h3>
                    @foreach($polls as $poll)
                    @php
                        $totalVotes  = $poll->votes->count();
                        $userVote    = $poll->votes->where('user_id', auth()->id())->first();
                        $hasVoted    = !is_null($userVote);
                        $isExpired   = $poll->ends_at && $poll->ends_at->isPast();
                        $showResults = $hasVoted || $isExpired;
                    @endphp
                    <div class="bg-white border border-purple-100 border-l-4 border-l-purple-500 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded-full font-medium">📊 Poll</span>
                            <span class="text-gray-500 text-xs">by {{ $poll->user->fname ?? 'Unknown' }}</span>
                            @if($isExpired)
                            <span class="text-red-500 text-xs ml-auto font-medium">Ended</span>
                            @elseif($poll->ends_at)
                            <span class="text-gray-400 text-xs ml-auto">Ends {{ $poll->ends_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <p class="font-semibold text-gray-900 mb-3">{{ $poll->question }}</p>

                        <div class="space-y-2">
                            @foreach($poll->options as $option)
                            @php
                                $voteCount = $poll->votes->where('option_id', $option->id)->count();
                                $pct       = $totalVotes > 0 ? round($voteCount / $totalVotes * 100) : 0;
                                $myVote    = $hasVoted && $userVote && $userVote->option_id === $option->id;
                            @endphp
                            @if($showResults)
                            <div class="mb-1">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 {{ $myVote ? 'font-semibold text-purple-700' : '' }}">
                                        {{ $option->text }} {!! $myVote ? '<span>✓</span>' : '' !!}
                                    </span>
                                    <span class="text-gray-500">{{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $myVote ? 'bg-purple-500' : 'bg-gray-300' }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            @else
                            <button onclick="votePoll({{ $poll->id }}, {{ $option->id }}, {{ $group->id }}, this)"
                                    class="w-full text-left border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 hover:border-purple-400 hover:bg-purple-50 transition-colors">
                                {{ $option->text }}
                            </button>
                            @endif
                            @endforeach
                        </div>

                        <p class="text-xs text-gray-400 mt-3">{{ $totalVotes }} total vote{{ $totalVotes !== 1 ? 's' : '' }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            @endif
        </div>

        {{-- Right Column: Guidelines & Members --}}
        <div class="space-y-6">
            {{-- Guidelines Box --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Community Rules
                </h3>
                <div class="text-xs text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $group->guidelines ?? '1. Be respectful and kind.'."\n".'2. Avoid giving direct medical advice if you are a patient.'."\n".'3. Stay on topic.' }}</div>
            </div>

            {{-- Members Box --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center justify-between">
                    Members
                    <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ number_format($membersCount) }}</span>
                </h3>
                <div class="space-y-3">
                    @foreach($members as $member)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <a href="{{ route('users.profile', $member->user->username) }}" class="w-8 h-8 rounded-full bg-green-100 flex-shrink-0 overflow-hidden flex items-center justify-center text-green-700 font-bold text-xs">
                                @if($member->user->profile_photo)
                                    <img src="{{ Storage::url($member->user->profile_photo) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($member->user->fname, 0, 1)) }}
                                @endif
                            </a>
                            <div class="min-w-0">
                                <a href="{{ route('users.profile', $member->user->username) }}" class="text-xs font-semibold text-gray-900 truncate hover:text-green-600">{{ $member->user->display_name }}</a>
                                <p class="text-[10px] text-gray-500 capitalize">{{ $member->user->role }}</p>
                            </div>
                        </div>
                        @if($member->role === 'admin')
                            <span class="text-[9px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded uppercase uppercase tracking-wide">Admin</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div id="create-poll-modal" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900 text-lg">Create a Poll</h3>
            <button onclick="document.getElementById('create-poll-modal').style.display='none'" class="text-gray-400 hover:text-gray-600 font-bold ml-auto text-xl leading-none">&times;</button>
        </div>
        
        <form id="create-poll-form">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                <input type="text" id="poll-question" placeholder="Ask the community..." class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                <div id="poll-options-container">
                    <input type="text" class="poll-option w-full border border-gray-300 rounded-xl px-4 py-2 text-sm mb-2 focus:ring-2 focus:ring-purple-500" placeholder="Option 1">
                    <input type="text" class="poll-option w-full border border-gray-300 rounded-xl px-4 py-2 text-sm mb-2 focus:ring-2 focus:ring-purple-500" placeholder="Option 2">
                </div>
                <button type="button" onclick="addPollOption()" class="text-purple-600 text-sm hover:underline font-medium">+ Add Option</button>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date (optional)</label>
                <input type="datetime-local" id="poll-ends-at" class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
            </div>
            
            <button type="button" onclick="submitPoll({{ $group->id }})" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl font-medium text-sm transition-colors mt-2">
                Create Poll
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function joinGroup() {
    axios.post('{{ route("communities.join", $group->id) }}')
        .then(res => {
            window.location.reload();
        })
        .catch(err => {
            alert(err.response?.data?.message || 'Could not join community.');
        });
}

function leaveGroup() {
    if(!confirm('Are you sure you want to leave this community?')) return;
    axios.post('{{ route("communities.leave", $group->id) }}')
        .then(res => {
            window.location.reload();
        })
        .catch(err => {
            alert(err.response?.data?.message || 'Could not leave community.');
        });
}

function submitPost() {
    const input = document.getElementById('post-input');
    const content = input.value.trim();
    if (!content) return;

    const btn = document.getElementById('submit-post-btn');
    btn.disabled = true;
    btn.textContent = 'Posting...';

    axios.post('{{ route("communities.post", $group->id) }}', { text_content: content })
        .then(res => {
            window.location.reload(); // Quickest way to show the new post and update counts
        })
        .catch(err => {
            alert('Failed to post. ' + (err.response?.data?.message || ''));
            btn.disabled = false;
            btn.textContent = 'Post';
        });
}

function addPollOption() {
    const container = document.getElementById('poll-options-container');
    const count = container.querySelectorAll('.poll-option').length;
    if (count >= 6) {
        alert('Maximum 6 options allowed');
        return;
    }
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'poll-option w-full border border-gray-300 rounded-xl px-4 py-2 text-sm mb-2 focus:ring-2 focus:ring-purple-500';
    input.placeholder = 'Option ' + (count + 1);
    container.appendChild(input);
}

function submitPoll(groupId) {
    const question = document.getElementById('poll-question').value.trim();
    const options = Array.from(document.querySelectorAll('.poll-option'))
        .map(i => i.value.trim())
        .filter(v => v !== '');
    const endsAt = document.getElementById('poll-ends-at').value;
    
    if (!question) {
        alert('Please enter a question');
        return;
    }
    if (options.length < 2) {
        alert('Please add at least 2 options');
        return;
    }
    
    const btn = document.querySelector('button[onclick^="submitPoll"]');
    if (btn) btn.disabled = true;

    axios.post('/communities/' + groupId + '/polls', {
        question: question,
        options: options,
        ends_at: endsAt || null
    }).then(res => {
        document.getElementById('create-poll-modal').style.display = 'none';
        location.reload();
    }).catch(err => {
        console.error('Poll error:', err);
        console.error('Response:', err.response?.data);
        alert('Error: ' + JSON.stringify(err.response?.data ?? 'Unknown error'));
        if (btn) btn.disabled = false;
    });
}

function votePoll(pollId, optionId, groupId, btn) {
    const orig = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Voting...';

    axios.post('/communities/' + groupId + '/polls/' + pollId + '/vote', {
        option_id: optionId
    }).then(res => {
        location.reload();
    }).catch(err => {
        console.error('Vote error:', err);
        alert(err.response?.data?.message || 'Could not cast vote.');
        btn.disabled = false;
        btn.textContent = orig;
    });
}
</script>
@endpush
@endsection
