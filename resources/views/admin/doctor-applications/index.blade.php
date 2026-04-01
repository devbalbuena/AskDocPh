@extends('layouts.admin')
@section('title', 'Doctor Applications')
@section('page-title', 'Doctor Applications')

@section('content')
<div class="space-y-5">
    {{-- Status Tabs --}}
    <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 w-fit">
        @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val => $label)
        <a href="{{ route('admin.doctor-applications.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $status === $val ? 'bg-green-600 text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($applications as $app)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
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
                <a href="{{ route('admin.doctor-applications.show', $app) }}" class="bg-green-600 hover:bg-green-700 text-gray-900 text-xs px-4 py-2 rounded-lg transition-colors">Review →</a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-gray-200 rounded-xl p-10 text-center">
            <p class="text-gray-500 text-sm">No applications found.</p>
        </div>
        @endforelse
    </div>
    @if($applications->hasPages())
    <div>{{ $applications->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
