<!-- Public Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="{{ route('beranda') }}" aria-label="BAPENDA RIAU">
            <img src="{{ asset('images/icon.png') }}" alt="Logo BAPENDA RIAU" class="brand-logo-img">
            <div class="brand-text">
                <div class="brand-title">
                    BAPENDA
                </div>
                <small class="brand-subtitle brand-badge">PROVINSI RIAU</small>
            </div>
        </a>

        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}"
                        href="{{ route('beranda') }}">Beranda</a>
                </li>

                @php
                    $profilSlugs = [
                        'struktur-organisasi',
                        'sejarah',
                        'visi-misi',
                        'kepala-bapenda',
                        'sekretaris-bapenda',
                        'bidang-data',
                        'bidang-pengawasan',
                    ];
                    $currentProfil = in_array(request()->route()?->getName(), ['profil.show', 'halaman.show'], true)
                        ? request()->route('slug')
                        : null;
                @endphp

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ in_array($currentProfil, $profilSlugs, true) ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        Profil
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ $currentProfil === 'struktur-organisasi' ? 'active' : '' }}"
                                href="{{ route('profil.show', 'struktur-organisasi') }}">Struktur Organisasi</a>
                            <a class="dropdown-item {{ $currentProfil === 'sejarah' ? 'active' : '' }}"
                                href="{{ route('profil.show', 'sejarah') }}">Sejarah</a>
                            <a class="dropdown-item {{ $currentProfil === 'visi-misi' ? 'active' : '' }}"
                                href="{{ route('profil.show', 'visi-misi') }}">Visi & Misi</a>
                        </li>
                        {{-- <li class="dropdown-submenu">
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
                        </li> --}}
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('regulasi.*') ? 'active' : '' }}"
                        href="{{ route('regulasi.index') }}">Regulasi</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beranda') }}#informasi">Informasi</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ in_array($currentProfil, ['maklumat-pelayanan', 'samsat-keliling', 'samsat-drive-thru', 'samsat-tanjak', 'layanan-mpp'], true) ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        Layanan Samsat
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ $currentProfil === 'maklumat-pelayanan' ? 'active' : '' }}"
                                href="{{ route('halaman.show', 'maklumat-pelayanan') }}">Maklumat Pelayanan</a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $currentProfil === 'samsat-keliling' ? 'active' : '' }}"
                                href="{{ route('halaman.show', 'samsat-keliling') }}">Samsat Keliling</a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $currentProfil === 'samsat-drive-thru' ? 'active' : '' }}"
                                href="{{ route('halaman.show', 'samsat-drive-thru') }}">Samsat Drive Thru</a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $currentProfil === 'samsat-tanjak' ? 'active' : '' }}"
                                href="{{ route('halaman.show', 'samsat-tanjak') }}">Samsat Tanjak</a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $currentProfil === 'layanan-mpp' ? 'active' : '' }}"
                                href="{{ route('halaman.show', 'layanan-mpp') }}">Layanan MPP</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('berita.*') ? 'active' : '' }}"
                        href="{{ route('berita.index') }}">Berita</a>
                </li>

                <li class="nav-item ms-lg-3">
                    <a href="{{ route('pajak-kendaraan.index') }}" class="btn-cta">
                        Pajak Kendaraan
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</nav>
