<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderSearchTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customerA;
    private Customer $customerB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerA = Customer::create(['name' => '顧客A']);
        $this->customerB = Customer::create(['name' => '顧客B']);

        WorkOrder::create(['customer_id' => $this->customerA->id, 'title' => '保守契約', 'status' => '進行中']);
        WorkOrder::create(['customer_id' => $this->customerA->id, 'title' => '新規開発', 'status' => '未着手']);
        WorkOrder::create(['customer_id' => $this->customerB->id, 'title' => '保守点検', 'status' => '完了']);
    }

    public function test_search_by_title(): void
    {
        $this->get(route('work_orders.index', ['q' => '保守']))
            ->assertOk()
            ->assertSee('保守契約')
            ->assertSee('保守点検')
            ->assertDontSee('新規開発');
    }

    public function test_filter_by_status(): void
    {
        $this->get(route('work_orders.index', ['status' => '進行中']))
            ->assertOk()
            ->assertSee('保守契約')
            ->assertDontSee('新規開発')
            ->assertDontSee('保守点検');
    }

    public function test_filter_by_customer(): void
    {
        $this->get(route('work_orders.index', ['customer_id' => $this->customerB->id]))
            ->assertOk()
            ->assertSee('保守点検')
            ->assertDontSee('保守契約')
            ->assertDontSee('新規開発');
    }

    public function test_combined_filters(): void
    {
        $this->get(route('work_orders.index', [
            'q' => '保守',
            'status' => '進行中',
            'customer_id' => $this->customerA->id,
        ]))
            ->assertOk()
            ->assertSee('保守契約')
            ->assertDontSee('保守点検')
            ->assertDontSee('新規開発');
    }

    public function test_no_filters_returns_all(): void
    {
        $this->get(route('work_orders.index'))
            ->assertOk()
            ->assertSee('保守契約')
            ->assertSee('新規開発')
            ->assertSee('保守点検');
    }

    public function test_no_results_shows_message(): void
    {
        $this->get(route('work_orders.index', ['q' => '存在しない件名']))
            ->assertOk()
            ->assertSee('検索条件に一致する受注がありません');
    }

    public function test_invalid_status_is_ignored(): void
    {
        $this->get(route('work_orders.index', ['status' => '不正値']))
            ->assertOk()
            ->assertSee('保守契約')
            ->assertSee('新規開発')
            ->assertSee('保守点検');
    }
}
