<!-- Sidebar (Bapenda Theme - Green & Dark Slate) -->
<nav id="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 overflow-hidden">
            <div class="brand-icon">
                <img src="{{ asset('images/adminpanel.png') }}" alt="Logo Panel Admin">
            </div>
            <div class="overflow-hidden min-w-0">
                <div class="text-white fw-bold lh-1" style="font-size: 1.05rem;">Panel Admin</div>
                <div class="sidebar-user-identity mt-1">
                    <div class="overflow-hidden min-w-0">
                        <div class="text-truncate" style="color: rgba(255,255,255,0.5); font-size: 0.725rem;">
                            {{ auth()->user()->name ?? (auth()->user()->nama ?? 'Administrator') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm text-white-50 p-1 d-lg-none" onclick="closeSidebar()"
            aria-label="Tutup Menu">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <!-- Navigation Pills -->
    <ul class="nav nav-pills flex-column mb-auto">
        @if (auth()->user()->role === 'uptup')
            <li class="sidebar-section-title">Layanan</li>
            <li class="nav-item">
                <a href="{{ route('admin.pengaduan.index') }}"
                    class="nav-link {{ request()->routeIs('admin.pengaduan.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text"></i>
                    <span>Pengaduan</span>
                </a>
            </li>
        @else
            <li class="sidebar-section-title">Menu Utama</li>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-section-title">Layanan</li>
            <li class="nav-item">
                <a href="{{ route('admin.pengaduan.index') }}"
                    class="nav-link {{ request()->routeIs('admin.pengaduan.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text"></i>
                    <span>Pengaduan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.survei.index') }}"
                    class="nav-link {{ request()->routeIs('admin.survei.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Survei</span>
                </a>
            </li>

            <li class="sidebar-section-title">Publikasi & Konten</li>
            <li class="nav-item">
                @php($informasiActive = request()->routeIs('admin.berita.*', 'admin.video.*', 'admin.kategori-berita.*'))
                <a href="#adminInformasiMenu" data-bs-toggle="collapse" role="button"
                    aria-expanded="{{ $informasiActive ? 'true' : 'false' }}" aria-controls="adminInformasiMenu"
                    class="nav-link {{ $informasiActive ? 'active' : '' }}">
                    <i class="bi bi-newspaper"></i>
                    <span>Informasi</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size: .8rem; margin-right: 0;"></i>
                </a>
                <ul id="adminInformasiMenu" class="nav flex-column collapse {{ $informasiActive ? 'show' : '' }}">
                    <li class="nav-item">
                        <a href="{{ route('admin.berita.index') }}"
                            class="nav-link ps-5 {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                            <span>Berita & Informasi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.video.index') }}"
                            class="nav-link ps-5 {{ request()->routeIs('admin.video.*') ? 'active' : '' }}">
                            <span>Video</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.kategori-berita.index') }}"
                            class="nav-link ps-5 {{ request()->routeIs('admin.kategori-berita.*') ? 'active' : '' }}">
                            <span>Kategori Berita</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.slider.index') }}"
                    class="nav-link {{ request()->routeIs('admin.slider.*') ? 'active' : '' }}">
                    <i class="bi bi-images"></i>
                    <span>Slider Hero</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.halaman-konten.index') }}"
                    class="nav-link {{ request()->routeIs('admin.halaman-konten.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Halaman Konten</span>
                </a>
            </li>
            <li class="nav-item">
                @php($regulasiActive = request()->routeIs('admin.regulasi.*', 'admin.kategori-regulasi.*'))
                <a href="#adminRegulasiMenu" data-bs-toggle="collapse" role="button"
                    aria-expanded="{{ $regulasiActive ? 'true' : 'false' }}" aria-controls="adminRegulasiMenu"
                    class="nav-link {{ $regulasiActive ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Regulasi</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size: .8rem; margin-right: 0;"></i>
                </a>
                <ul id="adminRegulasiMenu" class="nav flex-column collapse {{ $regulasiActive ? 'show' : '' }}">
                    <li class="nav-item">
                        <a href="{{ route('admin.regulasi.index') }}"
                            class="nav-link ps-5 {{ request()->routeIs('admin.regulasi.*') ? 'active' : '' }}">
                            <span>Daftar Regulasi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.kategori-regulasi.index') }}"
                            class="nav-link ps-5 {{ request()->routeIs('admin.kategori-regulasi.*') ? 'active' : '' }}">
                            <span>Kategori Regulasi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-section-title">Pengaturan Sistem</li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Kelola User</span>
                </a>
            </li>
        @endif
    </ul>

    <!-- User Profile Footer -->
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="btn btn-sm btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-1.5 text-white-50 border-secondary"
                style="font-size: 0.8rem; border-radius: 6px;">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar Sistem</span>
            </button>
        </form>
    </div>
</nav>
