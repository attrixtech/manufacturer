<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Multi-Company & Branch Context Switching
    Route::post('/switch-company', [CompanyController::class, 'switchCompany'])->name('companies.switch');
    Route::post('/switch-branch', [CompanyController::class, 'switchBranch'])->name('branches.switch');

    // Company & Branch Management
    Route::resource('companies', CompanyController::class)->only(['index', 'edit', 'update']);

    // User & RBAC Management
    Route::resource('users', UserController::class)->only(['index', 'store']);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Roadmap
    Route::get('/roadmap', function () {
        return view('roadmap.index');
    })->name('roadmap.index');

    // 1. Work Orders & MES
    Route::get('/work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
    Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
    Route::post('/work-orders/{id}/record-output', [WorkOrderController::class, 'recordOutput'])->name('work-orders.record-output');
    Route::patch('/work-orders/{id}/status', [WorkOrderController::class, 'updateStatus'])->name('work-orders.update-status');

    // 2. BOM & Routings
    Route::get('/bom', [BomController::class, 'index'])->name('bom.index');
    Route::post('/bom', [BomController::class, 'store'])->name('bom.store');
    Route::post('/bom/routings', [BomController::class, 'storeRouting'])->name('bom.routings.store');
    Route::patch('/bom/{id}/approve', [BomController::class, 'approve'])->name('bom.approve');

    // 3. Stock Ledger & Bins (Inventory)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');

    // 4. Quality & Inspections
    Route::get('/quality', [QualityController::class, 'index'])->name('quality.index');
    Route::match(['post', 'patch'], '/quality/{id}/inspect', [QualityController::class, 'inspect'])->name('quality.inspect');
    Route::post('/quality/ncr', [QualityController::class, 'storeNcr'])->name('quality.ncr.store');

    // 5. Procurement & PO
    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');

    // 6. General Ledger & GST (Accounting)
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::post('/accounting/journal', [AccountingController::class, 'storeJournal'])->name('accounting.journal.store');

    // 7. Breakdown & Downtime (Maintenance)
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance/breakdown', [MaintenanceController::class, 'storeBreakdown'])->name('maintenance.breakdown.store');
});
