@extends('layouts.admin')

@section('title', 'Manajemen Berita')
@section('page_title', 'Kelola Publikasi & Berita')
@section('page_subtitle', 'Publikasi artikel, siaran pers, dan pengumuman resmi Bapenda Provinsi Riau')

@section('page_actions')
<button type="button" id="btn-tambah" class="btn btn-green">
    <i class="bi bi-plus-lg"></i>
    <span>Tambah Berita Baru</span>
</button>
@endsection

@section('content')
<!-- Filter Box (Kinerja Styling) -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0 text-dark">Filter Publikasi</h6>
        <button type="button" id="btn-reset-filter" class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
        </button>
    </div>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Kategori</label>
            <select id="filter-kategori" class="form-select">
                <option value="all">Semua Kategori</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id_berita_kategori }}">{{ $kat->berita_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Status Publikasi</label>
            <select id="filter-status" class="form-select">
                <option value="all">Semua Status</option>
                <option value="1">Published</option>
                <option value="0">Draft</option>
            </select>
        </div>
    </div>
</div>

<!-- Table Card (Kinerja Styling) -->
<div class="table-custom mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">Daftar Berita & Informasi</h6>
        <button type="button" id="btn-reload" class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-clockwise"></i> Segarkan Tabel
        </button>
    </div>

    <div class="table-responsive p-3">
        <table id="table-berita" class="table table-hover align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Berita</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th class="text-center">Dibaca</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-3" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Diisi otomatis oleh DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Tambah / Edit Berita (Bootstrap 5) -->
<div class="modal fade" id="modal-berita" tabindex="-1" aria-labelledby="modalBeritaTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalBeritaTitle">Tambah Berita Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-berita" enctype="multipart/form-data" novalidate>
                <input type="hidden" id="berita-id" name="id">

                <div class="modal-body">
                    <!-- Judul -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="judul">
                            Judul Berita <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" class="form-control" placeholder="Masukkan judul berita lengkap" required>
                        <div class="invalid-feedback d-block" id="err-judul"></div>
                    </div>

                    <!-- Kategori, Status & Tanggal -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted" for="id_berita_kategori">
                                Kategori Berita <span class="text-danger">*</span>
                            </label>
                            <select id="id_berita_kategori" name="id_berita_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriList as $kat)
                                    <option value="{{ $kat->id_berita_kategori }}">{{ $kat->berita_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="err-id_berita_kategori"></div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted" for="status">
                                Status Publikasi <span class="text-danger">*</span>
                            </label>
                            <select id="status" name="status" class="form-select">
                                <option value="1">Publish (Tampilkan)</option>
                                <option value="0">Draft (Simpan Sementara)</option>
                            </select>
                            <div class="invalid-feedback d-block" id="err-status"></div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted" for="tanggal_berita">
                                Tanggal Berita <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" id="tanggal_berita" name="tanggal_berita" class="form-control" required>
                            <div class="invalid-feedback d-block" id="err-tanggal_berita"></div>
                        </div>
                    </div>

                    <!-- Gambar / Sampul -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="gambar">
                            Gambar Sampul / Thumbnail <small class="text-muted fw-normal">(Maks. 2MB, JPG/PNG/WebP)</small>
                        </label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                        <div class="invalid-feedback d-block" id="err-gambar"></div>
                        
                        <!-- Preview Container -->
                        <div id="preview-container" class="d-none mt-2 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                            <img id="image-preview" src="" alt="Preview" class="rounded-2 border" style="width: 60px; height: 60px; object-fit: cover;">
                            <div class="small text-muted">
                                <span class="fw-semibold text-dark d-block">Preview Gambar</span>
                                <span id="preview-info">Gambar saat ini / dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- Konten Berita Summernote Lite -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold small text-muted mb-0" for="isi">
                                Isi Berita Lengkap <span class="text-danger">*</span>
                            </label>
                            <small class="text-muted" style="font-size: 0.75rem;">Editor Ringan (Format Dasar & Link)</small>
                        </div>
                        <textarea id="isi" name="isi"></textarea>
                        <div class="invalid-feedback d-block" id="err-isi"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-submit" class="btn btn-green">
                        <span id="btn-submit-text">Simpan Berita</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Detail Berita (Bootstrap 5) -->
<div class="modal fade" id="modal-view" tabindex="-1" aria-labelledby="modalViewTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalViewTitle">Rincian Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <img id="view-gambar" src="" alt="Thumbnail" class="w-100 rounded-3 mb-3 border" style="max-height: 320px; object-fit: cover;" loading="lazy" decoding="async"
                    onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span id="view-kategori" class="badge bg-success-subtle text-success border border-success-subtle"></span>
                        <span id="view-status" class="badge"></span>
                        <span id="view-tanggal" class="text-muted small ms-auto"></span>
                    </div>
                    <h4 id="view-judul" class="fw-bold text-dark mb-1"></h4>
                    <small class="text-muted">Total Tayangan: <strong id="view-counter" class="text-dark">0</strong> kali</small>
                </div>
                <div id="view-isi" class="text-dark small leading-relaxed p-3 bg-light rounded-3 border" style="max-height: 280px; overflow-y: auto;">
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
<script>
$(document).ready(function () {
    // Setup Header CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const beritaModal = new bootstrap.Modal(document.getElementById('modal-berita'));
    const viewModal = new bootstrap.Modal(document.getElementById('modal-view'));

    // Inisialisasi Summernote Lite
    $('#isi').summernote({
        placeholder: 'Tuliskan isi berita di sini...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['paragraph']],
            ['insert', ['link']],
            ['view', ['undo', 'redo']]
        ],
        callbacks: {
            onInit: function(layoutInfo) {
                layoutInfo.editable.on('paste', function(e) {
                    const clipboard = (e.originalEvent || e).clipboardData;
                    if (!clipboard) return;

                    const text = clipboard.getData('text/plain');
                    if (!text) return;

                    e.preventDefault();

                    const safeText = text
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/\r\n|\r|\n/g, '<br>');

                    document.execCommand('insertHTML', false, safeText);
                });
            },
            onChange: function(contents, $editable) {
                if ($('#isi').summernote('isEmpty') === false) {
                    $('.note-editor').removeClass('is-invalid');
                    $('#err-isi').text('');
                }
            }
        }
    });

    // Inisialisasi DataTables Kinerja Styling
    const table = $('#table-berita').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.berita.data') }}",
            type: "GET",
            data: function (d) {
                d.kategori_filter = $('#filter-kategori').val();
                d.status_filter = $('#filter-status').val();
            },
            error: function (xhr, error, code) {
                console.error("DataTables Error:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat data berita.',
                    confirmButtonColor: '#16a34a'
                });
            }
        },
        order: [[3, 'desc']],
        columns: [
            { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'judul', name: 'judul' },
            { data: 'kategori', name: 'id_berita_kategori' },
            { data: 'tanggal', name: 'tanggal_berita' },
            { data: 'views', name: 'counter', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end pe-3' }
        ],
        language: {
            search: "Cari Berita:",
            searchPlaceholder: "Ketik judul / kata kunci...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data berita yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ berita",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                previous: "‹",
                next: "›"
            }
        }
    });

    // Filter event handlers
    $('#filter-kategori, #filter-status').on('change', function () {
        table.ajax.reload();
    });

    $('#btn-reset-filter').on('click', function () {
        $('#filter-kategori').val('all');
        $('#filter-status').val('all');
        table.ajax.reload();
    });

    $('#btn-reload').on('click', function () {
        table.ajax.reload(null, false);
    });

    // Preview Gambar saat upload
    $('#gambar').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#image-preview').attr('src', e.target.result);
                $('#preview-info').text(file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)');
                $('#preview-container').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Nilai default tanggal berita (waktu lokal) untuk input datetime-local
    function nowLocalValue() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        return now.toISOString().slice(0, 16);
    }

    // Reset Form Modal
    function resetForm() {
        $('#form-berita')[0].reset();
        $('#berita-id').val('');
        $('#tanggal_berita').val(nowLocalValue());
        $('#isi').summernote('reset');
        $('.invalid-feedback').text('');
        $('#form-berita input, #form-berita select, .note-editor').removeClass('is-invalid');
        $('#preview-container').addClass('d-none');
        $('#image-preview').attr('src', '');
        $('#btn-submit').prop('disabled', false);
        $('#btn-submit-text').text('Simpan Berita');
    }

    // 1. Create: Klik Tombol Tambah
    $('#btn-tambah').on('click', function () {
        resetForm();
        $('#modalBeritaTitle').text('Tambah Berita Baru');
        $('#btn-submit-text').text('Simpan Berita');
        beritaModal.show();
    });

    // 2. Submit Form (Create / Update via AJAX dengan FormData)
    $('#form-berita').on('submit', function (e) {
        e.preventDefault();

        $('.invalid-feedback').text('');
        $('#form-berita input, #form-berita select, .note-editor').removeClass('is-invalid');

        const id = $('#berita-id').val();
        const isEdit = id !== '';
        const url = isEdit ? "{{ url('admin/berita') }}/" + id : "{{ route('admin.berita.store') }}";

        const formData = new FormData(this);
        const summernoteContent = $('#isi').summernote('code');
        formData.set('isi', summernoteContent);

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        $('#btn-submit').prop('disabled', true);
        $('#btn-submit-text').text('Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                beritaModal.hide();
                if (isEdit) {
                    table.ajax.reload(null, false);
                } else {
                    // Berita baru selalu berada di urutan teratas
                    table.page(0).draw(false);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Berita berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false,
                    confirmButtonColor: '#16a34a'
                });
            },
            error: function (xhr) {
                $('#btn-submit').prop('disabled', false);
                $('#btn-submit-text').text(isEdit ? 'Perbarui Berita' : 'Simpan Berita');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function (field, msgs) {
                            if (field === 'isi') {
                                $('.note-editor').addClass('is-invalid');
                                $('#err-isi').text(msgs[0]);
                            } else {
                                $('#' + field).addClass('is-invalid');
                                $('#err-' + field).text(msgs[0]);
                            }
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Gagal menyimpan berita.',
                        confirmButtonColor: '#16a34a'
                    });
                }
            }
        });
    });

    // 3. Edit: Klik Tombol Edit
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        resetForm();

        $('#modalBeritaTitle').text('Edit Berita');
        $('#btn-submit-text').text('Perbarui Berita');

        $.ajax({
            url: "{{ url('admin/berita') }}/" + id,
            type: 'GET',
            success: function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#berita-id').val(data.id_berita);
                    $('#judul').val(data.judul);
                    $('#id_berita_kategori').val(data.id_berita_kategori);
                    $('#status').val(data.status);
                    $('#tanggal_berita').val(data.tanggal_berita_raw);
                    
                    $('#isi').summernote('code', data.isi || '');

                    if (data.gambar_url) {
                        $('#image-preview').attr('src', data.gambar_url);
                        $('#preview-info').text('Gambar saat ini (pilih file baru jika ingin mengganti)');
                        $('#preview-container').removeClass('d-none');
                    }

                    beritaModal.show();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengambil Data',
                    text: xhr.responseJSON?.message || 'Data berita tidak ditemukan.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    // 4. View Detail: Klik Tombol Lihat
    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');

        $.ajax({
            url: "{{ url('admin/berita') }}/" + id,
            type: 'GET',
            success: function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#view-judul').text(data.judul);
                    $('#view-kategori').text(data.kategori_nama);
                    $('#view-tanggal').text(data.tanggal_berita);
                    $('#view-counter').text(Number(data.counter).toLocaleString('id-ID'));
                    $('#view-gambar').attr('src', data.gambar_url);
                    $('#view-isi').html(data.isi);

                    if (data.status === '1') {
                        $('#view-status').text('Published').attr('class', 'badge bg-success-subtle text-success border border-success-subtle');
                    } else {
                        $('#view-status').text('Draft').attr('class', 'badge bg-secondary-subtle text-secondary border border-secondary-subtle');
                    }

                    viewModal.show();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tidak dapat menampilkan rincian berita.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    // 5. Delete: Klik Tombol Hapus dengan Konfirmasi
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Berita Ini?',
            text: "Data dan berkas gambar berita akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/berita') }}/" + id,
                    type: 'DELETE',
                    success: function (res) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message || 'Berita berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.',
                            confirmButtonColor: '#16a34a'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
