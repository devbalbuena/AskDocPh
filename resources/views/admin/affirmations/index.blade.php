@extends('layouts.admin')
@section('title', 'Daily Affirmations')
@section('page-title', 'Daily Affirmations')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Affirmations list --}}
    <div class="xl:col-span-2 space-y-3">
        @forelse($affirmations as $affirmation)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5" id="aff-{{ $affirmation->id }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <p class="text-white text-sm leading-relaxed italic">"{{ $affirmation->quote }}"</p>
                    @if($affirmation->author)<p class="text-gray-400 text-xs mt-1">— {{ $affirmation->author }}</p>@endif
                    <div class="flex items-center gap-3 mt-2">
                        <span class="{{ $affirmation->is_published ? 'bg-green-500/20 text-green-400' : 'bg-gray-600/40 text-gray-400' }} text-xs px-2 py-0.5 rounded-full">
                            {{ $affirmation->is_published ? 'Published' : 'Draft' }}
                        </span>
                        @if($affirmation->publish_at)
                        <span class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($affirmation->publish_at)->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.affirmations.destroy', $affirmation) }}" onsubmit="return confirm('Delete this affirmation?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors flex-shrink-0">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-10 text-center">
            <p class="text-gray-400 text-sm">No affirmations yet. Add the first one!</p>
        </div>
        @endforelse
        @if($affirmations->hasPages())
        <div>{{ $affirmations->links() }}</div>
        @endif
    </div>

    {{-- Add New Form --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 h-fit">
        <h3 class="text-base font-semibold text-white mb-4">Add New Affirmation</h3>
        <form method="POST" action="{{ route('admin.affirmations.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Quote *</label>
                <textarea name="quote" rows="4" placeholder="Enter the affirmation text..." required
                          class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 resize-none">{{ old('quote') }}</textarea>
                @error('quote')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Author</label>
                <input type="text" name="author" value="{{ old('author') }}" placeholder="Optional..."
                       class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-purple-500">
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Publish Date</label>
                <input type="datetime-local" name="publish_at" value="{{ old('publish_at', now()->format('Y-m-d\TH:i')) }}"
                       class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="w-4 h-4 accent-purple-500">
                <span class="text-sm text-gray-300">Publish immediately</span>
            </label>
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 rounded-xl text-sm transition-colors">Add Affirmation</button>
        </form>
    </div>
</div>
@endsection
