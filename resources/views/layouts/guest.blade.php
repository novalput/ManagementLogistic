<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Manajemen Logistic') }} - @yield('title', 'Authentication')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { position: relative; min-height: 100vh; background-color: #F4F7FE; overflow-x: hidden; }
            .bg-dreamy { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
            .bg-dreamy-gradient {
                position: absolute;
                inset: 0;
                background: #F4F7FE;
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
                background: radial-gradient(circle, rgba(200, 220, 255, 0.8) 0%, rgba(200, 220, 255, 0) 70%);
                top: -300px; 
                right: -200px; 
                left: auto;
                opacity: 0.8;
            }
            /* Bottom Left Orb - Blueish */
            .bg-dreamy-orb-2 { 
                width: 900px; 
                height: 900px; 
                background: radial-gradient(circle, rgba(180, 210, 255, 0.8) 0%, rgba(180, 210, 255, 0) 70%);
                bottom: -300px; 
                left: -300px; 
                right: auto;
                opacity: 0.8;
            }
            /* Remove 3rd orb */
            .bg-dreamy-orb-3 { display: none; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="bg-dreamy" aria-hidden="true">
            <div class="bg-dreamy-gradient"></div>
            <div class="bg-dreamy-orb bg-dreamy-orb-1"></div>
            <div class="bg-dreamy-orb bg-dreamy-orb-2"></div>
            <div class="bg-dreamy-orb bg-dreamy-orb-3"></div>
        </div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            <div>
                <a href="/" class="flex flex-col items-center">
                    <div class="w-80 h-20 mb-2 flex items-center justify-center">
                            <img src="{{ asset('images/logo-manajemen-logistic.svg') }}" 
                                alt="Manajemen Logistic" 
                             class="max-w-full object-contain"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white/60 backdrop-blur-md border border-white/50 shadow-xl overflow-hidden sm:rounded-[24px]">
                {{ $slot }}
            </div>

            <div class="fixed bottom-1 right-4 text-white/90 text-sm font-medium">
                &copy; {{ date('Y') }} Manajemen Logistic, All Rights Reserved.
            </div>
        </div>
    </body>
</html>
