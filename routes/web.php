<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function (): View {
    return view('poc', [
        'count' => DB::table('poc_checks')->count(),
        'databasePath' => DB::connection()->getDatabaseName(),
    ]);
});

Route::post('/write-test', function (): RedirectResponse {
    DB::table('poc_checks')->insert([
        'message' => 'NativePHP SQLite write test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/')->with('status', 'SQLiteへの書き込みに成功しました。');
});

Route::resource('customers', CustomerController::class)->except(['show']);
Route::resource('work_orders', WorkOrderController::class)->except(['show']);
Route::get('work_orders/export/csv', [WorkOrderController::class, 'exportCsv'])->name('work_orders.export_csv');
Route::get('work_orders/{work_order}/pdf', [WorkOrderController::class, 'exportPdf'])->name('work_orders.export_pdf');
