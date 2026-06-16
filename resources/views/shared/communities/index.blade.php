@extends($layout)
@section('title', 'Communities')
@section('page-title', 'Communities')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header & Search --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Support Communities</h2>
            <p class="text-sm text-gray-500 mt-1">Join groups focused on specific mental health topics</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <form method="GET" action="{{ route('communities.index') }}" class="flex-1 sm:w-64 relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -mt-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search communities..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </form>
            <a href="{{ route('communities.create') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create
            </a>
        </div>
    </div>

    {{-- Group Grid --}}
    @if($groups->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($groups as $group)
        <a href="{{ route('communities.show', $group->id) }}" class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md hover:border-green-300 transition-all overflow-hidden flex flex-col">
            {{-- Cover --}}
            @if($group->cover_photo)
            <div class="h-32 w-full bg-cover bg-center" style="background-image: url('{{ Storage::url($group->cover_photo) }}')"></div>
            @else
            <div class="h-32 bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center opacity-90 group-hover:opacity-100 transition-opacity">
                <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            @endif

            {{-- Content --}}
            <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-gray-900 group-hover:text-green-700 transition-colors line-clamp-1">
                        {{ $group->name }}
                    </h3>
                    @if($group->visibility === 'private')
                    <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded flex-shrink-0 uppercase tracking-wider title='Private Group'">
                        Priv
                    </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 line-clamp-2 flex-1 mb-4">{{ $group->description ?? 'No description provided.' }}</p>

                <div class="flex items-center justify-between mt-auto">
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        {{ number_format($group->members_count) }} {{ Str::plural('member', $group->members_count) }}
                    </div>

                    @if(in_array($group->id, $myMemberships))
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">Joined</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
    
    <div class="mt-6">
        {{ $groups->links() }}
    </div>

    @else
    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No communities found</h3>
        <p class="text-gray-500 text-sm mb-6">Create the first community or try adjusting your search.</p>
        <a href="{{ route('communities.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2.5 rounded-xl transition-colors">
            Create a Community
        </a>
    </div>
    @endif
</div>
@endsection
