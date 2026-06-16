<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AskDocPH') }} - Your Mental Health Journey Starts Here</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 selection:bg-emerald-200 selection:text-emerald-900 overflow-x-hidden">

    <!-- 1. Sticky Glassmorphic Navbar -->
    <nav x-data="{ scrolled: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="{ 'shadow-sm': scrolled }"
         class="fixed top-0 w-full z-50 transition-all duration-300 backdrop-blur-md bg-white/70 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="font-bold text-2xl tracking-tight text-emerald-900">AskDoc<span class="text-emerald-600">PH</span></span>
                </div>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-slate-600 hover:text-emerald-700 font-medium transition">Features</a>
                    <a href="#about" class="text-slate-600 hover:text-emerald-700 font-medium transition">About</a>
                    <a href="#contact" class="text-slate-600 hover:text-emerald-700 font-medium transition">Contact</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-slate-600 hover:text-emerald-700 font-medium transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-600 hover:text-emerald-700 font-medium transition">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-200 transition-all duration-300">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. Animated Hero Section -->
    <section class="relative pt-32 pb-16 lg:pt-48 lg:pb-32 overflow-hidden" 
             x-data="{ show: false }" 
             x-init="setTimeout(() => show = true, 100)">
        
        <!-- Decorative Background Element -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-emerald-50 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-mint-50 opacity-50 blur-3xl" style="background-color: #f0fdf4;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Left: Content -->
                <div class="max-w-2xl" 
                     x-show="show" 
                     x-transition:enter="transition ease-out duration-1000 delay-100" 
                     x-transition:enter-start="opacity-0 translate-y-8" 
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak>
                    <span class="inline-block py-1 px-3 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold tracking-wide mb-6">Available 24/7 Nationwide</span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-800 leading-tight mb-6">
                        Your Mental Health <br class="hidden sm:block"/> 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-emerald-800">Journey Starts Here</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-slate-600 leading-relaxed mb-10">
                        Connect with verified, compassionate mental health professionals in the Philippines. Safe, confidential, and accessible care right when you need it.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-8 py-4 bg-emerald-600 text-white font-semibold rounded-2xl hover:bg-emerald-700 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-200 transition-all duration-300">
                            Book a Consultation
                        </a>
                        <a href="#about" class="inline-flex justify-center items-center px-8 py-4 bg-white text-emerald-700 font-semibold rounded-2xl border border-emerald-200 hover:bg-emerald-50 hover:border-emerald-300 transition-all duration-300">
                            Learn More
                        </a>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex items-center gap-6 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            PRC Licensed
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            100% Confidential
                        </div>
                    </div>
                </div>

                <!-- Right: Visual Concept -->
                <div class="relative"
                     x-show="show" 
                     x-transition:enter="transition ease-out duration-1000 delay-300" 
                     x-transition:enter-start="opacity-0 translate-x-8" 
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-cloak>
                     <div class="aspect-square max-w-md mx-auto relative">
                        <!-- Abstract layered geometry representing connection -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-100 to-white rounded-3xl transform rotate-6 scale-105 shadow-sm border border-white/50"></div>
                        <div class="absolute inset-0 bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex items-center justify-center p-8">
                            <svg class="w-full h-full text-emerald-50" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M42.7,-74.6C56.6,-68.8,70.1,-58.9,80.1,-45.5C90.1,-32,96.6,-16,95.5,-0.6C94.4,14.8,85.6,29.6,74.9,41.9C64.1,54.2,51.3,64,37.3,71.2C23.3,78.3,8.1,82.8,-6.4,85C-20.9,87.2,-34.7,87,-47.9,80.9C-61.1,74.8,-73.7,62.7,-81.9,48.5C-90.1,34.2,-93.8,17.1,-93.2,0.4C-92.6,-16.4,-87.7,-32.8,-78.6,-46C-69.5,-59.1,-56.3,-69.1,-41.9,-74.5C-27.5,-79.8,-13.7,-80.6,0.6,-81.7C15,-82.7,30,-84.1,42.7,-74.6Z" transform="translate(100 100)" />
                            </svg>
                            <!-- Inner decorative shapes -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-32 h-32 rounded-full border-4 border-emerald-100 opacity-80 absolute animate-pulse"></div>
                                <div class="w-24 h-24 rounded-full border-4 border-emerald-200 opacity-60 absolute" style="animation: ping 3s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
                                <svg class="w-16 h-16 text-emerald-600 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. About & Statistics Section -->
    <section id="about" class="py-24 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Left: Typography -->
                <div class="max-w-xl">
                    <h2 class="text-3xl sm:text-4xl font-bold text-slate-800 leading-tight mb-6">
                        Breaking the Stigma, <br/> Empowering Filipinos.
                    </h2>
                    <p class="text-lg text-slate-600 leading-relaxed mb-6">
                        Mental health care should not be a luxury or a secret. We built AskDocPH to provide a safe, stigma-free space where you can seek professional help from the comfort of your home.
                    </p>
                    <p class="text-lg text-slate-600 leading-relaxed mb-8">
                        Our platform connects you with verified psychologists and psychiatrists, offering you a private community to share your journey, track your mood, and receive the care you deserve.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center text-slate-700 font-medium">
                            <svg class="w-6 h-6 text-emerald-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Verified Medical Professionals
                        </li>
                        <li class="flex items-center text-slate-700 font-medium">
                            <svg class="w-6 h-6 text-emerald-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Secure & Private Teleconsultations
                        </li>
                        <li class="flex items-center text-slate-700 font-medium">
                            <svg class="w-6 h-6 text-emerald-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Supportive Anonymous Community Feed
                        </li>
                    </ul>
                </div>

                <!-- Right: Stats Grid -->
                <div class="grid grid-cols-2 gap-6" 
                     x-data="{ count1: 0, count2: 0, count3: 0, count4: 0, init() { 
                         let observer = new IntersectionObserver(entries => {
                             if(entries[0].isIntersecting) {
                                 this.animate(this, 'count1', 50, 1500);
                                 this.animate(this, 'count2', 1000, 2000);
                                 this.animate(this, 'count3', 24, 1000);
                                 this.animate(this, 'count4', 15, 1000);
                                 observer.disconnect();
                             }
                         });
                         observer.observe(this.$el);
                     }, 
                     animate(obj, key, target, duration) {
                         let start = 0;
                         let stepTime = Math.abs(Math.floor(duration / target));
                         let timer = setInterval(() => {
                             start += Math.ceil(target / 100);
                             if (start >= target) {
                                 obj[key] = target;
                                 clearInterval(timer);
                             } else {
                                 obj[key] = start;
                             }
                         }, stepTime);
                     } }">
                    <!-- Stat 1 -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-100 group">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-4xl font-extrabold text-slate-800 mb-2"><span x-text="count1">0</span>+</h3>
                        <p class="text-slate-500 font-medium">Verified Doctors</p>
                    </div>
                    <!-- Stat 2 -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-100 group translate-y-6">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-4xl font-extrabold text-slate-800 mb-2"><span x-text="count2">0</span>+</h3>
                        <p class="text-slate-500 font-medium">Patients Helped</p>
                    </div>
                    <!-- Stat 3 -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-100 group">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-4xl font-extrabold text-slate-800 mb-2"><span x-text="count3">0</span>/7</h3>
                        <p class="text-slate-500 font-medium">Crisis Support</p>
                    </div>
                    <!-- Stat 4 -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-100 group translate-y-6">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-4xl font-extrabold text-slate-800 mb-2"><span x-text="count4">0</span> Mins</h3>
                        <p class="text-slate-500 font-medium">Avg. Response Time</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Trust & Testimonials Section -->
    <section class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-800 mb-4">Stories of Healing</h2>
                <p class="text-lg text-slate-600">Hear from our community members who took the brave step to seek help through AskDocPH.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative">
                    <svg class="w-10 h-10 text-emerald-100 absolute top-6 left-6" fill="currentColor" viewBox="0 0 32 32" aria-hidden="true"><path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"/></svg>
                    <blockquote class="relative z-10 mt-6">
                        <p class="text-slate-700 italic leading-relaxed mb-6">"Finding a psychologist was incredibly daunting. AskDocPH made it so easy and unintimidating. The privacy and the warmth of my doctor truly saved me during my darkest days."</p>
                        <footer class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold">M</div>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Anonymous Patient</div>
                                <div class="text-xs text-slate-500">Quezon City</div>
                            </div>
                        </footer>
                    </blockquote>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative">
                    <svg class="w-10 h-10 text-emerald-100 absolute top-6 left-6" fill="currentColor" viewBox="0 0 32 32" aria-hidden="true"><path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"/></svg>
                    <blockquote class="relative z-10 mt-6">
                        <p class="text-slate-700 italic leading-relaxed mb-6">"The community feed is a lifesaver. Being able to read that others are going through the same anxiety, and having doctors occasionally chime in with professional advice, is incredible."</p>
                        <footer class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold">J</div>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Anonymous Patient</div>
                                <div class="text-xs text-slate-500">Cebu City</div>
                            </div>
                        </footer>
                    </blockquote>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative">
                    <svg class="w-10 h-10 text-emerald-100 absolute top-6 left-6" fill="currentColor" viewBox="0 0 32 32" aria-hidden="true"><path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"/></svg>
                    <blockquote class="relative z-10 mt-6">
                        <p class="text-slate-700 italic leading-relaxed mb-6">"As a practicing psychologist, this platform allows me to reach Filipinos who wouldn't otherwise walk into a clinic due to fear or judgment. It's revolutionizing care in the PH."</p>
                        <footer class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold">D</div>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Verified Professional</div>
                                <div class="text-xs text-slate-500">AskDocPH Partner</div>
                            </div>
                        </footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. "Get In Touch" Contact Form Section -->
    <section id="contact" class="py-24 bg-white border-t border-slate-100">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-800 mb-4">We're Here for You</h2>
                <p class="text-lg text-slate-600">Have questions about our platform or need technical assistance? Send us a message.</p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                @if(session('contact_success'))
                    <div class="mb-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 flex items-center gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Thank you for reaching out! We will get back to you shortly.</span>
                    </div>
                @endif

                <form action="{{ route('contact') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <input type="text" name="name" id="name" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                            placeholder="Juan Dela Cruz">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                            placeholder="juan@example.com">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-slate-700 mb-2">How can we help?</label>
                        <textarea name="message" id="message" rows="4" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 resize-none"
                            placeholder="Your message here..."></textarea>
                    </div>
                    <button type="submit" 
                        class="w-full inline-flex justify-center items-center px-8 py-4 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-200 transition-all duration-300">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Simple Footer -->
    <footer class="bg-slate-50 py-12 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span class="font-bold text-lg text-emerald-900">AskDoc<span class="text-emerald-600">PH</span></span>
            </div>
            <p class="text-slate-500 text-sm text-center md:text-left">
                &copy; {{ date('Y') }} AskDocPH. Providing accessible mental health care to Filipinos.
            </p>
        </div>
    </footer>

</body>
</html>
