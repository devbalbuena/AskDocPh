<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="h-full {{ auth()->check() && auth()->user()->dark_mode ? 'dark' : '' }}">
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
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-['Inter'] antialiased">
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
    <aside class="w-64 bg-green-800 dark:bg-gray-900 flex flex-col flex-shrink-0 border-r dark:border-gray-700">
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
        <div class="border-t border-green-700 dark:border-gray-700 p-4 relative dark:bg-gray-900">
            @if(auth()->user()->role === 'doctor')
            <!-- Status Toggle switch for doctors -->
            <div class="mb-3 flex items-center justify-between bg-green-900 rounded-lg p-2 border border-green-600">
                <span class="text-xs text-green-100 font-semibold" id="doc-status-text">
                    {{ auth()->user()->online_status === 'online' ? 'Available' : 'Do Not Disturb' }}
                </span>
                <button onclick="toggleDocStatus()" id="doc-status-toggle" 
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none transition-colors ease-in-out duration-200 {{ auth()->user()->online_status === 'online' ? 'bg-green-500' : 'bg-red-500' }}">
                    <span class="sr-only">Toggle Status</span>
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ auth()->user()->online_status === 'online' ? 'translate-x-2' : '-translate-x-2' }}" id="doc-status-knob"></span>
                </button>
            </div>
            @endif

            <div class="flex items-center gap-3">
                <div class="relative w-9 h-9 flex-shrink-0">
                    <div class="w-full h-full rounded-full bg-white flex items-center justify-center text-sm font-bold text-green-800">
                        {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}{{ strtoupper(substr(auth()->user()->lname, 0, 1)) }}
                    </div>
                    @if(auth()->user()->role === 'doctor')
                    <!-- Glowing Dot -->
                    <span id="doc-status-dot" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-green-800 {{ auth()->user()->online_status === 'online' ? 'bg-green-400 shadow-[0_0_8px_rgba(74,222,128,0.8)]' : 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]' }}"></span>
                    @endif
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
        <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6 flex-shrink-0">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">@yield('page-title', 'Dashboard')</h2>

            <div class="flex items-center gap-4">
                {{-- Search --}}
                <div class="hidden md:flex items-center bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 gap-2 w-56">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" placeholder="Search AskDocPH..." class="bg-transparent text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none w-full">
                </div>

                {{-- Messages --}}
                @auth
                <a href="{{ url('/messages') }}" class="relative p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span id="msg-badge" class="absolute -top-0.5 -right-0.5 bg-green-600 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                </a>

                {{-- Dark Mode Toggle --}}
                <button id="dark-mode-btn" onclick="toggleDarkMode()"
                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 rounded-lg transition-colors"
                        title="Toggle dark mode">
                    {{-- Moon icon (light mode) --}}
                    <svg id="dark-icon" class="w-5 h-5 {{ auth()->check() && auth()->user()->dark_mode ? 'hidden' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    {{-- Sun icon (dark mode) --}}
                    <svg id="light-icon" class="w-5 h-5 {{ auth()->check() && auth()->user()->dark_mode ? '' : 'hidden' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

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

        {{-- ── Global Announcement Banners ──────────────────── --}}
        @foreach($globalAnnouncements ?? [] as $ann)
        <div id="announcement-{{ $ann->id }}"
             class="w-full px-6 py-3 text-sm font-medium flex items-center justify-between gap-4
             {{ $ann->type === 'urgent' ? 'bg-red-600 text-white' : ($ann->type === 'warning' ? 'bg-yellow-500 text-white' : 'bg-blue-600 text-white') }}">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold">{{ $ann->title }}:</span>
                <span>{{ $ann->message }}</span>
                @if($ann->expires_at)
                <span class="opacity-75 text-xs">· Expires {{ $ann->expires_at->diffForHumans() }}</span>
                @endif
            </div>
            <button onclick="dismissAnnouncement({{ $ann->id }})"
                    class="flex-shrink-0 opacity-75 hover:opacity-100 text-xl font-bold leading-none transition-opacity">&times;</button>
        </div>
        @endforeach

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
        <main class="flex-1 overflow-y-auto p-6 dark:bg-gray-900">
            @yield('content')
        </main>
    </div>
</div>

@auth
{{-- Feature P: AI Chatbot (Only for Authenticated Users) --}}
<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    <!-- Chat Window (hidden initially) -->
    <div id="ai-chat-window" style="display: none;" class="w-80 rounded-2xl shadow-2xl bg-white border border-gray-100 mb-4 flex-col">
        <!-- HEADER -->
        <div class="bg-green-600 rounded-t-2xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <div>
                    <div class="text-white font-bold tracking-wide">AskDoc AI</div>
                    <div class="text-white/80 text-xs">Mental Health Assistant</div>
                </div>
            </div>
            <button onclick="toggleAIChat()" class="text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- MESSAGES AREA -->
        <div id="ai-messages" class="h-72 overflow-y-auto p-4 flex flex-col gap-3">
            <!-- AI Welcome Message -->
            <div class="self-start bg-gray-100 text-gray-800 rounded-2xl rounded-bl-sm px-4 py-2 text-sm shadow-sm">
                Hi! I am AskDoc AI, your mental health assistant. How are you feeling today?
            </div>
        </div>
        
        <!-- INPUT AREA -->
        <div class="border-t border-gray-100 p-3 flex items-center gap-2">
            <input type="text" id="ai-input" placeholder="Type how you are feeling..." autocomplete="off"
                   class="flex-1 bg-gray-50 border border-gray-200 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500" 
                   onkeydown="if(event.key==='Enter') sendAIMessage()">
            <button onclick="sendAIMessage()" class="bg-green-600 text-white rounded-full p-2 hover:bg-green-700 transition">
                <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
    </div>

    <!-- Toggle Button -->
    <button id="ai-chat-btn" onclick="toggleAIChat()" class="relative w-14 h-14 bg-green-600 hover:bg-green-700 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-105">
        <svg class="w-6 h-6 outline-none" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 5.92 2 10.75c0 2.22 1.05 4.25 2.8 5.82L4 21l3.85-2.05A10.66 10.66 0 0012 19.5c5.52 0 10-3.92 10-8.75S17.52 2 12 2zm0 16c-1.37 0-2.67-.25-3.85-.7L5 19l.55-2.5C4.05 15.2 3 13.08 3 10.75 3 6.75 7.03 3.5 12 3.5s9 3.25 9 7.25-4.03 7.25-9 7.25z"></path></svg>
        <span id="ai-chat-dot" class="absolute top-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full animate-pulse"></span>
    </button>
</div>

<script>
    function toggleAIChat() {
        const win = document.getElementById('ai-chat-window');
        const dot = document.getElementById('ai-chat-dot');
        if (win.style.display === 'none') {
            win.style.display = 'flex';
            if(dot) dot.style.display = 'none'; // hide pulse dot once opened
        } else {
            win.style.display = 'none';
        }
    }

    function sendAIMessage() {
        const input = document.getElementById('ai-input');
        const msg = input.value.trim();
        if(!msg) return;
        
        const msgArea = document.getElementById('ai-messages');
        
        // Append user msg
        const userDiv = document.createElement('div');
        userDiv.className = 'self-end bg-green-600 text-white rounded-2xl rounded-br-sm px-4 py-2 text-sm shadow-sm max-w-[85%] break-words';
        userDiv.textContent = msg;
        msgArea.appendChild(userDiv);
        
        input.value = '';
        msgArea.scrollTop = msgArea.scrollHeight;
        
        // Append typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.className = 'self-start bg-gray-100 text-gray-800 rounded-2xl rounded-bl-sm px-4 py-2 text-sm shadow-sm flex items-center gap-1';
        typingDiv.id = 'ai-typing';
        typingDiv.innerHTML = '<span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span><span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span><span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>';
        msgArea.appendChild(typingDiv);
        msgArea.scrollTop = msgArea.scrollHeight;
        
        axios.post('/ai-chat', { message: msg })
            .then(res => {
                setTimeout(() => {
                    const typing = document.getElementById('ai-typing');
                    if(typing) typing.remove();
                    
                    const aiDiv = document.createElement('div');
                    aiDiv.className = 'self-start bg-gray-100 text-gray-800 rounded-2xl rounded-bl-sm px-4 py-2 text-sm shadow-sm max-w-[90%] break-words';
                    aiDiv.innerHTML = res.data.response;
                    
                    if (res.data.show_find_doctor) {
                        aiDiv.innerHTML += `<div class="mt-2"><a href="/patient/doctors" class="text-green-600 hover:text-green-800 text-xs font-semibold">Find a Doctor &rarr;</a></div>`;
                    }
                    if (res.data.show_crisis) {
                        aiDiv.innerHTML += `<div class="mt-2"><a href="/patient/dashboard" class="text-red-500 hover:text-red-700 text-xs font-semibold">Get Help Now &rarr;</a></div>`;
                    }
                    
                    msgArea.appendChild(aiDiv);
                    msgArea.scrollTop = msgArea.scrollHeight;
                }, 1000);
            })
            .catch(err => {
                const typing = document.getElementById('ai-typing');
                if(typing) typing.remove();
            });
    }
</script>
@endauth

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

@if(auth()->check() && auth()->user()->role === 'doctor')
function toggleDocStatus() {
    const toggle = document.getElementById('doc-status-toggle');
    const isOnline = toggle.classList.contains('bg-green-500');
    const nextStatus = isOnline ? 'away' : 'online';
    
    axios.post('/doctor/status/update', { status: nextStatus })
        .then(res => {
            const status = res.data.status;
            const knob = document.getElementById('doc-status-knob');
            const text = document.getElementById('doc-status-text');
            const dot = document.getElementById('doc-status-dot');
            
            if (status === 'online') {
                toggle.classList.remove('bg-red-500');
                toggle.classList.add('bg-green-500');
                knob.classList.remove('-translate-x-2');
                knob.classList.add('translate-x-2');
                text.textContent = 'Available';
                
                dot.classList.remove('bg-red-500', 'shadow-[0_0_8px_rgba(239,68,68,0.8)]');
                dot.classList.add('bg-green-400', 'shadow-[0_0_8px_rgba(74,222,128,0.8)]');
            } else {
                toggle.classList.remove('bg-green-500');
                toggle.classList.add('bg-red-500');
                knob.classList.remove('translate-x-2');
                knob.classList.add('-translate-x-2');
                text.textContent = 'Do Not Disturb';
                
                dot.classList.remove('bg-green-400', 'shadow-[0_0_8px_rgba(74,222,128,0.8)]');
                dot.classList.add('bg-red-500', 'shadow-[0_0_8px_rgba(239,68,68,0.8)]');
            }
        })
        .catch(err => alert('Failed to update availability status'));
}
@endif
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


<script>
// ── Announcement Dismiss ─────────────────────────────────────
function dismissAnnouncement(id) {
    axios.post('/announcements/' + id + '/dismiss')
        .then(() => {
            const el = document.getElementById('announcement-' + id);
            if (el) {
                el.style.transition = 'opacity 0.3s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            }
        })
        .catch(() => {}); // Silent fail — UX shouldn't block on this
}
</script>

<script>
// ── Dark Mode Toggle ─────────────────────────────────────────
function toggleDarkMode() {
    axios.post('/settings/dark-mode')
        .then(res => {
            const isDark = res.data.dark_mode;
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.getElementById('dark-icon')?.classList.add('hidden');
                document.getElementById('light-icon')?.classList.remove('hidden');
            } else {
                document.documentElement.classList.remove('dark');
                document.getElementById('dark-icon')?.classList.remove('hidden');
                document.getElementById('light-icon')?.classList.add('hidden');
            }
        })
        .catch(err => {
            if (err.response?.status === 403) {
                alert(err.response.data.error ?? 'Not available for demo accounts.');
            }
        });
}
</script>

@stack('scripts')

</body>
</html>
