@extends('layouts.patient')
@section('title', 'Identity Verification')
@section('page-title', 'Verify Identity')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-green-700 px-6 py-8 text-white text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-green-100 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
            </svg>
            <h2 class="text-2xl font-bold mb-2">Identity Verification</h2>
            <p class="text-green-100">Help us keep AskDocPH safe by verifying your identity.</p>
        </div>

        <div class="p-6 sm:p-8">
            @if(auth()->user()->id_verification_status === 'approved')
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 p-4 rounded-xl text-green-800">
                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-bold">Your ID is Verified</p>
                        <p class="text-sm text-green-700 mt-0.5">Thank you for verifying your identity. You have full access to all patient features.</p>
                    </div>
                </div>
            @elseif(auth()->user()->id_verification_status === 'pending')
                <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-yellow-800">
                    <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-bold">Verification Pending</p>
                        <p class="text-sm text-yellow-700 mt-0.5">Your document is currently under review by our administrators. This usually takes 1-2 business days.</p>
                    </div>
                </div>
            @else
                @if(auth()->user()->id_verification_status === 'rejected')
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 p-4 rounded-xl text-red-800 mb-6">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-bold">Verification Rejected</p>
                        <p class="text-sm text-red-700 mt-0.5">Your previously submitted ID was not accepted. Please upload a clear photo of a valid government ID.</p>
                    </div>
                </div>
                @endif

                <form action="{{ route('patient.id-verification.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Government ID</label>
                        <p class="text-xs text-gray-500 mb-4">Please upload a clear image or PDF of your Driver's License, Passport, or National ID. Maximum size: 5MB.</p>
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-green-500 transition-colors bg-gray-50">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="id_document" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500 px-1">
                                        <span>Upload a file</span>
                                        <input id="id_document" name="id_document" type="file" class="sr-only" required accept=".png,.jpg,.jpeg,.pdf">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500" id="file-name-display">PNG, JPG, PDF up to 5MB</p>
                            </div>
                        </div>
                        @error('id_document')
                            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            Submit for Verification
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    const fileInput = document.getElementById('id_document');
    const fileNameDisplay = document.getElementById('file-name-display');

    if(fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = 'Selected: ' + this.files[0].name;
                fileNameDisplay.classList.add('text-green-600', 'font-medium');
            } else {
                fileNameDisplay.textContent = 'PNG, JPG, PDF up to 5MB';
                fileNameDisplay.classList.remove('text-green-600', 'font-medium');
            }
        });
    }
</script>
@endpush
@endsection
