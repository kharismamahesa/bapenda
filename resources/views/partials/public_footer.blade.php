<!-- Footer -->
<footer>
    <div class="container">

        <div class="row g-4">

            <div class="col-lg-4">

                <div class="navbar-brand mb-3">
                    <div class="brand-logo">
                        <i class="bi bi-bank2"></i>
                    </div>

                    <div class="brand-text">
                        BAPENDA RIAU
                        <small>PROVINSI RIAU</small>
                    </div>
                </div>

                <p>
                    Badan Pendapatan Daerah Provinsi Riau.
                    Bersama mewujudkan pengelolaan pendapatan daerah
                    yang optimal dan berkelanjutan.
                </p>

            </div>

            <div class="col-6 col-lg-2">
                <h6>Menu Utama</h6>
                <p>
                    <a href="{{ route('beranda') }}">Beranda</a><br>
                    <a href="{{ route('beranda') }}#profil">Profil</a><br>
                    <a href="{{ route('beranda') }}#layanan">Layanan</a><br>
                    <a href="{{ route('berita.index') }}">Berita</a>
                </p>
            </div>

            <div class="col-6 col-lg-3">
                <h6>Layanan</h6>
                <p>
                    <a href="#">Informasi Pajak</a><br>
                    <a href="#">Antrian Online</a><br>
                    <a href="{{ route('regulasi.index') }}">Informasi Regulasi</a><br>
                    <a href="{{ route('pengaduan.index') }}">Pengaduan</a>
                </p>
            </div>

            <div class="col-lg-3">
                <h6>Hubungi Kami</h6>

                <p>
                    <i class="bi bi-geo-alt me-2"></i>
                    Pekanbaru, Provinsi Riau
                </p>

                <p>
                    <i class="bi bi-envelope me-2"></i>
                    Email resmi Bapenda Riau
                </p>

                <p>
                    <i class="bi bi-globe me-2"></i>
                    bapenda.riau.go.id
                </p>

            </div>

        </div>

        <div class="footer-bottom text-center">
            &copy; {{ date('Y') }} Badan Pendapatan Daerah Provinsi Riau.
            Seluruh Hak Cipta Dilindungi.
            <br>
            <span style="font-size:10px;">Foto hero: avisena via Wikimedia Commons (CC BY-SA 3.0)</span>
        </div>

    </div>
</footer>
