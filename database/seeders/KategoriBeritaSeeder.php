<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriBerita;

class KategoriBeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriList = [
            'Berita',
            'Pengumuman',
            'Artikel',
            'Sosialisasi & Edukasi',
            'Pajak Daerah',
            'Pelayanan Publik',
        ];

        foreach ($kategoriList as $nama) {
            KategoriBerita::firstOrCreate(
                ['berita_kategori' => $nama]
            );
        }
    }
}
