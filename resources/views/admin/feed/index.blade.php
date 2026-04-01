@extends('layouts.admin')
@section('title', 'Platform Feed')
@section('page-title', 'Platform Feed Observation')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    @forelse($posts as $post)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                    {{ strtoupper(substr($post->user->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-gray-900 text-sm font-semibold">{{ $post->user->display_name ?? 'Unknown' }}</p>
                        <span class="text-[10px] px-2 py-0.5 rounded-full uppercase font-bold tracking-wide {{ ($post->user->role ?? 'patient') === 'doctor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $post->user->role ?? 'patient' }}
                        </span>
                    </div>
                    <p class="text-gray-500 text-xs">{{ $post->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            {{-- Admin read-only, no actions --}}
        </div>

        @if($post->text_content)
        <div class="mt-3">
            <p class="text-gray-700 text-sm whitespace-pre-wrap">{{ $post->text_content }}</p>
        </div>
        @endif

        @if($post->media->isNotEmpty())
        <div class="mt-3 flex gap-2 overflow-x-auto">
            @foreach($post->media as $media)
                @if($media->media_type === 'image')
                    <img src="{{ asset('storage/'.$media->path) }}" class="h-48 rounded-lg object-cover">
                @endif
            @endforeach
        </div>
        @endif

        <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100 text-sm text-gray-500">
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                {{ $post->likes->count() }} Likes
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ $post->comments->count() }} Comments
            </span>
        </div>
    </div>
    @empty
    <div class="text-center py-10 bg-white shadow-sm border border-gray-100 rounded-xl">
        <p class="text-gray-500 text-sm">No posts on the platform yet.</p>
    </div>
    @endforelse

    @if($posts->hasPages())
    <div>{{ $posts->links() }}</div>
    @endif
</div>
@endsection
