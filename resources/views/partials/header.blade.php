<!-- Top Navbar (App Bar) -->
<header class="top-navbar">
    <div class="d-flex align-items-center gap-2">
        <button type="button" onclick="toggleSidebar()" class="btn btn-outline-custom d-lg-none px-2.5 py-1.5" aria-label="Buka Menu Navigasi">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="d-none d-sm-flex align-items-center gap-2 text-muted small">
            <span class="badge bg-success-subtle text-success border border-success-subtle fw-medium px-2 py-1">
                <i class="bi bi-shield-check"></i> Bapenda Riau
            </span>
            <i class="bi bi-chevron-right text-muted" style="font-size: 0.65rem;"></i>
            <span class="text-dark fw-medium">@yield('title', 'Dashboard')</span>
        </div>
        <span class="fw-bold text-dark d-sm-none fs-6">Bapenda Riau</span>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-muted border px-2.5 py-1.5 fw-normal d-none d-md-inline-flex align-items-center gap-1.5">
            <i class="bi bi-calendar3 text-success"></i>
            <span>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
        </span>
        <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-custom d-none d-sm-inline-flex align-items-center gap-1.5" title="Lihat Halaman Depan">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Portal Utama</span>
        </a>
    </div>
</header>

<!-- Page Header (Area Judul Halaman & Tombol Aksi) -->
<div class="page-header mb-4">
    <div class="row align-items-md-center g-3">
        <div class="col-12 col-md">
            <h1 class="page-title mb-1">@yield('page_title', 'Dashboard')</h1>
            <p class="page-subtitle mb-0">@yield('page_subtitle', 'Sistem Manajemen Badan Pendapatan Daerah Provinsi Riau')</p>
        </div>
        @hasSection('page_actions')
        <div class="col-12 col-md-auto">
            <div class="page-actions">
                @yield('page_actions')
            </div>
        </div>
        @endif
    </div>
</div>
