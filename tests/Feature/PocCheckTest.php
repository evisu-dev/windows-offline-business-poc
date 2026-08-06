<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PocCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_poc_screen_is_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Offline Work Order Manager PoC');
    }

    public function test_sqlite_write_route_adds_a_record(): void
    {
        $this->post('/write-test')->assertRedirect('/');

        $this->assertDatabaseCount('poc_checks', 1);
    }
}
