@extends($layout)
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    {{-- Header card --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-900">All Notifications</h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $notifications->total() }} notification{{ $notifications->total() !== 1 ? 's' : '' }} total
            </p>
        </div>
        @if($notifications->count())
        <form method="POST" action="{{ route('notifications.read', 'all') }}" class="hidden" id="mark-all-form">@csrf</form>
        @endif
    </div>

    {{-- Notification list --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden divide-y divide-gray-100">

        @forelse($notifications as $notif)
        <div id="notif-row-{{ $notif->id }}"
             class="flex items-start gap-4 px-5 py-4 transition-colors {{ $notif->isRead() ? 'bg-white' : 'bg-green-50' }}">

            {{-- Actor avatar --}}
            <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                @if($notif->actor)
                    {{ strtoupper(substr($notif->actor->fname, 0, 1)) }}
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                @endif
            </div>

            {{-- Notification content --}}
            <div class="flex-1 min-w-0">
                {{-- Type badge --}}
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-semibold uppercase tracking-wider
                        {{ match($notif->type ?? '') {
                            'like'    => 'text-red-500',
                            'comment' => 'text-blue-500',
                            'follow'  => 'text-purple-500',
                            'system'  => 'text-gray-500',
                            default   => 'text-green-600',
                        } }}">
                        {{ ucfirst($notif->type ?? 'notification') }}
                    </span>
                    @if(!$notif->isRead())
                    <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span>
                    @endif
                </div>

                {{-- Message from data JSON --}}
                <p class="text-sm text-gray-800 leading-snug">
                    @if($notif->actor)
                        <span class="font-semibold">{{ $notif->actor->display_name }}</span>
                    @endif
                    {{ $notif->data['message'] ?? ($notif->data['body'] ?? 'You have a new notification.') }}
                </p>

                <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
            </div>

            {{-- Mark as Read button --}}
            @if(!$notif->isRead())
            <button onclick="markRead({{ $notif->id }})"
                    id="mark-btn-{{ $notif->id }}"
                    class="flex-shrink-0 text-xs text-green-600 hover:text-green-800 font-medium border border-green-200 hover:border-green-400 px-3 py-1.5 rounded-lg transition-colors">
                Mark Read
            </button>
            @else
            <span class="flex-shrink-0 text-xs text-gray-400 px-3 py-1.5">Read</span>
            @endif
        </div>
        @empty
        <div class="px-5 py-16 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-500 font-medium">No notifications yet</p>
            <p class="text-gray-400 text-sm mt-1">You're all caught up!</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 shadow-sm">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function markRead(id) {
    axios.post(`/notifications/${id}/read`)
        .then(() => {
            const row = document.getElementById('notif-row-' + id);
            const btn = document.getElementById('mark-btn-' + id);
            if (row) {
                row.classList.remove('bg-green-50');
                row.classList.add('bg-white');
                // Remove the unread dot
                const dot = row.querySelector('.rounded-full.bg-green-500');
                if (dot) dot.remove();
            }
            if (btn) {
                btn.outerHTML = '<span class="flex-shrink-0 text-xs text-gray-400 px-3 py-1.5">Read</span>';
            }
        })
        .catch(() => alert('Could not mark notification as read.'));
}
</script>
@endpush
