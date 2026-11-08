@extends('layouts.admin')

@section('title', 'Kelola Video')
@section('page_title', 'Kelola Video')
@section('page_subtitle', 'Video yang aktif di halaman utama dan halaman informasi video.')

@section('page_actions')
    <a href="{{ route('admin.video.create') }}" class="btn btn-green">
        <i class="bi bi-plus-lg"></i> Tambah Video
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="text-danger small mb-2" id="video-table-error" role="status" aria-live="polite"></div>
    <div class="table-custom mb-4">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0 w-100" id="table-video">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Judul</th>
                        <th>Link YouTube tersimpan</th>
                        <th>Keterangan</th>
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
        const videoTableError = document.getElementById('video-table-error');
        $('#table-video').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50],
            order: [[0, 'desc']],
            ajax: {
                url: @json($dataUrl),
                error: xhr => {
                    videoTableError.textContent = `Daftar video gagal dimuat (${xhr.status}). Silakan muat ulang tabel.`;
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'judul', name: 'judul' },
                { data: 'video', name: 'video' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ video',
                infoEmpty: 'Belum ada video',
                infoFiltered: '(difilter dari _MAX_ video)',
                zeroRecords: 'Video tidak ditemukan',
                emptyTable: 'Belum ada video',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
            },
            drawCallback: () => { videoTableError.textContent = ''; }
        });
    </script>
@endpush
