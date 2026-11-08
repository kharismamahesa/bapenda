@extends('layouts.admin')

@section('title', 'Pengaduan Masyarakat')
@section('page_title', 'Pengaduan Masyarakat')
@section('page_subtitle', 'Daftar pengaduan yang masuk ke kantor pelayanan.')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="text-danger small mb-2" id="pengaduan-stats-error" role="status" aria-live="polite"></div>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Total Pengaduan</div>
                <div class="stat-value" id="pengaduan-total"><span class="spinner-border spinner-border-sm text-success" aria-label="Memuat"></span></div>
                <div class="text-muted small mt-1">{{ $scopeLabel }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Sudah Dibalas</div>
                <div class="stat-value" id="pengaduan-replied"><span class="spinner-border spinner-border-sm text-success" aria-label="Memuat"></span></div>
                <div class="text-muted small mt-1">Pengaduan dengan balasan tercatat</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Belum Dibalas</div>
                <div class="stat-value" id="pengaduan-pending"><span class="spinner-border spinner-border-sm text-success" aria-label="Memuat"></span></div>
                <div class="text-muted small mt-1">Pengaduan yang masih menunggu balasan</div>
            </div>
        </div>
    </div>

    <div class="text-danger small mb-2" id="pengaduan-load-error" role="status" aria-live="polite"></div>
    <div class="table-custom mb-4">
        <div class="p-3 border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-1">
                <h6 class="fw-bold mb-0 text-dark">Daftar Pengaduan</h6>
                <small class="text-muted">Gunakan kolom pencarian untuk menemukan kode, nama, email, kantor, atau isi pengaduan.</small>
            </div>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0 w-100" id="table-pengaduan">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pengadu</th>
                        <th>Kantor Pelayanan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const pengaduanError = document.getElementById('pengaduan-load-error');
        $('#table-pengaduan').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50],
            order: [[3, 'desc']],
            ajax: {
                url: @json($dataUrl),
                error: function (xhr) {
                    pengaduanError.textContent = `Daftar pengaduan gagal dimuat (${xhr.status}). Silakan coba muat ulang tabel.`;
                }
            },
            columns: [
                { data: 'kode', name: 'kode' },
                { data: 'pengadu', name: 'nama' },
                { data: 'kantor', name: 'kantor' },
                { data: 'tanggal', name: 'created_datetime' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ pengaduan',
                infoEmpty: 'Belum ada pengaduan',
                infoFiltered: '(difilter dari _MAX_ pengaduan)',
                zeroRecords: 'Pengaduan tidak ditemukan',
                emptyTable: 'Belum ada pengaduan',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
            },
            drawCallback: function () {
                pengaduanError.textContent = '';
            }
        });

        fetch(@json($statsUrl), { headers: { Accept: 'application/json' } })
            .then(response => {
                if (!response.ok) throw new Error(`Statistik pengaduan gagal dimuat (${response.status}).`);
                return response.json();
            })
            .then(stats => {
                document.getElementById('pengaduan-total').textContent = Number(stats.total).toLocaleString('id-ID');
                document.getElementById('pengaduan-replied').textContent = Number(stats.replied).toLocaleString('id-ID');
                document.getElementById('pengaduan-pending').textContent = Number(stats.pending).toLocaleString('id-ID');
            })
            .catch(error => {
                document.getElementById('pengaduan-stats-error').textContent =
                    'Statistik pengaduan gagal dimuat. Muat ulang halaman untuk mencoba kembali.';
                console.error('Gagal memuat statistik pengaduan:', error);
            });
    </script>
@endpush
