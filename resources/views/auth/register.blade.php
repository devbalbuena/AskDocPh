<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join — AskDocPH</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 bg-slate-50 overflow-x-hidden selection:bg-emerald-200 selection:text-emerald-900">

<div class="lg:grid lg:grid-cols-12 min-h-screen">
    
    {{-- LEFT PANEL (40% width / col-span-5) --}}
    <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-emerald-800 via-emerald-700 to-emerald-600 p-12 flex-col justify-between relative overflow-hidden sticky top-0 h-screen self-start">
        {{-- Decorative ambient circular overlays --}}
        <div class="absolute inset-0 z-0 pointer-events-none opacity-20">
            <div class="absolute -top-24 -left-24 w-[500px] h-[500px] rounded-full bg-white blur-3xl"></div>
            <div class="absolute bottom-0 right-0 translate-x-1/3 translate-y-1/3 w-[600px] h-[600px] rounded-full bg-white blur-3xl"></div>
        </div>
        
        <div class="relative z-10" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" x-show="show" x-transition:enter="transition ease-out duration-1000 delay-100" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 mb-16 group">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span class="font-bold text-3xl tracking-tight text-white group-hover:text-emerald-100 transition-colors">AskDoc<span class="text-emerald-200">PH</span></span>
            </a>

            <h1 class="text-4xl xl:text-5xl font-extrabold text-white mb-6 leading-tight">Join AskDocPH</h1>
            <p class="text-xl text-emerald-100 mb-12 max-w-md leading-relaxed">Create your account and start your mental health journey today</p>
            
            <ul class="space-y-6 text-lg font-medium text-white">
                <li class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-4 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    Free to join as a user
                </li>
                <li class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-4 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    Apply as a verified doctor
                </li>
                <li class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-4 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    Safe and private community
                </li>
            </ul>
        </div>
        
        <div class="relative z-10 pb-4 mt-12">
            <p class="text-emerald-100 font-medium">Already have an account? <a href="{{ route('login') }}" class="text-white font-bold underline hover:text-emerald-200 transition-colors">Sign in here</a></p>
        </div>
    </div>

    {{-- RIGHT PANEL (60% width / col-span-7) --}}
    <div class="w-full lg:col-span-7 flex justify-center py-12 px-4 sm:px-6 lg:px-12 xl:px-24">
        <div class="w-full max-w-2xl bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 sm:p-12 lg:p-14">
            
            {{-- Mobile Logo --}}
            <div class="text-center mb-8 lg:hidden">
                <a href="{{ route('landing') }}" class="inline-block">
                    <span class="text-3xl font-bold text-emerald-800 tracking-tight">AskDoc<span class="text-emerald-500">PH</span></span>
                </a>
            </div>

            <div class="text-center lg:text-left mb-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-2">Create Account</h2>
                <p class="text-slate-500 font-medium">Fill in your details to get started.</p>
            </div>

            {{-- Alpine Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-6" x-data="{ role: '{{ old('role', 'patient') }}' }">
                @csrf
                <input type="hidden" name="role" x-model="role">

                {{-- ROLE SELECTOR --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">I am joining as a...</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" @click="role = 'patient'" 
                            :class="role === 'patient' ? 'border-emerald-600 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600' : 'border-slate-200 text-slate-500 hover:border-emerald-300 hover:text-emerald-600 bg-white'"
                            class="flex-1 py-4 px-4 rounded-2xl border-2 font-semibold flex justify-center items-center gap-2 transition-all duration-200 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            User
                        </button>
                        <button type="button" @click="role = 'doctor'" 
                            :class="role === 'doctor' ? 'border-emerald-600 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600' : 'border-slate-200 text-slate-500 hover:border-emerald-300 hover:text-emerald-600 bg-white'"
                            class="flex-1 py-4 px-4 rounded-2xl border-2 font-semibold flex justify-center items-center gap-2 transition-all duration-200 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                            Doctor
                        </button>
                    </div>
                    
                    {{-- Doctor Notice --}}
                    <div x-show="role === 'doctor'" x-collapse x-cloak class="mt-4 bg-emerald-50/80 border border-emerald-100 rounded-2xl p-4 text-sm text-emerald-800 font-medium flex gap-3 items-start">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>After registration you will need to submit a doctor application for verification before you can see users.</span>
                    </div>
                </div>

                {{-- Name Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="fname" class="block text-sm font-medium text-slate-700 mb-2">First Name <span class="text-red-500">*</span></label>
                        <input id="fname" type="text" name="fname" value="{{ old('fname') }}" required autofocus
                            class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('fname') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('fname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="lname" class="block text-sm font-medium text-slate-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                        <input id="lname" type="text" name="lname" value="{{ old('lname') }}" required
                            class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('lname') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('lname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="mname" class="block text-sm font-medium text-slate-700 mb-2">Middle Name <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <input id="mname" type="text" name="mname" value="{{ old('mname') }}"
                        class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('mname') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                    @error('mname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Username & Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username <span class="text-red-500">*</span></label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                            class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('username') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Gender & Bday --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-slate-700 mb-2">Gender</label>
                        <select id="gender" name="gender" class="w-full border border-slate-200 bg-slate-50 text-slate-800 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none @error('gender') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="bday" class="block text-sm font-medium text-slate-700 mb-2">Birthday</label>
                        <input id="bday" type="date" name="bday" value="{{ old('bday') }}"
                            class="w-full border border-slate-200 bg-slate-50 text-slate-800 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('bday') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        @error('bday') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Passwords --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{ showPass: false, showConf: false }">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input id="password" :type="showPass ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-emerald-600 focus:outline-none transition-colors">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input id="password_confirmation" :type="showConf ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                                class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('password_confirmation') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                            <button type="button" @click="showConf = !showConf" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-emerald-600 focus:outline-none transition-colors">
                                <svg x-show="!showConf" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showConf" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Terms Checkbox --}}
                <div class="flex items-start mt-8">
                    <div class="flex items-center h-5 mt-0.5">
                        <input id="terms" name="terms" type="checkbox" required 
                            class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 transition-colors">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-slate-700">I agree to the <a href="#" class="text-emerald-600 hover:text-emerald-700 hover:underline">Terms of Service</a> and <a href="#" class="text-emerald-600 hover:text-emerald-700 hover:underline">Privacy Policy</a> <span class="text-red-500">*</span></label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-6">
                    <button type="submit" 
                        class="w-full flex justify-center items-center py-4 px-4 rounded-xl shadow-md text-base font-bold text-white bg-emerald-600 hover:bg-emerald-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                        Create Account
                    </button>
                </div>

                {{-- Mobile Login Link --}}
                <div class="pt-6 text-center lg:hidden">
                    <p class="text-slate-600 font-medium">Already have an account? <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Sign in here</a></p>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
