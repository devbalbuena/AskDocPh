@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('page-title', 'System Audit Logs')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Audit Logs</h2>
            <p class="text-sm text-gray-500 mt-1">Review system activities and admin operations across the platform.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin / User</th>
                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6 text-sm text-gray-600 whitespace-nowrap">
                            {{ $log->created_at->format('M d, Y') }}
                            <span class="block text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ $log->user ? strtoupper(substr($log->user->fname, 0, 1)) : '?' }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $log->user->display_name ?? 'System' }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-700">
                            {{ $log->description ?: '-' }}
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500 font-mono text-xs">
                            {{ $log->ip_address ?? 'Unknown' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-500">
                            No audit logs recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
