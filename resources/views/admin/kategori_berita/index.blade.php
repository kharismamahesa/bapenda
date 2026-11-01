@extends('layouts.admin')

@section('title', 'Kategori Berita')
@section('page_title', 'Kelola Kategori Berita')
@section('page_subtitle', 'Manajemen daftar kategori untuk pengelompokan berita dan publikasi')

@section('page_actions')
<button type="button" id="btn-tambah" class="btn btn-green">
    <i class="bi bi-plus-lg"></i>
    <span>Tambah Kategori</span>
</button>
@endsection

@section('content')
<!-- Table Card (Kinerja Styling) -->
<div class="table-custom mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">Data Kategori Berita</h6>
        <button type="button" id="btn-reload" class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-clockwise"></i> Segarkan Tabel
        </button>
    </div>

    <div class="table-responsive p-3">
        <table id="table-kategori" class="table table-hover align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">No</th>
                    <th>Nama Kategori</th>
                    <th class="text-center" style="width: 180px;">Jumlah Berita</th>
                    <th class="text-end pe-3" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Diisi otomatis oleh DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Edit Kategori (Bootstrap 5) -->
<div class="modal fade" id="modal-kategori" tabindex="-1" aria-labelledby="modalKategoriTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalKategoriTitle">Tambah Kategori Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-kategori" novalidate>
                <input type="hidden" id="kategori-id" name="id">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="berita_kategori">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="berita_kategori" name="berita_kategori" class="form-control" placeholder="Contoh: Pajak Daerah" required>
                        <div class="invalid-feedback d-block" id="err-berita_kategori"></div>
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

    const katModal = new bootstrap.Modal(document.getElementById('modal-kategori'));

    // Inisialisasi DataTables Kinerja Styling
    const table = $('#table-kategori').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.kategori-berita.data') }}",
            type: "GET",
            error: function (xhr, error, code) {
                console.error("DataTables Error:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat data kategori.',
                    confirmButtonColor: '#16a34a'
                });
            }
        },
        order: [],
        columns: [
            { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'berita_kategori', name: 'berita_kategori' },
            { data: 'total_berita', name: 'total_berita', className: 'text-center' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end pe-3' }
        ],
        language: {
            search: "Cari Kategori:",
            searchPlaceholder: "Ketik nama kategori...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data kategori yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                previous: "‹",
                next: "›"
            }
        }
    });

    // Refresh Table
    $('#btn-reload').on('click', function () {
        table.ajax.reload(null, false);
    });

    // Reset Form
    function resetForm() {
        $('#form-kategori')[0].reset();
        $('#kategori-id').val('');
        $('.invalid-feedback').text('');
        $('#berita_kategori').removeClass('is-invalid');
        $('#btn-submit').prop('disabled', false);
        $('#btn-submit-text').text('Simpan');
    }

    // 1. Create: Klik Tombol Tambah
    $('#btn-tambah').on('click', function () {
        resetForm();
        $('#modalKategoriTitle').text('Tambah Kategori Berita');
        $('#btn-submit-text').text('Simpan');
        katModal.show();
    });

    // 2. Submit Form
    $('#form-kategori').on('submit', function (e) {
        e.preventDefault();

        $('.invalid-feedback').text('');
        $('#berita_kategori').removeClass('is-invalid');

        const id = $('#kategori-id').val();
        const isEdit = id !== '';
        const url = isEdit
            ? "{{ url('admin/kategori-berita') }}/" + id
            : "{{ route('admin.kategori-berita.store') }}";
        const method = isEdit ? "PUT" : "POST";

        $('#btn-submit').prop('disabled', true);
        $('#btn-submit-text').text('Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function (res) {
                $('#btn-submit').prop('disabled', false);
                $('#btn-submit-text').text('Simpan');

                if (res.success) {
                    katModal.hide();
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
                    const errors = xhr.responseJSON.errors;
                    if (errors && errors.berita_kategori) {
                        $('#berita_kategori').addClass('is-invalid');
                        $('#err-berita_kategori').text(errors.berita_kategori[0]);
                    }
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

    // 3. Edit Kategori
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        resetForm();

        $.ajax({
            url: "{{ url('admin/kategori-berita') }}/" + id,
            type: "GET",
            success: function (res) {
                if (res.success) {
                    $('#kategori-id').val(res.data.id_berita_kategori);
                    $('#berita_kategori').val(res.data.berita_kategori);
                    $('#modalKategoriTitle').text('Edit Kategori Berita');
                    $('#btn-submit-text').text('Simpan Perubahan');
                    katModal.show();
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil data kategori.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    // 4. Delete Kategori
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Kategori Ini?',
            text: "Kategori yang dihapus tidak dapat dipulihkan.",
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
                    url: "{{ url('admin/kategori-berita') }}/" + id,
                    type: "DELETE",
                    success: function (res) {
                        if (res.success) {
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
                            text: xhr.responseJSON?.message || 'Kategori tidak dapat dihapus.',
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
