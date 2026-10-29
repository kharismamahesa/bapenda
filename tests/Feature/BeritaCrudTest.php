<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TUser;
use App\Models\KategoriBerita;
use App\Models\Berita;
use Illuminate\Http\UploadedFile;

class BeritaCrudTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = TUser::first() ?? TUser::create([
            'nama' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }

    public function test_kategori_berita_page_accessible(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/kategori-berita');
        $response->assertStatus(200);
        $response->assertSee('Data Kategori Berita');
    }

    public function test_kategori_berita_datatables_data_endpoint(): void
    {
        $response = $this->actingAs($this->user)->getJson('/admin/kategori-berita/data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data'
        ]);
    }

    public function test_kategori_berita_validation_fails_on_empty(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/kategori-berita', [
            'berita_kategori' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['berita_kategori']);
    }

    public function test_kategori_berita_crud_lifecycle(): void
    {
        $uniqueName = 'Kategori Test ' . uniqid();

        // 1. Create
        $storeRes = $this->actingAs($this->user)->postJson('/admin/kategori-berita', [
            'berita_kategori' => $uniqueName
        ]);
        $storeRes->assertStatus(201);
        $id = $storeRes->json('data.id_berita_kategori');

        // 2. Read / Show
        $showRes = $this->actingAs($this->user)->getJson('/admin/kategori-berita/' . $id);
        $showRes->assertStatus(200);
        $this->assertEquals($uniqueName, $showRes->json('data.berita_kategori'));

        // 3. Update
        $updatedName = $uniqueName . ' Updated';
        $updateRes = $this->actingAs($this->user)->putJson('/admin/kategori-berita/' . $id, [
            'berita_kategori' => $updatedName
        ]);
        $updateRes->assertStatus(200);

        // 4. Delete
        $deleteRes = $this->actingAs($this->user)->deleteJson('/admin/kategori-berita/' . $id);
        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('t_berita_kategori', ['id_berita_kategori' => $id]);
    }

    public function test_berita_page_accessible(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/berita');
        $response->assertStatus(200);
        $response->assertSee('Daftar Berita');
    }

    public function test_berita_datatables_data_endpoint(): void
    {
        $response = $this->actingAs($this->user)->getJson('/admin/berita/data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data'
        ]);

        $data = $response->json('data');
        if (count($data) >= 2) {
            // Pastikan data pertama memiliki id_berita yang lebih baru / lebih besar dari data berikutnya jika tanggal sama atau lebih baru
            $firstId = $data[0]['id_berita'];
            $secondId = $data[1]['id_berita'];
            $first = Berita::find($firstId);
            $second = Berita::find($secondId);
            $this->assertTrue(strtotime($first->tanggal_berita) >= strtotime($second->tanggal_berita));
        }
    }

    public function test_berita_validation_fails_on_empty_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/berita', [
            'judul' => '',
            'id_berita_kategori' => '',
            'isi' => '',
            'status' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['judul', 'id_berita_kategori', 'isi', 'status']);
    }

    public function test_berita_crud_lifecycle(): void
    {
        $kategori = KategoriBerita::first() ?? KategoriBerita::create(['berita_kategori' => 'Kategori Temp']);
        $uniqueTitle = 'Berita Pengujian ' . uniqid();

        // 1. Create
        $storeRes = $this->actingAs($this->user)->postJson('/admin/berita', [
            'judul' => $uniqueTitle,
            'id_berita_kategori' => $kategori->id_berita_kategori,
            'isi' => '<p>Konten pengujian otomatis CRUD berita.</p>',
            'status' => '1',
        ]);
        $storeRes->assertStatus(201);
        $id = $storeRes->json('data.id_berita');

        // 2. Read / Show
        $showRes = $this->actingAs($this->user)->getJson('/admin/berita/' . $id);
        $showRes->assertStatus(200);
        $this->assertEquals($uniqueTitle, $showRes->json('data.judul'));

        // 3. Update
        $updatedTitle = $uniqueTitle . ' Updated';
        $updateRes = $this->actingAs($this->user)->putJson('/admin/berita/' . $id, [
            'judul' => $updatedTitle,
            'id_berita_kategori' => $kategori->id_berita_kategori,
            'isi' => '<p>Konten telah diperbarui melalui pengujian otomatis.</p>',
            'status' => '0',
        ]);
        $updateRes->assertStatus(200);

        // 4. Delete
        $deleteRes = $this->actingAs($this->user)->deleteJson('/admin/berita/' . $id);
        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('t_berita', ['id_berita' => $id]);
    }
}

