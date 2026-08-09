<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'customerCount' => Customer::count(),
            'workOrderCount' => WorkOrder::count(),
            'pendingCount' => WorkOrder::where('status', '未着手')->count(),
            'inProgressCount' => WorkOrder::where('status', '進行中')->count(),
        ]);
    }
}
