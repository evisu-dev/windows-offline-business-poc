<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\WorkOrderExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('customers', CustomerController::class)->except(['show']);

Route::resource('work_orders', WorkOrderController::class)->except(['show']);
Route::get('work_orders/export/csv', [WorkOrderExportController::class, 'csv'])->name('work_orders.export_csv');
Route::get('work_orders/{work_order}/pdf', [WorkOrderExportController::class, 'pdf'])->name('work_orders.export_pdf');

Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
Route::get('backup/download', [BackupController::class, 'download'])->name('backup.download');
Route::post('backup/restore', [BackupController::class, 'restore'])->name('backup.restore');

Route::get('system', [SystemController::class, 'index'])->name('system.index');
