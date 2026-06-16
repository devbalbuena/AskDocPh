@extends('layouts.patient')
@section('title', 'My Bookmarks')
@section('page-title', 'Bookmarks')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    <!-- LEFT SIDEBAR -->
    <aside class="w-full md:w-64 flex-shrink-0 space-y-2">
        <a href="{{ route('patient.bookmarks.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-colors {{ !isset($collection) ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-transparent' }}">
            <span>All Bookmarks</span>
        </a>
        <div class="pt-2 pb-1 border-b border-gray-100">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider px-2">Collections</h3>
        </div>
        @foreach($collections ?? [] as $col)
        <div class="group flex items-center justify-between">
            <a href="{{ route('patient.bookmarks.collections.show', $col) }}" class="flex-1 flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-colors {{ isset($collection) && $collection->id === $col->id ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-transparent' }}">
                <span class="truncate pr-2">{{ $col->name }}</span>
                <span class="{{ isset($collection) && $collection->id === $col->id ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-500' }} text-[10px] px-2 py-0.5 rounded-full">{{ $col->items_count }}</span>
            </a>
            @if(isset($collection) && $collection->id === $col->id)
            <button onclick="deleteCollection({{ $col->id }})" class="p-2 ml-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete Collection">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
            @endif
        </div>
        @endforeach
        <button onclick="openNewCollectionModal()" class="w-full mt-4 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-dashed border-gray-300 text-sm font-medium text-gray-500 hover:text-green-600 hover:border-green-600 hover:bg-green-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Collection
        </button>
    </aside>

    <!-- CONTENT -->
    <div class="flex-1 space-y-4">
        @if(isset($collection) && $collection->description)
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 px-2">{{ $collection->description }}</p>
        @endif

        @forelse($bookmarks as $bm)
        @php $post = $bm->post; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" id="bookmark-card-{{ $post->id }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                        {{ strtoupper(substr($post->user->fname ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $post->user->display_name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                        <p class="text-gray-700 dark:text-gray-300 text-sm mt-2 line-clamp-3">{{ $post->text_content }}</p>
                        @if($post->media->isNotEmpty())
                        <div class="mt-2 flex gap-2 overflow-x-auto">
                            @foreach($post->media->take(3) as $media)
                            @if($media->media_type === 'image')
                            <img src="{{ asset('storage/'.$media->path) }}" class="h-20 w-28 object-cover rounded-lg flex-shrink-0" alt="">
                            @endif
                            @endforeach
                        </div>
                        @endif
                        <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1">❤️ {{ $post->likes->count() }}</span>
                            <a href="/feed" class="text-green-600 hover:underline">View in Feed →</a>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    @if(!isset($collection))
                    <div class="relative group">
                        <button class="flex items-center gap-1 text-xs px-3 py-1.5 border border-gray-200 rounded-lg text-gray-600 hover:border-green-500 hover:text-green-600 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add to Collection
                        </button>
                        <div class="absolute right-0 mt-1 w-48 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10 py-1">
                            @forelse($collections ?? [] as $col)
                            <button onclick="addToCollection({{ $col->id }}, {{ $bm->id }})" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700 truncate">
                                {{ $col->name }}
                            </button>
                            @empty
                            <p class="px-4 py-2 text-xs text-gray-400 italic">No collections</p>
                            @endforelse
                        </div>
                    </div>
                    @else
                    <button onclick="removeFromCollection({{ $collection->id }}, {{ $bm->id }}, this)" class="text-xs px-3 py-1.5 border border-red-100 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                        Remove from Collection
                    </button>
                    @endif

                    <button onclick="removeBookmark({{ $post->id }}, this)"
                            class="p-2 rounded-lg text-yellow-400 hover:bg-gray-700 transition-colors" title="Delete bookmark entirely">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
            <p class="text-gray-500 font-medium pt-1">No bookmarks found</p>
            <a href="{{ url('/feed') }}" class="mt-3 inline-block bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2.5 rounded-lg transition-colors">Go to Feed</a>
        </div>
        @endforelse
        @if($bookmarks->hasPages())
        <div>{{ $bookmarks->links() }}</div>
        @endif
    </div>
</div>

<!-- New Collection Modal -->
<div id="col-modal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Collection</h3>
        <input type="text" id="col-name" placeholder="Collection Name" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm mb-3 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
        <textarea id="col-desc" placeholder="Description (Optional)" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm mb-4 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 min-h-[80px]"></textarea>
        <div class="flex justify-end gap-2">
            <button onclick="document.getElementById('col-modal').style.display='none'" class="px-5 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 rounded-xl transition">Cancel</button>
            <button onclick="createCollection()" class="px-5 py-2 text-sm font-medium bg-green-600 text-white hover:bg-green-700 rounded-xl transition">Create</button>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="bookmark-toast" class="fixed bottom-4 right-4 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-50 opacity-0 pointer-events-none" style="transition: opacity 0.3s ease-in-out;">
    Bookmark removed
</div>
@endsection

@push('scripts')
<script>
function removeBookmark(postId, btn) {
    axios.post(`/patient/bookmarks/${postId}/toggle`)
        .then(res => {
            if (!res.data.bookmarked) {
                document.getElementById('bookmark-card-' + postId)?.remove();
                showToast('Bookmark removed completely');
            }
        });
}

function openNewCollectionModal() {
    document.getElementById('col-modal').style.display = 'flex';
}

function createCollection() {
    const name = document.getElementById('col-name').value.trim();
    const desc = document.getElementById('col-desc').value.trim();
    if(!name) return alert('Name is required');
    
    axios.post('{{ route("patient.bookmarks.collections.store") }}', { name, description: desc })
        .then(res => {
            window.location.reload();
        }).catch(err => alert(err.response?.data?.message || 'Error creating collection'));
}

function deleteCollection(id) {
    if(!confirm('Delete this collection? The bookmarks inside will still remain under "All Bookmarks".')) return;
    axios.delete(`/patient/bookmarks/collections/${id}`)
        .then(() => window.location.href = '{{ route("patient.bookmarks.index") }}')
        .catch(err => alert('Error deleting collection'));
}

function addToCollection(colId, bmId) {
    axios.post(`/patient/bookmarks/collections/${colId}/add`, { post_bookmark_id: bmId })
        .then(() => {
            showToast('Added to collection successfully');
            setTimeout(() => window.location.reload(), 1000);
        })
        .catch(err => alert('Error adding to collection'));
}

function removeFromCollection(colId, bmId, btn) {
    axios.delete(`/patient/bookmarks/collections/${colId}/items/${bmId}`)
        .then(() => {
            btn.closest('.bg-white.rounded-2xl').remove();
            showToast('Removed from collection');
        })
        .catch(err => alert('Error removing from collection'));
}

function showToast(msg) {
    const toast = document.getElementById('bookmark-toast');
    toast.textContent = msg;
    toast.classList.remove('opacity-0', 'pointer-events-none');
    toast.classList.add('opacity-100');
    setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0', 'pointer-events-none');
    }, 2500);
}
</script>
@endpush
