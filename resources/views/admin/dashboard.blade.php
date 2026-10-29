@extends('layouts.admin')

@section('title', 'Dashboard Monitoring')
@section('page_title', 'Ringkasan Kinerja & Pendapatan')
@section('page_subtitle', 'Pantau realisasi pendapatan daerah, pajak, retribusi, dan tren bulanan')

@section('page_actions')
<div class="grid grid-cols-2 gap-2 w-full md:w-auto md:flex md:items-center md:gap-3">
    <button
        class="flex items-center justify-center gap-2 px-4 py-2.5 border border-border rounded-button text-foreground font-medium hover:border-primary transition-all duration-200 cursor-pointer">
        <i data-lucide="download" class="w-4 h-4"></i>
        <span>Export Laporan</span>
    </button>
    <button onclick="openAddModal()"
        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Data</span>
    </button>
</div>
@endsection

@section('content')
<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Card 1 -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Total Realisasi PAD</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-3xl font-extrabold mb-1">142,5<span class="text-base font-normal text-gray-500 ml-1">Miliar</span></p>
                    <p class="text-green-600 text-sm font-medium">+8.2% bulan ini</p>
                </div>
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center">
                    <i data-lucide="wallet-2" class="w-7 h-7 text-foreground"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Capaian Target</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-3xl font-extrabold mb-1">92,4<span class="text-base font-normal text-gray-500 ml-1">%</span></p>
                    <p class="text-green-600 text-sm font-medium">+2.4% vs tahun lalu</p>
                </div>
                <div class="w-16 h-16 bg-accent-teal rounded-icon flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-7 h-7 text-foreground"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Wajib Pajak Aktif</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-3xl font-extrabold mb-1">28.450</p>
                    <p class="text-blue-600 text-sm font-medium">1.200 Terdaftar baru</p>
                </div>
                <div class="w-16 h-16 bg-accent-peach rounded-icon flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-foreground"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Tunggakan</h3>
        <div class="bg-white rounded-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-foreground text-3xl font-extrabold mb-1">3.120</p>
                    <p class="text-red-600 text-sm font-medium">Perlu penagihan</p>
                </div>
                <div class="w-16 h-16 bg-accent-lime rounded-icon flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-7 h-7 text-foreground"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Trend Chart -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Tren Penerimaan (6 Bulan Terakhir)</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full h-64">
                <canvas id="yieldChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Growth Rate Chart -->
    <div class="bg-muted rounded-card pt-5 px-3 pb-3">
        <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Persentase Target per Objek Pajak</h3>
        <div class="bg-white rounded-card p-4 md:p-5">
            <div class="w-full h-64">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Seasonal Performance Chart (Full Width) -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3 mb-8">
    <h3 class="text-foreground text-lg font-bold ml-3 mb-4">Analisis Performa Triwulan</h3>
    <div class="bg-white rounded-card p-4 md:p-5">
        <div class="w-full h-60">
            <canvas id="seasonalChart"></canvas>
        </div>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="bg-muted rounded-card p-4 mb-6">
    <h3 class="text-foreground text-lg font-bold mb-4">Filter Transaksi & Objek</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search -->
        <div class="lg:col-span-2 space-y-1">
            <label class="text-xs font-semibold text-gray-500 uppercase">Pencarian</label>
            <div class="relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                <input type="text" id="searchInput" onkeyup="applyFilters()"
                    placeholder="Cari nama objek atau jenis pajak..."
                    class="w-full pl-10 pr-4 py-3 bg-white border border-border rounded-button text-foreground placeholder:text-gray-500 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200">
            </div>
        </div>

        <!-- Category Filter -->
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-500 uppercase">Kategori</label>
            <select id="categoryFilter" onchange="applyFilters()"
                class="w-full px-4 py-3 bg-white border border-border rounded-button text-foreground focus:border-primary focus:outline-none transition-all duration-200">
                <option value="all">Semua Kategori</option>
                <option value="pbb">PBB-P2</option>
                <option value="bphtb">BPHTB</option>
                <option value="retribusi">Retribusi</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
            <select id="statusFilter" onchange="applyFilters()"
                class="w-full px-4 py-3 bg-white border border-border rounded-button text-foreground focus:border-primary focus:outline-none transition-all duration-200">
                <option value="all">Semua Status</option>
                <option value="lunas">Lunas</option>
                <option value="proses">Dalam Proses</option>
                <option value="tunggakan">Tunggakan</option>
            </select>
        </div>

        <!-- Date Range -->
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-500 uppercase">Periode</label>
            <div class="relative">
                <select class="w-full px-4 py-3 bg-white border border-border rounded-button text-foreground focus:border-primary focus:outline-none transition-all duration-200">
                    <option>30 Hari Terakhir</option>
                    <option>Bulan Ini</option>
                    <option>Tahun Ini</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex items-center justify-between mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">Daftar Objek Pajak & Retribusi</h3>
        <div class="flex gap-2">
            <button onclick="resetFilters()"
                class="flex items-center gap-2 px-3 py-1.5 bg-white border border-border rounded-lg text-xs font-medium hover:border-primary cursor-pointer transition-all">
                <i data-lucide="refresh-cw" class="w-3 h-3"></i> Reset Filter
            </button>
        </div>
    </div>
    <div class="bg-white rounded-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4">Nama Objek</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4 hidden sm:table-cell">Kategori</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4 hidden md:table-cell">Tanggal Penetapan</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4 hidden md:table-cell">Nominal</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4">Status</th>
                        <th class="text-right text-gray-500 text-sm font-medium px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cropsTableBody">
                    <!-- Item 1 -->
                    <tr data-item-id="1" data-status="proses" data-category="pbb"
                        data-searchable="pbb sektor perkotaan pbb proses"
                        class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0 text-primary border border-emerald-100">
                                    <i data-lucide="home" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-foreground font-semibold truncate">PBB Sektor Perkotaan</p>
                                    <p class="text-gray-500 text-xs truncate">Wilayah Kecamatan A</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                PBB-P2
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-gray-600 text-sm">15 Mar 2026</span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-gray-600 text-sm font-medium">Rp 450.000.000</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-info-light text-info-dark text-xs font-medium px-2.5 py-1 rounded-full">Dalam Proses</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="viewItem(1)" class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-600"></i>
                                </button>
                                <button class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="edit" class="w-4 h-4 text-gray-600"></i>
                                </button>
                                <button onclick="showDeleteModal(1)" class="p-2 hover:bg-error-light rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-error"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Item 2 -->
                    <tr data-item-id="2" data-status="lunas" data-category="bphtb"
                        data-searchable="bphtb perumahan harmoni bphtb lunas"
                        class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0 text-teal-700">
                                    <i data-lucide="building" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-foreground font-semibold truncate">BPHTB Perumahan Harmoni</p>
                                    <p class="text-gray-500 text-xs truncate">Kavling B-12 s/d B-20</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                BPHTB
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-gray-600 text-sm">02 Mei 2026</span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-gray-600 text-sm font-medium">Rp 85.000.000</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-success-light text-success-dark text-xs font-medium px-2.5 py-1 rounded-full">Lunas</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="viewItem(2)" class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-600"></i>
                                </button>
                                <button class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="edit" class="w-4 h-4 text-gray-600"></i>
                                </button>
                                <button onclick="showDeleteModal(2)" class="p-2 hover:bg-error-light rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-error"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Item 3 -->
                    <tr data-item-id="3" data-status="tunggakan" data-category="retribusi"
                        data-searchable="retribusi pasar central retribusi tunggakan"
                        class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0 text-yellow-800">
                                    <i data-lucide="store" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-foreground font-semibold truncate">Retribusi Pasar Central</p>
                                    <p class="text-gray-500 text-xs truncate">Blok Kios C-15</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Retribusi
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-gray-600 text-sm">10 Apr 2026</span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-gray-600 text-sm font-medium">Rp 32.500.000</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">Tunggakan</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="viewItem(3)" class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-600"></i>
                                </button>
                                <button class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="edit" class="w-4 h-4 text-gray-600"></i>
                                </button>
                                <button onclick="showDeleteModal(3)" class="p-2 hover:bg-error-light rounded-lg transition-all duration-200 cursor-pointer">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-error"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- No Results -->
        <div id="noResults" class="hidden text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-foreground text-lg font-bold mb-1">Data tidak ditemukan</h3>
            <p class="text-gray-500 text-sm">Silakan ubah filter atau kata kunci pencarian Anda.</p>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-card p-6 max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-foreground text-xl font-bold">Tambah Data Pendapatan</h3>
            <button onclick="closeModal('addModal')" class="p-2 hover:bg-gray-100 rounded-full cursor-pointer">
                <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>
        <form onsubmit="addItem(event)">
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-foreground text-sm font-medium">Nama Objek</label>
                    <input type="text" placeholder="Contoh: PBB Sektor Perkotaan"
                        class="w-full px-4 py-3 border border-border rounded-button text-foreground focus:border-primary focus:outline-none" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-foreground text-sm font-medium">Kategori</label>
                        <select class="w-full px-4 py-3 border border-border rounded-button text-foreground focus:border-primary focus:outline-none">
                            <option value="pbb">PBB-P2</option>
                            <option value="bphtb">BPHTB</option>
                            <option value="retribusi">Retribusi</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-foreground text-sm font-medium">Status</label>
                        <select class="w-full px-4 py-3 border border-border rounded-button text-foreground focus:border-primary focus:outline-none">
                            <option value="proses">Dalam Proses</option>
                            <option value="lunas">Lunas</option>
                            <option value="tunggakan">Tunggakan</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-foreground text-sm font-medium">Tanggal</label>
                    <input type="date" class="w-full px-4 py-3 border border-border rounded-button text-foreground focus:border-primary focus:outline-none">
                </div>
                <div class="space-y-2">
                    <label class="block text-foreground text-sm font-medium">Nominal (Rp)</label>
                    <input type="number" placeholder="0" class="w-full px-4 py-3 border border-border rounded-button text-foreground focus:border-primary focus:outline-none">
                </div>
            </div>
            <div class="flex gap-3 pt-6">
                <button type="button" onclick="closeModal('addModal')"
                    class="flex-1 px-4 py-2.5 border border-border rounded-button text-foreground font-medium hover:border-primary cursor-pointer">Batal</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-card p-6 max-w-sm w-full text-center">
        <div class="w-16 h-16 bg-error-light rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="trash-2" class="w-8 h-8 text-error"></i>
        </div>
        <h3 class="text-foreground text-xl font-bold mb-2">Hapus Data Ini?</h3>
        <p class="text-gray-500 text-sm mb-6">Tindakan ini akan menghapus catatan dan data historis terkait. Data yang dihapus tidak dapat dipulihkan.</p>
        <div class="flex gap-3">
            <button onclick="closeModal('deleteModal')"
                class="flex-1 px-4 py-2.5 border border-border rounded-button text-foreground font-medium hover:border-primary cursor-pointer">Batal</button>
            <button onclick="confirmDelete()"
                class="flex-1 px-4 py-2.5 bg-error text-white rounded-button font-medium hover:bg-error-dark cursor-pointer">Hapus</button>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div id="viewModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-card p-6 max-w-md w-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-foreground text-xl font-bold">Rincian Transaksi</h3>
            <button onclick="closeModal('viewModal')" class="p-2 hover:bg-gray-100 rounded-full cursor-pointer">
                <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-4 bg-muted rounded-card">
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                    <i data-lucide="file-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-foreground font-bold text-lg">PBB Sektor Perkotaan</h4>
                    <p class="text-gray-500 text-sm">Kecamatan A • Dalam Proses</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 border border-gray-100 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Tgl Penetapan</p>
                    <p class="text-foreground font-medium">15 Mar 2026</p>
                </div>
                <div class="p-3 border border-gray-100 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Jatuh Tempo</p>
                    <p class="text-foreground font-medium">31 Agu 2026</p>
                </div>
                <div class="p-3 border border-gray-100 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Nominal Tagihan</p>
                    <p class="text-green-600 font-medium">Rp 450.000.000</p>
                </div>
                <div class="p-3 border border-gray-100 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Kanal Bayar</p>
                    <p class="text-foreground font-medium">Bank BJB / QRIS</p>
                </div>
            </div>
            <button onclick="closeModal('viewModal')"
                class="w-full px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover cursor-pointer mt-2">
                Tutup Rincian
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast" class="fixed top-4 right-4 z-[110] hidden transform transition-all duration-300">
    <div class="bg-white rounded-card shadow-lg border border-border p-4 flex items-center gap-3">
        <div id="toastIcon" class="w-10 h-10 rounded-full flex items-center justify-center"></div>
        <p id="toastMessage" class="text-foreground font-medium"></p>
        <button onclick="hideToast()" class="p-1 hover:bg-gray-100 rounded-full cursor-pointer">
            <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush

