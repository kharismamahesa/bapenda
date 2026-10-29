<!-- SIDEBAR / NAVBAR -->
<aside id="sidebar"
    class="w-64 bg-muted fixed inset-y-0 left-0 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <!-- Logo Section -->
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-full flex items-center justify-center">
                    <i data-lucide="building-2" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h1 class="text-foreground text-lg font-bold">BAPENDA</h1>
                    <p class="text-foreground text-xs font-normal">Dashboard Panel</p>
                </div>
            </div>
            <button onclick="toggleSidebar()" aria-label="Close sidebar"
                class="lg:hidden p-2 rounded-lg hover:bg-gray-200 cursor-pointer transition-all duration-200">
                <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-6 py-4 space-y-6 overflow-y-auto">
        <!-- Main Section -->
        <div>
            <h3 class="text-foreground text-xs font-semibold uppercase tracking-wider mb-3">Main</h3>
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="group cursor-pointer block">
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-card {{ request()->routeIs('admin.dashboard') ? 'bg-[#276874] text-white' : 'hover:bg-gray-100' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-600 group-hover:text-primary' }}"></i>
                        <span class="{{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-foreground group-hover:text-primary' }}">Dashboard</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Publikasi & Berita Section -->
        <div>
            <h3 class="text-foreground text-xs font-semibold uppercase tracking-wider mb-3">Publikasi</h3>
            <div class="space-y-1">
                <a href="{{ route('admin.berita.index') }}" class="group cursor-pointer block">
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-card {{ request()->routeIs('admin.berita.*') ? 'bg-[#276874] text-white' : 'hover:bg-gray-100' }}">
                        <i data-lucide="newspaper" class="w-5 h-5 {{ request()->routeIs('admin.berita.*') ? 'text-white' : 'text-gray-600 group-hover:text-primary' }}"></i>
                        <span class="{{ request()->routeIs('admin.berita.*') ? 'text-white' : 'text-foreground group-hover:text-primary' }}">Berita</span>
                    </div>
                </a>
                <a href="{{ route('admin.kategori-berita.index') }}" class="group cursor-pointer block">
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-card {{ request()->routeIs('admin.kategori-berita.*') ? 'bg-[#276874] text-white' : 'hover:bg-gray-100' }}">
                        <i data-lucide="tags" class="w-5 h-5 {{ request()->routeIs('admin.kategori-berita.*') ? 'text-white' : 'text-gray-600 group-hover:text-primary' }}"></i>
                        <span class="{{ request()->routeIs('admin.kategori-berita.*') ? 'text-white' : 'text-foreground group-hover:text-primary' }}">Kategori Berita</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Keuangan & Pajak Section -->
        <div>
            <h3 class="text-foreground text-xs font-semibold uppercase tracking-wider mb-3">Keuangan & Pajak</h3>
            <div class="space-y-1">
                <a href="#" class="group cursor-pointer block">
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-card hover:bg-gray-100">
                        <i data-lucide="users" class="w-5 h-5 text-gray-600 group-hover:text-primary"></i>
                        <span class="text-foreground group-hover:text-primary">Wajib Pajak</span>
                    </div>
                </a>
                <a href="#" class="group cursor-pointer block">
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-card hover:bg-gray-100">
                        <i data-lucide="receipt" class="w-5 h-5 text-gray-600 group-hover:text-primary"></i>
                        <span class="text-foreground group-hover:text-primary">Retribusi & Pajak</span>
                    </div>
                </a>
                <a href="#" class="group cursor-pointer block">
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-card hover:bg-gray-100">
                        <i data-lucide="wallet" class="w-5 h-5 text-gray-600 group-hover:text-primary"></i>
                        <span class="text-foreground group-hover:text-primary">Realisasi PAD</span>
                    </div>
                </a>
            </div>
        </div>
    </nav>

    <!-- User Profile at Bottom -->
    <div class="px-6 pb-6 mt-auto">
        <div class="flex items-center justify-between gap-3 p-3 bg-white rounded-card border border-border/50">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-foreground text-sm font-semibold truncate">{{ auth()->user()->nama ?? 'Administrator' }}</p>
                    <p class="text-gray-500 text-xs truncate">{{ auth()->user()->email ?? 'admin@bapenda.go.id' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-gray-500 hover:text-error hover:bg-red-50 rounded-lg transition-colors cursor-pointer">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
