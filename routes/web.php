<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function (): View {
    $customerCount = DB::table('customers')->count();
    $workOrderCount = DB::table('work_orders')->count();
    $pendingCount = DB::table('work_orders')->where('status', '未着手')->count();
    $inProgressCount = DB::table('work_orders')->where('status', '進行中')->count();

    return view('dashboard', compact('customerCount', 'workOrderCount', 'pendingCount', 'inProgressCount'));
});

Route::resource('customers', CustomerController::class)->except(['show']);
Route::resource('work_orders', WorkOrderController::class)->except(['show']);
Route::get('work_orders/export/csv', [WorkOrderController::class, 'exportCsv'])->name('work_orders.export_csv');
Route::get('work_orders/{work_order}/pdf', [WorkOrderController::class, 'exportPdf'])->name('work_orders.export_pdf');

Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
Route::get('backup/download', [BackupController::class, 'download'])->name('backup.download');
Route::post('backup/restore', [BackupController::class, 'restore'])->name('backup.restore');

Route::get('system', [SystemController::class, 'index'])->name('system.index');
