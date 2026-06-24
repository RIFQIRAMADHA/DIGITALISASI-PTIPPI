<?php

namespace App\Http\Controllers\Produksi\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi\Master\MsProductionLine;
use App\Models\Produksi\Master\MsKaryawan;
use App\Models\Produksi\Transaksi\TrsInputHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyReportExport;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01')); 
        $endDate   = $request->get('end_date', date('Y-m-d')); 
        $lineName  = $request->get('line_name'); 
        $shift     = $request->get('shift', 'All Shift');

        $lines = MsProductionLine::where('Status', 1)->get();
        $supervisors = MsKaryawan::where('Jabatan', 'supervisor')->where('Status', 1)->get();
        $foremen     = MsKaryawan::where('Jabatan', 'foreman')->where('Status', 1)->get();

        $queryLeader = MsKaryawan::where('Status', 1);
        if ($lineName && $lineName !== 'All Line') {
            $suffix = strtolower(substr(trim($lineName), -1)); 
            $queryLeader->where('Jabatan', 'leader ' . $suffix);
        } else {
            $queryLeader->where('Jabatan', 'LIKE', '%leader%');
        }
        $leaders = $queryLeader->orderBy('Jabatan', 'asc')->get();

        // 1. Ambil SEMUA data untuk Tabel Actual (Termasuk MAN)
        $data = $this->getFilteredData($startDate, $endDate, $lineName, $shift);
        
        // 2. Filter khusus untuk Tabel Rencana (Hanya yang BUKAN manual)
        // Kita filter koleksi $data yang IdInputHarian-nya tidak mengandung 'MAN'
        $planDataOnly = $data->filter(function($item) {
            return !str_contains($item->IdInputHarian, 'MAN');
        });

        $summary = $this->calculateSummary($data);

        return view('Produksi.report.dailyreport.index', [
            'data'          => $data,         // Buat tabel ACTUAL (Bawah)
            'planData'      => $planDataOnly, // Buat tabel RENCANA (Atas) - BERSIH DARI 'MAN'
            'lines'         => $lines,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'lineName'      => $lineName,
            'shift'         => $shift,
            'summary'       => $summary, 
            'supervisors'   => $supervisors,
            'foremen'       => $foremen,
            'leaders'       => $leaders
        ]);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $lineName  = $request->get('line_name');
        $shift     = $request->get('shift', 'All Shift');
        
        $spv_name     = $request->get('spv_name');
        $foreman_name = $request->get('foreman_name');
        $leader_name  = $request->get('leader_name');

        // 1. Tarik semua data (Termasuk MAN)
        $data = $this->getFilteredData($startDate, $endDate, $lineName, $shift);

        // 2. ✅ FILTER KHUSUS: Buang item 'MAN' buat tabel rencana atas
        $planData = $data->filter(function($item) {
            return !str_contains($item->IdInputHarian, 'MAN');
        });

        $summary = $this->calculateSummary($data);

        $pdf = Pdf::loadView('Produksi.report.dailyreport.pdf', [
            'data'          => $data,     // Buat tabel ACTUAL (Bawah)
            'planData'      => $planData, // Buat tabel RENCANA (Atas) - BERSIH DARI 'MAN'
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'lineName'      => $lineName,
            'shift'         => $shift,
            'summary'       => $summary,
            'spv_name'      => $spv_name,
            'foreman_name'  => $foreman_name,
            'leader_name'   => $leader_name
        ]);

        $nameFile = 'Daily_Report_' . $startDate . '_to_' . $endDate;

        return $pdf->setPaper('a4', 'landscape')
            ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true])
            ->stream($nameFile.'.pdf');
    }

    public function exportExcel(Request $request) 
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $lineName  = $request->get('line_name');
        $shift     = $request->get('shift', 'All Shift');
        
        $spv_name     = $request->get('spv_name');
        $foreman_name = $request->get('foreman_name');
        $leader_name  = $request->get('leader_name');

        $data = $this->getFilteredData($startDate, $endDate, $lineName, $shift);
        $summary = $this->calculateSummary($data);

        $nameFile = 'Daily_Report_' . $startDate . '_to_' . $endDate;

        return Excel::download(
            // Sesuaikan parameter di constructor DailyReportExport lo kalau perlu
            new DailyReportExport($data, $summary, $startDate, $endDate, $lineName, $shift, $spv_name, $foreman_name, $leader_name), 
            $nameFile.'.xlsx'
        );
    }

    private function getFilteredData($startDate, $endDate, $lineName, $shift)
    {
        $query = TrsInputHarian::with(['item.customer', 'productionLine', 'karyawan']);

        // ✅ Filter berdasarkan Range Tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('TanggalProduksi', [$startDate, $endDate]);
        }

        // Filter Line
        if ($lineName && $lineName !== 'all') {
            $query->whereHas('productionLine', function($q) use ($lineName) {
                $q->where('NamaProductionLine', $lineName); 
            });
        }

        // Filter Shift
        if ($shift && $shift !== 'All Shift') {
            $query->whereHas('productionLine', function($q) use ($shift) {
                $cleanShift = str_replace('Shift ', '', $shift);
                $q->where('Shift', 'LIKE', '%' . $cleanShift . '%');
            });
        }

        $results = $query->orderBy('TanggalProduksi', 'asc')
                        ->orderBy('AktualStart', 'asc')
                        ->get();

        // Mapping Plan Data (tetap seperti semula)
        foreach ($results as $item) {
            $parts = explode('-', $item->IdInputHarian);
            $idPlan = isset($parts[1]) ? trim($parts[1]) : null;

            $item->plan_data = DB::table('prod_detailplanscheduleproduksi')
                        ->where('IdPlanSchedule', $idPlan)
                        ->where('IdItemProduksi', $item->IdItemProduksi)
                        ->first();
        }

        return $results;
    }

    private function calculateSummary($data)
    {
        if ($data->isEmpty()) {
            return (object)[
                'RepairRate' => 0, 'RejectRate' => 0, 'AktualGSPH' => 0,
                'Availability' => 0, 'Performance' => 0, 'QualityRate' => 0, 'OEE' => 0
            ];
        }

        return (object)[
            'RepairRate'   => $data->avg('RepairRate'),
            'RejectRate'   => $data->avg('RejectRate'),
            'AktualGSPH'   => $data->avg('AktualGSPH'),
            'Availability' => $data->avg('Availability'),
            'Performance'  => $data->avg('Performance'),
            'QualityRate'  => $data->avg('QualityRate'),
            'OEE'          => $data->avg('OEE'),
        ];
    }
}