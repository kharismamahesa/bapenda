<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AdminAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!User::where('email', 'admin@bapenda.go.id')->exists()) {
            User::create([
                'name' => 'Administrator Bapenda',
                'email' => 'admin@bapenda.go.id',
                'password' => bcrypt('password123'),
                'role' => 'superadmin',
            ]);
        }
    }

    public function test_admin_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('BAPENDA');
        $response->assertSee('bapenda-login.css');
    }

    public function test_user_can_login_with_seeded_credentials(): void
    {
        $user = User::where('email', 'admin@bapenda.go.id')->first();

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'admin@bapenda.go.id')->first();
        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Realisasi PAD');
        $response->assertSee('bapenda-dashboard.css');
        $response->assertSee('page-header');
        $response->assertSee('page-title');
        $response->assertSee('page-actions');
        $response->assertSee('toggleSidebar()');
        $response->assertSee('closeSidebar()');
    }
}
