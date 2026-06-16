<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — AskDocPH</title>
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

            <h1 class="text-4xl xl:text-5xl font-extrabold text-white mb-6 leading-tight">Welcome Back</h1>
            <p class="text-xl text-emerald-100 mb-12 max-w-md leading-relaxed">Sign in to continue your mental health journey with AskDocPH</p>
            
            <ul class="space-y-6 text-lg font-medium text-white">
                <li class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-4 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    Connect with verified doctors
                </li>
                <li class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-4 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    Track your appointments
                </li>
                <li class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-4 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    Join our supportive community
                </li>
            </ul>
        </div>
        
        <div class="relative z-10 pb-4 mt-12">
            <p class="text-emerald-100 font-medium">New to AskDocPH? <a href="{{ route('register') }}" class="text-white font-bold underline hover:text-emerald-200 transition-colors">Create an account</a></p>
        </div>
    </div>

    {{-- RIGHT PANEL (60% width / col-span-7) --}}
    <div class="w-full lg:col-span-7 flex justify-center items-center py-12 px-4 sm:px-6 lg:px-12 xl:px-24">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 sm:p-12">
            
            {{-- Logo --}}
            <div class="text-center mb-10">
                <a href="{{ route('landing') }}" class="inline-block">
                    <span class="text-3xl font-bold text-emerald-800 tracking-tight">AskDoc<span class="text-emerald-500">PH</span></span>
                </a>
            </div>

            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-2">Sign In</h2>
                <p class="text-slate-500 font-medium">Welcome back! Please enter your details.</p>
            </div>
            
            {{-- Session Status --}}
            @if(session('status'))
                <div class="mb-6 font-medium text-sm text-emerald-700 bg-emerald-50 p-4 rounded-xl border border-emerald-200 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Email Input --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div x-data="{ showPass: false }">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <input id="password" :type="showPass ? 'text' : 'password'" name="password" required autocomplete="current-password"
                            class="w-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 rounded-xl px-4 py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        
                        {{-- Show/Hide Password Toggle --}}
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-emerald-600 focus:outline-none transition-colors">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mt-6">
                    {{-- Remember me --}}
                    <div class="flex items-center h-5">
                        <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 transition-colors">
                        <label for="remember_me" class="ml-3 block text-sm font-medium text-slate-700">Remember me</label>
                    </div>

                    {{-- Forgot password --}}
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center items-center py-4 px-4 rounded-xl shadow-md text-base font-bold text-white bg-emerald-600 hover:bg-emerald-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                        Sign In
                    </button>
                </div>

                <div class="pt-6 text-center lg:hidden">
                    <p class="text-slate-600 font-medium">New to AskDocPH? <a href="{{ route('register') }}" class="text-emerald-600 font-bold hover:underline">Create an account</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
