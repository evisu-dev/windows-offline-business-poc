<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CustomerImportTest extends TestCase
{
    use RefreshDatabase;

    private function createCsvFile(string $content, string $filename = 'test.csv'): UploadedFile
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($tmpFile, $content);

        return new UploadedFile($tmpFile, $filename, 'text/csv', null, true);
    }

    public function test_import_page_is_accessible(): void
    {
        $this->get(route('customers.import'))->assertOk()->assertSee('顧客CSV取込');
    }

    public function test_valid_csv_imports_customers(): void
    {
        $csv = "名前,電話番号,メール,住所,備考\n";
        $csv .= "山田太郎,03-1234-5678,yamada@example.com,東京都,VIP顧客\n";
        $csv .= "田中花子,06-9876-5432,tanaka@example.com,大阪府,\n";

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('status', '2件の顧客を取り込みました。');

        $this->assertDatabaseHas('customers', ['name' => '山田太郎', 'email' => 'yamada@example.com']);
        $this->assertDatabaseHas('customers', ['name' => '田中花子']);
    }

    public function test_utf8_bom_csv_is_accepted(): void
    {
        $csv = "\xEF\xBB\xBF名前,電話番号,メール,住所,備考\n";
        $csv .= "BOMテスト,,,, \n";

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['name' => 'BOMテスト']);
    }

    public function test_invalid_header_is_rejected(): void
    {
        $csv = "氏名,TEL,Email,住所,メモ\n";
        $csv .= "山田,03-0000-0000,test@test.com,,\n";

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.import'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_missing_name_is_rejected(): void
    {
        $csv = "名前,電話番号,メール,住所,備考\n";
        $csv .= ",03-1234-5678,test@example.com,,\n";

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.import'))
            ->assertSessionHas('import_errors');

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $csv = "名前,電話番号,メール,住所,備考\n";
        $csv .= "山田太郎,,not-an-email,,\n";

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.import'))
            ->assertSessionHas('import_errors');

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_error_in_any_row_prevents_all_imports(): void
    {
        $csv = "名前,電話番号,メール,住所,備考\n";
        $csv .= "正常顧客,03-0000-0000,ok@example.com,,\n";
        $csv .= ",03-0000-0000,ok@example.com,,\n"; // 名前なし → エラー

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.import'));

        // エラー行があるため正常行も含めて0件
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_empty_rows_are_skipped(): void
    {
        $csv = "名前,電話番号,メール,住所,備考\n";
        $csv .= "有効顧客,,,,\n";
        $csv .= ",,,,\n"; // 全列空 → スキップ
        $csv .= "\n"; // 空行 → スキップ

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseHas('customers', ['name' => '有効顧客']);
    }

    public function test_empty_csv_is_rejected(): void
    {
        $csv = "名前,電話番号,メール,住所,備考\n";

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.import'))
            ->assertSessionHas('error');
    }

    public function test_max_length_exceeded_is_rejected(): void
    {
        $csv = "名前,電話番号,メール,住所,備考\n";
        $csv .= str_repeat('あ', 256) . ",,,,\n"; // 名前 max:255 超過

        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csv)])
            ->assertRedirect(route('customers.import'))
            ->assertSessionHas('import_errors');

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_file_is_required(): void
    {
        $this->post(route('customers.import_store'), [])
            ->assertSessionHasErrors('csv_file');
    }

    public function test_csv_roundtrip(): void
    {
        // 顧客を作成
        Customer::create([
            'name' => 'ラウンドトリップ顧客',
            'phone' => '090-1111-2222',
            'email' => 'round@example.com',
            'address' => '福岡県福岡市',
            'note' => 'テスト備考',
        ]);

        // CSV出力
        $exportResponse = $this->get(route('customers.export_csv'));
        $csvContent = $exportResponse->streamedContent();

        // DBを空にする
        Customer::query()->delete();
        $this->assertDatabaseCount('customers', 0);

        // CSV取込
        $this->post(route('customers.import_store'), ['csv_file' => $this->createCsvFile($csvContent)])
            ->assertRedirect(route('customers.index'));

        // 復元確認
        $this->assertDatabaseHas('customers', [
            'name' => 'ラウンドトリップ顧客',
            'phone' => '090-1111-2222',
            'email' => 'round@example.com',
            'address' => '福岡県福岡市',
            'note' => 'テスト備考',
        ]);
    }
}
