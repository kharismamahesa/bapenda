@extends('layouts.admin')

@section('title', 'Kategori Regulasi')
@section('page_title', 'Kelola Kategori Regulasi')
@section('page_subtitle', 'Manajemen daftar kategori untuk pengelompokan dokumen regulasi')

@section('page_actions')
<button type="button" id="btn-tambah" class="btn btn-green">
    <i class="bi bi-plus-lg"></i>
    <span>Tambah Kategori</span>
</button>
@endsection

@section('content')
<div class="table-custom mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">Data Kategori Regulasi</h6>
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
                    <th class="text-center" style="width: 180px;">Jumlah Regulasi</th>
                    <th class="text-end pe-3" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Diisi otomatis oleh DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Edit Kategori -->
<div class="modal fade" id="modal-kategori" tabindex="-1" aria-labelledby="modalKategoriTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalKategoriTitle">Tambah Kategori Regulasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-kategori" novalidate>
                <input type="hidden" id="kategori-id" name="id">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="regulasi_kategori">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="regulasi_kategori" name="regulasi_kategori" class="form-control" placeholder="Contoh: Peraturan Daerah" required>
                        <div class="invalid-feedback d-block" id="err-regulasi_kategori"></div>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const katModal = new bootstrap.Modal(document.getElementById('modal-kategori'));

    const table = $('#table-kategori').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.kategori-regulasi.data') }}",
            type: "GET",
            error: function (xhr) {
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
            { data: 'regulasi_kategori', name: 'regulasi_kategori' },
            { data: 'total_regulasi', name: 'total_regulasi', className: 'text-center' },
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

    $('#btn-reload').on('click', function () {
        table.ajax.reload(null, false);
    });

    function resetForm() {
        $('#form-kategori')[0].reset();
        $('#kategori-id').val('');
        $('.invalid-feedback').text('');
        $('#regulasi_kategori').removeClass('is-invalid');
        $('#btn-submit').prop('disabled', false);
        $('#btn-submit-text').text('Simpan');
    }

    $('#btn-tambah').on('click', function () {
        resetForm();
        $('#modalKategoriTitle').text('Tambah Kategori Regulasi');
        $('#btn-submit-text').text('Simpan');
        katModal.show();
    });

    $('#form-kategori').on('submit', function (e) {
        e.preventDefault();

        $('.invalid-feedback').text('');
        $('#regulasi_kategori').removeClass('is-invalid');

        const id = $('#kategori-id').val();
        const isEdit = id !== '';
        const url = isEdit
            ? "{{ url('admin/kategori-regulasi') }}/" + id
            : "{{ route('admin.kategori-regulasi.store') }}";
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

                if (res.status === 'success') {
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
                    if (errors && errors.regulasi_kategori) {
                        $('#regulasi_kategori').addClass('is-invalid');
                        $('#err-regulasi_kategori').text(errors.regulasi_kategori[0]);
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

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        resetForm();

        $.ajax({
            url: "{{ url('admin/kategori-regulasi') }}/" + id,
            type: "GET",
            success: function (res) {
                if (res.status === 'success') {
                    $('#kategori-id').val(res.data.id_regulasi_kategori);
                    $('#regulasi_kategori').val(res.data.regulasi_kategori);
                    $('#modalKategoriTitle').text('Edit Kategori Regulasi');
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
                    url: "{{ url('admin/kategori-regulasi') }}/" + id,
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
