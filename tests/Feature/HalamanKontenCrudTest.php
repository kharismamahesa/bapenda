<?php

namespace Tests\Feature;

use App\Models\HalamanKonten;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class HalamanKontenCrudTest extends TestCase
{
    protected User $user;
    protected array $createdHalamanIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::create([
            'name' => 'Admin Halaman Test',
            'email' => 'admin_halaman_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        if (!empty($this->createdHalamanIds)) {
            HalamanKonten::whereIn('id', $this->createdHalamanIds)->delete();
        }
        parent::tearDown();
    }

    public function test_halaman_konten_index_page_is_accessible(): void
    {
        $response = $this->get(route('admin.halaman-konten.index'));
        $response->assertStatus(200);
        $response->assertSee('Halaman Konten');
    }

    public function test_halaman_konten_datatables_data_endpoint_returns_json(): void
    {
        $halaman = HalamanKonten::create([
            'judul' => 'Profil Test ' . uniqid(),
            'slug' => 'profil-test-' . uniqid(),
            'isi' => '<p>Konten profil test</p>',
        ]);
        $this->createdHalamanIds[] = $halaman->id;

        $response = $this->getJson(route('admin.halaman-konten.data', ['draw' => 1, 'start' => 0, 'length' => 10]));
        $response->assertStatus(200);
        $response->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
        $this->assertGreaterThanOrEqual(1, $response->json('recordsTotal'));
    }

    public function test_can_create_halaman_konten_with_text(): void
    {
        $title = 'Visi & Misi ' . uniqid();
        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => $title,
            'isi' => '<p>Visi dan Misi Bapenda</p>',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('halaman_konten', [
            'judul' => $title,
        ]);

        $created = HalamanKonten::where('judul', $title)->first();
        if ($created) {
            $this->createdHalamanIds[] = $created->id;
        }
    }

    public function test_can_create_halaman_konten_with_image_only(): void
    {
        $file = UploadedFile::fake()->image('struktur.png', 800, 600);
        $title = 'Struktur Organisasi ' . uniqid();

        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => $title,
            'gambar' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('halaman_konten', [
            'judul' => $title,
        ]);

        $created = HalamanKonten::where('judul', $title)->first();
        if ($created) {
            $this->createdHalamanIds[] = $created->id;
            if ($created->gambar && file_exists(public_path('uploads/halaman/' . $created->gambar))) {
                @unlink(public_path('uploads/halaman/' . $created->gambar));
            }
        }
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
            'judul' => 'Sejarah ' . uniqid(),
            'slug' => 'sejarah-' . uniqid(),
            'isi' => '<p>Sejarah Bapenda</p>',
        ]);
        $this->createdHalamanIds[] = $halaman->id;

        $response = $this->getJson(route('admin.halaman-konten.show', $halaman->id));
        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.judul', $halaman->judul);
    }

    public function test_can_update_halaman_konten(): void
    {
        $halaman = HalamanKonten::create([
            'judul' => 'Profil Lama ' . uniqid(),
            'slug' => 'profil-lama-' . uniqid(),
            'isi' => '<p>Konten lama</p>',
        ]);
        $this->createdHalamanIds[] = $halaman->id;

        $newTitle = 'Profil Baru ' . uniqid();
        $response = $this->putJson(route('admin.halaman-konten.update', $halaman->id), [
            'judul' => $newTitle,
            'isi' => '<p>Konten baru yang diperbarui</p>',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('halaman_konten', [
            'id' => $halaman->id,
            'judul' => $newTitle,
        ]);
    }

    public function test_can_delete_halaman_konten(): void
    {
        $halaman = HalamanKonten::create([
            'judul' => 'Halaman Hapus ' . uniqid(),
            'slug' => 'halaman-hapus-' . uniqid(),
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
        $uniqueTitle = 'Profil Unique ' . uniqid();
        $baseSlug = Str::slug($uniqueTitle);

        $halaman1 = HalamanKonten::create([
            'judul' => $uniqueTitle,
            'slug' => $baseSlug,
            'isi' => '<p>Profil 1</p>',
        ]);
        $this->createdHalamanIds[] = $halaman1->id;

        $response = $this->postJson(route('admin.halaman-konten.store'), [
            'judul' => $uniqueTitle,
            'isi' => '<p>Profil 2</p>',
        ]);

        $response->assertStatus(201);
        $expectedSecondSlug = $baseSlug . '-1';
        $this->assertDatabaseHas('halaman_konten', ['slug' => $expectedSecondSlug]);

        $halaman2 = HalamanKonten::where('slug', $expectedSecondSlug)->first();
        if ($halaman2) {
            $this->createdHalamanIds[] = $halaman2->id;
        }
    }
}
