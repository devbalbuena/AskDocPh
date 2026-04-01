@extends('layouts.admin')
@section('title', 'Crisis Reports')
@section('page-title', 'Crisis Reports')

@section('content')
<div class="space-y-5">
    {{-- Status Filter --}}
    <div class="flex gap-1 bg-gray-800 border border-gray-700 rounded-xl p-1 w-fit flex-wrap">
        @foreach(['all'=>'All','pending'=>'Pending','responding'=>'Responding','resolved'=>'Resolved'] as $val => $label)
        <a href="{{ route('admin.crisis.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $status === $val ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-3">User</th>
                        <th class="text-left px-5 py-3">Description</th>
                        <th class="text-left px-5 py-3">Submitted</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-white font-medium">{{ $report->user->display_name ?? 'Unknown' }}</p>
                            <p class="text-gray-500 text-xs">{{ $report->user->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-400 max-w-xs"><p class="line-clamp-2">{{ $report->description }}</p></td>
                        <td class="px-5 py-4 text-gray-400 text-xs">{{ $report->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-4">
                            <span class="{{ $report->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : ($report->status === 'responding' ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400') }} text-xs px-2.5 py-1 rounded-full capitalize">
                                {{ $report->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($report->status === 'pending')
                                <form method="POST" action="{{ route('admin.crisis.respond', $report) }}">
                                    @csrf
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">Respond</button>
                                </form>
                                @endif
                                @if(in_array($report->status, ['pending','responding']))
                                <form method="POST" action="{{ route('admin.crisis.resolve', $report) }}">
                                    @csrf
                                    <button class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">Resolve</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">No crisis reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="px-5 py-4 border-t border-gray-700">{{ $reports->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
