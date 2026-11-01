@extends('layouts.admin')

@section('title', 'Halaman Konten')
@section('page_title', 'Kelola Halaman Konten')
@section('page_subtitle', 'Profil, sejarah, visi misi, dan halaman statis lainnya')

@section('page_actions')
<button type="button" id="btn-tambah" class="btn btn-green">
    <i class="bi bi-plus-lg"></i>
    <span>Tambah Halaman</span>
</button>
@endsection

@section('content')
<!-- Table Card -->
<div class="table-custom mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">Daftar Halaman Konten</h6>
        <button type="button" id="btn-reload" class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-clockwise"></i> Segarkan Tabel
        </button>
    </div>

    <div class="table-responsive p-3">
        <table id="table-halaman" class="table table-hover align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Halaman</th>
                    <th>Tipe Konten</th>
                    <th>Terakhir Diubah</th>
                    <th class="text-end pe-3" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Diisi otomatis oleh DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Tambah / Edit Halaman Konten (Bootstrap 5) -->
<div class="modal fade" id="modal-halaman" tabindex="-1" aria-labelledby="modalHalamanTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalHalamanTitle">Tambah Halaman Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-halaman" enctype="multipart/form-data" novalidate>
                <input type="hidden" id="halaman-id" name="id">

                <div class="modal-body">
                    <!-- Judul -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="judul">
                            Judul Halaman <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" class="form-control" placeholder="Contoh: Profil, Visi & Misi, Struktur Organisasi" required>
                        <div class="invalid-feedback d-block" id="err-judul"></div>
                    </div>

                    <!-- Gambar -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="gambar">
                            Gambar <small class="text-muted fw-normal">(Maks. 5MB, JPG/PNG/WebP/SVG — opsional)</small>
                        </label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml">
                        <div class="invalid-feedback d-block" id="err-gambar"></div>

                        <!-- Preview Container -->
                        <div id="preview-container" class="d-none mt-2 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                            <img id="image-preview" src="" alt="Preview" class="rounded-2 border" style="max-width: 120px; max-height: 80px; object-fit: contain;">
                            <div class="small text-muted flex-grow-1">
                                <span class="fw-semibold text-dark d-block">Preview Gambar</span>
                                <span id="preview-info">Gambar yang dipilih</span>
                            </div>
                            <button type="button" id="btn-hapus-gambar" class="btn btn-sm btn-outline-danger" title="Hapus gambar">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="hapus_gambar" name="hapus_gambar" value="0">

                    <!-- Isi Konten (Summernote) -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold small text-muted mb-0" for="isi">
                                Isi Konten <small class="text-muted fw-normal">(opsional jika ada gambar)</small>
                            </label>
                            <small class="text-muted" style="font-size: 0.75rem;">Editor Ringan (Format Dasar & Link)</small>
                        </div>
                        <textarea id="isi" name="isi"></textarea>
                        <div class="invalid-feedback d-block" id="err-isi"></div>
                    </div>

                    <div class="alert alert-light border small mb-0">
                        <i class="bi bi-info-circle text-muted me-1"></i>
                        <strong>Catatan:</strong> Minimal salah satu harus diisi — isi konten (teks) atau gambar. Contoh: halaman Struktur Organisasi cukup gambar saja.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-submit" class="btn btn-green">
                        <span id="btn-submit-text">Simpan Halaman</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Detail Halaman Konten (Bootstrap 5) -->
<div class="modal fade" id="modal-view" tabindex="-1" aria-labelledby="modalViewTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalViewTitle">Rincian Halaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span id="view-slug" class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"></span>
                        <span id="view-updated" class="text-muted small ms-auto"></span>
                    </div>
                    <h4 id="view-judul" class="fw-bold text-dark mb-3"></h4>
                </div>

                <!-- Gambar (jika ada) -->
                <div id="view-gambar-container" class="mb-3 d-none">
                    <img id="view-gambar" src="" alt="Gambar Halaman" class="w-100 rounded-3 border" style="max-height: 400px; object-fit: contain; background: #f8f9fa;">
                </div>

                <!-- Isi Konten (jika ada) -->
                <div id="view-isi-container" class="d-none">
                    <div id="view-isi" class="text-dark small leading-relaxed p-3 bg-light rounded-3 border" style="max-height: 350px; overflow-y: auto;">
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
<script>
$(document).ready(function () {
    // Setup Header CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const halamanModal = new bootstrap.Modal(document.getElementById('modal-halaman'));
    const viewModal = new bootstrap.Modal(document.getElementById('modal-view'));

    // Inisialisasi Summernote
    $('#isi').summernote({
        placeholder: 'Tuliskan isi konten halaman di sini (opsional jika menggunakan gambar)...',
        height: 220,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'table']],
            ['view', ['undo', 'redo', 'codeview']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                if ($('#isi').summernote('isEmpty') === false) {
                    $('.note-editor').removeClass('is-invalid');
                    $('#err-isi').text('');
                }
            }
        }
    });

    // Inisialisasi DataTables
    const table = $('#table-halaman').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.halaman-konten.data') }}",
            type: "GET",
            error: function (xhr, error, code) {
                console.error("DataTables Error:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat data halaman konten.',
                    confirmButtonColor: '#16a34a'
                });
            }
        },
        order: [[3, 'desc']],
        columns: [
            { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'judul', name: 'judul' },
            { data: 'tipe', name: 'tipe', orderable: false, className: 'text-center' },
            { data: 'updated', name: 'updated_at' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end pe-3' }
        ],
        language: {
            search: "Cari Halaman:",
            searchPlaceholder: "Ketik judul halaman...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada halaman konten yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ halaman",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                previous: "‹",
                next: "›"
            }
        }
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
                $('#hapus_gambar').val('0');
            };
            reader.readAsDataURL(file);
        }
    });

    // Hapus gambar (saat edit)
    $('#btn-hapus-gambar').on('click', function () {
        $('#gambar').val('');
        $('#preview-container').addClass('d-none');
        $('#image-preview').attr('src', '');
        $('#hapus_gambar').val('1');
    });

    // Reset Form Modal
    function resetForm() {
        $('#form-halaman')[0].reset();
        $('#halaman-id').val('');
        $('#hapus_gambar').val('0');
        $('#isi').summernote('reset');
        $('.invalid-feedback').text('');
        $('#form-halaman input, #form-halaman select, .note-editor').removeClass('is-invalid');
        $('#preview-container').addClass('d-none');
        $('#image-preview').attr('src', '');
        $('#btn-submit').prop('disabled', false);
        $('#btn-submit-text').text('Simpan Halaman');
    }

    // 1. Create: Klik Tombol Tambah
    $('#btn-tambah').on('click', function () {
        resetForm();
        $('#modalHalamanTitle').text('Tambah Halaman Baru');
        $('#btn-submit-text').text('Simpan Halaman');
        halamanModal.show();
    });

    // 2. Submit Form (Create / Update via AJAX dengan FormData)
    $('#form-halaman').on('submit', function (e) {
        e.preventDefault();

        $('.invalid-feedback').text('');
        $('#form-halaman input, #form-halaman select, .note-editor').removeClass('is-invalid');

        const id = $('#halaman-id').val();
        const isEdit = id !== '';
        const url = isEdit ? "{{ url('admin/halaman-konten') }}/" + id : "{{ route('admin.halaman-konten.store') }}";

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
                halamanModal.hide();
                table.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Halaman konten berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false,
                    confirmButtonColor: '#16a34a'
                });
            },
            error: function (xhr) {
                $('#btn-submit').prop('disabled', false);
                $('#btn-submit-text').text(isEdit ? 'Perbarui Halaman' : 'Simpan Halaman');

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
                        text: xhr.responseJSON?.message || 'Gagal menyimpan halaman konten.',
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

        $('#modalHalamanTitle').text('Edit Halaman Konten');
        $('#btn-submit-text').text('Perbarui Halaman');

        $.ajax({
            url: "{{ url('admin/halaman-konten') }}/" + id,
            type: 'GET',
            success: function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#halaman-id').val(data.id);
                    $('#judul').val(data.judul);

                    $('#isi').summernote('code', data.isi || '');

                    if (data.gambar_url) {
                        $('#image-preview').attr('src', data.gambar_url);
                        $('#preview-info').text('Gambar saat ini (pilih file baru untuk mengganti)');
                        $('#preview-container').removeClass('d-none');
                    }

                    halamanModal.show();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengambil Data',
                    text: xhr.responseJSON?.message || 'Data halaman tidak ditemukan.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    // 4. View Detail: Klik Tombol Lihat
    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');

        $.ajax({
            url: "{{ url('admin/halaman-konten') }}/" + id,
            type: 'GET',
            success: function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#view-judul').text(data.judul);
                    $('#view-slug').text('/' + data.slug);
                    $('#view-updated').text('Diubah: ' + data.updated_at);

                    // Gambar
                    if (data.gambar_url) {
                        $('#view-gambar').attr('src', data.gambar_url);
                        $('#view-gambar-container').removeClass('d-none');
                    } else {
                        $('#view-gambar-container').addClass('d-none');
                    }

                    // Isi
                    if (data.isi && data.isi !== '<p><br></p>') {
                        $('#view-isi').html(data.isi);
                        $('#view-isi-container').removeClass('d-none');
                    } else {
                        $('#view-isi-container').addClass('d-none');
                    }

                    viewModal.show();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tidak dapat menampilkan rincian halaman.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    // 5. Delete: Klik Tombol Hapus dengan Konfirmasi
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Halaman Ini?',
            text: "Data halaman dan berkas gambar akan dihapus permanen!",
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
                    url: "{{ url('admin/halaman-konten') }}/" + id,
                    type: 'DELETE',
                    success: function (res) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message || 'Halaman konten berhasil dihapus.',
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

