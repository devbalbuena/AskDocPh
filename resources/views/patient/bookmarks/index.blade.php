@extends('layouts.patient')
@section('title', 'My Bookmarks')
@section('page-title', 'Bookmarks')

@section('content')
<div class="space-y-4">
    @forelse($bookmarks as $bm)
    @php $post = $bm->post; @endphp
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5" id="bookmark-card-{{ $post->id }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3 flex-1 min-w-0">
                <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                    {{ strtoupper(substr($post->user->fname ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $post->user->display_name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                    <p class="text-gray-700 text-sm mt-2 line-clamp-3">{{ $post->text_content }}</p>
                    @if($post->media->isNotEmpty())
                    <div class="mt-2 flex gap-2 overflow-x-auto">
                        @foreach($post->media->take(3) as $media)
                        @if($media->media_type === 'image')
                        <img src="{{ asset('storage/'.$media->path) }}" class="h-20 w-28 object-cover rounded-lg flex-shrink-0" alt="">
                        @endif
                        @endforeach
                    </div>
                    @endif
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                        <span>❤️ {{ $post->likes->count() }}</span>
                    </div>
                </div>
            </div>
            <button onclick="removeBookmark({{ $post->id }}, this)"
                    class="flex-shrink-0 p-2 rounded-lg text-yellow-400 hover:bg-gray-700 transition-colors" title="Remove bookmark">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                </svg>
            </button>
        </div>
    </div>
    @empty
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
        <p class="text-gray-500">You haven't bookmarked any posts yet.</p>
        <a href="{{ url('/feed') }}" class="mt-3 inline-block bg-green-600 hover:bg-green-700 text-gray-900 text-sm px-5 py-2.5 rounded-lg transition-colors">Go to Feed</a>
    </div>
    @endforelse
    @if($bookmarks->hasPages())
    <div>{{ $bookmarks->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function removeBookmark(postId, btn) {
    axios.post(`/patient/bookmarks/${postId}/toggle`)
        .then(res => {
            if (!res.data.bookmarked) {
                document.getElementById('bookmark-card-' + postId)?.remove();
            }
        });
}
</script>
@endpush
