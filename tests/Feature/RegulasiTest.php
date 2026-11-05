<?php

namespace Tests\Feature;

use App\Models\Regulasi;
use App\Models\RegulasiKategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegulasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_regulasi(): void
    {
        $user = User::create([
            'name' => 'Admin Regulasi',
            'email' => 'admin-regulasi@example.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $kategori = RegulasiKategori::create([
            'regulasi_kategori' => 'Peraturan Daerah',
        ]);

        $storeResponse = $this->actingAs($user)->postJson('/admin/regulasi', [
            'regulasi' => 'Peraturan Daerah Nomor 1 Tahun 2026',
            'id_regulasi_kategori' => $kategori->id_regulasi_kategori,
            'keterangan' => 'Keterangan regulasi.',
        ]);

        $storeResponse->assertCreated();
        $id = $storeResponse->json('data.id_regulasi');
        $this->assertDatabaseHas('regulasi', [
            'id_regulasi' => $id,
            'regulasi' => 'Peraturan Daerah Nomor 1 Tahun 2026',
        ]);

        $this->actingAs($user)->putJson('/admin/regulasi/'.$id, [
            'regulasi' => 'Peraturan Daerah Nomor 1 Tahun 2026 (Perubahan)',
            'id_regulasi_kategori' => $kategori->id_regulasi_kategori,
            'keterangan' => 'Keterangan diperbarui.',
        ])->assertOk();

        $this->actingAs($user)->deleteJson('/admin/regulasi/'.$id)
            ->assertOk();

        $this->assertDatabaseMissing('regulasi', ['id_regulasi' => $id]);
    }

    public function test_regulasi_is_listed_on_the_public_page(): void
    {
        $kategori = RegulasiKategori::create([
            'regulasi_kategori' => 'Peraturan Daerah',
        ]);
        Regulasi::create([
            'regulasi' => 'Peraturan Daerah Nomor 2 Tahun 2026',
            'id_regulasi_kategori' => $kategori->id_regulasi_kategori,
            'keterangan' => 'Informasi publik regulasi.',
        ]);

        $this->get('/regulasi')
            ->assertOk()
            ->assertSee('Peraturan Daerah Nomor 2 Tahun 2026')
            ->assertSee('Informasi Regulasi')
            ->assertSee('href="'.route('regulasi.index').'"', false);
    }
}
