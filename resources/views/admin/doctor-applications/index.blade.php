@extends('layouts.admin')
@section('title', 'Doctor Applications')
@section('page-title', 'Doctor Applications')

@section('content')
<div class="space-y-5">
    {{-- Status Tabs --}}
    <div class="flex gap-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-1 w-fit">
        @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val => $label)
        <a href="{{ route('admin.doctor-applications.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $status === $val ? 'bg-green-600 text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($applications as $app)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-11 h-11 rounded-full bg-green-100 flex items-center justify-center font-bold text-green-700">
                    {{ strtoupper(substr($app->user->fname ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-gray-900 font-medium truncate">{{ $app->user->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-xs truncate">{{ $app->user->email ?? '' }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-200">
                <div>
                    <span class="{{ $app->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($app->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }} text-xs px-2.5 py-1 rounded-full capitalize">{{ $app->status }}</span>
                    <p class="text-gray-500 text-xs mt-1">{{ $app->created_at->format('M d, Y') }}</p>
                </div>
                <a href="{{ route('admin.doctor-applications.show', $app) }}"
                   class="{{ $app->status === 'pending' ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-white border border-green-600 text-green-600 hover:bg-green-50' }} text-xs px-4 py-2 rounded-lg transition-colors">
                    {{ $app->status === 'pending' ? 'Review' : 'View Details' }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-medium">No applications found</p>
            <p class="text-gray-400 text-sm mt-1">Check back later</p>
        </div>
        @endforelse
    </div>
    @if($applications->hasPages())
    <div>{{ $applications->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
