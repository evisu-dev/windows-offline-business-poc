<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
