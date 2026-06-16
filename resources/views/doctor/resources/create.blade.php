@extends('layouts.doctor')
@section('title', 'Create Resource')
@section('page-title', 'Create Resource')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Back link --}}
    <a href="{{ route('resources.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-green-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Resources
    </a>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-5">Publish a New Resource</h2>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('doctor.resources.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-medium text-gray-700 mb-1.5">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="Enter resource title..."
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 transition-colors">
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-medium text-gray-700 mb-1.5">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="3" required
                          placeholder="Briefly describe what this resource covers..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 resize-none transition-colors">{{ old('description') }}</textarea>
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-xs font-medium text-gray-700 mb-1.5">
                    Resource Type <span class="text-red-500">*</span>
                </label>
                <select id="type" name="type" required onchange="handleTypeChange(this.value)"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                    <option value="" disabled {{ !old('type') ? 'selected' : '' }}>Select type...</option>
                    <option value="article" {{ old('type') === 'article' ? 'selected' : '' }}>Article</option>
                    <option value="video"   {{ old('type') === 'video'   ? 'selected' : '' }}>Video</option>
                    <option value="pdf"     {{ old('type') === 'pdf'     ? 'selected' : '' }}>PDF Document</option>
                </select>
            </div>

            {{-- Article content textarea (shown for articles) --}}
            <div id="content-wrapper" class="{{ old('type') === 'article' ? '' : 'hidden' }}">
                <label for="content" class="block text-xs font-medium text-gray-700 mb-1.5">
                    Article Content
                </label>
                <textarea id="content" name="content" rows="12"
                          placeholder="Write your full article here..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 resize-y transition-colors">{{ old('content') }}</textarea>
            </div>

            {{-- File upload (shown for video and pdf) --}}
            <div id="file-wrapper" class="{{ in_array(old('type'), ['video','pdf']) ? '' : 'hidden' }}">
                <label for="file" class="block text-xs font-medium text-gray-700 mb-1.5">
                    Upload File
                    <span id="file-hint" class="text-gray-400 font-normal">(PDF or video — max 20 MB)</span>
                </label>
                <input type="file" id="file" name="file"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-colors cursor-pointer">

                {{-- Video URL alternative — only shown for video type --}}
                <div id="video-url-wrapper" class="{{ old('type') === 'video' ? '' : 'hidden' }} mt-4">
                    <div class="flex items-center gap-3 my-3">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Or</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <label for="video_url" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Paste a Video URL <span class="text-gray-400 font-normal">(YouTube, Vimeo, etc.)</span>
                    </label>
                    <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}"
                           placeholder="https://youtube.com/watch?v=..."
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-green-500 transition-colors">
                    <p class="text-xs text-gray-400 mt-1">Leave both fields empty if not applicable.</p>
                </div>
            </div>

            {{-- Buttons: Cancel (left) + Publish (right) --}}
            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('resources.index') }}"
                   class="bg-white border border-gray-300 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-8 py-2.5 rounded-xl transition-colors shadow-sm">
                    Publish Resource
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function handleTypeChange(type) {
    const contentWrapper  = document.getElementById('content-wrapper');
    const fileWrapper     = document.getElementById('file-wrapper');
    const videoUrlWrapper = document.getElementById('video-url-wrapper');
    const fileHint        = document.getElementById('file-hint');
    const fileInput       = document.getElementById('file');

    // Hide all conditional sections first
    contentWrapper.classList.add('hidden');
    fileWrapper.classList.add('hidden');
    videoUrlWrapper.classList.add('hidden');

    if (type === 'article') {
        contentWrapper.classList.remove('hidden');
    } else if (type === 'pdf') {
        fileWrapper.classList.remove('hidden');
        fileHint.textContent = '(PDF only — max 20 MB)';
        fileInput.accept = '.pdf,application/pdf';
    } else if (type === 'video') {
        fileWrapper.classList.remove('hidden');
        videoUrlWrapper.classList.remove('hidden');
        fileHint.textContent = '(MP4, WebM — max 20 MB)';
        fileInput.accept = 'video/*';
    }
}

// Run on page load in case of form validation bounce-back
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    if (typeSelect.value) handleTypeChange(typeSelect.value);
});
</script>
@endpush
