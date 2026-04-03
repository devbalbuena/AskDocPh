@extends('layouts.admin')
@section('title', 'Application Review')
@section('page-title', 'Application Review')

@section('content')
<div class="max-w-3xl space-y-5">
    <a href="{{ route('admin.doctor-applications.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 w-fit transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Applications
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Applicant: {{ $application->user->display_name ?? 'Unknown' }}</h2>
            <span class="{{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($application->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }} text-sm px-4 py-1 rounded-full capitalize">
                {{ $application->status }}
            </span>
        </div>

        <div class="p-6 space-y-5">
            {{-- Professional Titles --}}
            @if($application->professionalTitles->isNotEmpty())
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Professional Titles</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($application->professionalTitles as $pt)
                    <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">{{ $pt->professionalTitle->name ?? '—' }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Documents --}}
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Submitted Documents</p>
                <div class="space-y-2">
                    @forelse($application->documents as $doc)
                    <div class="flex items-center justify-between bg-gray-50/60 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-gray-900 text-sm">{{ $doc->requirement->name ?? 'Document' }}</p>
                            @if($doc->text_value)<p class="text-gray-500 text-xs mt-0.5">{{ $doc->text_value }}</p>@endif
                        </div>
                        @if($doc->file_path)
                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-green-600 hover:text-green-700 text-xs transition-colors">View File →</a>
                        @endif
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No documents submitted.</p>
                    @endforelse
                </div>
            </div>

            @if($application->admin_notes)
            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                <p class="text-xs text-red-400 uppercase tracking-wider mb-1">Admin Notes</p>
                <p class="text-gray-700 text-sm">{{ $application->admin_notes }}</p>
            </div>
            @endif
        </div>

        {{-- Action buttons --}}
        @if($application->status === 'pending')
        <div class="px-6 pb-6 flex flex-col gap-4">
            <form method="POST" action="{{ route('admin.doctor-applications.approve', $application) }}">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-gray-900 font-medium py-3 rounded-xl transition-colors">✓ Approve Application</button>
            </form>

            <form method="POST" action="{{ route('admin.doctor-applications.reject', $application) }}" class="space-y-3">
                @csrf
                <textarea name="admin_notes" rows="3" placeholder="Reason for rejection (required)..."
                          class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-red-500 resize-none" required></textarea>
                @error('admin_notes')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-gray-900 font-medium py-3 rounded-xl transition-colors">✗ Reject Application</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
