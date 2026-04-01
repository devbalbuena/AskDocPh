@extends('layouts.admin')
@section('title', 'Doctor Applications')
@section('page-title', 'Doctor Applications')

@section('content')
<div class="space-y-5">
    {{-- Status Tabs --}}
    <div class="flex gap-1 bg-gray-800 border border-gray-700 rounded-xl p-1 w-fit">
        @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val => $label)
        <a href="{{ route('admin.doctor-applications.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $status === $val ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($applications as $app)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex flex-col">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-11 h-11 rounded-full bg-purple-600/30 flex items-center justify-center font-bold text-purple-300">
                    {{ strtoupper(substr($app->user->fname ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-medium truncate">{{ $app->user->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-400 text-xs truncate">{{ $app->user->email ?? '' }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-700">
                <div>
                    <span class="{{ $app->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : ($app->status === 'approved' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400') }} text-xs px-2.5 py-1 rounded-full capitalize">{{ $app->status }}</span>
                    <p class="text-gray-500 text-xs mt-1">{{ $app->created_at->format('M d, Y') }}</p>
                </div>
                <a href="{{ route('admin.doctor-applications.show', $app) }}" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-4 py-2 rounded-lg transition-colors">Review →</a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-gray-800 border border-gray-700 rounded-xl p-10 text-center">
            <p class="text-gray-400 text-sm">No applications found.</p>
        </div>
        @endforelse
    </div>
    @if($applications->hasPages())
    <div>{{ $applications->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
