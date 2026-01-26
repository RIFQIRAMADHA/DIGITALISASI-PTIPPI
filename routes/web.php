<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produksi\Master\Employee\EmployeeController;

Route::get('/', function () {
    return redirect()->route('master.employee.index');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('Produksi.dashboard.index');
})->name('dashboard');

// MASTER
Route::prefix('master')->name('master.')->group(function () {
    Route::resource('employee', EmployeeController::class);
});
