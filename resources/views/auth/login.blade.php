<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { min-height: 100%; }
        body { position: relative; min-height: 100dvh; background-color: #eef7f5; overflow: hidden; }
        .bg-dreamy { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .bg-dreamy-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #eef7f5 0%, #f8faf8 48%, #fff8eb 100%);
        }
        .bg-dreamy-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            -webkit-filter: blur(100px);
        }
        /* Top Right Orb - Blueish */
        .bg-dreamy-orb-1 {
            width: 800px; 
            height: 800px; 
            background: radial-gradient(circle, rgba(45, 166, 153, 0.25) 0%, rgba(45, 166, 153, 0) 70%);
            top: -300px; 
            right: -200px; 
            left: auto;
            opacity: 0.8;
        }
        /* Bottom Left Orb - Blueish */
        .bg-dreamy-orb-2 {
            width: 900px; 
            height: 900px; 
            background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0) 70%);
            bottom: -300px; 
            left: -300px; 
            right: auto;
            opacity: 0.8;
        }
        /* Remove 3rd orb */
        .bg-dreamy-orb-3 { display: none; }
    </style>
</head>
<body class="font-sans text-gray-800 antialiased">
    <div class="bg-dreamy" aria-hidden="true">
        <div class="bg-dreamy-gradient"></div>
        <div class="bg-dreamy-orb bg-dreamy-orb-1"></div>
        <div class="bg-dreamy-orb bg-dreamy-orb-2"></div>
        <div class="bg-dreamy-orb bg-dreamy-orb-3"></div>
    </div>
    <div class="min-h-[100dvh] h-[100dvh] flex items-center justify-center px-3 sm:px-6 lg:px-8 relative z-10">

        <!-- Main layout: kiri login (lebih besar), kanan Warehouse (lebih kecil) -->
        <div class="flex w-full max-w-6xl h-[min(94dvh,720px)] rounded-2xl overflow-hidden backdrop-blur-xl bg-white/70 border border-white/70 shadow-[0_18px_60px_rgba(15,118,110,0.14)]">
            <!-- Left panel: login form -->
            <div class="w-full md:w-7/12 min-w-0 flex flex-col justify-between py-12 px-8 sm:px-10 lg:px-14 relative">
                <!-- Top Right: Register Link -->
                <div class="flex justify-end items-center text-sm text-gray-500">
                    <span class="mr-2">Don't have an account?</span>
                    <a href="{{ route('register') }}" class="px-4 py-1.5 border border-gray-300 rounded-full text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-800 transition-colors">Register</a>
                </div>

                <div class="w-full mx-auto" style="max-width: 320px;">
                    <div class="mb-8 text-center">
                        <!-- Mobile Logo (Visible on mobile, hidden on desktop) -->
                        <div class="md:hidden flex justify-center mb-6">
                            <div class="bg-white p-2 rounded-xl shadow-lg inline-block">
                                <img src="{{ asset('images/logo-manajemen-logistic.svg') }}" alt="Manajemen Logistic" class="h-10 w-auto">
                            </div>
                        </div>

                        <!-- Desktop Avatar (Hidden on mobile, visible on desktop) -->
                        <div class="hidden md:flex w-20 h-20 rounded-full items-center justify-center mx-auto mb-4"
                             style="background: radial-gradient(circle at 50% 45%, rgba(116,56,50,0.18) 0%, rgba(116,56,50,0.10) 35%, rgba(116,56,50,0.00) 72%);">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                 style="background-color: #FFFFFF; border: 2px solid rgba(116,56,50,0.22);">
                                <svg class="w-8 h-8" style="color: #9a7b77;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Login to your account</h1>
                        <p class="text-gray-400 mt-2 text-sm font-normal">Enter your details to login.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-1 focus:ring-[#2B8AE2] focus:border-[#2B8AE2] sm:text-sm transition-colors"
                                    placeholder="hello@example.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                    class="block w-full pl-10 pr-14 py-2.5 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-1 focus:ring-[#2B8AE2] focus:border-[#2B8AE2] sm:text-sm transition-colors"
                                    placeholder="••••••••••">
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 cursor-pointer" @click="show = !show">
                                    <svg x-show="!show" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-[#2B8AE2] focus:ring-[#2B8AE2]">
                                <span class="text-sm text-gray-600">Keep me logged in</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-gray-500 underline hover:text-gray-800">Forgot password?</a>
                            @endif
                        </div>

                        <div class="pt-2 mb-8">
                            <button type="submit" class="w-full flex justify-center items-center py-3 px-4 rounded-full text-sm font-medium text-white shadow-[0_12px_28px_rgba(255,255,255,0.28)] transition hover:shadow-[0_14px_34px_rgba(255,255,255,0.34)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0D1A63] bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] hover:from-[#050B30] hover:to-[#050B30]">
                                Login
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Copyright: bottom-left corner -->
                <div class="absolute bottom-6 left-8 sm:left-10 lg:left-14 flex items-center text-xs text-[#0D1A63]">
                    <span class="w-1.5 h-1.5 rounded-full mb-2"></span>
                    &copy; {{ date('Y') }} Manajemen Logistic.
                </div>
            </div>

            <!-- Right panel: branding Warehouse -->
            <div class="hidden md:flex md:w-5/12 min-w-0 relative items-center justify-center py-10 px-8 sm:px-10 lg:px-14 rounded-r-2xl overflow-hidden" style="background: linear-gradient(160deg, #0f766e 0%, #115e59 58%, #12343b 100%);">
                <!-- Subtle, non-dominant illustration -->
                <div class="absolute inset-0 z-0 flex items-center justify-end pointer-events-none opacity-20">
                    <svg class="w-[70%] h-[60%] max-h-[280px] translate-x-[12%]" viewBox="0 0 400 380" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <defs>
                            <linearGradient id="petal1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.1" />
                                <stop offset="100%" style="stop-color:#ffffff;stop-opacity:0.2" />
                            </linearGradient>
                            <linearGradient id="petal2" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.1" />
                                <stop offset="100%" style="stop-color:#ffffff;stop-opacity:0.15" />
                            </linearGradient>
                            <linearGradient id="petal3" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.15" />
                                <stop offset="100%" style="stop-color:#ffffff;stop-opacity:0.2" />
                            </linearGradient>
                            <linearGradient id="petal4" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.1" />
                                <stop offset="100%" style="stop-color:#ffffff;stop-opacity:0.15" />
                            </linearGradient>
                        </defs>
                        <path d="M320 320 Q200 200 80 320 Q160 240 260 300 Q320 280 320 320Z" fill="url(#petal1)" />
                        <path d="M340 300 Q220 120 100 280 Q180 200 280 260 Q340 260 340 300Z" fill="url(#petal2)" />
                        <path d="M360 260 Q260 80 120 240 Q200 160 300 220 Q360 240 360 260Z" fill="url(#petal3)" />
                        <path d="M380 220 Q300 60 140 200 Q220 120 320 180 Q380 200 380 220Z" fill="url(#petal4)" />
                    </svg>
                </div>

                <!-- Content: same max-width as left, left-aligned to mirror -->
                <div class="relative z-10 w-full max-w-[440px] mx-auto text-left flex flex-col justify-between h-full -translate-x-4 lg:-translate-x-6">
                    <!-- Top-left: icon rectangle + brand text -->
                    <div>
                        <!-- Logo -->
                        <div class="mb-8">
                            <div class="bg-white p-3 rounded-xl shadow-lg inline-block">
                                <img src="{{ asset('images/logo-manajemen-logistic.svg') }}" alt="Manajemen Logistic" class="h-12 w-auto">
                            </div>
                        </div>

                        <h2 class="text-3xl font-bold tracking-tight text-white">Manajemen Logistic</h2>
                        <p class="text-white mt-1.5 text-lg font-normal">Kelola stok, peminjaman, dan pengembalian dengan mudah</p>
                    </div>

                    <!-- Bottom: Get Access (left) and Support (right) -->
                    <div class="mt-10 grid grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-base font-bold text-white mb-2">Get Access</h3>
                            <p class="text-sm text-white font-normal leading-relaxed">
                                Sign up to start<br>managing your<br>inventory
                            </p>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white mb-2">Support</h3>
                            <p class="text-sm text-white font-normal leading-relaxed">
                                Need help? Contact<br>
                                <span class="font-medium text-white/90">support@manajemenlogistic.com</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
