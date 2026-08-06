<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_index_is_accessible(): void
    {
        $this->get(route('customers.index'))->assertOk();
    }

    public function test_customer_create_form_is_accessible(): void
    {
        $this->get(route('customers.create'))->assertOk();
    }

    public function test_customer_can_be_stored(): void
    {
        $this->post(route('customers.store'), [
            'name' => 'テスト顧客',
            'phone' => '03-1234-5678',
            'email' => 'test@example.com',
            'address' => '東京都千代田区',
            'note' => '備考テスト',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['name' => 'テスト顧客']);
    }

    public function test_customer_store_requires_name(): void
    {
        $this->post(route('customers.store'), [
            'name' => '',
        ])->assertSessionHasErrors('name');
    }

    public function test_customer_edit_form_is_accessible(): void
    {
        $customer = Customer::create(['name' => '編集テスト']);

        $this->get(route('customers.edit', $customer))->assertOk();
    }

    public function test_customer_can_be_updated(): void
    {
        $customer = Customer::create(['name' => '更新前']);

        $this->put(route('customers.update', $customer), [
            'name' => '更新後',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['name' => '更新後']);
    }

    public function test_customer_can_be_deleted(): void
    {
        $customer = Customer::create(['name' => '削除テスト']);

        $this->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
