@extends('layouts.admin')

@section('title', 'Regulasi')
@section('page_title', 'Kelola Regulasi')
@section('page_subtitle', 'Manajemen dokumen peraturan dan kebijakan pendapatan daerah')

@section('page_actions')
<button type="button" id="btn-tambah" class="btn btn-green">
    <i class="bi bi-plus-lg"></i>
    <span>Tambah Regulasi</span>
</button>
@endsection

@section('content')
<div class="table-custom mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="fw-bold mb-0 text-dark">Data Regulasi</h6>
        <div class="d-flex align-items-center gap-2">
            <select id="filter-kategori" class="form-select form-select-sm" style="min-width: 210px;">
                <option value="all">Semua Kategori</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori->id_regulasi_kategori }}">{{ $kategori->regulasi_kategori }}</option>
                @endforeach
            </select>
            <button type="button" id="btn-reload" class="btn btn-sm btn-outline-custom">
                <i class="bi bi-arrow-clockwise"></i> Segarkan
            </button>
        </div>
    </div>

    <div class="table-responsive p-3">
        <table id="table-regulasi" class="table table-hover align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">No</th>
                    <th>Regulasi</th>
                    <th style="width: 200px;">Kategori</th>
                    <th class="text-center" style="width: 140px;">File</th>
                    <th style="width: 160px;">Tanggal</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Diisi otomatis oleh DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Edit Regulasi -->
<div class="modal fade" id="modal-regulasi" tabindex="-1" aria-labelledby="modalRegulasiTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalRegulasiTitle">Tambah Regulasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-regulasi" novalidate>
                <input type="hidden" id="regulasi-id" name="id">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted" for="regulasi">
                                Nama / Judul Regulasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="regulasi" name="regulasi" class="form-control" placeholder="Contoh: Perda No. 12 Tahun 2024" required>
                            <div class="invalid-feedback d-block" id="err-regulasi"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="id_regulasi_kategori">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select id="id_regulasi_kategori" name="id_regulasi_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriList as $kategori)
                                    <option value="{{ $kategori->id_regulasi_kategori }}">{{ $kategori->regulasi_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="err-id_regulasi_kategori"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted" for="file">File Regulasi</label>
                            <input type="file" id="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <small class="text-muted">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Maksimal 10MB.</small>
                            <div class="invalid-feedback d-block" id="err-file"></div>
                            <div id="file-lama" class="mt-2 d-none">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-paperclip"></i>
                                    <span id="file-lama-nama"></span>
                                </span>
                                <a href="#" id="file-lama-link" target="_blank" class="small ms-2">Lihat file</a>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted" for="keterangan">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" class="form-control" rows="4" placeholder="Keterangan singkat mengenai regulasi ini"></textarea>
                            <div class="invalid-feedback d-block" id="err-keterangan"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-submit" class="btn btn-green">
                        <span id="btn-submit-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Regulasi -->
<div class="modal fade" id="modal-detail" tabindex="-1" aria-labelledby="modalDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalDetailTitle">Detail Regulasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="detail-regulasi" class="fw-bold mb-3"></h6>
                <div class="mb-2">
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle" id="detail-kategori"></span>
                </div>
                <p class="text-muted small mb-3" id="detail-tanggal"></p>
                <p class="mb-3" id="detail-keterangan"></p>
                <a href="#" id="detail-file" target="_blank" class="btn btn-sm btn-outline-custom d-none">
                    <i class="bi bi-download"></i> Unduh File
                </a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const regulasiModal = new bootstrap.Modal(document.getElementById('modal-regulasi'));
    const detailModal = new bootstrap.Modal(document.getElementById('modal-detail'));

    const table = $('#table-regulasi').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.regulasi.data') }}",
            type: "GET",
            data: function (d) {
                d.kategori_filter = $('#filter-kategori').val();
            },
            error: function (xhr) {
                console.error("DataTables Error:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat data regulasi.',
                    confirmButtonColor: '#16a34a'
                });
            }
        },
        order: [],
        columns: [
            { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'regulasi', name: 'regulasi' },
            { data: 'kategori', name: 'kategori', orderable: false },
            { data: 'file', name: 'file', orderable: false, searchable: false, className: 'text-center' },
            { data: 'tanggal', name: 'tanggal', orderable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            search: "Cari Regulasi:",
            searchPlaceholder: "Ketik nama atau keterangan...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data regulasi yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                previous: "‹",
                next: "›"
            }
        }
    });

    $('#filter-kategori').on('change', function () {
        table.ajax.reload();
    });

    $('#btn-reload').on('click', function () {
        table.ajax.reload(null, false);
    });

    function resetForm() {
        $('#form-regulasi')[0].reset();
        $('#regulasi-id').val('');
        $('.invalid-feedback').text('');
        $('#regulasi, #id_regulasi_kategori, #file, #keterangan').removeClass('is-invalid');
        $('#file-lama').addClass('d-none');
        $('#btn-submit').prop('disabled', false);
        $('#btn-submit-text').text('Simpan');
    }

    $('#btn-tambah').on('click', function () {
        resetForm();
        $('#modalRegulasiTitle').text('Tambah Regulasi');
        $('#btn-submit-text').text('Simpan');
        regulasiModal.show();
    });

    $('#form-regulasi').on('submit', function (e) {
        e.preventDefault();

        $('.invalid-feedback').text('');
        $('#regulasi, #id_regulasi_kategori, #file, #keterangan').removeClass('is-invalid');

        const id = $('#regulasi-id').val();
        const isEdit = id !== '';
        const url = isEdit
            ? "{{ url('admin/regulasi') }}/" + id
            : "{{ route('admin.regulasi.store') }}";

        const formData = new FormData(this);
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
                $('#btn-submit').prop('disabled', false);
                $('#btn-submit-text').text('Simpan');

                if (res.status === 'success') {
                    regulasiModal.hide();
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false,
                        confirmButtonColor: '#16a34a'
                    });
                }
            },
            error: function (xhr) {
                $('#btn-submit').prop('disabled', false);
                $('#btn-submit-text').text('Simpan');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    Object.keys(errors).forEach(function (field) {
                        const input = $('#' + field);
                        input.addClass('is-invalid');
                        $('#err-' + field).text(errors[field][0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan pada sistem.',
                        confirmButtonColor: '#16a34a'
                    });
                }
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        resetForm();

        $.ajax({
            url: "{{ url('admin/regulasi') }}/" + id,
            type: "GET",
            success: function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#regulasi-id').val(data.id_regulasi);
                    $('#regulasi').val(data.regulasi);
                    $('#id_regulasi_kategori').val(data.id_regulasi_kategori);
                    $('#keterangan').val(data.keterangan);

                    if (data.file_url) {
                        $('#file-lama').removeClass('d-none');
                        $('#file-lama-nama').text(data.file_name);
                        $('#file-lama-link').attr('href', data.file_url);
                    }

                    $('#modalRegulasiTitle').text('Edit Regulasi');
                    $('#btn-submit-text').text('Simpan Perubahan');
                    regulasiModal.show();
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil data regulasi.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');

        $.ajax({
            url: "{{ url('admin/regulasi') }}/" + id,
            type: "GET",
            success: function (res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#detail-regulasi').text(data.regulasi);
                    $('#detail-kategori').text(data.kategori_nama);
                    $('#detail-tanggal').html('<i class="bi bi-calendar3"></i> ' + data.created_datetime);
                    $('#detail-keterangan').text(data.keterangan || 'Tidak ada keterangan.');

                    if (data.file_url) {
                        $('#detail-file').removeClass('d-none').attr('href', data.file_url);
                    } else {
                        $('#detail-file').addClass('d-none').attr('href', '#');
                    }

                    detailModal.show();
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil detail regulasi.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Regulasi Ini?',
            text: "Regulasi yang dihapus tidak dapat dipulihkan.",
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
                    url: "{{ url('admin/regulasi') }}/" + id,
                    type: "DELETE",
                    success: function (res) {
                        if (res.status === 'success') {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: xhr.responseJSON?.message || 'Regulasi tidak dapat dihapus.',
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
