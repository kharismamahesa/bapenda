<!-- Footer -->
<footer>
    <div class="container">

        <div class="row g-4">

            <div class="col-lg-4">

                <div class="navbar-brand mb-3">
                    <div class="brand-logo">
                        <img src="{{ asset('images/icon.png') }}" alt="Logo BAPENDA RIAU" class="brand-logo-img">
                    </div>

                    <div class="brand-text">
                        BAPENDA
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
                    <a href="{{ route('survei.index') }}">Survei Kepuasan</a><br>
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
                    bapenda@riau.go.id
                </p>

                <p>
                    <i class="bi bi-globe me-2"></i>
                    bapenda.riau.go.id
                </p>

            </div>

        </div>

        <div class="row g-3 mt-4" aria-label="Ringkasan survei dan pengunjung website">
            <div class="col-12 col-lg-6">
                <section class="footer-insight-card h-100" id="survey-footer-scores"
                    data-api-url="{{ route('survei.scores.api') }}"
                    data-start-date="{{ now()->startOfYear()->toDateString() }}"
                    data-end-date="{{ today()->toDateString() }}" aria-labelledby="footer-survey-title">
                    <div class="footer-insight-heading">
                        <div class="footer-insight-icon footer-insight-icon-survey">
                            <i class="bi bi-clipboard2-data" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h6 id="footer-survey-title" class="mb-1">Hasil Survei {{ now()->year }}</h6>
                            <small>Periode
                                {{ now()->startOfYear()->format('d/m/Y') }}–{{ today()->format('d/m/Y') }}</small>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <div class="footer-score-tile">
                                <span class="footer-score-label">IKM <span>(SKM)</span></span>
                                <strong aria-live="polite">
                                    <span id="footer-ikm-value">Memuat...</span><small id="footer-ikm-suffix"></small>
                                </strong>
                                <span class="footer-score-quality" id="footer-ikm-quality"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="footer-score-tile">
                                <span class="footer-score-label">Nilai SAK</span>
                                <strong aria-live="polite">
                                    <span id="footer-sak-value">Memuat...</span><small id="footer-sak-suffix"></small>
                                </strong>
                                <span class="footer-score-quality" id="footer-sak-quality"></span>
                            </div>
                        </div>
                    </div>
                    <p class="footer-insight-note mb-0 mt-3" id="footer-survey-respondents"></p>
                </section>
            </div>

            <div class="col-12 col-lg-6">
                <section class="footer-insight-card h-100" aria-labelledby="footer-visitors-title">
                    <div class="footer-insight-heading">
                        <div class="footer-insight-icon footer-insight-icon-visitors">
                            <i class="bi bi-people" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h6 id="footer-visitors-title" class="mb-1">Pengunjung Website</h6>
                            <small>Unik per IP dan browser setiap hari</small>
                        </div>
                    </div>

                    <div class="footer-visitor-stats">
                        <div><span>Hari ini</span><strong>{{ number_format($visitorToday) }}</strong></div>
                        <div><span>Bulan ini</span><strong>{{ number_format($visitorMonth) }}</strong></div>
                        <div><span>Tahun ini</span><strong>{{ number_format($visitorYear) }}</strong></div>
                    </div>

                    <div class="footer-browser-list">
                        <div class="footer-browser-heading">
                            <span>Browser bulan ini</span>
                            <span>Pengunjung</span>
                        </div>
                        @forelse ($visitorBrowserStats as $browserStat)
                            @php
                                [$browserIcon, $browserLabel] = match ($browserStat->browser) {
                                    'Google Chrome' => ['bi-browser-chrome', 'Chrome'],
                                    'Microsoft Edge' => ['bi-browser-edge', 'Edge'],
                                    'Mozilla Firefox' => ['bi-browser-firefox', 'Firefox'],
                                    'Safari' => ['bi-browser-safari', 'Safari'],
                                    'Opera' => ['bi-browser', 'Opera'],
                                    'Internet Explorer' => ['bi-browser', 'Internet Explorer'],
                                    default => ['bi-globe2', 'Lainnya'],
                                };
                            @endphp
                            <div class="footer-browser-row">
                                <span><i class="bi {{ $browserIcon }}"
                                        aria-hidden="true"></i>{{ $browserLabel }}</span>
                                <strong>{{ number_format($browserStat->total) }}</strong>
                            </div>
                        @empty
                            <p class="footer-insight-note mb-0">Belum ada data pengunjung bulan ini.</p>
                        @endforelse
                    </div>
                </section>
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

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const footerScores = document.getElementById('survey-footer-scores');
        if (!footerScores) return;

        const params = new URLSearchParams({
            start_date: footerScores.dataset.startDate,
            end_date: footerScores.dataset.endDate
        });

        try {
            const response = await fetch(`${footerScores.dataset.apiUrl}?${params.toString()}`, {
                headers: {
                    Accept: 'application/json'
                }
            });
            if (!response.ok) throw new Error(`Permintaan nilai survei gagal (${response.status}).`);

            const result = await response.json();
            if (!result.success) throw new Error(result.message || 'Nilai survei tidak dapat dimuat.');

            document.getElementById('footer-ikm-value').textContent = result.skm.ikm_value === null ?
                '-' :
                Number(result.skm.ikm_value).toFixed(2);
            document.getElementById('footer-ikm-suffix').textContent = result.skm.ikm_value === null ? '' :
                ' /100';
            document.getElementById('footer-ikm-quality').textContent = result.skm.quality ?
                `Mutu ${result.skm.quality.grade} — ${result.skm.quality.performance}` :
                'Belum ada data SKM';
            document.getElementById('footer-sak-value').textContent = result.sak.converted_value === null ?
                '-' :
                Number(result.sak.converted_value).toFixed(2);
            document.getElementById('footer-sak-suffix').textContent = result.sak.converted_value === null ?
                '' : ' /100';
            document.getElementById('footer-sak-quality').textContent = result.sak.quality ?
                `Mutu ${result.sak.quality.grade} — ${result.sak.quality.performance}` :
                'Belum ada data SAK';
            document.getElementById('footer-survey-respondents').textContent =
                `Berdasarkan ${Number(result.respondents).toLocaleString('id-ID')} responden. SKM dan SAK dihitung terpisah.`;
        } catch (error) {
            document.getElementById('footer-ikm-value').textContent = 'Tidak tersedia';
            document.getElementById('footer-sak-value').textContent = 'Tidak tersedia';
            document.getElementById('footer-ikm-suffix').textContent = '';
            document.getElementById('footer-sak-suffix').textContent = '';
            document.getElementById('footer-sak-quality').textContent = '';
            document.getElementById('footer-survey-respondents').textContent =
                'Nilai survei belum dapat dimuat.';
            console.error('Gagal memuat ringkasan survei pada footer:', error);
        }
    });
</script>
