@extends('layouts.admin')

@section('title', 'Slider Hero')
@section('page_title', 'Kelola Slider Hero')
@section('page_subtitle', 'Atur gambar dan tulisan pada hero halaman utama.')

@section('page_actions')
    <a href="{{ route('admin.slider.create') }}" class="btn btn-green">
        <i class="bi bi-plus-lg"></i> Tambah Slider
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="text-danger small mb-2" id="slider-table-error" role="status" aria-live="polite"></div>
    <div class="table-custom mb-4">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0 w-100" id="table-slider">
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const sliderTableError = document.getElementById('slider-table-error');
        $('#table-slider').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50],
            order: [[0, 'asc']],
            ajax: {
                url: @json($dataUrl),
                error: xhr => {
                    sliderTableError.textContent = `Daftar slider gagal dimuat (${xhr.status}). Silakan muat ulang tabel.`;
                }
            },
            columns: [
                { data: 'urutan', name: 'urutan' },
                { data: 'gambar', name: 'gambar', orderable: false, searchable: false },
                { data: 'judul', name: 'judul_putih' },
                { data: 'status', name: 'status' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ slider',
                infoEmpty: 'Belum ada slider',
                infoFiltered: '(difilter dari _MAX_ slider)',
                zeroRecords: 'Slider tidak ditemukan',
                emptyTable: 'Belum ada slider',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
            },
            drawCallback: () => { sliderTableError.textContent = ''; }
        });
    </script>
@endpush
