@extends($layout)
@section('title', 'Send Help Request')
@section('page-title', 'Help Requests')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('help-requests.index') }}" class="p-2 bg-white text-gray-400 hover:text-green-600 rounded-xl shadow-sm border border-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Send Direct Help Request</h2>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex gap-3 text-sm text-blue-800">
        <svg class="w-5 h-5 flex-shrink-0 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="font-semibold mb-1">What is a help request?</p>
            <p class="leading-relaxed opacity-90">A help request is a direct, private thread between you and a specific doctor to ask for advice or guidance outside of a formal appointment. Doctors may accept or decline these requests based on their availability.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('help-requests.store') }}" class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm">
        @csrf

        {{-- Select Doctor --}}
        <div class="mb-5">
            <label for="doctor_id" class="block text-sm font-semibold text-gray-700 mb-1">Request Help From <span class="text-red-500">*</span></label>
            <div class="relative">
                <select name="doctor_id" id="doctor_id" required
                        class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl pl-4 pr-10 py-2.5 outline-none transition appearance-none bg-white">
                    <option value="" disabled {{ !$doctorId ? 'selected' : '' }}>-- Select a Doctor --</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>
                            Dr. {{ $doc->fname }} {{ $doc->lname }}
                            @if($doc->specialty) - {{ $doc->specialty }} @endif
                        </option>
                    @endforeach
                </select>
                <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            @error('doctor_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Suggested Title/Reason --}}
        <div class="mb-5">
            <label for="suggested_title" class="block text-sm font-semibold text-gray-700 mb-1">Topic or Reason <span class="text-red-500">*</span></label>
            <input type="text" name="suggested_title" id="suggested_title" value="{{ old('suggested_title') }}" required maxlength="200"
                   class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl px-4 py-2.5 outline-none transition"
                   placeholder="e.g. Question about my medication dosage">
            <p class="text-xs text-gray-500 mt-1.5">Provide a clear, brief title for your request so the doctor understands what you need help with.</p>
            @error('suggested_title')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('help-requests.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send Request
            </button>
        </div>
    </form>
</div>
@endsection
