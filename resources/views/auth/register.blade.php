<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join — AskDocPH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-gray-900 bg-gray-50 overflow-x-hidden">

<div class="min-h-screen flex">
    
    {{-- LEFT COLUMN (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-2/5 bg-gradient-to-br from-green-600 to-green-800 p-12 flex-col justify-between relative overflow-hidden sticky top-0 h-screen self-start">
        {{-- Decorative circles --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-10 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full bg-white"></div>
        </div>
        
        <div class="relative z-10 mt-12 text-white">
            <h1 class="text-4xl lg:text-5xl font-bold mb-4">Join AskDocPH</h1>
            <p class="text-xl text-green-100 mb-12 max-w-md">Create your account and start your mental health journey today</p>
            
            <ul class="space-y-4 text-lg font-medium">
                <li class="flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Free to join as a user
                </li>
                <li class="flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Apply as a verified doctor
                </li>
                <li class="flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Safe and private community
                </li>
            </ul>
        </div>
        
        <div class="relative z-10 pb-4">
            <p class="text-green-100 font-medium">Already have an account? <a href="{{ route('login') }}" class="text-white underline hover:text-green-50 transition-colors">Sign in here</a></p>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="w-full lg:w-7/12 xl:w-3/5 flex items-center justify-center p-8 sm:p-12 lg:py-16">
        <div class="w-full max-w-xl bg-white rounded-2xl lg:rounded-none lg:shadow-none p-6 sm:p-10 lg:p-0 shadow-sm border border-gray-100 lg:border-none">
            {{-- Logo --}}
            <div class="text-center mb-8">
                <a href="{{ route('landing') }}" class="inline-block">
                    <span class="text-3xl font-bold text-green-700 tracking-tight">AskDoc<span class="text-green-500">PH</span></span>
                </a>
                <p class="text-sm text-gray-400 mt-1">Your Health, Our Priority</p>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Create Account</h2>

            <form method="POST" action="{{ route('register') }}" class="space-y-6" id="registerForm">
                @csrf

                {{-- ROLE SELECTOR --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">I am joining as a...</label>
                    <div class="flex gap-4">
                        <button type="button" onclick="setRole('patient')" id="btn-patient" class="flex-1 py-3 px-4 rounded-xl border-2 border-green-600 bg-green-600 text-white font-semibold flex justify-center items-center gap-2 transition-colors focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            User
                        </button>
                        <button type="button" onclick="setRole('doctor')" id="btn-doctor" class="flex-1 py-3 px-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 font-semibold flex justify-center items-center gap-2 transition-colors focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                            Doctor
                        </button>
                    </div>
                    <input type="hidden" name="role" id="role" value="patient">
                    <div id="doctor-note" class="hidden mt-4 bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-700 font-medium">
                        <svg class="w-5 h-5 inline-block mr-1 text-blue-500 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        After registration you will need to submit a doctor application for verification before you can see users.
                    </div>
                </div>

                {{-- Name Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input id="fname" type="text" name="fname" value="{{ old('fname') }}" required autofocus
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('fname') border-red-500 @enderror">
                        @error('fname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="lname" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input id="lname" type="text" name="lname" value="{{ old('lname') }}" required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('lname') border-red-500 @enderror">
                        @error('lname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="mname" class="block text-sm font-medium text-gray-700 mb-1">Middle Name (Optional)</label>
                    <input id="mname" type="text" name="mname" value="{{ old('mname') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('mname') border-red-500 @enderror">
                    @error('mname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Username & Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('username') border-red-500 @enderror">
                        @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Gender & Bday --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="gender" name="gender" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('gender') border-red-500 @enderror">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="bday" class="block text-sm font-medium text-gray-700 mb-1">Birthday</label>
                        <input id="bday" type="date" name="bday" value="{{ old('bday') }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('bday') border-red-500 @enderror">
                        @error('bday') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Passwords --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('password') border-red-500 @enderror">
                            <button type="button" onclick="toggleInput('password', 'eye-img-1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg id="eye-img-1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('password_confirmation') border-red-500 @enderror">
                            <button type="button" onclick="toggleInput('password_confirmation', 'eye-img-2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg id="eye-img-2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                        @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Terms Checkbox --}}
                <div class="flex items-start mt-6">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">I agree to the <a href="#" class="text-green-600 hover:underline">Terms of Service</a> and <a href="#" class="text-green-600 hover:underline">Privacy Policy</a> <span class="text-red-500">*</span></label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-4">
                    <button type="submit" id="submit-btn" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Create Account
                    </button>
                </div>

                <div class="relative py-4 lg:hidden">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-gray-500">or</span>
                    </div>
                </div>

                <p class="text-center text-sm text-gray-600 mt-2 lg:hidden">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-500 transition-colors">Sign in here</a>
                </p>

            </form>
        </div>
    </div>
</div>

<script>
    function toggleInput(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }

    function setRole(role) {
        document.getElementById('role').value = role;
        const btnPatient = document.getElementById('btn-patient');
        const btnDoctor = document.getElementById('btn-doctor');
        const note = document.getElementById('doctor-note');
        const submitBtn = document.getElementById('submit-btn');

        if (role === 'patient') {
            btnPatient.className = "flex-1 py-3 px-4 rounded-xl border-2 border-green-600 bg-green-600 text-white font-semibold flex justify-center items-center gap-2 transition-colors focus:outline-none";
            btnDoctor.className = "flex-1 py-3 px-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 font-semibold flex justify-center items-center gap-2 transition-colors focus:outline-none";
            note.classList.add('hidden');
            
            submitBtn.className = "w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors";
            
            // Adjust input rings (ignoring generic classes to change specific outline)
            document.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]), select').forEach(el => {
                el.classList.remove('focus:border-blue-500', 'focus:ring-blue-500');
                el.classList.add('focus:border-green-500', 'focus:ring-green-500');
            });
            document.getElementById('terms').classList.remove('text-blue-600', 'focus:ring-blue-500');
            document.getElementById('terms').classList.add('text-green-600', 'focus:ring-green-500');

        } else if (role === 'doctor') {
            btnDoctor.className = "flex-1 py-3 px-4 rounded-xl border-2 border-blue-600 bg-blue-600 text-white font-semibold flex justify-center items-center gap-2 transition-colors focus:outline-none";
            btnPatient.className = "flex-1 py-3 px-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-green-300 hover:text-green-600 font-semibold flex justify-center items-center gap-2 transition-colors focus:outline-none";
            note.classList.remove('hidden');

            submitBtn.className = "w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors";
            
            // Adjust input rings
            document.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]), select').forEach(el => {
                el.classList.remove('focus:border-green-500', 'focus:ring-green-500');
                el.classList.add('focus:border-blue-500', 'focus:ring-blue-500');
            });
            document.getElementById('terms').classList.remove('text-green-600', 'focus:ring-green-500');
            document.getElementById('terms').classList.add('text-blue-600', 'focus:ring-blue-500');
        }
    }

    // Maintain role on validation error
    let oldRole = "{{ old('role', 'patient') }}";
    if(oldRole === 'doctor') {
        setRole('doctor');
    }
</script>

</body>
</html>
