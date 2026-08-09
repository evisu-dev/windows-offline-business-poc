<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkOrderExportController extends Controller
{
    public function csv(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF"); // BOM付きUTF-8（Excel対応）

            fputcsv($handle, ['ID', '顧客名', '件名', 'ステータス', '納期', '登録日']);

            foreach (WorkOrder::with('customer')->orderBy('id')->cursor() as $workOrder) {
                fputcsv($handle, [
                    $workOrder->id,
                    $workOrder->customer->name,
                    $workOrder->title,
                    $workOrder->status,
                    $workOrder->due_date?->format('Y-m-d') ?? '',
                    $workOrder->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 'work_orders_' . date('Ymd') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function pdf(WorkOrder $workOrder): Response
    {
        $workOrder->load('customer');

        $pdf = Pdf::loadView('work_orders.pdf', compact('workOrder'));

        return $pdf->download('work_order_' . $workOrder->id . '.pdf');
    }
}
