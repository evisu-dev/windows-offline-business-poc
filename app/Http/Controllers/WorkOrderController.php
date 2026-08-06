<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkOrderController extends Controller
{
    public function index(): View
    {
        $workOrders = WorkOrder::with('customer')->orderBy('id', 'desc')->get();

        return view('work_orders.index', compact('workOrders'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();

        return view('work_orders.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status' => 'required|string|max:50',
            'due_date' => 'nullable|date',
        ]);

        WorkOrder::create($validated);

        return redirect()->route('work_orders.index')
            ->with('status', '受注を登録しました。');
    }

    public function edit(WorkOrder $workOrder): View
    {
        $customers = Customer::orderBy('name')->get();

        return view('work_orders.edit', compact('workOrder', 'customers'));
    }

    public function update(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status' => 'required|string|max:50',
            'due_date' => 'nullable|date',
        ]);

        $workOrder->update($validated);

        return redirect()->route('work_orders.index')
            ->with('status', '受注情報を更新しました。');
    }

    public function destroy(WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->delete();

        return redirect()->route('work_orders.index')
            ->with('status', '受注を削除しました。');
    }

    public function exportCsv(): StreamedResponse
    {
        $workOrders = WorkOrder::with('customer')->orderBy('id')->get();

        return response()->streamDownload(function () use ($workOrders): void {
            $handle = fopen('php://output', 'w');

            // BOM付きUTF-8（Excel対応）
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['ID', '顧客名', '件名', 'ステータス', '納期', '登録日']);

            foreach ($workOrders as $workOrder) {
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

    public function exportPdf(WorkOrder $workOrder): Response
    {
        $workOrder->load('customer');

        $pdf = Pdf::loadView('work_orders.pdf', compact('workOrder'));

        return $pdf->download('work_order_' . $workOrder->id . '.pdf');
    }
}
