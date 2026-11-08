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
                    $currentPageSlug = in_array(request()->route()?->getName(), ['profil.show', 'halaman.show'], true)
                        ? request()->route('slug')
                        : null;
                    $profilActive = $profilPages->contains('slug', $currentPageSlug);
                    $layananActive = $layananPages->contains('slug', $currentPageSlug);
                @endphp

                @if ($profilPages->isNotEmpty())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ $profilActive ? 'active' : '' }}" href="#"
                            role="button" data-bs-toggle="dropdown">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            @foreach ($profilPages as $page)
                                <li>
                                    <a class="dropdown-item {{ $currentPageSlug === $page->slug ? 'active' : '' }}"
                                        href="{{ route('profil.show', $page->slug) }}">{{ $page->judul }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('regulasi.*') ? 'active' : '' }}"
                        href="{{ route('regulasi.index') }}">Regulasi</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('berita.*', 'video.*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown">
                        Informasi
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('berita.*') ? 'active' : '' }}"
                                href="{{ route('berita.index') }}">Berita</a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('video.*') ? 'active' : '' }}"
                                href="{{ route('video.index') }}">Video</a>
                        </li>
                    </ul>
                </li>

                @if ($layananPages->isNotEmpty())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ $layananActive ? 'active' : '' }}" href="#"
                            role="button" data-bs-toggle="dropdown">
                            Layanan Samsat
                        </a>
                        <ul class="dropdown-menu">
                            @foreach ($layananPages as $page)
                                <li>
                                    <a class="dropdown-item {{ $currentPageSlug === $page->slug ? 'active' : '' }}"
                                        href="{{ route('halaman.show', $page->slug) }}">{{ $page->judul }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('survei.*') ? 'active' : '' }}"
                        href="{{ route('survei.index') }}">Survei</a>
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
