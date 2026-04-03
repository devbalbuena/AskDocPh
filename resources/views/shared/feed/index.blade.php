@extends($layout)
@section('title', 'Feed')
@section('page-title', 'Feed')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Post Composer --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}
            </div>
            <div class="flex-1">
                <textarea id="post-content" rows="3" placeholder="Share your thoughts, experiences, or questions..."
                          class="w-full bg-gray-50/60 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors" style="min-height: 80px; resize: none; overflow: hidden;"></textarea>

                {{-- Anonymous Toggle --}}
                <div class="mt-2 flex items-center justify-between">
                    <label class="flex items-center gap-1.5 text-xs font-medium text-gray-500 cursor-pointer hover:text-gray-700 transition-colors">
                        <input type="checkbox" id="post-anonymous-toggle" class="appearance-none w-8 h-4 bg-gray-200 rounded-full relative transition-colors checked:bg-green-500 before:content-[''] before:absolute before:w-3 before:h-3 before:bg-white before:rounded-full before:top-0.5 before:left-0.5 before:transition-transform checked:before:translate-x-4 shadow-inner" onchange="document.getElementById('post-anonymous-input').value = this.checked ? '1' : '0'; document.getElementById('post-anonymous-note').classList.toggle('hidden', !this.checked);">
                        <svg class="w-3 h-3 text-gray-400 lock-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-top:-1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Post Anonymously
                    </label>
                    <span id="post-anonymous-note" class="text-[10px] text-gray-400 hidden italic">Your name will be hidden from this post</span>
                    <input type="hidden" id="post-anonymous-input" name="is_anonymous" value="0">
                </div>

                {{-- Media Preview --}}
                <div id="media-preview" class="hidden mt-2 flex gap-2 flex-wrap"></div>

                {{-- Link Input --}}
                <div id="link-input-wrapper" class="hidden mt-2">
                    <input type="url" id="post-link" placeholder="https://..." class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500">
                </div>

                {{-- Link Preview Card --}}
                <div id="link-preview-card" class="hidden mt-2 border border-gray-200 bg-gray-50 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p id="link-preview-domain" class="text-sm font-semibold text-gray-900 truncate"></p>
                        <p id="link-preview-url" class="text-xs text-gray-500 truncate"></p>
                    </div>
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
        @php
            $userReaction = $post->likes->firstWhere('user_id', auth()->id());
            $emojiMap     = ['heart' => '❤️', 'sad' => '😢', 'wow' => '😮', 'haha' => '😂', 'like' => '👍'];
            $currentEmoji = $userReaction ? ($emojiMap[$userReaction->reaction_type] ?? '❤️') : '❤️';
            $reacted      = $userReaction !== null;

            $isAnonymous = str_starts_with($post->text_content ?? '', '[ANONYMOUS]');
            $textContent = $isAnonymous ? substr($post->text_content, 11) : $post->text_content;
        @endphp
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5" id="post-{{ $post->id }}">

            {{-- Post header --}}
            <div class="flex items-center gap-3 mb-3">
                @if($isAnonymous)
                <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-500 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-sm font-semibold text-gray-900">
                            Anonymous
                        </span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Patient</span>
                    </div>
                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                </div>
                @else
                <a href="{{ url('/users/' . ($post->user->username ?? '')) }}"
                   class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0 hover:ring-2 hover:ring-green-400 transition">
                    {{ strtoupper(substr($post->user->fname ?? 'U', 0, 1)) }}
                </a>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ url('/users/' . ($post->user->username ?? '')) }}"
                           class="text-sm font-semibold text-gray-900 hover:text-green-700 transition-colors">
                            {{ $post->user->display_name ?? 'Unknown' }}
                        </a>
                        @if($post->user->role === 'admin')
                            <span class="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded-full">Announcement</span>
                        @elseif($post->user->role === 'doctor')
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full flex items-center"><svg class="w-3 h-3 text-green-600 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> ✓ Verified Doctor</span>
                        @else
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Patient</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                </div>
                @endif

                {{-- Three-dot menu --}}
                <div class="relative post-menu-wrapper flex-shrink-0">
                    <button onclick="togglePostMenu({{ $post->id }}, event)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors font-bold text-lg">
                        &#8942;
                    </button>
                    <div id="post-menu-{{ $post->id }}"
                         class="post-menu hidden absolute right-0 top-9 w-44 bg-white border border-gray-200 rounded-xl shadow-xl py-1 z-20">
                        @if(auth()->id() === $post->user_id)
                        <button onclick="deletePost({{ $post->id }})"
                                class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Post
                        </button>
                        @else
                        <button onclick="openReportModal({{ $post->id }})"
                                class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                            Report Post
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Post body --}}
            @if($textContent)
            <p class="text-sm text-gray-800 leading-relaxed mb-3">{{ $textContent }}</p>
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

            {{-- Link preview --}}
            @if($post->link_url)
            <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-3">
                <a href="{{ $post->link_url }}" target="_blank" class="text-green-600 hover:text-green-700 text-xs break-all transition-colors">{{ $post->link_url }}</a>
            </div>
            @endif

            {{-- Action Bar --}}
            <div class="flex items-center gap-4 pt-2 border-t border-gray-100">

                {{-- Emoji Reaction Wrapper --}}
                <div class="relative inline-block reaction-wrapper"
                     id="reaction-wrapper-{{ $post->id }}"
                     onmouseenter="showReactionPicker({{ $post->id }})"
                     onmouseleave="scheduleHideReactionPicker({{ $post->id }})">
                    <button onclick="handleReactionBtnClick({{ $post->id }}, event)"
                            id="reaction-btn-{{ $post->id }}"
                            class="flex items-center gap-1.5 text-xs font-medium {{ $reacted ? 'text-red-400' : 'text-gray-500' }} hover:text-red-400 transition-colors px-1 py-1">
                        <span id="reaction-emoji-{{ $post->id }}" class="text-base leading-none">{{ $currentEmoji }}</span>
                        <span id="like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
                    </button>
                    {{-- Emoji picker row --}}
                    <div id="reaction-picker-{{ $post->id }}"
                         class="reaction-picker hidden absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-2xl shadow-xl p-1.5 flex gap-0.5 z-10 whitespace-nowrap"
                         onmouseenter="cancelHideReactionPicker({{ $post->id }})"
                         onmouseleave="scheduleHideReactionPicker({{ $post->id }})">
                        @foreach(['❤️' => 'heart', '😢' => 'sad', '😮' => 'wow', '😂' => 'haha', '👍' => 'like'] as $emoji => $type)
                        <button onclick="reactPost({{ $post->id }}, '{{ $type }}', '{{ $emoji }}')"
                                title="{{ ucfirst($type) }}"
                                class="w-9 h-9 flex items-center justify-center text-xl rounded-xl hover:bg-gray-100 hover:scale-125 transform transition-all duration-150">
                            {{ $emoji }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Comment toggle --}}
                <button class="comment-toggle-btn flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-green-600 transition-colors"
                        data-post-id="{{ $post->id }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ $post->comments->count() }} Comment{{ $post->comments->count() !== 1 ? 's' : '' }}
                </button>

                <button onclick="toggleBookmark({{ $post->id }}, this)"
                    class="flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-yellow-500 transition-colors bookmark-btn"
                    data-post-id="{{ $post->id }}"
                    data-bookmarked="{{ in_array($post->id, $userBookmarks) ? 'true' : 'false' }}">
                    <svg class="w-4 h-4" fill="{{ in_array($post->id, $userBookmarks) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <span class="bookmark-label">{{ in_array($post->id, $userBookmarks) ? 'Saved' : 'Save' }}</span>
                </button>
            </div>

            {{-- Comments Section (hidden by default) --}}
            <div id="comments-{{ $post->id }}" class="hidden mt-4 space-y-3">
                <div id="comment-list-{{ $post->id }}" class="space-y-2">
                    @foreach($post->comments->take(5) as $comment)
                    <div class="p-2 border-t border-gray-100 text-sm">
                        <span class="font-semibold text-gray-800">{{ $comment->user->display_name ?? 'Unknown' }}</span>
                        <span class="text-gray-700 ml-1">{{ $comment->comment_text }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    @endforeach
                </div>
                <form class="comment-form flex gap-2 items-end" data-post-id="{{ $post->id }}">
                    @csrf
                    <textarea name="comment_text" rows="2" placeholder="Write a comment..."
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

{{-- Toast --}}
<div id="feed-toast" class="hidden fixed bottom-6 right-6 z-50 bg-green-600 text-white text-sm px-5 py-3 rounded-xl shadow-xl flex items-center gap-2 max-w-sm">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    <span id="feed-toast-msg"></span>
</div>

{{-- Report Post Modal --}}
<div id="report-modal" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-24 px-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReportModal()"></div>
    <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Report Post</h3>
            <button onclick="closeReportModal()" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors text-xl leading-none">&times;</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Reason <span class="text-red-500">*</span></label>
                <select id="report-reason"
                        onchange="document.getElementById('report-submit-btn').disabled = !this.value"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                    <option value="">Select a reason...</option>
                    <option value="spam">Spam</option>
                    <option value="inappropriate">Inappropriate Content</option>
                    <option value="misinformation">Misinformation</option>
                    <option value="harassment">Harassment</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Additional Details <span class="text-gray-400">(optional)</span></label>
                <textarea id="report-details" rows="3" placeholder="Describe the issue in more detail..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 resize-none transition-colors"></textarea>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeReportModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition-colors">Cancel</button>
            <button id="report-submit-btn" onclick="submitReport()" disabled
                    class="bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                Submit Report
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Axios CSRF is already set globally by app.js — no need to set again here

// ── Media preview ───────────────────────────────────────────────
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

// ── Post submission ─────────────────────────────────────────────
function submitPost() {
    const content    = document.getElementById('post-content').value.trim();
    const link       = document.getElementById('post-link')?.value.trim();
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
    formData.append('is_anonymous', document.getElementById('post-anonymous-input')?.value || '0');
    formData.append('post_type', mediaFiles.length > 0 ? 'media' : (link ? 'link' : 'text'));
    if (link) formData.append('link_url', link);
    Array.from(mediaFiles).forEach(f => formData.append('media[]', f));

    axios.post('{{ route("posts.store") }}', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => {
        if (res.data.success) {
            document.getElementById('post-content').value = '';
            document.getElementById('media-input').value  = '';
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

// ── Emoji reaction picker ───────────────────────────────────────
const _hideTimers = {};

function showReactionPicker(postId) {
    cancelHideReactionPicker(postId);
    document.getElementById(`reaction-picker-${postId}`)?.classList.remove('hidden');
}

function scheduleHideReactionPicker(postId) {
    _hideTimers[postId] = setTimeout(() => {
        document.getElementById(`reaction-picker-${postId}`)?.classList.add('hidden');
    }, 300);
}

function cancelHideReactionPicker(postId) {
    clearTimeout(_hideTimers[postId]);
}

// Click fallback for touch — toggles picker; double-click = heart
function handleReactionBtnClick(postId, event) {
    event.stopPropagation();
    const picker = document.getElementById(`reaction-picker-${postId}`);
    if (picker.classList.contains('hidden')) {
        document.querySelectorAll('.reaction-picker').forEach(p => p.classList.add('hidden'));
        picker.classList.remove('hidden');
    } else {
        picker.classList.add('hidden');
        reactPost(postId, 'heart', '❤️');
    }
}

function reactPost(postId, reactionType, emoji) {
    document.getElementById(`reaction-picker-${postId}`)?.classList.add('hidden');

    axios.post(`/posts/${postId}/like`, { reaction_type: reactionType })
        .then(res => {
            const countEl = document.getElementById(`like-count-${postId}`);
            const emojiEl = document.getElementById(`reaction-emoji-${postId}`);
            const btn     = document.getElementById(`reaction-btn-${postId}`);
            if (countEl) countEl.textContent = res.data.likes_count;
            if (res.data.liked) {
                if (emojiEl) emojiEl.textContent = emoji;
                btn?.classList.add('text-red-400');
                btn?.classList.remove('text-gray-500');
            } else {
                if (emojiEl) emojiEl.textContent = '❤️';
                btn?.classList.remove('text-red-400');
                btn?.classList.add('text-gray-500');
            }
        })
        .catch(err => console.error('Reaction failed:', err));
}

// ── Three-dot post menu ─────────────────────────────────────────
function togglePostMenu(postId, event) {
    event.stopPropagation();
    const menu   = document.getElementById(`post-menu-${postId}`);
    const isOpen = !menu.classList.contains('hidden');
    document.querySelectorAll('.post-menu').forEach(m => m.classList.add('hidden'));
    if (!isOpen) menu.classList.remove('hidden');
}

// ── Delete own post ─────────────────────────────────────────────
function deletePost(postId) {
    if (!confirm('Delete this post? This cannot be undone.')) return;
    document.getElementById(`post-menu-${postId}`)?.classList.add('hidden');
    axios.delete(`/posts/${postId}`)
        .then(() => document.getElementById('post-' + postId)?.remove())
        .catch(() => alert('Failed to delete post.'));
}

// ── Bookmark toggle ─────────────────────────────────────────────
function toggleBookmark(postId, btn) {
    axios.post('/patient/bookmarks/' + postId + '/toggle')
    .then(res => {
        const label = btn.querySelector('.bookmark-label');
        const icon = btn.querySelector('svg');
        if (res.data.bookmarked) {
            label.textContent = 'Saved';
            icon.setAttribute('fill', 'currentColor');
            btn.classList.add('text-yellow-500');
        } else {
            label.textContent = 'Save';
            icon.setAttribute('fill', 'none');
            btn.classList.remove('text-yellow-500');
        }
    });
}

// ── Report post modal ───────────────────────────────────────────
let _reportPostId = null;

function openReportModal(postId) {
    _reportPostId = postId;
    document.getElementById(`post-menu-${postId}`)?.classList.add('hidden');
    document.getElementById('report-reason').value = '';
    document.getElementById('report-details').value = '';
    document.getElementById('report-submit-btn').disabled = true;
    document.getElementById('report-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReportModal() {
    document.getElementById('report-modal').classList.add('hidden');
    document.body.style.overflow = '';
    _reportPostId = null;
}

function submitReport() {
    if (!_reportPostId) return;
    const reason  = document.getElementById('report-reason').value;
    const details = document.getElementById('report-details').value;
    const btn     = document.getElementById('report-submit-btn');
    btn.disabled     = true;
    btn.textContent  = 'Submitting...';

    axios.post(`/posts/${_reportPostId}/report`, { reason, details })
        .then(() => {
            closeReportModal();
            showToast('Post reported. Thank you for helping keep our community safe.');
        })
        .catch(err => {
            alert(err.response?.data?.message ?? 'Failed to submit report.');
            btn.disabled    = false;
            btn.textContent = 'Submit Report';
        });
}

// ── Toast ───────────────────────────────────────────────────────
function showToast(message) {
    const toast = document.getElementById('feed-toast');
    const msg   = document.getElementById('feed-toast-msg');
    if (!toast || !msg) return;
    msg.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4500);
}

// ── Global click: close menus & pickers when clicking outside ───
document.addEventListener('click', function (e) {
    if (!e.target.closest('.post-menu-wrapper')) {
        document.querySelectorAll('.post-menu').forEach(m => m.classList.add('hidden'));
    }
    if (!e.target.closest('.reaction-wrapper')) {
        document.querySelectorAll('.reaction-picker').forEach(p => p.classList.add('hidden'));
    }
});

// Close report modal on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeReportModal();
});

// ── Comment toggle & submit ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    console.log('feed js loaded'); // DEBUG: confirms script runs after Vite/axios

    // Auto-resizing textarea
    const textarea = document.getElementById('post-content');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Link preview extraction
    const linkInput = document.getElementById('post-link');
    const previewCard = document.getElementById('link-preview-card');
    if (linkInput && previewCard) {
        linkInput.addEventListener('input', function() {
            try {
                const url = new URL(this.value);
                document.getElementById('link-preview-domain').textContent = url.hostname;
                document.getElementById('link-preview-url').textContent = this.value.length > 60 ? this.value.substring(0, 60) + '...' : this.value;
                previewCard.classList.remove('hidden');
            } catch (e) {
                previewCard.classList.add('hidden');
            }
        });
    }

    document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const section = document.getElementById('comments-' + this.dataset.postId);
            if (section) section.classList.toggle('hidden');
        });
    });

    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const postId    = this.dataset.postId;
            const textarea  = this.querySelector('textarea[name="comment_text"]');
            const text      = textarea.value.trim();
            if (!text) return;

            const submitBtn      = this.querySelector('button[type="submit"]');
            submitBtn.disabled   = true;
            submitBtn.textContent = '...';

            axios.post(`/posts/${postId}/comments`, { comment_text: text })
                .then(res => {
                    if (res.data.success) {
                        const list = document.getElementById('comment-list-' + postId);
                        const div  = document.createElement('div');
                        div.className = 'p-2 border-t border-gray-100 text-sm';
                        div.innerHTML =
                            `<span class="font-semibold text-gray-800">${res.data.comment.author}</span>` +
                            ` <span class="text-gray-700">${res.data.comment.text}</span>` +
                            ` <span class="text-xs text-gray-400 ml-2">${res.data.comment.created_at}</span>`;
                        list.appendChild(div);
                        textarea.value = '';
                    }
                })
                .catch(err => alert(err.response?.data?.message ?? 'Failed to post comment.'))
                .finally(() => {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'Send';
                });
        });
    });
}); // end DOMContentLoaded
</script>
@endpush
