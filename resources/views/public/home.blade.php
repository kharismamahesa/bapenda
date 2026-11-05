@extends('layouts.public')

@section('title', 'Beranda')

@section('content')

    <!-- HERO -->
    <section class="hero" id="beranda">

        <!-- HERO CONTENT -->
        <div class="hero-content">
            <div class="inner">

                <div class="hero-label">
                    <span></span>
                    BADAN PENDAPATAN DAERAH PROVINSI RIAU
                </div>

                <h1>
                    Bersama Membangun
                    <span>Riau Lebih Maju</span>
                </h1>

                <p class="hero-description">
                    Mewujudkan tata kelola pendapatan daerah yang optimal,
                    transparan, dan akuntabel untuk mendukung pembangunan
                    serta kesejahteraan masyarakat Provinsi Riau.
                </p>

                <div class="hero-buttons">
                    <a href="#layanan" class="btn-cta">
                        Jelajahi Layanan
                        <i class="bi bi-arrow-right"></i>
                    </a>

                    <a href="#profil" class="btn-outline-light-custom">
                        Tentang Bapenda
                        <i class="bi bi-arrow-up-right ms-2"></i>
                    </a>
                </div>

            </div>
        </div>

        <!-- HERO BOTTOM -->
        <div class="hero-bottom">
            <div class="container">
                <div class="row gy-3">

                    <div class="col-md-4">
                        <div class="hero-bottom-item">
                            <i class="bi bi-geo-alt"></i>
                            Provinsi Riau, Indonesia
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="hero-bottom-item">
                            <i class="bi bi-shield-check"></i>
                            Pelayanan Publik Transparan
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="hero-bottom-item">
                            <i class="bi bi-building-check"></i>
                            Mendukung Pembangunan Daerah
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>


    <!-- LAYANAN -->
    <section class="section services-section" id="layanan">
        <div class="container">

            <div class="text-center mb-5">
                <div class="section-label">LAYANAN PUBLIK</div>

                <h2 class="section-title">
                    Akses Layanan Bapenda
                </h2>

                <p class="section-description">
                    Berbagai layanan informasi dan administrasi pendapatan
                    daerah yang mudah diakses oleh masyarakat.
                </p>
            </div>

            <div class="row g-4">

                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-car-front"></i>
                        </div>

                        <h5>Informasi Pajak Kendaraan</h5>

                        <p>
                            Akses informasi dan pengecekan pajak kendaraan
                            bermotor secara mudah dan praktis.
                        </p>

                        <a href="{{ route('pajak-kendaraan.index') }}" class="service-link">
                            Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>

                        <h5>Antrian Online</h5>

                        <p>
                            Kemudahan mendapatkan layanan Samsat melalui
                            sistem pendaftaran antrian online.
                        </p>

                        <a href="#" class="service-link">
                            Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>

                        <h5>Informasi Regulasi</h5>

                        <p>
                            Akses berbagai peraturan dan kebijakan
                            terkait pendapatan daerah Provinsi Riau.
                        </p>

                        <a href="{{ route('regulasi.index') }}" class="service-link">
                            Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-headset"></i>
                        </div>

                        <h5>Pengaduan Masyarakat</h5>

                        <p>
                            Sampaikan pertanyaan, aspirasi, dan pengaduan
                            terkait pelayanan pendapatan daerah.
                        </p>

                        <a href="{{ route('pengaduan.index') }}" class="service-link">
                            Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- PROFIL -->
    <section class="section" id="profil">
        <div class="container">

            <div class="row align-items-center g-4">

                <div class="col-lg-6">
                    <div class="about-image"></div>
                </div>

                <div class="col-lg-6">
                    <div class="about-content">

                        <div class="section-label">TENTANG KAMI</div>

                        <h2 class="section-title">
                            Mengoptimalkan Pendapatan,
                            Membangun Masa Depan Riau
                        </h2>

                        <p class="section-description">
                            Badan Pendapatan Daerah Provinsi Riau merupakan
                            perangkat daerah yang melaksanakan fungsi
                            penunjang urusan pemerintahan di bidang
                            pendapatan daerah.
                        </p>

                        <div class="about-point">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <h6>Pelayanan Berkualitas</h6>
                                <p>
                                    Memberikan pelayanan yang profesional,
                                    efektif, dan mudah diakses.
                                </p>
                            </div>
                        </div>

                        <div class="about-point">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <h6>Transparansi dan Akuntabilitas</h6>
                                <p>
                                    Mendorong tata kelola pendapatan daerah
                                    yang transparan dan bertanggung jawab.
                                </p>
                            </div>
                        </div>

                        <div class="about-point">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <h6>Transformasi Digital</h6>
                                <p>
                                    Mengembangkan inovasi teknologi untuk
                                    meningkatkan kualitas pelayanan publik.
                                </p>
                            </div>
                        </div>

                        <a href="{{ $profil ? route('halaman.show', $profil->slug) : '#' }}" class="btn-cta mt-4">
                            Selengkapnya Tentang Kami
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- STATISTIK -->
    <section class="stats-section section" id="informasi">
        <div class="container">

            <div class="text-center mb-5">
                <div class="section-label text-warning">
                    PENDAPATAN DAERAH
                </div>

                <h2 class="section-title text-white">
                    Bersama Membangun Provinsi Riau
                </h2>

                <p class="text-white-50 small">
                    Kontribusi pendapatan daerah untuk pembangunan dan
                    pelayanan masyarakat.
                </p>
            </div>

            <div class="row">

                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <h2><i class="bi bi-building"></i></h2>
                        <p>Pelayanan Publik</p>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <h2><i class="bi bi-people"></i></h2>
                        <p>Pelayanan Masyarakat</p>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <h2><i class="bi bi-graph-up-arrow"></i></h2>
                        <p>Optimalisasi Pendapatan</p>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <h2><i class="bi bi-globe-asia-australia"></i></h2>
                        <p>Digitalisasi Layanan</p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- BERITA -->
    <section class="section" id="berita">
        <div class="container">

            <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">

                <div>
                    <div class="section-label">INFORMASI TERKINI</div>

                    <h2 class="section-title mb-0">
                        Berita & Kegiatan
                    </h2>

                    <p class="section-description mt-2 mb-0">
                        Informasi terbaru seputar kegiatan dan kebijakan Bapenda Riau.
                    </p>
                </div>

                <a href="{{ route('berita.index') }}" class="service-link">
                    Lihat Semua Berita
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

            <div class="row g-4">

                @forelse($berita as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="news-card">

                            <div class="news-image">
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" loading="lazy" decoding="async"
                                    onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                            </div>

                            <div class="news-body">
                                <div class="news-date">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $item->kategori ? strtoupper($item->kategori->berita_kategori) : 'INFORMASI BAPENDA' }}
                                    &middot; {{ $item->tanggal_berita ? date('d M Y', strtotime($item->tanggal_berita)) : '' }}
                                </div>

                                <h5>
                                    <a href="{{ route('berita.show', $item->url ?: $item->id_berita) }}">
                                        {{ $item->judul }}
                                    </a>
                                </h5>

                                <p>
                                    {{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 120) }}
                                </p>

                                <a href="{{ route('berita.show', $item->url ?: $item->id_berita) }}" class="service-link">
                                    Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-newspaper fs-1 d-block mb-2"></i>
                            Belum ada berita yang dipublikasikan.
                        </div>
                    </div>
                @endforelse

            </div>
        </div>
    </section>


    <!-- CTA -->
    <section class="cta-section">
        <div class="container">

            <div class="cta-box text-center">

                <h2>
                    Mari Berkontribusi untuk Riau
                </h2>

                <p>
                    Pajak daerah merupakan salah satu kontribusi penting
                    dalam mendukung pembangunan dan kemajuan Provinsi Riau.
                </p>

                <a href="#layanan" class="btn-cta">
                    Akses Layanan Sekarang
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        </div>
    </section>

@endsection
