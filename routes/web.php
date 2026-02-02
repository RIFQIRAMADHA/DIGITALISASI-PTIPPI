<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produksi\Auth\LoginController;
use App\Http\Controllers\Produksi\Dashboard\DashboardController;
use App\Http\Controllers\Produksi\Master\Employee\EmployeeController;
use App\Http\Controllers\Produksi\Master\Customer\CustomerController;
use App\Http\Controllers\Produksi\Master\Item\ItemController;
use App\Http\Controllers\Produksi\Master\Line\LineController;
use App\Http\Controllers\Produksi\Transaksi\ProductionScheduleController;


// --- RUTE LOGIN (Hanya bisa diakses jika belum login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.process');
});

// --- SEMUA RUTE DI BAWAH INI WAJIB LOGIN ---
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute awal otomatis ke Dashboard atau Employee Index
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // MASTER DATA (Dikelompokkan dalam satu prefix 'master')
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('employee', EmployeeController::class);
        Route::resource('customer', CustomerController::class);
        Route::resource('itemproduction', ItemController::class);
        Route::resource('productionline', LineController::class);
    });

    // Production Schedule
    Route::get('/productionschedule', function () {
        return view('productionschedule.index');
    })->name('productionschedule.index');

    // PRODUCTION SCHEDULE (Ganti ke Resource atau Manual Controller)
    // Pakai resource agar rapi untuk fungsi index, create, store, edit, update
    Route::resource('productionschedule', ProductionScheduleController::class);

    Route::get('/productionschedule/get-detail-row/{index}', [ProductionScheduleController::class, 'getDetailRow']);
});