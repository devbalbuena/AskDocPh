@extends($layout)
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Profile Header Card ──────────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Cover Photo Banner --}}
        <div class="relative w-full h-48 bg-green-800 overflow-hidden">
            @if($user->cover_photo)
                <img src="{{ Storage::url($user->cover_photo) }}"
                     alt="Cover Photo"
                     class="w-full h-full object-cover">
            @else
                {{-- Decorative gradient when no cover photo --}}
                <div class="absolute inset-0 bg-gradient-to-br from-green-700 via-green-800 to-emerald-900">
                    <div class="absolute inset-0 opacity-20"
                         style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 30px 30px;"></div>
                </div>
            @endif
        </div>

        {{-- Profile Info Row --}}
        <div class="px-6 pb-6 relative">
            {{-- Profile Photo (overlapping cover) --}}
            <div class="absolute -top-12 left-6">
                <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-green-100">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}"
                             alt="{{ $user->display_name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-3xl font-bold text-green-700">
                            {{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Name / Username / Bio / Role --}}
            <div class="pt-14">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $user->display_name }}</h1>
                        <p class="text-sm text-gray-500">&commat;{{ $user->username }}</p>
                        @if($user->bio)
                        <p class="text-sm text-gray-700 mt-2 leading-relaxed max-w-lg">{{ $user->bio }}</p>
                        @endif
                    </div>
                    {{-- Role badge --}}
                    <span class="flex-shrink-0 text-xs font-semibold px-3 py-1.5 rounded-full capitalize
                        {{ match($user->role) {
                            'doctor' => 'bg-blue-100 text-blue-700',
                            'admin'  => 'bg-green-100 text-green-700',
                            default  => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ $user->role }}
                    </span>
                </div>

                {{-- Meta info row --}}
                <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-gray-500">
                    @if($user->gender)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ ucfirst(str_replace('_', ' ', $user->gender)) }}
                    </span>
                    @endif
                    @if($user->bday)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $user->bday->format('F j, Y') }}
                    </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Joined {{ $user->created_at->format('F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Edit Profile Form ────────────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-8">
        <h2 class="text-base font-semibold text-gray-900 mb-5">Edit Profile</h2>

        @if(session('success'))
        <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name Row --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="fname" class="block text-xs font-medium text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                    <input type="text" id="fname" name="fname" value="{{ old('fname', $user->fname) }}" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                </div>
                <div>
                    <label for="mname" class="block text-xs font-medium text-gray-700 mb-1.5">Middle Name <span class="text-gray-400">(optional)</span></label>
                    <input type="text" id="mname" name="mname" value="{{ old('mname', $user->mname) }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                </div>
                <div>
                    <label for="lname" class="block text-xs font-medium text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" id="lname" name="lname" value="{{ old('lname', $user->lname) }}" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                </div>
            </div>

            {{-- Username --}}
            <div>
                <label for="username" class="block text-xs font-medium text-gray-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
            </div>

            {{-- Bio --}}
            <div>
                <label for="bio" class="block text-xs font-medium text-gray-700 mb-1.5">Bio <span class="text-gray-400">(optional, max 500 characters)</span></label>
                <textarea id="bio" name="bio" rows="3"
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 resize-none transition-colors"
                          placeholder="Tell others a little about you...">{{ old('bio', $user->bio) }}</textarea>
            </div>

            {{-- Gender & Birthday --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="gender" class="block text-xs font-medium text-gray-700 mb-1.5">Gender</label>
                    <select id="gender" name="gender"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                        <option value="">Prefer not to say</option>
                        <option value="male"              {{ old('gender', $user->gender) === 'male'              ? 'selected' : '' }}>Male</option>
                        <option value="female"            {{ old('gender', $user->gender) === 'female'            ? 'selected' : '' }}>Female</option>
                        <option value="other"             {{ old('gender', $user->gender) === 'other'             ? 'selected' : '' }}>Other</option>
                        <option value="prefer_not_to_say" {{ old('gender', $user->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                </div>
                <div>
                    <label for="bday" class="block text-xs font-medium text-gray-700 mb-1.5">Birthday</label>
                    <input type="date" id="bday" name="bday"
                           value="{{ old('bday', $user->bday?->format('Y-m-d')) }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500 transition-colors">
                </div>
            </div>

            {{-- Photo Uploads --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <label for="profile_photo" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Profile Photo <span class="text-gray-400">(JPG, PNG, GIF, WebP — max 5 MB)</span>
                    </label>
                    @if($user->profile_photo)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ Storage::url($user->profile_photo) }}" class="w-12 h-12 rounded-full object-cover border border-gray-200" alt="Current profile photo">
                        <span class="text-xs text-gray-500">Current photo</span>
                    </div>
                    @endif
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-colors cursor-pointer">
                </div>
                <div>
                    <label for="cover_photo" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Cover Photo <span class="text-gray-400">(JPG, PNG — max 10 MB)</span>
                    </label>
                    @if($user->cover_photo)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ Storage::url($user->cover_photo) }}" class="w-20 h-10 rounded-lg object-cover border border-gray-200" alt="Current cover photo">
                        <span class="text-xs text-gray-500">Current cover</span>
                    </div>
                    @endif
                    <input type="file" id="cover_photo" name="cover_photo" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-colors cursor-pointer">
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-2 flex justify-end">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-8 py-2.5 rounded-xl transition-colors shadow-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
