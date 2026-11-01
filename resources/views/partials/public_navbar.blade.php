<!-- Public Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="{{ route('beranda') }}">
            <div class="brand-logo">
                <i class="bi bi-bank2"></i>
            </div>
            <div class="brand-text">
                BAPENDA
                <small>PROVINSI RIAU</small>
            </div>
        </a>

        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}" href="{{ route('beranda') }}">Beranda</a>
                </li>

                @php
                    $profilSlugs = ['struktur-organisasi', 'sejarah', 'visi-misi', 'kepala-bapenda', 'sekretaris-bapenda', 'bidang-data', 'bidang-pengawasan'];
                    $currentProfil = in_array(request()->route()?->getName(), ['profil.show', 'halaman.show'], true)
                        ? request()->route('slug')
                        : null;
                @endphp

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ in_array($currentProfil, $profilSlugs, true) ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        Profil
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ $currentProfil === 'struktur-organisasi' ? 'active' : '' }}" href="{{ route('profil.show', 'struktur-organisasi') }}">Struktur Organisasi</a>
                            <a class="dropdown-item {{ $currentProfil === 'sejarah' ? 'active' : '' }}" href="{{ route('profil.show', 'sejarah') }}">Sejarah</a>
                            <a class="dropdown-item {{ $currentProfil === 'visi-misi' ? 'active' : '' }}" href="{{ route('profil.show', 'visi-misi') }}">Visi & Misi</a>
                            <a class="dropdown-item {{ $currentProfil === 'kepala-bapenda' ? 'active' : '' }}" href="{{ route('profil.show', 'kepala-bapenda') }}">Kepala Bapenda</a>
                            <a class="dropdown-item {{ $currentProfil === 'sekretaris-bapenda' ? 'active' : '' }}" href="{{ route('profil.show', 'sekretaris-bapenda') }}">Sekretaris Bapenda</a>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle {{ in_array($currentProfil, ['bidang-data', 'bidang-pengawasan'], true) ? 'active' : '' }}" href="#" role="button">
                                Bidang Kerja
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ $currentProfil === 'bidang-data' ? 'active' : '' }}" href="{{ route('profil.show', 'bidang-data') }}">Bidang Data</a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ $currentProfil === 'bidang-pengawasan' ? 'active' : '' }}" href="{{ route('profil.show', 'bidang-pengawasan') }}">Bidang Pengawasan</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beranda') }}#layanan">Layanan</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beranda') }}#informasi">Informasi</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('berita.*') ? 'active' : '' }}" href="{{ route('berita.index') }}">Berita</a>
                </li>

                <li class="nav-item ms-lg-3">
                    <a href="{{ route('beranda') }}#layanan" class="btn-cta">
                        Layanan Publik
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</nav>
