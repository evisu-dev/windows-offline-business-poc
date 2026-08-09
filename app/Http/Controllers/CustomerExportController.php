<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerExportController extends Controller
{
    public function csv(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF"); // BOM付きUTF-8

            fputcsv($handle, ['名前', '電話番号', 'メール', '住所', '備考']);

            foreach (Customer::orderBy('id')->cursor() as $customer) {
                fputcsv($handle, [
                    $customer->name,
                    $customer->phone ?? '',
                    $customer->email ?? '',
                    $customer->address ?? '',
                    $customer->note ?? '',
                ]);
            }

            fclose($handle);
        }, 'customers_' . date('Ymd') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
