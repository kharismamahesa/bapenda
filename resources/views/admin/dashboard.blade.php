@extends('layouts.admin')

@section('title', 'Dashboard Monitoring')
@section('page_title', 'Ringkasan Kinerja & Pendapatan')
@section('page_subtitle', 'Pantau realisasi pendapatan asli daerah, target pajak, retribusi, dan tren bulanan')

@section('page_actions')
<button type="button" class="btn btn-outline-custom">
    <i class="bi bi-download"></i>
    <span>Export Laporan</span>
</button>
<button type="button" onclick="openAddModal()" class="btn btn-green">
    <i class="bi bi-plus-lg"></i>
    <span>Tambah Data</span>
</button>
@endsection

@section('content')
<!-- Stats Cards Grid -->
<div class="row g-3 g-lg-4 mb-4">
    <!-- Stat 1: Realisasi PAD -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Total Realisasi PAD</div>
                    <div class="stat-value">142,5 <span class="fs-6 fw-normal text-muted">Miliar</span></div>
                    <div class="text-success small fw-medium mt-1">
                        <i class="bi bi-arrow-up-short"></i> +8.2% bulan ini
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Capaian Target -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Capaian Target</div>
                    <div class="stat-value">92,4<span class="fs-6 fw-normal text-muted">%</span></div>
                    <div class="text-success small fw-medium mt-1">
                        <i class="bi bi-arrow-up-short"></i> +2.4% vs tahun lalu
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Wajib Pajak Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Wajib Pajak Aktif</div>
                    <div class="stat-value">28.450</div>
                    <div class="text-primary small fw-medium mt-1">
                        <i class="bi bi-person-plus-fill"></i> 1.200 Baru
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Tunggakan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Tunggakan Pajak</div>
                    <div class="stat-value">3.120</div>
                    <div class="text-danger small fw-medium mt-1">
                        <i class="bi bi-exclamation-circle-fill"></i> Perlu penagihan
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Trend Penerimaan -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-dark">Tren Penerimaan (6 Bulan Terakhir)</h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle">PAD 2026</span>
            </div>
            <div style="height: 270px; position: relative;">
                <canvas id="yieldChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Capaian per Objek Pajak -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-dark">Persentase Target per Objek Pajak</h6>
                <span class="badge bg-light text-muted border">Akumulasi Tahunan</span>
            </div>
            <div style="height: 270px; position: relative;">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Seasonal Performance Chart (Full Width) -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0 text-dark">Analisis Performa Triwulan (Pajak vs Retribusi)</h6>
        <span class="badge bg-success-subtle text-success">Realisasi per Triwulan</span>
    </div>
    <div style="height: 250px; position: relative;">
        <canvas id="seasonalChart"></canvas>
    </div>
</div>

<!-- Filter Section -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0 text-dark">Filter Transaksi & Objek</h6>
        <button type="button" onclick="resetFilters()" class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
        </button>
    </div>
    <div class="row g-3">
        <!-- Pencarian -->
        <div class="col-12 col-md-5">
            <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Pencarian</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" onkeyup="applyFilters()"
                       placeholder="Cari nama objek atau jenis penerimaan..."
                       class="form-control border-start-0 ps-0">
            </div>
        </div>

        <!-- Filter Kategori -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Kategori</label>
            <select id="categoryFilter" onchange="applyFilters()" class="form-select">
                <option value="all">Semua Kategori</option>
                <option value="pbb">PBB-P2</option>
                <option value="bphtb">BPHTB</option>
                <option value="retribusi">Retribusi</option>
            </select>
        </div>

        <!-- Filter Status -->
        <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Status</label>
            <select id="statusFilter" onchange="applyFilters()" class="form-select">
                <option value="all">Semua Status</option>
                <option value="lunas">Lunas</option>
                <option value="proses">Dalam Proses</option>
                <option value="tunggakan">Tunggakan</option>
            </select>
        </div>

        <!-- Periode -->
        <div class="col-12 col-md-2">
            <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Periode</label>
            <select class="form-select">
                <option>30 Hari Terakhir</option>
                <option>Bulan Ini</option>
                <option>Tahun Ini</option>
            </select>
        </div>
    </div>
</div>

<!-- Table Card (Dark Header Table) -->
<div class="table-custom mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">Daftar Objek Pajak & Retribusi Daerah</h6>
        <span class="text-muted small">Update Real-time</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Nama Objek</th>
                    <th>Kategori</th>
                    <th>Tanggal Penetapan</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody id="cropsTableBody">
                <!-- Item 1 -->
                <tr data-item-id="1" data-status="proses" data-category="pbb" data-searchable="pbb sektor perkotaan kecamatan a dalam proses">
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-success border" style="width: 40px; height: 40px;">
                                <i class="bi bi-houses fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">PBB Sektor Perkotaan</div>
                                <div class="text-muted small">Wilayah Kecamatan A</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                            PBB-P2
                        </span>
                    </td>
                    <td>
                        <span class="text-muted small">15 Mar 2026</span>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">Rp 450.000.000</span>
                    </td>
                    <td>
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                            Dalam Proses
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <button type="button" onclick="viewItem(1)" class="btn btn-light border text-muted" title="Lihat Rincian">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-light border text-muted" title="Ubah Data">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" onclick="showDeleteModal(1)" class="btn btn-light border text-danger" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Item 2 -->
                <tr data-item-id="2" data-status="lunas" data-category="bphtb" data-searchable="bphtb perumahan harmoni kavling b-12 lunas">
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-success border" style="width: 40px; height: 40px;">
                                <i class="bi bi-building fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">BPHTB Perumahan Harmoni</div>
                                <div class="text-muted small">Kavling B-12 s/d B-20</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            BPHTB
                        </span>
                    </td>
                    <td>
                        <span class="text-muted small">02 Mei 2026</span>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">Rp 85.000.000</span>
                    </td>
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            Lunas
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <button type="button" onclick="viewItem(2)" class="btn btn-light border text-muted" title="Lihat Rincian">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-light border text-muted" title="Ubah Data">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" onclick="showDeleteModal(2)" class="btn btn-light border text-danger" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Item 3 -->
                <tr data-item-id="3" data-status="tunggakan" data-category="retribusi" data-searchable="retribusi pasar central blok kios c-15 tunggakan">
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-success border" style="width: 40px; height: 40px;">
                                <i class="bi bi-shop fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">Retribusi Pasar Central</div>
                                <div class="text-muted small">Blok Kios C-15</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                            Retribusi
                        </span>
                    </td>
                    <td>
                        <span class="text-muted small">10 Apr 2026</span>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">Rp 32.500.000</span>
                    </td>
                    <td>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                            Tunggakan
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <button type="button" onclick="viewItem(3)" class="btn btn-light border text-muted" title="Lihat Rincian">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-light border text-muted" title="Ubah Data">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" onclick="showDeleteModal(3)" class="btn btn-light border text-danger" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div id="noResults" class="text-center py-5 d-none">
        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 60px; height: 60px;">
            <i class="bi bi-search text-muted fs-4"></i>
        </div>
        <h6 class="fw-bold mb-1 text-dark">Data Tidak Ditemukan</h6>
        <p class="text-muted small mb-0">Coba sesuaikan kata kunci pencarian atau pilihan filter Anda.</p>
    </div>
</div>

<!-- ==========================================================================
     MODALS (Bootstrap 5 Standard)
     ========================================================================== -->

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addModalLabel">Tambah Data Pendapatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="addItem(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nama Objek Pendapatan</label>
                        <input type="text" class="form-control" placeholder="Contoh: Pajak Kendaraan Bermotor Pekanbaru" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">Kategori</label>
                            <select class="form-select">
                                <option value="pbb">PBB-P2</option>
                                <option value="bphtb">BPHTB</option>
                                <option value="retribusi">Retribusi</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">Status</label>
                            <select class="form-select">
                                <option value="proses">Dalam Proses</option>
                                <option value="lunas">Lunas</option>
                                <option value="tunggakan">Tunggakan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Tanggal Penetapan</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nominal (Rp)</label>
                        <input type="number" class="form-control" placeholder="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-green">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-3">
            <div class="modal-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-trash3 fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Hapus Data Ini?</h5>
                <p class="text-muted small mb-4">Data yang dihapus tidak dapat dipulihkan kembali dari sistem.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-custom flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" onclick="confirmDelete()" class="btn btn-danger flex-fill">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Rincian Objek Penerimaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
                    <div class="stat-icon" style="width: 44px; height: 44px;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">PBB Sektor Perkotaan</h6>
                        <small class="text-muted">Kecamatan A &bull; Dalam Proses</small>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2.5 border rounded-2 bg-white">
                            <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Tgl Penetapan</div>
                            <div class="fw-semibold text-dark">15 Mar 2026</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 border rounded-2 bg-white">
                            <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Jatuh Tempo</div>
                            <div class="fw-semibold text-dark">31 Agu 2026</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 border rounded-2 bg-white">
                            <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Nominal Tagihan</div>
                            <div class="fw-bold text-success">Rp 450.000.000</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 border rounded-2 bg-white">
                            <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Kanal Bayar</div>
                            <div class="fw-semibold text-dark">Bank BRK / QRIS</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green w-100" data-bs-dismiss="modal">Tutup Rincian</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
