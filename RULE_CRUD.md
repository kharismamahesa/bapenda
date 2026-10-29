# CRUD Laravel + AJAX + Modal + DataTables

Fitur:
- Menampilkan data dengan DataTables (pencarian, pagination, pengurutan).
- Tambah data lewat modal.
- Edit data lewat modal.
- Hapus data dengan konfirmasi.
- Semua proses CRUD menggunakan AJAX tanpa reload halaman.
- Validasi dari Laravel ditampilkan di form.

## Alur fitur

1. **Read:** DataTables meminta data ke endpoint `/pegawai/data` melalui AJAX.
2. **Create:** Tombol Tambah membuka modal. Submit form mengirim `POST /pegawai`.
3. **Edit:** Tombol Edit mengambil data melalui `GET /pegawai/{id}`, lalu submit mengirim `PUT /pegawai/{id}`.
4. **Delete:** Tombol Hapus menampilkan konfirmasi browser. Jika disetujui, AJAX mengirim `DELETE /pegawai/{id}`.
5. **Validasi:** Laravel mengembalikan status `422` beserta pesan validasi; pesan ditampilkan di input yang sesuai.
6. **Refresh tabel:** Setelah operasi sukses, DataTables dimuat ulang tanpa me-refresh seluruh halaman.

## Troubleshooting

- **419 Page Expired:** Pastikan meta `csrf-token` ada di `<head>` dan header `X-CSRF-TOKEN` disetel.
- **422 Validation Error:** Periksa pesan yang ditampilkan pada form, terutama email duplikat atau input kosong.
- **DataTables error / JSON tidak valid:** Pastikan endpoint `/pegawai/data` mengembalikan JSON dengan properti `draw`, `recordsTotal`, `recordsFiltered`, dan `data`. Periksa juga log `storage/logs/laravel.log`.
- **404 saat edit/hapus:** Pastikan route tersedia dan ID yang dikirim benar.
- **CDN tidak termuat:** Pastikan internet tersedia atau unduh aset dan sajikan dari folder publik proyek.

## Catatan keamanan

- Validasi dilakukan di server; validasi HTML di browser bukan pengganti validasi Laravel.
- Route contoh ini belum memakai middleware autentikasi atau policy. Tambahkan proteksi sesuai kebutuhan aplikasi.
- Gunakan HTTPS di production.
- Jangan memasukkan data pengguna langsung ke HTML tanpa escaping. Contoh ini menggunakan `.text()` untuk pesan dan ID numerik pada tombol aksi.