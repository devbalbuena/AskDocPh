{{-- Single Post Card Partial --}}
<div class="bg-white border border-gray-200 rounded-2xl overflow-hidden" id="post-{{ $post->id }}">

    {{-- Post Header --}}
    <div class="flex items-start justify-between px-5 pt-4 pb-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                {{ strtoupper(substr($post->user->fname ?? '?', 0, 1)) }}
            </div>
            <div>
                <p class="text-gray-900 text-sm font-semibold">{{ $post->user->display_name ?? 'Unknown' }}</p>
                <p class="text-gray-500 text-xs">{{ $post->user->username ?? '' }} • {{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @if(auth()->id() === $post->user_id)
        <button onclick="deletePost({{ $post->id }}, this)" class="text-gray-500 hover:text-red-400 transition-colors p-1 rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
        @endif
    </div>

    {{-- Mood Tag --}}
    @if($post->moodTags->isNotEmpty())
    <div class="px-5 pb-2">
        @foreach($post->moodTags as $mt)
        @if($mt->moodTag)
        <span class="text-xs px-2.5 py-1 rounded-full mr-1" style="background: {{ $mt->moodTag->color ?? '#7C3AED' }}30; color: {{ $mt->moodTag->color ?? '#A78BFA' }}">
            {{ $mt->moodTag->name }}
        </span>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Text Content --}}
    @if($post->text_content)
    <div class="px-5 pb-3">
        <p class="text-gray-700 text-sm leading-relaxed">{{ $post->text_content }}</p>
    </div>
    @endif

    {{-- Media --}}
    @if($post->media->isNotEmpty())
    <div class="px-5 pb-3">
        <div class="flex gap-2 overflow-x-auto">
            @foreach($post->media as $media)
            @if($media->media_type === 'image')
            <img src="{{ asset('storage/'.$media->path) }}" class="h-48 rounded-xl object-cover flex-shrink-0 max-w-full" alt="">
            @elseif($media->media_type === 'video')
            <video src="{{ asset('storage/'.$media->path) }}" class="h-48 rounded-xl flex-shrink-0" controls></video>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Link Preview --}}
    @if($post->link_url)
    <div class="mx-5 mb-3 bg-gray-50/60 border border-gray-200 rounded-xl px-4 py-3">
        <a href="{{ $post->link_url }}" target="_blank" class="text-green-600 hover:text-green-700 text-xs break-all transition-colors">{{ $post->link_url }}</a>
    </div>
    @endif

    {{-- Action Bar --}}
    <div class="flex items-center gap-1 px-5 py-3 border-t border-gray-200">
        {{-- Like --}}
        <button onclick="toggleLike({{ $post->id }}, this)"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $post->likes->where('user_id', auth()->id())->isNotEmpty() ? 'text-red-400' : 'text-gray-500' }} hover:bg-gray-700 transition-colors text-xs font-medium">
            <svg class="w-4 h-4" fill="{{ $post->likes->where('user_id', auth()->id())->isNotEmpty() ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span id="like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
        </button>

        {{-- Comment --}}
        <button onclick="toggleComments({{ $post->id }})"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-700 transition-colors text-xs font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            {{ $post->allComments->count() }}
        </button>

        {{-- Bookmark --}}
        @if(auth()->user()->role === 'patient')
        <button onclick="toggleBookmark({{ $post->id }}, this)"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg ml-auto {{ $post->bookmarks->where('user_id', auth()->id())->isNotEmpty() ? 'text-yellow-400' : 'text-gray-500' }} hover:bg-gray-700 transition-colors text-xs font-medium">
            <svg class="w-4 h-4" fill="{{ $post->bookmarks->where('user_id', auth()->id())->isNotEmpty() ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
        </button>
        @endif
    </div>

    {{-- Comments Section --}}
    <div id="comments-{{ $post->id }}" class="hidden border-t border-gray-200 px-5 py-4 space-y-3 bg-gray-50/30">
        @foreach($post->comments as $comment)
        <div class="flex items-start gap-2">
            <div class="w-7 h-7 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-700 flex-shrink-0">
                {{ strtoupper(substr($comment->user->fname ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 bg-white rounded-xl px-3 py-2">
                <p class="text-xs font-medium text-gray-900">{{ $comment->user->display_name ?? 'Unknown' }}</p>
                <p class="text-gray-700 text-xs mt-0.5">{{ $comment->comment_text }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function deletePost(postId, btn) {
    if (!confirm('Delete this post?')) return;
    axios.delete(`/posts/${postId}`)
        .then(() => document.getElementById('post-' + postId)?.remove())
        .catch(() => alert('Failed to delete.'));
}
</script>
