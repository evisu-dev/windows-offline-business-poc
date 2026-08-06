<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkOrderRequest;
use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreWorkOrderRequest $request): RedirectResponse
    {
        WorkOrder::create($request->validated());

        return redirect()->route('work_orders.index')
            ->with('status', '受注を登録しました。');
    }

    public function edit(WorkOrder $workOrder): View
    {
        $customers = Customer::orderBy('name')->get();

        return view('work_orders.edit', compact('workOrder', 'customers'));
    }

    public function update(StoreWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->update($request->validated());

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
