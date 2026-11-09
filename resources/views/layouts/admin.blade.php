<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Warehouse Management') }} - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="bg-dreamy" aria-hidden="true">
        <div class="bg-dreamy-gradient"></div>
        <div class="bg-dreamy-orb bg-dreamy-orb-1"></div>
        <div class="bg-dreamy-orb bg-dreamy-orb-2"></div>
        <div class="bg-dreamy-orb bg-dreamy-orb-3"></div>
    </div>
    <div class="flex h-screen p-2 md:p-4 gap-0 md:gap-6 relative z-10" id="app-container">
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0" onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 glass-sidebar-mobile md:glass rounded-r-[30px] md:rounded-[30px] flex flex-col p-6 transition-transform duration-300 ease-in-out fixed inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 md:static md:inset-auto md:flex shadow-2xl md:shadow-none h-full md:h-[calc(100vh-2rem)] max-h-screen md:max-h-[calc(100vh-2rem)] flex-col">
            <!-- Logo -->
            <div class="flex justify-center mb-10 flex-shrink-0">
                <div class="logo-box">
                    <img
                    src="{{ asset('images/logo-manajemen-logistic.svg') }}"
                    alt="Manajemen Logistic"
                    class="logo-img"
                    >
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-6 overflow-y-auto custom-scrollbar flex-none md:flex-1" style="min-height: 0; overflow-y: auto;">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 text-gray-600 hover:text-gray-900 transition-colors group {{ request()->routeIs('dashboard') || request()->is('dashboard') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="text-sm">Dashboard</span>
                </a>
                
                <a href="{{ route('items.list') }}" class="flex items-center gap-4 text-gray-600 hover:text-gray-900 transition-colors group {{ request()->routeIs('items.*') || request()->is('items*') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="text-sm">Items</span>
                </a>

                @if(Auth::user()?->role === 'admin')
                <a href="{{ route('borrow.create', ['type' => 'consumable']) }}" class="flex items-center gap-4 text-gray-600 hover:text-gray-900 transition-colors group {{ request()->is('borrow/consumable') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    <span class="text-sm">Borrow Consumable</span>
                </a>
                <a href="{{ route('borrow.create', ['type' => 'returnable']) }}" class="flex items-center gap-4 text-gray-600 hover:text-gray-900 transition-colors group {{ request()->is('borrow/returnable') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    <span class="text-sm">Borrow Returnable</span>
                </a>
                @endif

                @if(Auth::user()?->role === 'admin')
                <a href="{{ route('return') }}" class="flex items-center gap-4 text-gray-600 hover:text-gray-900 transition-colors group {{ request()->routeIs('return*') || request()->is('return*') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5M12 19l-7-7 7-7"></path>
                    </svg>
                    <span class="text-sm">Return</span>
                </a>
                @endif

                <a href="{{ route('reports') }}" class="flex items-center gap-4 text-gray-600 hover:text-gray-900 transition-colors group {{ request()->routeIs('reports') || request()->is('reports') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <span class="text-sm">Reports</span>
                </a>
            </nav>

            <!-- User Profile -->
            @if(Auth::check())
            <div class="user-profile-section pt-4 border-t border-gray-200/30 mt-6 flex items-center gap-3 flex-shrink-0 w-full" style="position: relative; z-index: 10; display: flex; visibility: visible;">
                <a href="{{ route('profile.edit') }}" class="w-10 h-10 rounded-full bg-gray-300 flex-shrink-0 overflow-hidden hover:opacity-80 transition-opacity">
                    @php
                        $user = Auth::user();
                        $avatarUrl = $user && $user->avatar_path 
                            ? asset('storage/'.$user->avatar_path) 
                            : 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'Admin User').'&background=random';
                    @endphp
                    <img src="{{ $avatarUrl }}" alt="Admin" class="w-full h-full object-cover">
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('profile.edit') }}" class="block text-sm font-semibold text-gray-900 truncate hover:text-[#2B8AE2] transition-colors">
                        {{ Auth::user()->name ?? 'Admin' }}
                    </a>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? 'Administrator' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0" title="Log Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
            @endif
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden w-full">
            <!-- Header -->
            <header class="flex justify-between items-end mb-8 px-2 pt-2 md:pt-8">
                <div class="flex items-center gap-4">
                    <!-- Hamburger Menu (Mobile Only) -->
                    <button onclick="openSidebar()" class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    @yield('header')
                </div>
                <div class="hidden md:flex items-center gap-2 text-gray-600 font-medium">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>{{ now()->format('M d, Y') }}</span>
                </div>
            </header>

            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto pr-2 flex flex-col no-scrollbar">
                <div class="flex-1">
                @yield('content')
                </div>
                
                <footer class="mt-8 pt-4 pb-6 text-right text-sm text-[#0D1A63] font-medium">
                    &copy; {{ date('Y') }} Manajemen Logistic.
                </footer>
            </div>
        </main>
    </div>
</body>
</html>
