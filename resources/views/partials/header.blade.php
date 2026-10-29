<!-- Mobile Top Navigation Header -->
<div class="lg:hidden flex items-center justify-between mb-6 bg-muted rounded-card p-4">
    <button onclick="toggleSidebar()" aria-label="Open menu"
        class="p-2 rounded-lg hover:bg-gray-200 cursor-pointer transition-all duration-200">
        <i data-lucide="menu" class="w-6 h-6 text-foreground"></i>
    </button>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
            <i data-lucide="building-2" class="w-4 h-4 text-white"></i>
        </div>
        <h1 class="text-foreground text-lg font-bold">Bapenda</h1>
    </div>
    <div class="w-10"></div>
</div>

<!-- Page Title Header (Desktop / Tablet) -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold mb-1">@yield('page_title', 'Dashboard')</h1>
        <p class="text-gray-500 text-sm md:text-base">@yield('page_subtitle', 'Selamat datang di Panel Administrasi Bapenda')</p>
    </div>
    @yield('page_actions')
</div>

