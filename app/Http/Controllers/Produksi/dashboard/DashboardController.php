<?php

namespace App\Http\Controllers\Produksi\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Produksi\Master\Customer;
use App\Models\Produksi\Master\Karyawan;
use App\Models\Produksi\Master\ItemProduction;
use App\Models\Produksi\Master\ProductionLine;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil hitungan jumlah data untuk statistik
        $count = [
            'karyawan' => Karyawan::count(),
            'customer' => Customer::count(),
            'item'     => ItemProduction::count(),
            'line'     => ProductionLine::count(),
        ];

        return view('Produksi.dashboard.index', compact('count'));
    }
}