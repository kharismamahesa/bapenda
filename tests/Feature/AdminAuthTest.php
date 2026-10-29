<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TUser;

class AdminAuthTest extends TestCase
{
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
    }

    public function test_user_can_login_with_seeded_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(TUser::where('username', 'admin')->first());
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = TUser::where('username', 'admin')->first();
        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Realisasi PAD');
    }
}

