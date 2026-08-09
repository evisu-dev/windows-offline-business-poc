<?php

namespace App\Http\Controllers;

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

        $file = $request->file('csv_file');
        $content = file_get_contents($file->getRealPath());

        // BOM除去
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = array_filter(explode("\n", str_replace("\r\n", "\n", $content)), fn ($line) => trim($line) !== '');

        if (count($lines) < 1) {
            return redirect()->route('customers.import')
                ->with('error', 'CSVファイルが空です。');
        }

        // ヘッダ検証
        $headerLine = array_shift($lines);
        $headers = str_getcsv($headerLine);
        $headers = array_map('trim', $headers);

        if ($headers !== self::EXPECTED_HEADERS) {
            return redirect()->route('customers.import')
                ->with('error', 'CSVのヘッダ形式が正しくありません。期待する列: ' . implode(', ', self::EXPECTED_HEADERS));
        }

        if (count($lines) === 0) {
            return redirect()->route('customers.import')
                ->with('error', 'CSVにデータ行がありません。');
        }

        // 行パース＋バリデーション
        $rows = [];
        $errors = [];

        foreach ($lines as $index => $line) {
            $fields = str_getcsv($line);

            // 全列空の行はスキップ
            if (count(array_filter($fields, fn ($f) => trim($f) !== '')) === 0) {
                continue;
            }

            // カラム数調整（不足分は空文字で埋める）
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

            $lineNumber = $index + 2; // ヘッダ行=1、データ行=2始まり

            $validator = Validator::make($row, [
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:1000',
                'note' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = "{$lineNumber}行目: {$message}";
                }
            } else {
                $rows[] = $row;
            }
        }

        if (count($errors) > 0) {
            return redirect()->route('customers.import')
                ->with('error', '取込エラーがあります。')
                ->with('import_errors', $errors);
        }

        if (count($rows) === 0) {
            return redirect()->route('customers.import')
                ->with('error', '取込可能なデータ行がありません。');
        }

        // トランザクションで一括登録
        DB::transaction(function () use ($rows): void {
            foreach ($rows as $row) {
                Customer::create($row);
            }
        });

        return redirect()->route('customers.index')
            ->with('status', count($rows) . '件の顧客を取り込みました。');
    }
}
