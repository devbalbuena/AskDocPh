<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AskDocPH — Your Health, Our Priority</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-white pt-16">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 w-full bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Left: Logo --}}
                <div class="flex-shrink-0 flex flex-col justify-center">
                    <a href="{{ route('landing') }}" class="flex flex-col">
                        <span class="text-xl font-bold text-green-700 tracking-tight">AskDoc<span class="text-green-500">PH</span></span>
                        <span class="text-xs text-gray-500 -mt-1">Your Health, Our Priority</span>
                    </a>
                </div>

                {{-- Middle: Navigation Links --}}
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-green-600 font-medium transition-colors">Features</a>
                    <a href="#about" class="text-gray-600 hover:text-green-600 font-medium transition-colors">About</a>
                    <a href="#contact" class="text-gray-600 hover:text-green-600 font-medium transition-colors">Contact</a>
                </div>

                {{-- Right: Auth Buttons --}}
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-green-600 border border-green-600 hover:bg-green-50 px-4 py-2 rounded-lg font-medium transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="bg-gradient-to-br from-green-600 to-green-800 py-20 lg:py-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="lg:w-1/2 static lg:absolute top-1/2 lg:-translate-y-1/2 right-0 hidden lg:flex justify-end opacity-20 lg:opacity-100 mb-12 lg:mb-0">
                 {{-- CSS/SVG Illustration --}}
                 <svg class="w-[400px] h-[300px]" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="200" cy="150" r="120" fill="#166534" fill-opacity="0.3"/>
                    <circle cx="200" cy="150" r="90" fill="#15803d" fill-opacity="0.5"/>
                    <path d="M160 170c0-20 15-30 30-30s30 10 30 30" stroke="white" stroke-width="8" stroke-linecap="round"/>
                    <circle cx="160" cy="130" r="10" fill="white"/>
                    <circle cx="220" cy="130" r="10" fill="white"/>
                    <path d="M190 220C170 200 130 170 130 140C130 115 150 95 175 95C185 95 195 100 200 110C205 100 215 95 225 95C250 95 270 115 270 140C270 170 230 200 210 220C205 225 195 225 190 220Z" fill="#ef4444" opacity="0.9"/>
                 </svg>
            </div>

            <div class="lg:w-1/2 relative z-10">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-on-scroll">
                    Your Mental Health Journey Starts Here
                </h1>
                <p class="text-lg sm:text-xl text-green-100 mb-8 max-w-lg leading-relaxed animate-on-scroll" style="animation-delay: 100ms;">
                    Connect with verified doctors, join a supportive community, and take control of your mental wellbeing.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 animate-on-scroll" style="animation-delay: 200ms;">
                    <a href="{{ route('register') }}" class="bg-white text-green-700 hover:bg-gray-50 font-semibold px-8 py-3 rounded-xl shadow-lg transition-all text-center">
                        Get Started Free
                    </a>
                    <a href="#features" class="bg-transparent border-2 border-white text-white hover:bg-white/10 font-semibold px-8 py-3 rounded-xl transition-all text-center">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 animate-on-scroll">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl mb-4">Everything You Need</h2>
                <p class="text-lg text-gray-500">A complete mental health platform built for the Philippines</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Card 1 --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Verified Doctors</h3>
                    <p class="text-gray-500 text-sm">Connect with PRC-licensed mental health professionals</p>
                </div>
                
                {{-- Card 2 --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Book Appointments</h3>
                    <p class="text-gray-500 text-sm">Schedule online or in-person consultations easily</p>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Community Feed</h3>
                    <p class="text-gray-500 text-sm">Share experiences and support others in a safe space</p>
                </div>

                {{-- Card 4 --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Health Resources</h3>
                    <p class="text-gray-500 text-sm">Access articles, videos, and guides from verified doctors</p>
                </div>

                {{-- Card 5 --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Crisis Support</h3>
                    <p class="text-gray-500 text-sm">24/7 crisis reporting with immediate doctor response</p>
                </div>

                {{-- Card 6 --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animate-on-scroll">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Privacy First</h3>
                    <p class="text-gray-500 text-sm">Anonymous posting and secure data handling</p>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS SECTION --}}
    <section class="py-24 bg-green-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-16 animate-on-scroll">How It Works</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                {{-- Decorative Line --}}
                <div class="hidden md:block absolute top-6 left-1/6 right-1/6 h-0.5 bg-green-200 z-0"></div>

                <div class="relative z-10 flex flex-col items-center text-center animate-on-scroll">
                    <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold shadow-lg mb-6 outline outline-4 outline-green-50">
                        1
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Create Account</h3>
                    <p class="text-gray-600 max-w-xs">Sign up as a patient or apply as a doctor</p>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center animate-on-scroll">
                    <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold shadow-lg mb-6 outline outline-4 outline-green-50">
                        2
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Connect</h3>
                    <p class="text-gray-600 max-w-xs">Find verified doctors and book appointments</p>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center animate-on-scroll">
                    <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold shadow-lg mb-6 outline outline-4 outline-green-50">
                        3
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Heal</h3>
                    <p class="text-gray-600 max-w-xs">Get professional support and join our community</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ABOUT SECTION --}}
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-16 items-center">
                <div class="lg:w-1/2 animate-on-scroll">
                    <h2 class="text-4xl font-bold text-green-700 mb-6">About AskDocPH</h2>
                    <p class="text-gray-600 text-lg mb-6 leading-relaxed">
                        AskDocPH is a mental health community platform built for Filipinos. We connect patients with verified mental health professionals in a safe, supportive, and accessible online environment.
                    </p>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Our mission is to break the stigma around mental health and make professional support available to every Filipino.
                    </p>
                </div>

                <div class="lg:w-1/2 w-full space-y-4 animate-on-scroll">
                    <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl shadow-sm">
                        <div class="text-2xl font-bold text-gray-900">50+ Verified Doctors</div>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl shadow-sm">
                        <div class="text-2xl font-bold text-gray-900">1000+ Patients Helped</div>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl shadow-sm">
                        <div class="text-2xl font-bold text-gray-900">24/7 Crisis Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTACT SECTION --}}
    <section id="contact" class="py-24 bg-gray-50 border-t border-gray-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Get In Touch</h2>
                <p class="text-gray-600">Have questions? We are here to help.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 animate-on-scroll">
                @if(session('contact_success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-medium">Thank you! We will get back to you soon.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="name" name="name" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-3">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-3">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-3"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white font-semibold py-3 px-4 rounded-xl hover:bg-green-700 transition-colors">
                        Send Message
                    </button>
                </form>

                <div class="mt-12 text-center text-sm text-gray-500 space-y-2 border-t border-gray-100 pt-8">
                    <p>Email: <a href="mailto:support@askdocph.com" class="text-green-600 hover:underline">support@askdocph.com</a></p>
                    <p>Location: Philippines</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-green-800 text-green-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-center md:text-left">
                    <div class="text-2xl font-bold text-white tracking-tight mb-2">AskDoc<span class="text-green-400">PH</span></div>
                    <div class="text-green-200 text-sm">Your Health, Our Priority</div>
                </div>

                <div class="flex gap-6 text-sm">
                    <a href="#features" class="hover:text-white transition-colors">Features</a>
                    <a href="#about" class="hover:text-white transition-colors">About</a>
                    <a href="#contact" class="hover:text-white transition-colors">Contact</a>
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a>
                </div>

                <div class="text-sm text-green-300 text-center md:text-right">
                    &copy; 2026 AskDocPH.<br>All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-4');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                el.classList.add('opacity-0', 'translate-y-4', 'transition-all', 'duration-500');
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
