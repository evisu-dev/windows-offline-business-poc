<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PocCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_is_accessible(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('ダッシュボード');
    }

    public function test_system_page_is_accessible(): void
    {
        $this->get(route('system.index'))
            ->assertOk()
            ->assertSee('システム情報');
    }
}
