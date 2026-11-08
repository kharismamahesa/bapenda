@extends('layouts.admin')

@section('title', 'Dashboard Survei')
@section('page_title', 'Dashboard Survei Masyarakat')
@section('page_subtitle', 'Ringkasan SKM dan SAK tahun berjalan, serta pengaduan masyarakat.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #visitorMap { min-height: 360px; }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Ringkasan Tahun {{ $year }}</h5>
        <a href="{{ route('admin.survei.index') }}" class="btn btn-green">
            <i class="bi bi-clipboard-data me-1"></i> Detail dan Filter Survei
        </a>
    </div>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-12 col-sm-6 col-xl">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Total Responden</div>
                <div class="stat-value">{{ number_format($totalSurvei) }}</div>
                <div class="text-muted small mt-1">Sejak awal tahun {{ $year }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Nilai IKM (SKM)</div>
                <div class="stat-value">{{ $ikmValue !== null ? number_format($ikmValue, 2) : '-' }} <span class="fs-6 fw-normal text-muted">/ 100</span></div>
                <div class="text-muted small mt-1">
                    @if ($ikmQuality)
                        Mutu {{ $ikmQuality['mutu'] }} — {{ $ikmQuality['kinerja'] }}
                    @else
                        Belum ada jawaban SKM
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Nilai SAK</div>
                <div class="stat-value">{{ $sakValue !== null ? number_format($sakValue, 2) : '-' }} <span class="fs-6 fw-normal text-muted">/ 100</span></div>
                <div class="text-muted small mt-1">
                    @if ($sakQuality)
                        Mutu {{ $sakQuality['mutu'] }} — {{ $sakQuality['kinerja'] }}
                    @else
                        Belum ada jawaban SAK
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Berita & Informasi</div>
                <div class="stat-value">{{ number_format($totalBerita) }}</div>
                <div class="text-muted small mt-1">Total berita terkelola</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Video</div>
                <div class="stat-value">{{ number_format($totalVideo) }}</div>
                <div class="text-muted small mt-1">Total video terkelola</div>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark">Pengaduan per Bulan</h6>
            <span class="badge bg-light text-muted border">Tahun {{ $year }}</span>
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="dashboardComplaintsChart" role="img" aria-label="Jumlah pengaduan per bulan tahun berjalan"></canvas>
        </div>
        <div class="text-muted small mt-2" id="dashboardComplaintsStatus" aria-live="polite"></div>
    </div>

    <section class="mb-4" aria-labelledby="visitor-overview-title">
        <h2 id="visitor-overview-title" class="h5 fw-bold mb-3">Overview Pengunjung Website</h2>
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-4">
                <div class="card card-custom p-4 h-100">
                    <div class="stat-title">Hari Ini</div>
                    <div class="stat-value">{{ number_format($visitorToday) }}</div>
                    <div class="text-muted small mt-1">Pengunjung unik per IP dan browser</div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="card card-custom p-4 h-100">
                    <div class="stat-title">Bulan Ini</div>
                    <div class="stat-value">{{ number_format($visitorMonth) }}</div>
                    <div class="text-muted small mt-1">Akumulasi catatan harian bulan ini</div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="card card-custom p-4 h-100">
                    <div class="stat-title">Tahun Ini</div>
                    <div class="stat-value">{{ number_format($visitorYear) }}</div>
                    <div class="text-muted small mt-1">Akumulasi catatan harian tahun ini</div>
                </div>
            </div>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="p-3 p-md-4 border-bottom">
                <h3 class="h6 fw-bold mb-1">Peta Sebaran Pengunjung</h3>
                <p class="text-muted small mb-0">Lokasi berupa perkiraan. Titik hanya muncul untuk catatan yang memiliki koordinat.</p>
            </div>
            <div id="visitorMap" role="application" aria-label="Peta sebaran lokasi pengunjung website"></div>
            @if ($visitorMapMarkers->isEmpty())
                <div class="p-3 text-muted small" id="visitorMapEmpty">
                    Belum ada koordinat lokasi pada data pengunjung. IP tetap tercatat, tetapi koordinat memerlukan sumber GeoIP lokal.
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const dashboardText = '#64748b';
        const dashboardGrid = 'rgba(148, 163, 184, .18)';
        const dashboardComplaintApi = @json($complaintApiUrl);

        async function loadMonthlyComplaints() {
            const status = document.getElementById('dashboardComplaintsStatus');
            try {
                const response = await fetch(dashboardComplaintApi, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error(`API pengaduan gagal (${response.status}).`);
                const result = await response.json();
                if (!result.success) throw new Error('Data pengaduan tidak tersedia.');

                new Chart(document.getElementById('dashboardComplaintsChart'), {
                    type: 'bar',
                    data: {
                        labels: result.labels,
                        datasets: [{ label: `Pengaduan ${result.year}`, data: result.values, backgroundColor: 'rgba(36, 123, 160, .78)', borderRadius: 5 }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            x: { ticks: { color: dashboardText }, grid: { display: false } },
                            y: { beginAtZero: true, ticks: { precision: 0, color: dashboardText }, grid: { color: dashboardGrid } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
                status.textContent = `Total pengaduan tahun ${result.year}: ${Number(result.total).toLocaleString('id-ID')}.`;
            } catch (error) {
                status.textContent = 'Data pengaduan bulanan tidak dapat dimuat.';
                console.error('Gagal memuat data pengaduan bulanan:', error);
            }
        }

        loadMonthlyComplaints();

        const visitorMapElement = document.getElementById('visitorMap');
        if (visitorMapElement && window.L) {
            const visitorMarkers = @json($visitorMapMarkers);
            const map = L.map(visitorMapElement).setView([0.5, 101.4], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 18
            }).addTo(map);

            const bounds = [];
            visitorMarkers.forEach(marker => {
                const latitude = Number(marker.lat);
                const longitude = Number(marker.lng);
                if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

                const popup = document.createElement('div');
                const place = document.createElement('strong');
                place.textContent = marker.city || 'Lokasi tidak diketahui';
                popup.appendChild(place);
                const region = document.createElement('div');
                region.textContent = [marker.region, marker.country].filter(Boolean).join(', ');
                popup.appendChild(region);
                const count = document.createElement('div');
                count.textContent = `${Number(marker.total).toLocaleString('id-ID')} kunjungan`;
                popup.appendChild(count);

                L.marker([latitude, longitude]).addTo(map).bindPopup(popup);
                bounds.push([latitude, longitude]);
            });

            if (bounds.length > 0) map.fitBounds(bounds, { padding: [24, 24] });
            setTimeout(() => map.invalidateSize(), 0);
        }
    </script>
@endpush
