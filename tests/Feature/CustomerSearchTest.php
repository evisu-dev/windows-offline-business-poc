<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Customer::create(['name' => '株式会社ヱビス商店']);
        Customer::create(['name' => '山田電機']);
        Customer::create(['name' => 'ヱビス工業']);
    }

    public function test_search_returns_matching_customers(): void
    {
        $this->get(route('customers.index', ['q' => 'ヱビス']))
            ->assertOk()
            ->assertSee('株式会社ヱビス商店')
            ->assertSee('ヱビス工業')
            ->assertDontSee('山田電機');
    }

    public function test_search_is_partial_match(): void
    {
        $this->get(route('customers.index', ['q' => '山田']))
            ->assertOk()
            ->assertSee('山田電機')
            ->assertDontSee('ヱビス');
    }

    public function test_empty_query_returns_all_customers(): void
    {
        $this->get(route('customers.index'))
            ->assertOk()
            ->assertSee('株式会社ヱビス商店')
            ->assertSee('山田電機')
            ->assertSee('ヱビス工業');
    }

    public function test_no_results_shows_appropriate_message(): void
    {
        $this->get(route('customers.index', ['q' => '存在しない']))
            ->assertOk()
            ->assertSee('検索条件に一致する顧客がありません');
    }

    public function test_search_with_japanese_characters(): void
    {
        Customer::create(['name' => '田中太郎']);

        $this->get(route('customers.index', ['q' => '太郎']))
            ->assertOk()
            ->assertSee('田中太郎');
    }
}
