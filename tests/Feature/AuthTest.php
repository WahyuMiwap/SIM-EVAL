<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_berhasil_redirect_dashboard(): void
    {
        $user = User::factory()->operator()->create([
            'email' => 'wahyu@bnnsurabaya.go.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wahyu@bnnsurabaya.go.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('operator.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_password_salah(): void
    {
        User::factory()->create([
            'email' => 'wahyu@bnnsurabaya.go.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wahyu@bnnsurabaya.go.id',
            'password' => 'salah-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_akun_nonaktif_ditolak(): void
    {
        User::factory()->inactive()->create([
            'email' => 'nonaktif@bnnsurabaya.go.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'nonaktif@bnnsurabaya.go.id',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_validasi_email_dan_password_wajib(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_logout_membersihkan_sesi(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
