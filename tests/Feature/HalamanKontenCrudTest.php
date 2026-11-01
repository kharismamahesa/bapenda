<?php

namespace Tests\Feature;

use App\Models\HalamanKonten;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HalamanKontenCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_halaman_konten_index_page_is_accessible(): void
    {
        $response = $this->get(route('admin.halaman-konten.index'));
        $response->assertStatus(200);
        $response->assertSee('Halaman Konten');
    }

    public function test_halaman_konten_datatables_data_endpoint_returns_json(): void
    {
        HalamanKonten::create([
            'judul' => 'Profil Test',
            'slug' => 'profil-test',
            'isi' => '<p>Konten profil test</p>',
        ]);

        $response = $this->getJson(route('admin.halaman-konten.data', ['draw' => 1, 'start' => 0, 'length' => 10]));
        $response->assertStatus(200);
        $response->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
        $response->assertJsonPath('recordsTotal', 1);
    }

    public function test_can_create_halaman_konten_with_text(): void
    {
        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => 'Visi & Misi',
            'isi' => '<p>Visi dan Misi Bapenda</p>',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('halaman_konten', [
            'judul' => 'Visi & Misi',
            'slug' => 'visi-misi',
        ]);
    }

    public function test_can_create_halaman_konten_with_image_only(): void
    {
        $file = UploadedFile::fake()->image('struktur.png', 800, 600);

        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => 'Struktur Organisasi',
            'gambar' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('halaman_konten', [
            'judul' => 'Struktur Organisasi',
            'slug' => 'struktur-organisasi',
        ]);
    }

    public function test_validation_fails_when_both_isi_and_gambar_empty(): void
    {
        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => 'Halaman Kosong',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('isi');
    }

    public function test_validation_fails_on_empty_judul(): void
    {
        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => '',
            'isi' => '<p>Ada konten</p>',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('judul');
    }

    public function test_can_show_halaman_konten_details(): void
    {
        $halaman = HalamanKonten::create([
            'judul' => 'Sejarah',
            'slug' => 'sejarah',
            'isi' => '<p>Sejarah Bapenda</p>',
        ]);

        $response = $this->getJson(route('admin.halaman-konten.show', $halaman->id));
        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.judul', 'Sejarah');
    }

    public function test_can_update_halaman_konten(): void
    {
        $halaman = HalamanKonten::create([
            'judul' => 'Profil Lama',
            'slug' => 'profil-lama',
            'isi' => '<p>Konten lama</p>',
        ]);

        $response = $this->putJson(route('admin.halaman-konten.update', $halaman->id), [
            'judul' => 'Profil Baru',
            'isi' => '<p>Konten baru yang diperbarui</p>',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('halaman_konten', [
            'id' => $halaman->id,
            'judul' => 'Profil Baru',
            'slug' => 'profil-baru',
        ]);
    }

    public function test_can_delete_halaman_konten(): void
    {
        $halaman = HalamanKonten::create([
            'judul' => 'Halaman Hapus',
            'slug' => 'halaman-hapus',
            'isi' => '<p>Akan dihapus</p>',
        ]);

        $response = $this->deleteJson(route('admin.halaman-konten.destroy', $halaman->id));
        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('halaman_konten', [
            'id' => $halaman->id,
        ]);
    }

    public function test_slug_is_auto_generated_unique(): void
    {
        HalamanKonten::create([
            'judul' => 'Profil',
            'slug' => 'profil',
            'isi' => '<p>Profil 1</p>',
        ]);

        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => 'Profil',
            'isi' => '<p>Profil 2</p>',
        ]);

        $response->assertStatus(201);

        // Slug kedua harus berbeda (profil-1)
        $this->assertDatabaseHas('halaman_konten', ['slug' => 'profil-1']);
    }
}

