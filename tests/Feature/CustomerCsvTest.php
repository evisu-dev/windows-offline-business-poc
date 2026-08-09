<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCsvTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_export_returns_csv_response(): void
    {
        Customer::create(['name' => 'テスト顧客', 'phone' => '03-1234-5678', 'email' => 'test@example.com']);

        $response = $this->get(route('customers.export_csv'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition');
    }

    public function test_csv_export_contains_customer_data(): void
    {
        Customer::create([
            'name' => '株式会社ヱビス',
            'phone' => '03-9999-0000',
            'email' => 'info@evisu.example',
            'address' => '東京都渋谷区',
            'note' => '重要顧客',
        ]);

        $response = $this->get(route('customers.export_csv'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('株式会社ヱビス', $content);
        $this->assertStringContainsString('03-9999-0000', $content);
        $this->assertStringContainsString('info@evisu.example', $content);
        $this->assertStringContainsString('東京都渋谷区', $content);
        $this->assertStringContainsString('重要顧客', $content);
    }

    public function test_csv_export_has_bom(): void
    {
        Customer::create(['name' => 'BOMテスト']);

        $response = $this->get(route('customers.export_csv'));
        $content = $response->streamedContent();

        $this->assertTrue(str_starts_with($content, "\xEF\xBB\xBF"));
    }

    public function test_csv_export_has_correct_headers(): void
    {
        $response = $this->get(route('customers.export_csv'));
        $content = $response->streamedContent();

        // BOMを除去して最初の行を確認
        $withoutBom = substr($content, 3);
        $firstLine = strtok($withoutBom, "\n");

        $this->assertStringContainsString('名前', $firstLine);
        $this->assertStringContainsString('電話番号', $firstLine);
        $this->assertStringContainsString('メール', $firstLine);
        $this->assertStringContainsString('住所', $firstLine);
        $this->assertStringContainsString('備考', $firstLine);
    }

    public function test_csv_export_with_no_data_returns_header_only(): void
    {
        $response = $this->get(route('customers.export_csv'));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('名前', $content);
    }

    public function test_csv_filename_contains_date(): void
    {
        $response = $this->get(route('customers.export_csv'));

        $disposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('customers_' . date('Ymd'), $disposition);
    }
}
