<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produksi\Auth\LoginController;
use App\Http\Controllers\Produksi\Dashboard\DashboardController;
use App\Http\Controllers\Produksi\Master\Employee\EmployeeController;
use App\Http\Controllers\Produksi\Master\Customer\CustomerController;
use App\Http\Controllers\Produksi\Master\Item\ItemController;
use App\Http\Controllers\Produksi\Master\Line\LineController;
use App\Http\Controllers\Produksi\Transaksi\ProductionScheduleController;
use App\Http\Controllers\Produksi\Transaksi\InputHarianController;
use App\Http\Controllers\Produksi\Report\BaRejectController;
use App\Http\Controllers\Produksi\Report\DailyReportController;
use App\Http\Controllers\Produksi\Report\QprReportController;
use App\Http\Controllers\Produksi\Report\AsakaiReportController;

// --- GUEST ONLY (Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.process');
    Route::get('/', function () { return redirect()->route('login'); });
});

// --- TERPROTEKSI (Wajib Login) ---
Route::middleware('auth')->group(function () {
    
    // --- Dashboard & Logout (Akses Semua Role) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/detail-harian/{id}', [DashboardController::class, 'detailHarian']);
    Route::get('/dashboard/detail-downtime/{id}', [DashboardController::class, 'detailDowntime']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard/pareto-detail', [DashboardController::class, 'getParetoDetail']);

    // ==========================================
    // KELOMPOK MASTER DATA
    // ==========================================
    Route::prefix('master')->name('master.')->group(function () {
        // Master Data Penuh: Admin & SPV
        Route::middleware('role:admin,supervisor')->group(function () {
            Route::resource('employee', EmployeeController::class);
            Route::resource('productionline', LineController::class);
        });

        // Master Data Terbatas: Admin, SPV, dan PPC
        Route::middleware('role:admin,supervisor,ppc')->group(function () {
            Route::resource('customer', CustomerController::class);
            Route::resource('itemproduction', ItemController::class);
        });
    });

    // ==========================================
    // KELOMPOK SCHEDULE
    // ==========================================
    Route::middleware('role:admin,supervisor,ppc')->group(function () {
        Route::get('/productionschedule/check-duplicate', [ProductionScheduleController::class, 'checkDuplicate'])->name('productionschedule.checkDuplicate');
        Route::resource('productionschedule', ProductionScheduleController::class);
        Route::get('/productionschedule/get-detail-row/{index}', [ProductionScheduleController::class, 'getDetailRow']);
        Route::post('/productionschedule/import', [ProductionScheduleController::class, 'import'])->name('productionschedule.import');
    });

    // ==========================================
    // KELOMPOK INPUT HARIAN (OPERASIONAL LAPANGAN)
    // ==========================================
    Route::prefix('produksi/input-harian')->name('inputharian.')->group(function () {
        
        // --- 1. Rute Gabungan (Ops, Admin, SPV, Quality) ---
        // Quality harus bisa akses index biar bisa lihat tabel, dan akses fitur downtime
        Route::middleware('role:admin,supervisor,foreman,leader k,leader e,leader f,quality')->group(function () {
            Route::get('/', [InputHarianController::class, 'index'])->name('index');
            Route::get('/downtime/{id}', [InputHarianController::class, 'detailDowntime'])->name('downtime');
            Route::post('/downtime/{id}/store', [InputHarianController::class, 'storeDetailDowntime'])->name('downtime.store');
        });

        // --- 2. Rute Khusus Operasional (TANPA Quality) ---
        // Quality tidak bisa edit, start/stop mesin, atau input reject/repair
        Route::middleware('role:admin,supervisor,foreman,leader k,leader e,leader f')->group(function () {
            Route::post('/update/{id}', [InputHarianController::class, 'update'])->name('update');
            Route::post('/update-status/{id}', [InputHarianController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/reject/{id}', [InputHarianController::class, 'detailReject'])->name('reject');
            Route::post('/reject/{id}/store', [InputHarianController::class, 'storeDetailReject'])->name('reject.store');
            Route::get('/repair/{id}', [InputHarianController::class, 'detailRepair'])->name('repair');
            Route::post('/repair/{id}/store', [InputHarianController::class, 'storeDetailRepair'])->name('repair.store');
            Route::get('/idletime/{id}', [InputHarianController::class, 'detailIdleTime'])->name('idletime');
            Route::post('/idletime/store/{id}', [InputHarianController::class, 'storeDetailIdleTime'])->name('idletime.store');
            Route::post('/oper-manual/{id}', [InputHarianController::class, 'operManual'])->name('operManual');
            Route::post('/oper-massal-otomatis', [InputHarianController::class, 'operMassalOtomatis'])->name('operMassalOtomatis');
            Route::post('/plan-details', [InputHarianController::class, 'getPlanDetails'])->name('planDetails');
            Route::post('/store-extra', [InputHarianController::class, 'storeExtra'])->name('storeExtra');
            Route::post('/set-next', [InputHarianController::class, 'setNext']);
        });
    });

    // ==========================================
    // KELOMPOK REPORT LAPANGAN
    // ==========================================
    Route::prefix('report')->name('report.')->group(function () {
        
        // --- 1. BA Reject (Ops, Admin, SPV) ---
        Route::middleware('role:admin,supervisor,foreman,leader k,leader e,leader f')->group(function () {
            Route::prefix('ba-reject')->name('bareject.')->group(function () {
                Route::get('/', [BaRejectController::class, 'index'])->name('index');
                Route::get('/create', [BaRejectController::class, 'create'])->name('create');
                Route::post('/store', [BaRejectController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [BaRejectController::class, 'edit'])->name('edit');
                Route::put('/update/{id}', [BaRejectController::class, 'update'])->name('update');
                Route::delete('/delete/{id}', [BaRejectController::class, 'destroy'])->name('destroy');
                Route::get('/show/{id}', [BaRejectController::class, 'show'])->name('show');
                Route::get('/ambil-nomor', [BaRejectController::class, 'ambilNomorTerakhir'])->name('ambilNomor');
                Route::get('/excel', [BaRejectController::class, 'exportExcel'])->name('excel');
                Route::get('/pdf', [BaRejectController::class, 'exportPdf'])->name('pdf');
                Route::get('/get-no-ba/{id}', [BaRejectController::class, 'getNoBa'])->where('id', '.*');
            });
        });

        // --- 2. QPR (Ops, Admin, SPV, Quality) ---
        Route::middleware('role:admin,supervisor,foreman,leader k,leader e,leader f,quality')->group(function () {
            Route::prefix('qpr')->name('qpr.')->group(function () {
                Route::get('/', [QprReportController::class, 'index'])->name('index');
                Route::get('/create', [QprReportController::class, 'create'])->name('create');
                Route::post('/store', [QprReportController::class, 'store'])->name('store');
                Route::get('/show/{id}', [QprReportController::class, 'show'])->name('show')->where('id', '.*');
                Route::get('/edit/{id}', [QprReportController::class, 'edit'])->name('edit')->where('id', '.*');
                Route::put('/update/{id}', [QprReportController::class, 'update'])->name('update')->where('id', '.*');
                Route::get('/export-pdf/{id}', [QprReportController::class, 'exportPdf'])->name('export.pdf')->where('id', '.*');
                Route::get('/add-masalah-row/{index}', [QprReportController::class, 'addMasalahRow']);
                Route::get('/add-verifikasi-row/{index}', [QprReportController::class, 'addVerifikasiRow']);
                Route::get('/get-job-detail/{id}', [QprReportController::class, 'getJobDetail']);
                Route::get('/get-items-by-date', [QprReportController::class, 'getItemsByDate'])->name('getItemsByDate');
            });
        });

        // --- 3. Daily Report (Ops, Admin, SPV, PPC) ---
        Route::middleware('role:admin,supervisor,foreman,leader k,leader e,leader f,ppc')->group(function () {
            Route::prefix('daily-report')->name('dailyreport.')->group(function () {
                Route::get('/', [DailyReportController::class, 'index'])->name('index');
                Route::get('/excel', [DailyReportController::class, 'exportExcel'])->name('excel');
                Route::get('/pdf', [DailyReportController::class, 'exportPdf'])->name('pdf');
            });
        });
    });

    // ==========================================
    // KELOMPOK KHUSUS: ASAKAI (Hanya Admin & SPV)
    // ==========================================
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::prefix('report/asakai')->name('report.asakai.')->group(function () {
            Route::get('/', [AsakaiReportController::class, 'index'])->name('index');
            Route::get('/manage/{id?}', [AsakaiReportController::class, 'create'])->name('create');
            Route::post('/store', [AsakaiReportController::class, 'store'])->name('store');
            Route::get('/show/{id}', [AsakaiReportController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [AsakaiReportController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [AsakaiReportController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [AsakaiReportController::class, 'destroy'])->name('destroy');
            Route::get('/export-excel', [AsakaiReportController::class, 'exportExcel'])->name('export');
        });
    });

}); // Penutup middleware auth