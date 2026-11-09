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
        {{ $slot }}
    </nav>

    <!-- User Profile -->
    <x-sidebar.profile />
</aside>
