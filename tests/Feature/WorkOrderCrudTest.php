<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderCrudTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::create(['name' => 'テスト顧客']);
    }

    public function test_work_order_index_is_accessible(): void
    {
        $this->get(route('work_orders.index'))->assertOk();
    }

    public function test_work_order_create_form_is_accessible(): void
    {
        $this->get(route('work_orders.create'))->assertOk();
    }

    public function test_work_order_can_be_stored(): void
    {
        $this->post(route('work_orders.store'), [
            'customer_id' => $this->customer->id,
            'title' => 'テスト受注',
            'description' => '受注の詳細',
            'status' => '未着手',
            'due_date' => '2026-09-01',
        ])->assertRedirect(route('work_orders.index'));

        $this->assertDatabaseHas('work_orders', [
            'title' => 'テスト受注',
            'customer_id' => $this->customer->id,
        ]);
    }

    public function test_work_order_store_requires_title_and_customer(): void
    {
        $this->post(route('work_orders.store'), [
            'customer_id' => '',
            'title' => '',
            'status' => '未着手',
        ])->assertSessionHasErrors(['customer_id', 'title']);
    }

    public function test_work_order_edit_form_is_accessible(): void
    {
        $workOrder = WorkOrder::create([
            'customer_id' => $this->customer->id,
            'title' => '編集テスト',
            'status' => '未着手',
        ]);

        $this->get(route('work_orders.edit', $workOrder))->assertOk();
    }

    public function test_work_order_can_be_updated(): void
    {
        $workOrder = WorkOrder::create([
            'customer_id' => $this->customer->id,
            'title' => '更新前',
            'status' => '未着手',
        ]);

        $this->put(route('work_orders.update', $workOrder), [
            'customer_id' => $this->customer->id,
            'title' => '更新後',
            'status' => '進行中',
            'due_date' => '2026-10-01',
        ])->assertRedirect(route('work_orders.index'));

        $this->assertDatabaseHas('work_orders', [
            'title' => '更新後',
            'status' => '進行中',
        ]);
    }

    public function test_work_order_can_be_deleted(): void
    {
        $workOrder = WorkOrder::create([
            'customer_id' => $this->customer->id,
            'title' => '削除テスト',
            'status' => '未着手',
        ]);

        $this->delete(route('work_orders.destroy', $workOrder))
            ->assertRedirect(route('work_orders.index'));

        $this->assertDatabaseMissing('work_orders', ['id' => $workOrder->id]);
    }

    public function test_deleting_customer_cascades_to_work_orders(): void
    {
        $workOrder = WorkOrder::create([
            'customer_id' => $this->customer->id,
            'title' => 'カスケード削除テスト',
            'status' => '未着手',
        ]);

        $this->customer->delete();

        $this->assertDatabaseMissing('work_orders', ['id' => $workOrder->id]);
    }
}
