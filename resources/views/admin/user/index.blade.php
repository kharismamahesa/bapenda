@extends('layouts.admin')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Kelola Pengguna Sistem')
@section('page_subtitle', 'Manajemen daftar akun administrator dan operator Bapenda Provinsi Riau')

@section('page_actions')
<button type="button" id="btn-tambah" class="btn btn-green">
    <i class="bi bi-person-plus-fill"></i>
    <span>Tambah Pengguna</span>
</button>
@endsection

@section('content')
<!-- Table Card (Kinerja Styling) -->
<div class="table-custom mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">Daftar Pengguna Sistem</h6>
        <button type="button" id="btn-reload" class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-clockwise"></i> Segarkan Tabel
        </button>
    </div>

    <div class="table-responsive p-3">
        <table id="table-users" class="table table-hover align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama & Akun</th>
                    <th>Email</th>
                    <th class="text-center">Peran (Role)</th>
                    <th class="text-center">Terdaftar Sejak</th>
                    <th class="text-end pe-3" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Diisi otomatis oleh DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Edit Pengguna (Bootstrap 5) -->
<div class="modal fade" id="modal-user" tabindex="-1" aria-labelledby="modalUserTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalUserTitle">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-user" novalidate>
                <input type="hidden" id="user-id" name="id">

                <div class="modal-body">
                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="name">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Administrator Bapenda" required>
                        <div class="invalid-feedback d-block" id="err-name"></div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="email">
                            Alamat Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Contoh: admin@bapenda.go.id" required>
                        <div class="invalid-feedback d-block" id="err-email"></div>
                    </div>

                    <!-- Role / Hak Akses -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="role">
                            Hak Akses (Role) <span class="text-danger">*</span>
                        </label>
                        <select id="role" name="role" class="form-select">
                            <option value="admin">Admin (Pengelola Konten & Layanan)</option>
                            <option value="superadmin">Superadmin (Akses Penuh Sistem)</option>
                        </select>
                        <div class="invalid-feedback d-block" id="err-role"></div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="password">
                            Password <span class="text-danger" id="password-required-mark">*</span>
                        </label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter">
                        <small class="text-muted d-block mt-1" id="password-hint">Minimal 8 karakter.</small>
                        <div class="invalid-feedback d-block" id="err-password"></div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted" for="password_confirmation">
                            Konfirmasi Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password di atas">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-submit" class="btn btn-green">
                        <span id="btn-submit-text">Simpan Pengguna</span>
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

    const userModal = new bootstrap.Modal(document.getElementById('modal-user'));

    // Inisialisasi DataTables Kinerja Styling
    const table = $('#table-users').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.users.data') }}",
            type: "GET",
        },
        columns: [
            { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role', className: 'text-center' },
            { data: 'created_at', name: 'created_at', className: 'text-center text-muted small' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-end pe-3' },
        ],
        language: {
            search: "Cari Pengguna:",
            searchPlaceholder: "Ketik nama atau email...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data pengguna",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Pengguna tidak ditemukan",
            paginate: {
                previous: "‹",
                next: "›"
            }
        }
    });

    // Tombol Reload
    $('#btn-reload').on('click', function () {
        table.ajax.reload(null, false);
    });

    // Reset dan Buka Modal
    function openModal(isEdit = false) {
        $('.invalid-feedback').text('');
        $('input, select').removeClass('is-invalid');
        $('#form-user')[0].reset();
        
        if (isEdit) {
            $('#modalUserTitle').text('Edit Data Pengguna');
            $('#btn-submit-text').text('Simpan Perubahan');
            $('#password-required-mark').addClass('d-none');
            $('#password-hint').text('Kosongkan jika tidak ingin mengubah password.');
        } else {
            $('#user-id').val('');
            $('#modalUserTitle').text('Tambah Pengguna Baru');
            $('#btn-submit-text').text('Simpan Pengguna');
            $('#password-required-mark').removeClass('d-none');
            $('#password-hint').text('Minimal 8 karakter.');
        }

        userModal.show();
    }

    function closeModal() {
        userModal.hide();
    }

    $('#btn-tambah').on('click', function () {
        openModal(false);
    });

    // Tombol Edit
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        openModal(true);

        $.ajax({
            url: "{{ url('admin/users') }}/" + id,
            type: "GET",
            success: function (res) {
                if (res.success) {
                    $('#user-id').val(res.data.id);
                    $('#name').val(res.data.name);
                    $('#email').val(res.data.email);
                    $('#role').val(res.data.role);
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal memuat data pengguna.',
                    confirmButtonColor: '#16a34a'
                });
                closeModal();
            }
        });
    });

    // Submit Form (Tambah / Edit)
    $('#form-user').on('submit', function (e) {
        e.preventDefault();
        
        const id = $('#user-id').val();
        const isEdit = id !== '';
        const url = isEdit ? "{{ url('admin/users') }}/" + id : "{{ route('admin.users.store') }}";
        const method = isEdit ? "PUT" : "POST";

        $('.invalid-feedback').text('');
        $('input, select').removeClass('is-invalid');

        const btnSubmit = $('#btn-submit');
        const btnText = $('#btn-submit-text');
        btnSubmit.prop('disabled', true);
        btnText.text('Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function (res) {
                btnSubmit.prop('disabled', false);
                btnText.text(isEdit ? 'Simpan Perubahan' : 'Simpan Pengguna');

                if (res.success) {
                    closeModal();
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
                btnSubmit.prop('disabled', false);
                btnText.text(isEdit ? 'Simpan Perubahan' : 'Simpan Pengguna');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        $('#' + field).addClass('is-invalid');
                        $('#err-' + field).text(messages[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem saat menyimpan data.',
                        confirmButtonColor: '#16a34a'
                    });
                }
            }
        });
    });

    // Tombol Hapus User
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Pengguna Ini?',
            text: "Akun pengguna yang dihapus tidak akan dapat mengakses panel kembali.",
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
                    url: "{{ url('admin/users') }}/" + id,
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
                            text: xhr.responseJSON?.message || 'Pengguna tidak dapat dihapus.',
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
