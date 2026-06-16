@extends($layout)
@section('title', 'Create Community - Communities')
@section('page-title', 'Create Community')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('communities.index') }}" class="p-2 bg-white text-gray-400 hover:text-green-600 rounded-xl shadow-sm border border-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Start a New Community</h2>
    </div>

    <form method="POST" action="{{ route('communities.store') }}" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm">
        @csrf

        {{-- Cover Photo --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Photo (Optional)</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl" id="drop-zone">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 justify-center">
                        <label for="cover_photo" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none text-center">
                            <span>Upload a file</span>
                            <input id="cover_photo" name="cover_photo" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                </div>
            </div>
            <div id="image-preview" class="hidden mt-4 relative rounded-xl overflow-hidden h-40">
                <img src="" class="w-full h-full object-cover">
                <button type="button" onclick="clearImage()" class="absolute top-2 right-2 bg-gray-900/50 hover:bg-gray-900/70 text-white rounded-full p-1 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @error('cover_photo')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Name --}}
        <div class="mb-5">
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Community Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="100"
                   class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl px-4 py-2.5 outline-none transition"
                   placeholder="e.g. Anxiety Support Group">
            @error('name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-5">
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" id="description" rows="3" maxlength="1000"
                      class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl px-4 py-2.5 outline-none transition resize-y"
                      placeholder="What is this community about?">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Guidelines --}}
        <div class="mb-5">
            <label for="guidelines" class="block text-sm font-semibold text-gray-700 mb-1">Community Guidelines</label>
            <textarea name="guidelines" id="guidelines" rows="4" maxlength="2000"
                      class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl px-4 py-2.5 outline-none transition resize-y"
                      placeholder="Set the rules and expectations for members.">{{ old('guidelines') }}</textarea>
            @error('guidelines')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Visibility --}}
        <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Visibility <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm focus:outline-none has-[:checked]:border-green-500 has-[:checked]:ring-1 has-[:checked]:ring-green-500 hover:bg-gray-50 transition">
                    <input type="radio" name="visibility" value="public" class="sr-only" checked>
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Public
                            </span>
                            <span class="mt-1 flex items-center text-xs text-gray-500">Anyone can see and join this community.</span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-green-600 hidden has-[:checked]:block" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </label>
                
                <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm focus:outline-none has-[:checked]:border-green-500 has-[:checked]:ring-1 has-[:checked]:ring-green-500 hover:bg-gray-50 transition">
                    <input type="radio" name="visibility" value="private" class="sr-only">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Private
                            </span>
                            <span class="mt-1 flex items-center text-xs text-gray-500">Membership requires approval.</span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-green-600 hidden has-[:checked]:block" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </label>
            </div>
            @error('visibility')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('communities.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
                Create Community
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const img = preview.querySelector('img');
    const dropZone = document.getElementById('drop-zone');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
            dropZone.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage() {
    const input = document.getElementById('cover_photo');
    const preview = document.getElementById('image-preview');
    const dropZone = document.getElementById('drop-zone');
    
    input.value = '';
    preview.classList.add('hidden');
    dropZone.classList.remove('hidden');
}
</script>
@endpush
@endsection
