<?php

namespace Database\Seeders;

use App\Models\HalamanKonten;
use Illuminate\Database\Seeder;

class HalamanKontenSeeder extends Seeder
{
    public function run(): void
    {
        $halaman = [
            [
                'judul' => 'Profil',
                'slug' => 'profil',
                'isi' => '<h4>Profil Badan Pendapatan Daerah Provinsi Riau</h4>
<p>Badan Pendapatan Daerah (Bapenda) Provinsi Riau merupakan unsur penunjang urusan pemerintahan di bidang keuangan, khususnya pengelolaan pendapatan daerah. Bapenda Provinsi Riau dibentuk berdasarkan Peraturan Daerah Provinsi Riau dan bertanggung jawab langsung kepada Gubernur Riau melalui Sekretaris Daerah.</p>
<p>Sebagai institusi yang mengemban amanah dalam pengelolaan pendapatan daerah, Bapenda Provinsi Riau terus berkomitmen untuk meningkatkan penerimaan daerah secara optimal melalui intensifikasi dan ekstensifikasi sumber-sumber pendapatan daerah.</p>',
                'gambar' => null,
            ],
            [
                'judul' => 'Sejarah',
                'slug' => 'sejarah',
                'isi' => '<h4>Sejarah Badan Pendapatan Daerah Provinsi Riau</h4>
<p>Badan Pendapatan Daerah Provinsi Riau memiliki sejarah panjang dalam pengelolaan pendapatan daerah di Provinsi Riau. Awalnya bernama Dinas Pendapatan Daerah (Dispenda) yang kemudian bertransformasi menjadi Badan Pendapatan Daerah (Bapenda) seiring dengan perkembangan organisasi perangkat daerah.</p>
<p>Perubahan nomenklatur ini merupakan bagian dari reformasi birokrasi untuk meningkatkan efisiensi dan efektivitas pengelolaan pendapatan daerah di Provinsi Riau.</p>',
                'gambar' => null,
            ],
            [
                'judul' => 'Visi & Misi',
                'slug' => 'visi-misi',
                'isi' => '<h4>Visi</h4>
<p>Terwujudnya Badan Pendapatan Daerah Provinsi Riau yang profesional, transparan, dan akuntabel dalam pengelolaan pendapatan daerah.</p>
<h4>Misi</h4>
<ol>
<li>Meningkatkan kualitas pelayanan kepada masyarakat wajib pajak dan retribusi daerah.</li>
<li>Mengoptimalkan penerimaan pendapatan daerah melalui intensifikasi dan ekstensifikasi sumber pendapatan.</li>
<li>Meningkatkan kompetensi dan profesionalisme aparatur pengelola pendapatan daerah.</li>
<li>Mengembangkan sistem informasi dan teknologi dalam pengelolaan pendapatan daerah.</li>
<li>Meningkatkan koordinasi dan kerja sama dengan instansi terkait dalam pengelolaan pendapatan daerah.</li>
</ol>',
                'gambar' => null,
            ],
            [
                'judul' => 'Tugas & Fungsi',
                'slug' => 'tugas-fungsi',
                'isi' => '<h4>Tugas Pokok</h4>
<p>Badan Pendapatan Daerah Provinsi Riau mempunyai tugas membantu Gubernur dalam melaksanakan fungsi penunjang urusan pemerintahan bidang keuangan, khususnya pengelolaan pendapatan daerah.</p>
<h4>Fungsi</h4>
<ol>
<li>Penyusunan kebijakan teknis di bidang pendapatan daerah.</li>
<li>Pelaksanaan tugas dukungan teknis di bidang pendapatan daerah.</li>
<li>Pemantauan, evaluasi, dan pelaporan pelaksanaan tugas dukungan teknis di bidang pendapatan daerah.</li>
<li>Pembinaan teknis penyelenggaraan fungsi penunjang urusan pemerintahan daerah di bidang pendapatan.</li>
<li>Pelaksanaan fungsi lain yang diberikan oleh Gubernur sesuai dengan tugas dan fungsinya.</li>
</ol>',
                'gambar' => null,
            ],
            [
                'judul' => 'Struktur Organisasi',
                'slug' => 'struktur-organisasi',
                'isi' => '<p class="text-center text-muted"><em>Gambar struktur organisasi Badan Pendapatan Daerah Provinsi Riau.</em></p>',
                'gambar' => null,
            ],
            [
                'judul' => 'Sambutan Kepala Badan',
                'slug' => 'sambutan',
                'isi' => '<h4>Sambutan Kepala Badan Pendapatan Daerah Provinsi Riau</h4>
<p>Assalamu\'alaikum Warahmatullahi Wabarakatuh.</p>
<p>Puji syukur kehadirat Allah SWT atas segala rahmat dan karunia-Nya. Selamat datang di website resmi Badan Pendapatan Daerah Provinsi Riau.</p>
<p>Website ini merupakan salah satu wujud komitmen kami dalam memberikan informasi yang transparan dan akuntabel kepada masyarakat terkait pengelolaan pendapatan daerah di Provinsi Riau.</p>
<p>Kami berharap website ini dapat menjadi sarana komunikasi yang efektif antara Bapenda Provinsi Riau dengan seluruh pemangku kepentingan.</p>
<p>Wassalamu\'alaikum Warahmatullahi Wabarakatuh.</p>',
                'gambar' => null,
            ],
        ];

        foreach ($halaman as $data) {
            HalamanKonten::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}

