<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::where('role', 'superadmin')->first() ?? User::create([
            'name' => 'Super Admin Test',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);
    }

    public function test_user_index_page_is_accessible(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Daftar Pengguna Sistem');
        $response->assertSee('Tambah Pengguna');
    }

    public function test_user_datatables_data_endpoint_returns_json(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/admin/users/data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
    }

    public function test_can_create_new_user(): void
    {
        $email = 'petugas.' . uniqid() . '@bapenda.go.id';
        $userData = [
            'name' => 'Petugas Baru',
            'email' => $email,
            'role' => 'admin',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/users', $userData);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Petugas Baru',
            'role' => 'admin',
        ]);
    }

    public function test_cannot_create_user_with_duplicate_email(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/admin/users', [
            'name' => 'Duplikat Email',
            'email' => $this->adminUser->email,
            'role' => 'admin',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_can_show_user_details(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/admin/users/' . $this->adminUser->id);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->adminUser->id,
                'email' => $this->adminUser->email,
            ],
        ]);
    }

    public function test_can_update_user(): void
    {
        $email = 'update.' . uniqid() . '@bapenda.go.id';
        $user = User::create([
            'name' => 'User Sebelum Update',
            'email' => $email,
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($this->adminUser)->putJson('/admin/users/' . $user->id, [
            'name' => 'User Setelah Update',
            'email' => $email,
            'role' => 'admin',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'User Setelah Update',
        ]);
    }

    public function test_cannot_delete_own_active_user(): void
    {
        $response = $this->actingAs($this->adminUser)->deleteJson('/admin/users/' . $this->adminUser->id);
        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }

    public function test_can_delete_other_user(): void
    {
        $email = 'delete.' . uniqid() . '@bapenda.go.id';
        $otherUser = User::create([
            'name' => 'User To Delete',
            'email' => $email,
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($this->adminUser)->deleteJson('/admin/users/' . $otherUser->id);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('users', [
            'id' => $otherUser->id,
        ]);
    }
}
