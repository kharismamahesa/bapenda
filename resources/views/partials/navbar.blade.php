<!-- Sidebar (Bapenda Theme - Green & Dark Slate) -->
<nav id="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 overflow-hidden">
            <div class="brand-icon">
                <i class="bi bi-building"></i>
            </div>
            <div class="overflow-hidden">
                <div class="text-white fw-bold lh-1" style="font-size: 1.05rem;">BAPENDA RIAU</div>
                <small style="color: rgba(255,255,255,0.5); font-size: 0.725rem; font-weight: 400;">Dashboard Panel</small>
            </div>
        </div>
        <button type="button" class="btn btn-sm text-white-50 p-1 d-lg-none" onclick="closeSidebar()" aria-label="Tutup Menu">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <!-- Navigation Pills -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="sidebar-section-title">Menu Utama</li>
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="sidebar-section-title">Publikasi & Konten</li>
        <li class="nav-item">
            <a href="{{ route('admin.berita.index') }}" class="nav-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i>
                <span>Berita & Informasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.kategori-berita.index') }}" class="nav-link {{ request()->routeIs('admin.kategori-berita.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>Kategori Berita</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.halaman-konten.index') }}" class="nav-link {{ request()->routeIs('admin.halaman-konten.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Halaman Konten</span>
            </a>
        </li>

        <li class="sidebar-section-title">Pengaturan Sistem</li>
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Kelola User</span>
            </a>
        </li>
    </ul>

    <!-- User Profile Footer -->
    <div class="sidebar-footer">
        <div class="user-profile-badge">
            <div class="user-avatar-circle">
                {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->nama ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden min-w-0 flex-grow-1">
                <div class="text-white fw-semibold text-truncate" style="font-size: 0.85rem;">
                    {{ auth()->user()->name ?? auth()->user()->nama ?? 'Administrator' }}
                </div>
                <div class="text-truncate" style="color: rgba(255,255,255,0.5); font-size: 0.725rem;">
                    {{ auth()->user()->email ?? 'admin@bapenda.go.id' }}
                </div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-1.5 text-white-50 border-secondary" style="font-size: 0.8rem; border-radius: 6px;">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar Sistem</span>
            </button>
        </form>
    </div>
</nav>
