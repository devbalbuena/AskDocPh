@extends($layout)
@section('title', $resource->title)
@section('page-title', 'Resource Detail')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Back link --}}
    <a href="{{ route('resources.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-green-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Resources
    </a>

    {{-- Resource header card --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        {{-- Type banner --}}
        <div class="h-32 flex items-center justify-center
            {{ match($resource->type) {
                'video'   => 'bg-gradient-to-br from-blue-500 to-blue-700',
                'pdf'     => 'bg-gradient-to-br from-red-500 to-red-700',
                default   => 'bg-gradient-to-br from-green-600 to-emerald-700',
            } }}">
            @if($resource->type === 'video')
            <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @elseif($resource->type === 'pdf')
            <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            @else
            <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            @endif
        </div>

        <div class="p-6">
            {{-- Type badge + meta --}}
            <div class="flex items-center gap-3 mb-3 flex-wrap">
                <span class="text-xs font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full
                    {{ match($resource->type) {
                        'video'   => 'bg-blue-100 text-blue-700',
                        'pdf'     => 'bg-red-100 text-red-700',
                        default   => 'bg-green-100 text-green-700',
                    } }}">
                    {{ $resource->type }}
                </span>
                @if(optional($resource->author)->username)
                <a href="/users/{{ optional($resource->author)->username }}"
                   class="text-green-600 hover:underline text-xs">
                    Dr. {{ optional($resource->author)->fname }} {{ optional($resource->author)->lname }}
                </a>
                @else
                <span class="text-xs text-gray-400">Unknown author</span>
                @endif
                <span class="text-xs text-gray-400">
                    {{ $resource->created_at->format('M d, Y') }}
                </span>
            </div>

            <h1 class="text-xl font-bold text-gray-900 mb-3 leading-snug">{{ $resource->title }}</h1>
            <p class="text-gray-600 text-sm leading-relaxed">{{ $resource->description }}</p>

            {{-- Doctor delete button (own resources only) --}}
            @if(auth()->user()->role === 'doctor' && auth()->id() === $resource->user_id)
            <form method="POST" action="{{ route('doctor.resources.destroy', $resource) }}"
                  class="mt-4"
                  onsubmit="return confirm('Delete this resource?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-red-500 hover:text-red-700 text-xs border border-red-200 hover:border-red-400 px-3 py-1.5 rounded-lg transition-colors">
                    Delete Resource
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Resource content --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

        @if($resource->type === 'article')
            {{-- Article: render the body content --}}
            @if($resource->body && $resource->body->content)
                <div class="prose prose-green prose-sm max-w-none text-gray-800 leading-relaxed">
                    {!! nl2br(e($resource->body->content)) !!}
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-8">No article content available.</p>
            @endif

        @elseif($resource->type === 'pdf')
            {{-- PDF: show download link --}}
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                @if($resource->file_path)
                <a href="{{ Storage::url($resource->file_path) }}"
                   download
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-3 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </a>
                @else
                <p class="text-gray-400 text-sm">PDF file not available.</p>
                @endif
            </div>

        @elseif($resource->type === 'video')
            {{-- Video: detect if file_path is a URL (YouTube/Vimeo) or a stored file --}}
            @if($resource->file_path && str_starts_with($resource->file_path, 'http'))
                {{-- External video URL --}}
                <div class="text-center py-6">
                    <a href="{{ $resource->file_path }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Watch Video
                    </a>
                </div>
            @elseif($resource->file_path)
                {{-- Uploaded video file --}}
                <video controls
                       class="w-full rounded-xl max-h-96 bg-black"
                       src="{{ Storage::url($resource->file_path) }}">
                    Your browser does not support the video tag.
                </video>
            @else
                <p class="text-gray-400 text-sm text-center py-8">Video not available.</p>
            @endif
        @endif
    </div>
</div>
@endsection
