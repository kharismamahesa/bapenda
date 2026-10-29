<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\KategoriBerita;
use App\Models\TUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = TUser::first();
        $kategori = KategoriBerita::first();

        // Jika belum ada berita di database, tambahkan data contoh
        if (Berita::count() == 0) {
            $beritaList = [
                [
                    'id_berita_kategori' => $kategori ? $kategori->id_berita_kategori : 1,
                    'judul' => 'Bapenda Berlakukan Pembebasan Denda Pajak Daerah Tahun 2026',
                    'isi' => '<p>Badan Pendapatan Daerah mengumumkan kebijakan terbaru mengenai pembebasan denda keterlambatan pembayaran Pajak Bumi dan Bangunan (PBB-P2) serta Pajak Kendaraan Bermotor.</p>',
                    'tanggal_berita' => now(),
                    'counter' => 125,
                    'url' => Str::slug('Bapenda Berlakukan Pembebasan Denda Pajak Daerah Tahun 2026'),
                    'status' => 1,
                    'created_datetime' => now(),
                    'created_by' => $user ? $user->id : 1,
                ],
                [
                    'id_berita_kategori' => $kategori ? $kategori->id_berita_kategori : 1,
                    'judul' => 'Inovasi Pembayaran Pajak Kini Bisa Lewat QRIS dan Mobile Banking',
                    'isi' => '<p>Memudahkan wajib pajak, kini pembayaran tagihan retribusi dan pajak daerah dapat dilakukan secara instan lewat QRIS melalui mobile banking perbankan nasional.</p>',
                    'tanggal_berita' => now(),
                    'counter' => 84,
                    'url' => Str::slug('Inovasi Pembayaran Pajak Kini Bisa Lewat QRIS dan Mobile Banking'),
                    'status' => 1,
                    'created_datetime' => now(),
                    'created_by' => $user ? $user->id : 1,
                ],
            ];

            foreach ($beritaList as $item) {
                Berita::create($item);
            }
        }
    }
}
