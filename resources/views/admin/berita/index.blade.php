@extends('layouts.admin')

@section('title', 'Manajemen Berita')
@section('page_title', 'Kelola Publikasi & Berita')
@section('page_subtitle', 'Publikasi artikel, siaran pers, dan pengumuman resmi Bapenda')

@section('page_actions')
<button type="button" id="btn-tambah"
    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover transition-all duration-200 cursor-pointer shadow-sm">
    <i data-lucide="plus" class="w-4 h-4"></i>
    <span>Tambah Berita Baru</span>
</button>
@endsection

@section('content')
<!-- Filter Box (Gaya FILE_HTML_ACUAN.html) -->
<div class="bg-muted rounded-card p-4 mb-6">
    <h3 class="text-foreground text-sm font-bold uppercase tracking-wider mb-3">Filter Berita</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kategori</label>
            <select id="filter-kategori" class="w-full px-4 py-3 bg-white border border-border rounded-button text-foreground text-sm focus:border-primary focus:outline-none transition-all duration-200">
                <option value="all">Semua Kategori</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id_berita_kategori }}">{{ $kat->berita_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
            <select id="filter-status" class="w-full px-4 py-3 bg-white border border-border rounded-button text-foreground text-sm focus:border-primary focus:outline-none transition-all duration-200">
                <option value="all">Semua Status</option>
                <option value="1">Published</option>
                <option value="0">Draft</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="button" id="btn-reset-filter"
                class="w-full px-4 py-3 bg-white border border-border rounded-button text-gray-600 font-medium hover:border-primary cursor-pointer transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset Filter
            </button>
        </div>
    </div>
</div>

<!-- Table Card (Gaya FILE_HTML_ACUAN.html) -->
<div class="bg-muted rounded-card pt-5 px-3 pb-3">
    <div class="flex items-center justify-between mb-4 px-3">
        <h3 class="text-foreground text-lg font-bold">Daftar Berita</h3>
        <button type="button" id="btn-reload"
            class="flex items-center gap-2 px-3 py-1.5 bg-white border border-border rounded-lg text-xs font-medium hover:border-primary cursor-pointer transition-all">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Segarkan Tabel
        </button>
    </div>

    <div class="bg-white rounded-card overflow-hidden p-2">
        <div class="overflow-x-auto">
            <table id="table-berita" class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-center text-gray-500 text-sm font-medium px-4 py-4 w-12">No</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4">Berita</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4">Kategori</th>
                        <th class="text-left text-gray-500 text-sm font-medium px-6 py-4">Tanggal</th>
                        <th class="text-center text-gray-500 text-sm font-medium px-4 py-4">Dibaca</th>
                        <th class="text-center text-gray-500 text-sm font-medium px-4 py-4">Status</th>
                        <th class="text-right text-gray-500 text-sm font-medium px-6 py-4 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cropsTableBody">
                    <!-- Diisi otomatis oleh DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Tambah / Edit Berita -->
<div id="modal-form" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-card p-6 max-w-3xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100">
            <h3 id="modal-title" class="text-foreground text-xl font-bold">Tambah Berita</h3>
            <button type="button" class="btn-close-modal p-2 hover:bg-gray-100 rounded-full cursor-pointer transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>

        <form id="form-berita" enctype="multipart/form-data" novalidate>
            <input type="hidden" id="berita-id" name="id">

            <div class="space-y-4">
                <!-- Judul -->
                <div class="space-y-1.5">
                    <label class="block text-foreground text-sm font-semibold" for="judul">
                        Judul Berita <span class="text-error">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" placeholder="Masukkan judul berita lengkap"
                        class="w-full px-4 py-2.5 border border-border rounded-button text-foreground focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all">
                    <p class="error-feedback text-error text-xs mt-1 hidden" id="err-judul"></p>
                </div>

                <!-- Kategori & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-foreground text-sm font-semibold" for="id_berita_kategori">
                            Kategori Berita <span class="text-error">*</span>
                        </label>
                        <select id="id_berita_kategori" name="id_berita_kategori"
                            class="w-full px-4 py-2.5 border border-border rounded-button text-foreground focus:border-primary focus:outline-none transition-all">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id_berita_kategori }}">{{ $kat->berita_kategori }}</option>
                            @endforeach
                        </select>
                        <p class="error-feedback text-error text-xs mt-1 hidden" id="err-id_berita_kategori"></p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-foreground text-sm font-semibold" for="status">
                            Status Publikasi <span class="text-error">*</span>
                        </label>
                        <select id="status" name="status"
                            class="w-full px-4 py-2.5 border border-border rounded-button text-foreground focus:border-primary focus:outline-none transition-all">
                            <option value="1">Publish (Tampilkan)</option>
                            <option value="0">Draft (Simpan Sementara)</option>
                        </select>
                        <p class="error-feedback text-error text-xs mt-1 hidden" id="err-status"></p>
                    </div>
                </div>

                <!-- Gambar / Sampul -->
                <div class="space-y-1.5">
                    <label class="block text-foreground text-sm font-semibold" for="gambar">
                        Gambar Sampul / Thumbnail <span class="text-xs text-gray-500 font-normal">(Maks. 2MB, JPG/PNG/WebP)</span>
                    </label>
                    <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="w-full px-3 py-2 border border-border rounded-button text-foreground text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="error-feedback text-error text-xs mt-1 hidden" id="err-gambar"></p>
                    
                    <!-- Preview Container -->
                    <div id="preview-container" class="hidden mt-2 flex items-center gap-3 p-2 bg-gray-50 rounded-xl border border-gray-100">
                        <img id="image-preview" src="" alt="Preview" class="w-16 h-16 object-cover rounded-lg border border-border">
                        <div class="text-xs text-gray-500">
                            <p class="font-medium text-foreground">Preview Gambar</p>
                            <span id="preview-info">Gambar saat ini / dipilih</span>
                        </div>
                    </div>
                </div>

                <!-- Konten dengan Summernote Lite (Fitur Terbatas) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-foreground text-sm font-semibold" for="isi">
                            Isi Berita Lengkap <span class="text-error">*</span>
                        </label>
                        <span class="text-xs text-gray-400">Editor: Bold, Italic, Underline, Link, Paragraf</span>
                    </div>
                    <textarea id="isi" name="isi"></textarea>
                    <p class="error-feedback text-error text-xs mt-1 hidden" id="err-isi"></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 mt-4 border-t border-gray-100">
                <button type="button" class="btn-close-modal flex-1 px-4 py-2.5 border border-border rounded-button text-foreground font-medium hover:border-primary cursor-pointer transition-all">
                    Batal
                </button>
                <button type="submit" id="btn-submit"
                    class="flex-1 px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover cursor-pointer transition-all flex items-center justify-center gap-2">
                    <span id="btn-submit-text">Simpan Berita</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal View Detail Berita (Gaya FILE_HTML_ACUAN.html) -->
<div id="modal-view" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-card p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <h3 class="text-foreground text-xl font-bold">Rincian Berita</h3>
            <button type="button" class="btn-close-view p-2 hover:bg-gray-100 rounded-full cursor-pointer transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>

        <div class="space-y-4">
            <img id="view-gambar" src="" alt="Thumbnail" class="w-full h-64 object-cover rounded-xl border border-gray-100">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span id="view-kategori" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100"></span>
                    <span id="view-status" class="px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                    <span id="view-tanggal" class="text-xs text-gray-500 ml-auto"></span>
                </div>
                <h2 id="view-judul" class="text-foreground text-xl font-bold mb-2"></h2>
                <p class="text-xs text-gray-400 mb-4">Total Tayangan: <span id="view-counter" class="font-semibold text-foreground">0</span> kali</p>
            </div>
            <div id="view-isi" class="text-gray-700 text-sm leading-relaxed p-4 bg-gray-50 rounded-xl max-h-72 overflow-y-auto border border-gray-100 prose max-w-none">
            </div>
            <div class="pt-2">
                <button type="button" class="btn-close-view w-full px-4 py-2.5 bg-primary text-white rounded-button font-medium hover:bg-primary-hover cursor-pointer transition-all">
                    Tutup Rincian
                </button>
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

    // Inisialisasi Summernote Lite dengan Fitur Terbatas
    // Sesuai permintaan: hanya bold, italic, underline, url (link), paragraf (+ undo/redo)
    $('#isi').summernote({
        placeholder: 'Tuliskan isi berita di sini...',
        height: 220,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['paragraph']],
            ['insert', ['link']],
            ['view', ['undo', 'redo']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                // Hapus highlight error saat pengguna mengetik
                if ($('#isi').summernote('isEmpty') === false) {
                    $('.note-editor').removeClass('border-error');
                    $('#err-isi').text('').addClass('hidden');
                }
            }
        }
    });

    // Inisialisasi DataTables dengan Desain FILE_HTML_ACUAN.html
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
                    text: 'Terjadi kesalahan saat memuat data berita.'
                });
            }
        },
        order: [[3, 'desc']], // Default: urutkan berdasarkan data tanggal berita terakhir (terbaru)
        columns: [
            { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'judul', name: 'judul' },
            { data: 'kategori', name: 'id_berita_kategori' },
            { data: 'tanggal', name: 'tanggal_berita' },
            { data: 'views', name: 'counter', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-right' }
        ],
        drawCallback: function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },
        language: {
            search: "Cari Berita:",
            searchPlaceholder: "Ketik judul / kata kunci...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data berita yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ berita",
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
                $('#preview-container').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Reset Form Modal
    function resetForm() {
        $('#form-berita')[0].reset();
        $('#berita-id').val('');
        $('#isi').summernote('reset');
        $('.error-feedback').text('').addClass('hidden');
        $('#form-berita input, #form-berita select, .note-editor').removeClass('border-error');
        $('#preview-container').addClass('hidden');
        $('#image-preview').attr('src', '');
        $('#btn-submit').prop('disabled', false);
        $('#btn-submit-text').text('Simpan Berita');
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
        $('#modal-title').text('Tambah Berita Baru');
        $('#btn-submit-text').text('Simpan Berita');
        openModalForm();
    });

    // 2. Submit Form (Create / Update via AJAX dengan FormData)
    $('#form-berita').on('submit', function (e) {
        e.preventDefault();

        // Reset error feedback
        $('.error-feedback').text('').addClass('hidden');
        $('#form-berita input, #form-berita select, .note-editor').removeClass('border-error');

        const id = $('#berita-id').val();
        const isEdit = id !== '';
        const url = isEdit ? "{{ url('admin/berita') }}/" + id : "{{ route('admin.berita.store') }}";

        const formData = new FormData(this);

        // Ambil konten HTML dari Summernote
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
                closeModalForm();
                table.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Berita berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                $('#btn-submit').prop('disabled', false);
                $('#btn-submit-text').text(isEdit ? 'Perbarui Berita' : 'Simpan Berita');

                // 422: Validasi Error dari Laravel
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function (field, msgs) {
                            if (field === 'isi') {
                                $('.note-editor').addClass('border-error');
                                $('#err-isi').text(msgs[0]).removeClass('hidden');
                            } else {
                                $('#' + field).addClass('border-error');
                                $('#err-' + field).text(msgs[0]).removeClass('hidden');
                            }
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Gagal menyimpan berita.'
                    });
                }
            }
        });
    });

    // 3. Edit: Klik Tombol Edit
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        resetForm();

        $('#modal-title').text('Edit Berita');
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
                    
                    // Set konten ke Summernote
                    $('#isi').summernote('code', data.isi || '');

                    if (data.gambar_url) {
                        $('#image-preview').attr('src', data.gambar_url);
                        $('#preview-info').text('Gambar saat ini (pilih file baru jika ingin mengganti)');
                        $('#preview-container').removeClass('hidden');
                    }

                    openModalForm();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengambil Data',
                    text: xhr.responseJSON?.message || 'Data berita tidak ditemukan.'
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
                        $('#view-status').text('Published').attr('class', 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800');
                    } else {
                        $('#view-status').text('Draft').attr('class', 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700');
                    }

                    $('#modal-view').removeClass('hidden');
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tidak dapat menampilkan rincian berita.'
                });
            }
        });
    });

    $('.btn-close-view').on('click', function () {
        $('#modal-view').addClass('hidden');
    });

    // 5. Delete: Klik Tombol Hapus dengan Konfirmasi
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Berita Ini?',
            text: "Data dan berkas gambar berita akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
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
