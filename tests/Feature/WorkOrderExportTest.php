<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderExportTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::create(['name' => 'CSV/PDFテスト顧客']);
    }

    public function test_csv_export_returns_csv_file(): void
    {
        WorkOrder::create([
            'customer_id' => $this->customer->id,
            'title' => 'CSVテスト受注',
            'status' => '未着手',
            'due_date' => '2026-09-01',
        ]);

        $response = $this->get(route('work_orders.export_csv'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition');

        $content = $response->streamedContent();
        $this->assertStringContainsString('CSVテスト受注', $content);
        $this->assertStringContainsString('CSV/PDFテスト顧客', $content);
    }

    public function test_csv_export_with_no_data_returns_header_only(): void
    {
        $response = $this->get(route('work_orders.export_csv'));

        $response->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('ID', $content);
    }

    public function test_pdf_export_returns_pdf_file(): void
    {
        $workOrder = WorkOrder::create([
            'customer_id' => $this->customer->id,
            'title' => 'PDFテスト受注',
            'status' => '進行中',
            'due_date' => '2026-10-15',
        ]);

        $response = $this->get(route('work_orders.export_pdf', $workOrder));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_export_with_invalid_id_returns_404(): void
    {
        $this->get(route('work_orders.export_pdf', ['work_order' => 9999]))
            ->assertNotFound();
    }
}
