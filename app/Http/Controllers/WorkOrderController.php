<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkOrderRequest;
use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = WorkOrder::with('customer');

        if ($request->filled('q')) {
            $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $request->string('q')->trim());
            $query->where('title', 'like', "%{$escaped}%");
        }

        if ($request->filled('status') && in_array($request->input('status'), WorkOrder::STATUSES, true)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->integer('customer_id'));
        }

        $workOrders = $query->orderBy('id', 'desc')->get();
        $customers = Customer::orderBy('name')->get();

        return view('work_orders.index', compact('workOrders', 'customers'));
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
