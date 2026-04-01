@extends($layout)
@section('title', 'Feed')
@section('page-title', 'Feed')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Post Composer --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}
            </div>
            <div class="flex-1">
                <textarea id="post-content" rows="3" placeholder="Share your thoughts, experiences, or questions..."
                          class="w-full bg-gray-50/60 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 resize-none transition-colors"></textarea>

                {{-- Media Preview --}}
                <div id="media-preview" class="hidden mt-2 flex gap-2 flex-wrap"></div>

                {{-- Link Input --}}
                <div id="link-input-wrapper" class="hidden mt-2">
                    <input type="url" id="post-link" placeholder="https://..." class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500">
                </div>

                <input type="file" id="media-input" multiple accept="image/*,video/*" class="hidden">

                <div class="flex items-center justify-between mt-3">
                    <div class="flex items-center gap-1">
                        <button onclick="document.getElementById('media-input').click()"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-gray-500 hover:text-green-600 hover:bg-gray-100 text-xs font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Photo/Video
                        </button>
                        <button onclick="document.getElementById('link-input-wrapper').classList.toggle('hidden')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-gray-500 hover:text-green-600 hover:bg-gray-100 text-xs font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            Link
                        </button>
                    </div>
                    <button id="post-submit-btn" onclick="submitPost()"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Share
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Posts Feed --}}
    <div id="posts-container" class="space-y-4">
        @foreach($posts as $post)
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            {{-- Post header --}}
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ url('/users/' . ($post->user->username ?? '')) }}"
                   class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0 hover:ring-2 hover:ring-green-400 transition">
                    {{ strtoupper(substr($post->user->fname ?? 'U', 0, 1)) }}
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ url('/users/' . ($post->user->username ?? '')) }}"
                       class="text-sm font-semibold text-gray-900 hover:text-green-700 transition-colors truncate block">
                        {{ $post->user->display_name ?? 'Unknown' }}
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

            {{-- Action Bar --}}
            <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                {{-- Like --}}
                <button onclick="toggleLike({{ $post->id }}, this)"
                        class="flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span id="like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
                </button>

                {{-- Comment toggle --}}
                <button class="comment-toggle-btn flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-green-600 transition-colors"
                        data-post-id="{{ $post->id }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ $post->comments->count() }} Comment{{ $post->comments->count() !== 1 ? 's' : '' }}
                </button>
            </div>

            {{-- Comments Section (hidden by default) --}}
            <div id="comments-{{ $post->id }}" class="hidden mt-4 space-y-3">

                {{-- Existing comments list --}}
                <div id="comment-list-{{ $post->id }}" class="space-y-2">
                    @foreach($post->comments->take(5) as $comment)
                    <div class="p-2 border-t border-gray-100 text-sm">
                        <span class="font-semibold text-gray-800">{{ $comment->user->display_name ?? 'Unknown' }}</span>
                        <span class="text-gray-700 ml-1">{{ $comment->comment_text }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Comment form --}}
                <form class="comment-form flex gap-2 items-end" data-post-id="{{ $post->id }}">
                    @csrf
                    <textarea name="comment_text" rows="2"
                              placeholder="Write a comment..."
                              class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 resize-none transition-colors"></textarea>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition-colors flex-shrink-0">
                        Send
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    @if($posts->hasPages())
    <div class="flex justify-center">
        <button id="load-more-btn" onclick="loadMore()" data-page="2"
                class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-500 hover:text-gray-900 text-sm px-6 py-3 rounded-xl transition-colors">
            Load More Posts
        </button>
    </div>
    @endif
</div>
@endsection

<script>
// Set Axios CSRF token globally so all requests include it automatically
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Media file selection preview
document.getElementById('media-input').addEventListener('change', function () {
    const preview = document.getElementById('media-preview');
    preview.classList.remove('hidden');
    preview.innerHTML = '';
    Array.from(this.files).forEach(file => {
        const url = URL.createObjectURL(file);
        const el = file.type.startsWith('video')
            ? `<video src="${url}" class="h-24 rounded-lg object-cover" controls></video>`
            : `<img src="${url}" class="h-24 w-32 rounded-lg object-cover">`;
        preview.innerHTML += el;
    });
});

function submitPost() {
    const content = document.getElementById('post-content').value.trim();
    const link = document.getElementById('post-link')?.value.trim();
    const mediaFiles = document.getElementById('media-input').files;

    if (!content && mediaFiles.length === 0 && !link) {
        alert('Please write something before sharing.');
        return;
    }

    const btn = document.getElementById('post-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Sharing...';

    const formData = new FormData();
    formData.append('text_content', content);
    formData.append('post_type', mediaFiles.length > 0 ? 'media' : (link ? 'link' : 'text'));
    if (link) formData.append('link_url', link);
    Array.from(mediaFiles).forEach(f => formData.append('media[]', f));

    axios.post('{{ route("posts.store") }}', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => {
        if (res.data.success) {
            document.getElementById('post-content').value = '';
            document.getElementById('media-input').value = '';
            document.getElementById('media-preview').innerHTML = '';
            document.getElementById('media-preview').classList.add('hidden');
            document.getElementById('link-input-wrapper').classList.add('hidden');
            window.location.reload();
        }
    }).catch(err => {
        alert(err.response?.data?.message ?? 'Failed to post. Please try again.');
    }).finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Share';
    });
}

function toggleLike(postId, btn) {
    axios.post(`/posts/${postId}/like`)
        .then(res => {
            const countEl = document.getElementById(`like-count-${postId}`);
            if (countEl) countEl.textContent = res.data.likes_count;
            btn.classList.toggle('text-red-400', res.data.liked);
            btn.classList.toggle('text-gray-500', !res.data.liked);
        });
}

// Comment toggle & form submit — wrapped in DOMContentLoaded
// so all post card elements exist before we attach listeners
document.addEventListener('DOMContentLoaded', function () {
    console.log('feed js loaded'); // DEBUG: verify script is running

    // Toggle show/hide comment section per post
    document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const postId = this.dataset.postId;
            const section = document.getElementById('comments-' + postId);
            if (section) {
                section.classList.toggle('hidden');
            }
        });
    });

    // Comment form submit — Axios POST, append new comment without page reload
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            const textarea = this.querySelector('textarea[name="comment_text"]');
            const text = textarea.value.trim();

            if (!text) return;

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = '...';

            axios.post(`/posts/${postId}/comments`, {
                comment_text: text,
            }).then(res => {
                if (res.data.success) {
                    const list = document.getElementById('comment-list-' + postId);
                    const div = document.createElement('div');
                    div.className = 'p-2 border-t border-gray-100 text-sm';
                    div.innerHTML = `<span class="font-semibold text-gray-800">${res.data.comment.author}</span>`
                        + ` <span class="text-gray-700">${res.data.comment.text}</span>`
                        + ` <span class="text-xs text-gray-400 ml-2">${res.data.comment.created_at}</span>`;
                    list.appendChild(div);
                    textarea.value = '';
                }
            }).catch(err => {
                alert(err.response?.data?.message ?? 'Failed to post comment.');
            }).finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send';
            });
        });
    });

}); // end DOMContentLoaded
</script>
