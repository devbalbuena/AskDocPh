@extends($layout)
@section('title', 'Help Request — ' . $helpRequest->suggested_title)
@section('page-title', 'Help Request Thread')

@section('content')
<div class="h-full flex flex-col -m-6" style="height: calc(100vh - 4rem);">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex-shrink-0 mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 text-sm font-medium px-4 py-3 rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('info'))
    <div class="flex-shrink-0 mx-6 mt-4 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-medium px-4 py-3 rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('info') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0 flex items-center justify-between z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('help-requests.index') }}" class="p-2 bg-gray-50 text-gray-400 hover:text-green-600 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $helpRequest->suggested_title }}</h2>
                <div class="flex items-center gap-2 mt-0.5">
                    @php $person = auth()->user()->role === 'doctor' ? $helpRequest->user : $helpRequest->doctor; @endphp
                    <span class="text-xs text-gray-500 font-medium">With {{ $person->display_name }}</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] uppercase tracking-wider font-bold capitalize
                        @if($helpRequest->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($helpRequest->status === 'accepted') bg-green-100 text-green-800
                        @elseif($helpRequest->status === 'declined') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $helpRequest->status }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Doctor Actions --}}
        @if(auth()->user()->role === 'doctor' && $helpRequest->status === 'pending')
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('help-requests.decline', $helpRequest->id) }}">
                @csrf
                <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">Decline</button>
            </form>
            <form method="POST" action="{{ route('help-requests.accept', $helpRequest->id) }}">
                @csrf
                <button type="submit" class="bg-green-600 text-white hover:bg-green-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">Accept Request</button>
            </form>
        </div>
        @elseif(auth()->user()->role === 'doctor' && $helpRequest->status === 'accepted')
        <form method="POST" action="{{ route('help-requests.resolve', $helpRequest->id) }}">
            @csrf
            <button type="submit" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Mark as Resolved
            </button>
        </form>
        @endif
    </div>

    {{-- Check Status --}}
    @if($helpRequest->status === 'pending')
        <div class="flex-1 flex flex-col items-center justify-center bg-gray-50/50 p-6">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Request is Pending</h3>
            <p class="text-gray-500 text-center max-w-md">This help request has not been accepted yet. You will be able to send messages once the doctor reviews and accepts the request.</p>
        </div>
    @elseif($helpRequest->status === 'declined')
        <div class="flex-1 flex flex-col items-center justify-center bg-gray-50/50 p-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Request Declined</h3>
            <p class="text-gray-500 text-center max-w-md">The doctor has declined this request. It is no longer active.</p>
        </div>
    @else

        {{-- Messages Area (Accepted or Resolved) --}}
        <div id="messages-container" class="flex-1 overflow-y-auto px-6 py-6 space-y-4 bg-gray-50/30">
            @if($messages->isEmpty())
                <div class="flex flex-col items-center justify-center h-full py-12">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">The request is open. You can start discussing here.</p>
                    <p class="text-xs text-gray-400 mt-1">Type a message below to get started.</p>
                </div>
            @else
                @foreach($messages as $msg)
                @php $isOwn = $msg->sender_user_id === auth()->id(); @endphp
                <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} gap-2.5">
                    
                    @if(!$isOwn)
                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold overflow-hidden
                        {{ $msg->sender->role === 'doctor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                        @if($msg->sender->profile_photo)
                            <img src="{{ Storage::url($msg->sender->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($msg->sender->fname, 0, 1)) }}
                        @endif
                    </div>
                    @endif

                    <div class="max-w-md sm:max-w-lg lg:max-w-xl flex flex-col {{ $isOwn ? 'items-end' : 'items-start' }}">
                        @if(!$isOwn)
                            <span class="text-xs text-gray-500 mb-1 ml-1">{{ $msg->sender->display_name }}</span>
                        @endif
                        <div class="px-4 py-3 text-sm leading-relaxed shadow-sm
                            {{ $isOwn
                                ? 'bg-green-600 text-white rounded-2xl rounded-br-sm'
                                : 'bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-bl-sm' }}">
                            {{ $msg->body }}
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 px-1">{{ $msg->created_at->format('M d, g:i A') }}</span>
                    </div>

                </div>
                @endforeach
            @endif
        </div>

        {{-- Message Input --}}
        @if($helpRequest->status === 'accepted')
        <div class="bg-white border-t border-gray-200 px-6 py-4 flex-shrink-0">
            <form id="msg-form" method="POST" action="{{ route('help-requests.message', $helpRequest->id) }}" class="flex items-end gap-3 max-w-4xl mx-auto w-full">
                @csrf
                <textarea id="reply-input" name="message" required rows="1" maxlength="1000"
                          class="flex-1 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-green-500 transition resize-none outline-none"
                          style="max-height: 120px;" placeholder="Type your reply..." oninput="autoResize(this)" onkeydown="checkEnter(event)"></textarea>
                <button type="submit" id="send-btn" class="flex-shrink-0 bg-green-600 hover:bg-green-700 text-white p-3 rounded-xl transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
        @elseif($helpRequest->status === 'resolved')
        <div class="bg-gray-100 border-t border-gray-200 px-6 py-4 text-center text-sm text-gray-500 font-medium">
            This help request has been marked as resolved and is now closed.
        </div>
        @endif

    @endif
</div>

@push('scripts')
<script>
window.addEventListener('load', () => {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function checkEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        const form = document.getElementById('msg-form');
        const input = document.getElementById('reply-input');
        if (input.value.trim() !== '') {
            document.getElementById('send-btn').disabled = true;
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
