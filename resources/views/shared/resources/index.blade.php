@extends($layout)
@section('title', 'Health Resources')
@section('page-title', 'Health Resources')

@section('content')
<div class="space-y-6">

    {{-- Header row --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        {{-- Filter tabs --}}
        <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 flex-wrap">
            @foreach([''=>'All', 'article'=>'Articles', 'video'=>'Videos', 'pdf'=>'PDFs'] as $val => $label)
            <a href="{{ route('resources.index', $val ? ['type' => $val] : []) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-all
                      {{ $activeType === ($val ?: null) ? 'bg-green-600 text-white' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        {{-- Doctor-only: Create Resource button --}}
        @if(auth()->user()->role === 'doctor')
        <a href="/doctor/resources/create"
           class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Resource
        </a>
        @endif
    </div>

    {{-- Resources grid --}}
    @if($resources->count())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($resources as $resource)
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden group hover:shadow-md transition-shadow">

            {{-- Type icon banner --}}
            <div class="h-28 flex items-center justify-center
                {{ match($resource->type) {
                    'video'   => 'bg-gradient-to-br from-blue-500 to-blue-700',
                    'pdf'     => 'bg-gradient-to-br from-red-500 to-red-700',
                    default   => 'bg-gradient-to-br from-green-600 to-emerald-700',
                } }}">
                @if($resource->type === 'video')
                <svg class="w-14 h-14 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @elseif($resource->type === 'pdf')
                <svg class="w-14 h-14 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                @else
                <svg class="w-14 h-14 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                @endif
            </div>

            {{-- Card body --}}
            <div class="p-5">
                {{-- Type badge --}}
                <span class="inline-block text-xs font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full mb-3
                    {{ match($resource->type) {
                        'video'   => 'bg-blue-100 text-blue-700',
                        'pdf'     => 'bg-red-100 text-red-700',
                        default   => 'bg-green-100 text-green-700',
                    } }}">
                    {{ $resource->type }}
                </span>

                <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-2 line-clamp-2">
                    {{ $resource->title }}
                </h3>

                <p class="text-gray-500 text-xs leading-relaxed line-clamp-3 mb-4">
                    {{ Str::limit($resource->description, 120) }}
                </p>

                <div class="flex items-center justify-between">
                    @if(optional($resource->author)->username)
                    <a href="/users/{{ optional($resource->author)->username }}"
                       class="text-green-600 hover:underline text-xs">
                        Dr. {{ optional($resource->author)->fname }} {{ optional($resource->author)->lname }}
                    </a>
                    @else
                    <span class="text-xs text-gray-400">Unknown author</span>
                    @endif
                    <a href="{{ route('resources.show', $resource) }}"
                       class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-4 py-1.5 rounded-lg transition-colors">
                        View
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($resources->hasPages())
    <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 shadow-sm">
        {{ $resources->links() }}
    </div>
    @endif

    @else
    <div class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm">
        <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-600 font-medium">No resources found</p>
        <p class="text-gray-400 text-sm mt-1">
            @if($activeType)
                No {{ $activeType }} resources available yet.
                <a href="{{ route('resources.index') }}" class="text-green-600 hover:underline">View all resources</a>
            @else
                No resources have been published yet.
            @endif
        </p>
    </div>
    @endif
</div>
@endsection
