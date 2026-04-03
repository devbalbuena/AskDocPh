<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <meta name="description" content="AskDocPH — Mental Health Support Platform">
    <title>@yield('title', 'AskDocPH') — Mental Health Support</title>
    <link rel="icon" href="data:,">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('head')
</head>
<body class="h-full bg-gray-50 text-gray-900 font-['Inter'] antialiased">
<div id="page-loader" 
    style="position:fixed;top:0;left:0;
    width:100%;height:100%;
    background:#fff;z-index:9999;
    display:flex;align-items:center;
    justify-content:center;">
    <div style="text-align:center">
        <div style="width:40px;height:40px;
        border:3px solid #e5e7eb;
        border-top:3px solid #16a34a;
        border-radius:50%;
        animation:spin 0.8s linear infinite;
        margin:0 auto;">
        </div>
        <p style="color:#16a34a;
        font-size:14px;margin-top:8px;
        font-family:sans-serif;">
        Loading...</p>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ──────────────────────────────────────── --}}
    <aside class="w-64 bg-green-800 flex flex-col flex-shrink-0">
        {{-- Logo --}}
        <div class="flex items-center px-6 py-5 bg-green-800">
            <a href="{{ url('/') }}">
                {{-- <img src="{{ asset('img/logo.png') }}" alt="AskDocPH Logo"> --}}
                <span class="text-white font-bold text-xl tracking-tight">AskDoc<span class="text-green-400">PH</span></span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @yield('sidebar-links')
        </nav>

        {{-- User info at bottom --}}
        @auth
        <div class="border-t border-green-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-sm font-bold text-green-800 flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}{{ strtoupper(substr(auth()->user()->lname, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->display_name }}</p>
                    <p class="text-xs text-green-200 truncate capitalize">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </div>
        @endauth
    </aside>

    {{-- ── Main Content ─────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Navbar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">
            <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h2>

            <div class="flex items-center gap-4">
                {{-- Search --}}
                <div class="hidden md:flex items-center bg-white border border-gray-200 rounded-lg px-3 py-2 gap-2 w-56">
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" placeholder="Search AskDocPH..." class="bg-transparent text-sm text-gray-900 placeholder-gray-400 focus:outline-none w-full">
                </div>

                {{-- Messages --}}
                @auth
                <a href="{{ url('/messages') }}" class="relative p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span id="msg-badge" class="absolute -top-0.5 -right-0.5 bg-green-600 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                </a>

                {{-- Notification Bell --}}
                <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span id="notif-badge" class="absolute -top-0.5 -right-0.5 bg-green-600 text-gray-900 text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                </a>

                {{-- User Dropdown --}}
                <div class="relative" id="user-dropdown-wrapper">
                    <button onclick="document.getElementById('user-menu').classList.toggle('hidden')" class="flex items-center gap-2 p-1 rounded-lg hover:bg-white transition-colors">
                        <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}{{ strtoupper(substr(auth()->user()->lname, 0, 1)) }}
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-xl py-1 z-50">
                        <p class="px-4 py-2 text-xs text-gray-500 border-b border-gray-200 truncate">{{ auth()->user()->email }}</p>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div id="flash-msg" class="mx-6 mt-4 flex items-center gap-3 bg-green-100/50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- Close dropdown on outside click --}}
@vite(['resources/js/app.js'])

<script>
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('user-dropdown-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('user-menu')?.classList.add('hidden');
    }
});

@auth
document.addEventListener('DOMContentLoaded', function () {
    // Notification badge polling every 30s
    function pollNotifications() {
        axios.get('{{ route("notifications.count") }}')
            .then(res => {
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    if (res.data.count > 0) {
                        badge.textContent = res.data.count > 99 ? '99+' : res.data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            }).catch(() => {});
    }
    // Messages unread badge polling
    function pollMessages() {
        axios.get('{{ route("messages.unread-count") }}')
            .then(res => {
                const navBadge = document.getElementById('msg-badge');
                
                // Try to find the sidebar badge if it exists (we added 'badge' => true in the layouts)
                const sidebarLinks = document.querySelectorAll('.sidebar-nav-link');
                let sidebarBadgeContainer = null;
                sidebarLinks.forEach(link => {
                    if (link.href.includes('/messages') && link.querySelector('.msg-sidebar-badge-container')) {
                        sidebarBadgeContainer = link.querySelector('.msg-sidebar-badge-container');
                    }
                });

                if (res.data.count > 0) {
                    const txt = res.data.count > 99 ? '99+' : res.data.count;
                    if (navBadge) {
                        navBadge.textContent = txt;
                        navBadge.classList.remove('hidden');
                    }
                    if (sidebarBadgeContainer) {
                        sidebarBadgeContainer.innerHTML = `<span class="bg-green-600 text-white text-[10px] font-bold rounded-full px-2 py-0.5">${txt}</span>`;
                    }
                } else {
                    if (navBadge) navBadge.classList.add('hidden');
                    if (sidebarBadgeContainer) sidebarBadgeContainer.innerHTML = '';
                }
            }).catch(() => {});
    }

    pollNotifications();
    pollMessages();
    setInterval(() => {
        pollNotifications();
        pollMessages();
    }, 60000);

    // Auto-hide flash
    const flash = document.getElementById('flash-msg');
    if (flash) setTimeout(() => flash.style.opacity = '0', 3500);
});
@endauth
</script>

<script>
window.addEventListener('load', function() {
    const loader = document.getElementById(
        'page-loader');
    if (loader) {
        loader.style.opacity = '0';
        loader.style.transition = 
            'opacity 0.2s ease';
        setTimeout(function() {
            loader.style.display = 'none';
        }, 200);
    }
});
</script>

@stack('scripts')
</body>
</html>
