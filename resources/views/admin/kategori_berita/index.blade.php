@extends('layouts.admin')

@section('title', 'Kategori Berita')
@section('page_title', 'Kelola Kategori Berita')
@section('page_subtitle', 'Manajemen daftar kategori untuk pengelompokan berita dan publikasi')

@section('page_actions')
<button type="button" id="btn-tambah"
    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer shadow-sm">
    <i data-lucide="plus" class="w-4 h-4"></i>
    <span>Tambah Kategori</span>
</button>
@endsection

@section('content')
<!-- Table Card (Gaya FILE_HTML_ACUAN.html) -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex items-center justify-between mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">Data Kategori Berita</h3>
        <button type="button" id="btn-reload"
            class="flex items-center gap-2 px-3 py-1.5 bg-white border border-border rounded-lg text-xs font-medium hover:border-primary cursor-pointer transition-all">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Segarkan Tabel
        </button>
    </div>

    <div class="bg-white rounded-card overflow-hidden p-2">
        <div class="overflow-x-auto">
            <table id="table-kategori" class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-center text-gray-500 text-sm font-medium px-4 py-4 w-16">No</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4">Nama Kategori</th>
                        <th class="text-center text-gray-500 text-sm font-medium px-6 py-4">Jumlah Berita</th>
                        <th class="text-right text-gray-500 text-sm font-medium px-6 py-4 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Diisi otomatis oleh DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Kategori -->
<div id="modal-form" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-card p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100">
            <h3 id="modal-title" class="text-foreground text-xl font-bold">Tambah Kategori Berita</h3>
            <button type="button" class="btn-close-modal p-2 hover:bg-gray-100 rounded-full cursor-pointer transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>

        <form id="form-kategori" novalidate>
            <input type="hidden" id="kategori-id" name="id">

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-foreground text-sm font-semibold" for="berita_kategori">
                        Nama Kategori <span class="text-error">*</span>
                    </label>
                    <input type="text" id="berita_kategori" name="berita_kategori" placeholder="Contoh: Pajak Daerah"
                        class="w-full px-4 py-2.5 border border-border rounded-button text-foreground placeholder:text-gray-400 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all">
                    <p class="error-feedback text-error text-xs mt-1 hidden" id="err-berita_kategori"></p>
                </div>
            </div>

            <div class="flex gap-3 pt-6 mt-4 border-t border-gray-100">
                <button type="button" class="btn-close-modal flex-1 px-4 py-2.5 border border-border rounded-button text-foreground font-medium hover:border-primary cursor-pointer transition-all">
                    Batal
                </button>
                <button type="submit" id="btn-submit"
                    class="flex-1 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover cursor-pointer transition-all flex items-center justify-center gap-2">
                    <span id="btn-submit-text">Simpan</span>
                </button>
            </div>
        </form>
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

    // Inisialisasi DataTables Desain FILE_HTML_ACUAN.html
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
                    text: 'Terjadi kesalahan saat memuat data kategori.'
                });
            }
        },
        order: [], // Default: mengikuti urutan backend (data kategori terakhir / terbaru)
        columns: [
            { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'berita_kategori', name: 'berita_kategori' },
            { data: 'total_berita', name: 'total_berita', className: 'text-center' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-right' }
        ],
        drawCallback: function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },
        language: {
            search: "Cari Kategori:",
            searchPlaceholder: "Ketik nama kategori...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data kategori yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "→",
                previous: "←"
            }
        }
    });

    // Refresh Table
    $('#btn-reload').on('click', function () {
        table.ajax.reload(null, false);
    });

    // Reset Form & Error Messages
    function resetForm() {
        $('#form-kategori')[0].reset();
        $('#kategori-id').val('');
        $('.error-feedback').text('').addClass('hidden');
        $('#berita_kategori').removeClass('border-error');
        $('#btn-submit').prop('disabled', false);
        $('#btn-submit-text').text('Simpan');
    }

    function openModalForm() {
        $('#modal-form').removeClass('hidden');
    }

    function closeModalForm() {
        $('#modal-form').addClass('hidden');
        resetForm();
    }

    $('.btn-close-modal').on('click', closeModalForm);

    // 1. Create: Klik Tombol Tambah
    $('#btn-tambah').on('click', function () {
        resetForm();
        $('#modal-title').text('Tambah Kategori Berita');
        $('#btn-submit-text').text('Simpan');
        openModalForm();
    });

    // 2. Submit Form
    $('#form-kategori').on('submit', function (e) {
        e.preventDefault();

        $('.error-feedback').text('').addClass('hidden');
        $('#berita_kategori').removeClass('border-error');

        const id = $('#kategori-id').val();
        const isEdit = id !== '';
        const url = isEdit ? "{{ url('admin/kategori-berita') }}/" + id : "{{ route('admin.kategori-berita.store') }}";
        const method = isEdit ? 'PUT' : 'POST';

        $('#btn-submit').prop('disabled', true);
        $('#btn-submit-text').text('Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: {
                berita_kategori: $('#berita_kategori').val()
            },
            success: function (res) {
                closeModalForm();
                table.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Data berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                $('#btn-submit').prop('disabled', false);
                $('#btn-submit-text').text('Simpan');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors && errors.berita_kategori) {
                        $('#berita_kategori').addClass('border-error');
                        $('#err-berita_kategori').text(errors.berita_kategori[0]).removeClass('hidden');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Gagal menyimpan data.'
                    });
                }
            }
        });
    });

    // 3. Edit: Klik Tombol Edit
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        resetForm();

        $('#modal-title').text('Edit Kategori Berita');
        $('#btn-submit-text').text('Perbarui');

        $.ajax({
            url: "{{ url('admin/kategori-berita') }}/" + id,
            type: 'GET',
            success: function (res) {
                if (res.status === 'success') {
                    $('#kategori-id').val(res.data.id_berita_kategori);
                    $('#berita_kategori').val(res.data.berita_kategori);
                    openModalForm();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengambil Data',
                    text: xhr.responseJSON?.message || 'Data tidak ditemukan.'
                });
            }
        });
    });

    // 4. Delete: Klik Tombol Hapus dengan Konfirmasi
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Kategori Ini?',
            text: "Kategori yang dihapus tidak dapat dipulihkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/kategori-berita') }}/" + id,
                    type: 'DELETE',
                    success: function (res) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message || 'Kategori berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
