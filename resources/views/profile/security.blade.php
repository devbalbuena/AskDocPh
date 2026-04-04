@extends($layout)
@section('title', 'Security Settings')
@section('page-title', 'Security Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-12">

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('profile.edit') }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                General Profile
            </a>
            <a href="{{ route('profile.security') }}" 
               class="border-green-500 text-green-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Security Settings
            </a>
        </nav>
    </div>

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

    {{-- Update Password --}}
    @unless(auth()->user()->isDemo())
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Change Password</h2>
            <p class="text-sm text-gray-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        <form method="post" action="{{ route('password.update') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                @error('password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                    Save Password
                </button>
                @if (session('status') === 'password-updated')
                    <p class="text-sm text-gray-600">Saved.</p>
                @endif
            </div>
        </form>
    </div>

    {{-- Two-Factor Authentication --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Two-Factor Authentication</h2>
                <p class="text-sm text-gray-500 max-w-xl">Add an extra layer of security to your account. When enabled, you'll be required to enter a 6-digit code during login.</p>
            </div>
            <div>
                <span id="tfa-status-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ auth()->user()->two_factor_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
        </div>
        
        <div class="mt-6 flex items-center justify-between py-4 border-t border-gray-100">
            <div>
                <p class="text-sm font-medium text-gray-700">Code Verification</p>
                <p class="text-xs text-gray-500 mt-0.5">Use email/SMS to receive your authentication codes.</p>
            </div>
            <button onclick="toggle2FA(this)" type="button" 
                    class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 {{ auth()->user()->two_factor_enabled ? 'bg-green-600' : 'bg-gray-200' }}" role="switch" aria-checked="{{ auth()->user()->two_factor_enabled ? 'true' : 'false' }}">
                <span class="sr-only">Toggle 2FA</span>
                <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ auth()->user()->two_factor_enabled ? 'translate-x-5' : 'translate-x-0' }}" id="tfa-toggle-dot"></span>
            </button>
        </div>
    </div>

    {{-- Delete Account --}}
    <div class="bg-white border border-red-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Delete Account</h2>
        <p class="text-sm text-gray-500 mb-6">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
        
        <button onclick="document.getElementById('delete-modal').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors shadow-sm">
            Delete Account
        </button>
    </div>

    {{-- Delete Account Modal --}}
    <div id="delete-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-sm p-6 max-w-md w-full">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Are you sure you want to delete your account?</h3>
            <p class="text-sm text-gray-500 mb-6">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>
            
            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')
                
                <div>
                    <label for="delete_password" class="sr-only">Password</label>
                    <input type="password" id="delete_password" name="password" required placeholder="Password"
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-red-500 transition-colors">
                    @error('password', 'userDeletion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-6 py-5">
        <strong class="font-bold">Notice:</strong> Demo accounts are locked and cannot change their password, enable 2FA, or delete their account. This is to ensure the demo environment remains accessible.
    </div>
    @endunless

</div>

@push('scripts')
<script>
    function toggle2FA(btn) {
        btn.disabled = true;
        axios.post('{{ route("profile.toggle-2fa") }}')
            .then(res => {
                const enabled = res.data.enabled;
                btn.setAttribute('aria-checked', enabled.toString());
                
                // Update button color
                if (enabled) {
                    btn.classList.remove('bg-gray-200');
                    btn.classList.add('bg-green-600');
                    document.getElementById('tfa-toggle-dot').classList.remove('translate-x-0');
                    document.getElementById('tfa-toggle-dot').classList.add('translate-x-5');
                } else {
                    btn.classList.remove('bg-green-600');
                    btn.classList.add('bg-gray-200');
                    document.getElementById('tfa-toggle-dot').classList.remove('translate-x-5');
                    document.getElementById('tfa-toggle-dot').classList.add('translate-x-0');
                }

                // Update badge
                const badge = document.getElementById('tfa-status-badge');
                badge.textContent = enabled ? 'Enabled' : 'Disabled';
                if (enabled) {
                    badge.classList.remove('bg-gray-100', 'text-gray-800');
                    badge.classList.add('bg-green-100', 'text-green-800');
                } else {
                    badge.classList.remove('bg-green-100', 'text-green-800');
                    badge.classList.add('bg-gray-100', 'text-gray-800');
                }
            })
            .catch(err => {
                alert(err.response?.data?.error || 'Failed to toggle 2FA.');
            })
            .finally(() => {
                btn.disabled = false;
            });
    }

    // Auto open modal on validation error
    @if($errors->userDeletion->isNotEmpty())
        document.getElementById('delete-modal').classList.remove('hidden');
    @endif
</script>
@endpush
@endsection
