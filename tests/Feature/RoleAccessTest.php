<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_diarahkan_ke_login(): void
    {
        $this->get(route('operator.dashboard'))->assertRedirect(route('login'));
        $this->get(route('operator.kegiatan.index'))->assertRedirect(route('login'));
        $this->get(route('operator.staf.index'))->assertRedirect(route('login'));
    }

    public function test_magang_ditolak_masuk_staf_dan_pengaturan(): void
    {
        $magang = User::factory()->magang()->create();

        $this->actingAs($magang)
            ->get(route('operator.staf.index'))
            ->assertForbidden();

        $this->actingAs($magang)
            ->get(route('operator.setting.edit'))
            ->assertForbidden();
    }

    public function test_operator_bisa_kegiatan_tapi_tidak_bisa_staf(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->get(route('operator.kegiatan.index'))
            ->assertOk();

        $this->actingAs($operator)
            ->get(route('operator.staf.index'))
            ->assertForbidden();
    }

    public function test_superadmin_bisa_akses_staf_dan_pengaturan(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get(route('operator.staf.index'))
            ->assertOk();

        $this->actingAs($superadmin)
            ->get(route('operator.setting.edit'))
            ->assertOk();
    }

    public function test_magang_masih_bisa_dashboard_dan_kegiatan(): void
    {
        $magang = User::factory()->magang()->create();

        $this->actingAs($magang)
            ->get(route('operator.dashboard'))
            ->assertOk();

        $this->actingAs($magang)
            ->get(route('operator.kegiatan.index'))
            ->assertOk();
    }
}
