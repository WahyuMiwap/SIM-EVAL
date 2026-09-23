<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_operator_dashboard(): void
    {
        $response = $this->get('/');

        // '/' redirect ke operator.dashboard (wajib auth -> redirect login bila guest)
        $response->assertRedirect(route('operator.dashboard'));
    }

    public function test_login_page_returns_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
