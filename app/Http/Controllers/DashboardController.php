<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'customerCount' => DB::table('customers')->count(),
            'workOrderCount' => DB::table('work_orders')->count(),
            'pendingCount' => DB::table('work_orders')->where('status', '未着手')->count(),
            'inProgressCount' => DB::table('work_orders')->where('status', '進行中')->count(),
        ]);
    }
}
