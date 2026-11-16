<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
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

    // Modules Roadmap Overview
    Route::get('/roadmap', function () {
        return view('roadmap.index');
    })->name('roadmap.index');
});
