<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CustomerImportController extends Controller
{
    private const EXPECTED_HEADERS = ['名前', '電話番号', 'メール', '住所', '備考'];

    public function create(): View
    {
        return view('customers.import');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $lines = $this->parseFile($request->file('csv_file'));

        if ($lines === null) {
            return redirect()->route('customers.import')
                ->with('error', 'CSVファイルが空です。');
        }

        $headerValidation = $this->validateHeader($lines);
        if ($headerValidation !== null) {
            return redirect()->route('customers.import')
                ->with('error', $headerValidation);
        }

        [$rows, $errors] = $this->validateRows($lines);

        if (count($errors) > 0) {
            return redirect()->route('customers.import')
                ->with('error', '取込エラーがあります。')
                ->with('import_errors', $errors);
        }

        if (count($rows) === 0) {
            return redirect()->route('customers.import')
                ->with('error', '取込可能なデータ行がありません。');
        }

        DB::transaction(function () use ($rows): void {
            foreach ($rows as $row) {
                Customer::create($row);
            }
        });

        return redirect()->route('customers.index')
            ->with('status', count($rows) . '件の顧客を取り込みました。');
    }

    /**
     * CSVファイルを読み込み、行配列を返す。空ファイルの場合はnull。
     *
     * @return array<string>|null
     */
    private function parseFile(\Illuminate\Http\UploadedFile $file): ?array
    {
        $content = file_get_contents($file->getRealPath());

        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = array_filter(
            explode("\n", str_replace("\r\n", "\n", $content)),
            fn ($line) => trim($line) !== ''
        );

        return count($lines) < 1 ? null : array_values($lines);
    }

    /**
     * ヘッダ行を検証する。問題がなければnull、エラーがあればメッセージを返す。
     * 検証後、$linesからヘッダ行を除去する。
     */
    private function validateHeader(array &$lines): ?string
    {
        $headerLine = array_shift($lines);
        $headers = array_map('trim', str_getcsv($headerLine));

        if ($headers !== self::EXPECTED_HEADERS) {
            return 'CSVのヘッダ形式が正しくありません。期待する列: ' . implode(', ', self::EXPECTED_HEADERS);
        }

        if (count($lines) === 0) {
            return 'CSVにデータ行がありません。';
        }

        return null;
    }

    /**
     * データ行をパース・バリデーションし、[有効行, エラー] を返す。
     *
     * @return array{0: array<array<string, mixed>>, 1: array<string>}
     */
    private function validateRows(array $lines): array
    {
        $rows = [];
        $errors = [];
        $rules = StoreCustomerRequest::customerRules();

        foreach ($lines as $index => $line) {
            $fields = str_getcsv($line);

            if (count(array_filter($fields, fn ($f) => trim($f) !== '')) === 0) {
                continue;
            }

            while (count($fields) < 5) {
                $fields[] = '';
            }

            $row = [
                'name' => trim($fields[0]),
                'phone' => trim($fields[1]) ?: null,
                'email' => trim($fields[2]) ?: null,
                'address' => trim($fields[3]) ?: null,
                'note' => trim($fields[4]) ?: null,
            ];

            $lineNumber = $index + 2;

            $validator = Validator::make($row, $rules);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = "{$lineNumber}行目: {$message}";
                }
            } else {
                $rows[] = $row;
            }
        }

        return [$rows, $errors];
    }
}
