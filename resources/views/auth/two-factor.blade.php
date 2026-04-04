<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Two-Factor Authentication — AskDocPH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-white text-gray-900">

<div class="min-h-screen flex">
    
    {{-- LEFT COLUMN (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-600 to-green-900 p-12 flex-col justify-between relative overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-10 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full bg-white"></div>
        </div>
        
        <div class="relative z-10 mt-12 text-white">
            <h1 class="text-4xl lg:text-5xl font-bold mb-4">Security Check</h1>
            <p class="text-xl text-green-100 mb-12 max-w-md">Enter your two-factor authentication code to verify your identity.</p>
        </div>
        
        <div class="relative z-10 pb-4">
            <p class="text-green-100 font-medium">Need help? <a href="{{ route('contact') }}" class="text-white underline hover:text-green-50 transition-colors">Contact Support</a></p>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="text-center mb-10">
                <a href="{{ route('landing') }}" class="inline-block">
                    <span class="text-3xl font-bold text-green-700 tracking-tight">AskDoc<span class="text-green-500">PH</span></span>
                </a>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Two-Factor Authentication</h2>

            <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 text-sm mb-6 text-center">
                <p>A 6-digit authentication code has been sent to your registered email address.</p>
                <p class="mt-1 text-xs text-blue-600">Please check your inbox (and spam folder).</p>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-5 mb-8">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-1.5">Authentication Code</label>
                    <input type="text" id="code" name="code" required autofocus autocomplete="one-time-code" maxlength="6"
                           class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-lg tracking-widest text-center focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors placeholder-gray-400"
                           placeholder="••••••">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Verify Code
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors underline">
                    Back to login
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
