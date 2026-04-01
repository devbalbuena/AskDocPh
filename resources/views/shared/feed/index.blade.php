@extends('layouts.patient')
@section('title', 'Feed')
@section('page-title', 'Feed')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Post Composer --}}
    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-purple-600/30 flex items-center justify-center text-sm font-bold text-purple-300 flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}
            </div>
            <div class="flex-1">
                <textarea id="post-content" rows="3" placeholder="Share your thoughts, experiences, or questions..."
                          class="w-full bg-gray-900/60 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 resize-none transition-colors"></textarea>

                {{-- Media Preview --}}
                <div id="media-preview" class="hidden mt-2 flex gap-2 flex-wrap"></div>

                {{-- Link Input --}}
                <div id="link-input-wrapper" class="hidden mt-2">
                    <input type="url" id="post-link" placeholder="https://..." class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-purple-500">
                </div>

                <input type="file" id="media-input" multiple accept="image/*,video/*" class="hidden">

                <div class="flex items-center justify-between mt-3">
                    <div class="flex items-center gap-1">
                        <button onclick="document.getElementById('media-input').click()"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-gray-400 hover:text-purple-400 hover:bg-gray-700 text-xs font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Photo/Video
                        </button>
                        <button onclick="document.getElementById('link-input-wrapper').classList.toggle('hidden')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-gray-400 hover:text-purple-400 hover:bg-gray-700 text-xs font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            Link
                        </button>
                    </div>
                    <button id="post-submit-btn" onclick="submitPost()"
                            class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors flex items-center gap-2">
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
        @include('shared.feed._post', ['post' => $post])
        @endforeach
    </div>

    @if($posts->hasPages())
    <div class="flex justify-center">
        <button id="load-more-btn" onclick="loadMore()" data-page="2"
                class="bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-400 hover:text-white text-sm px-6 py-3 rounded-xl transition-colors">
            Load More Posts
        </button>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
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
            btn.classList.toggle('text-gray-400', !res.data.liked);
        });
}

function toggleBookmark(postId, btn) {
    axios.post(`/patient/bookmarks/${postId}/toggle`)
        .then(res => {
            btn.classList.toggle('text-yellow-400', res.data.bookmarked);
            btn.classList.toggle('text-gray-400', !res.data.bookmarked);
        });
}

function toggleComments(postId) {
    const el = document.getElementById(`comments-${postId}`);
    el?.classList.toggle('hidden');
}
</script>
@endpush
