@extends('Produksi.layouts.main')

@section('title', 'Daily Report')
@section('page-title', 'Daily Report')

@section('card-actions')
<div class="export-actions" style="display: flex; gap: 10px;">
    <button onclick="doExport('pdf')" class="btn-export-pdf" style="background-color: #f82b3d; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-file-pdf"></i> Eksport PDF
    </button>
    <button onclick="doExport('excel')" class="btn-export-excel" style="background-color: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-file-excel"></i> Eksport Excel
    </button>
</div>
@endsection

@section('content')
<style>
    .table-report { width: 100%; border-collapse: collapse; font-size: 8px; background: white; margin-bottom: 20px; }
    .table-report th { background-color: #faeeb1; color: #000; border: 1px solid #000; padding: 2px; text-align: center; vertical-align: middle; }
    .table-report td { border: 1px solid #000; padding: 2px; text-align: center; vertical-align: middle; }
    .table-summary { width: 100%; min-width: 260px; border-collapse: collapse; font-size: 11px; font-weight: bold; }
    .table-summary th { background-color: #faeeb1; border: 1px solid #000; padding: 6px; text-align: center; }
    .table-summary td { border: 1px solid #000; padding: 6px; text-align: center; }
    .info-box { border: 1px solid #000; border-collapse: collapse; width: 100%; min-width: 260px; font-size: 11px; }
    .info-box td { border: 1px solid #000; padding: 5px 10px; }
    
    /* 🛠️ SINKRONISASI FORCE FIX: Mengamankan Tabel Tanda Tangan Biar Stabil Pas Split Screen */
    .header-sign-table { width: 100%; border-collapse: collapse; background: white; table-layout: fixed; }
    .header-sign-table th { border: 1px solid #000; text-align: center; padding: 5px; font-size: 11px; background-color: #faeeb1; }
    .header-sign-table td { border: 1px solid #000; text-align: center; padding: 0; font-size: 11px; }
    .select-sign { width: 100%; border: none; background: transparent; padding: 4px; font-size: 11px; font-weight: bold; text-align: center; cursor: pointer; }
    
    /* 🛠️ CONTAINER GRID UNTUK HEADER ATAS */
    .report-top-bar {
        display: grid;
        grid-template-columns: minmax(280px, 320px) 1fr minmax(320px, 500px);
        gap: 20px;
        align-items: flex-start;
        margin-bottom: 25px;
    }

    /* Penyesuaian Responsif Saat Split Screen Di-resize Mengecil */
    @media (max-width: 1200px) {
        .report-top-bar {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
            gap: 15px;
        }
    }

    /* Penanda Running */
    .row-running { background-color: #d1ecf1 !important; }
    .badge-running { color: #0c5460; font-size: 7px; background: #bee5eb; padding: 1px 3px; border-radius: 3px; display: inline-block; margin-top: 2px; font-weight: 800; border: 1px solid #abdde5; }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span> 
    <span>Report</span> <span class="separator">></span> 
    <span class="active" style="color: #f82b3d; font-weight: 800;">Daily Report</span>
</div>

{{-- TOOLBAR FILTER --}}
<div class="table-toolbar" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px;">
    <div style="display: flex; gap: 15px; align-items: flex-start; flex-wrap: wrap;">
        
        {{-- Filter Start Date --}}
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-size: 11px; font-weight: 800; color: #888; text-transform: uppercase;">Dari Tanggal</label>
            <input type="date" id="startDate" class="form-control" 
                style="width: 160px; height: 40px; border-radius: 8px; border: 1px solid #ddd; padding: 0 10px; font-weight: 600;" 
                value="{{ request('start_date', date('Y-m-01')) }}">
        </div>

        {{-- Filter End Date --}}
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-size: 11px; font-weight: 800; color: #888; text-transform: uppercase;">Sampai Tanggal</label>
            <input type="date" id="endDate" class="form-control" 
                style="width: 160px; height: 40px; border-radius: 8px; border: 1px solid #ddd; padding: 0 10px; font-weight: 600;" 
                value="{{ request('end_date', date('Y-m-t')) }}">
        </div>

        {{-- Filter Line --}}
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-size: 11px; font-weight: 800; color: #888; text-transform: uppercase;">Line Produksi</label>
            <select id="filterLine" class="form-select" style="width: 140px; height: 40px; border-radius: 8px; border: 1px solid #ddd; padding: 0 10px; font-weight: 600; cursor: pointer;">
                <option value="">All Line</option>
                @foreach($lines->unique('NamaProductionLine') as $l)
                    <option value="{{ $l->NamaProductionLine }}" {{ request('line_name') == $l->NamaProductionLine ? 'selected' : '' }}>
                        {{ $l->NamaProductionLine }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Shift --}}
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-size: 11px; font-weight: 800; color: #888; text-transform: uppercase;">Shift</label>
            <select id="filterShift" class="form-select" style="width: 130px; height: 40px; border-radius: 8px; border: 1px solid #ddd; padding: 0 10px; font-weight: 600; cursor: pointer;">
                <option value="All Shift" {{ request('shift') == 'All Shift' ? 'selected' : '' }}>All Shift</option>
                <option value="Shift 1" {{ request('shift') == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                <option value="Shift 2" {{ request('shift') == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
            </select>
        </div>
    </div>
</div>

<form id="formReport" action="{{ route('report.dailyreport.pdf') }}" method="GET" target="_blank">
    <input type="hidden" name="start_date" value="{{ $startDate }}">
    <input type="hidden" name="end_date" value="{{ $endDate }}">
    <input type="hidden" name="line_name" value="{{ $lineName }}">
    <input type="hidden" name="shift" value="{{ $shift }}">

    <div class="report-top-bar">
        
        {{-- BLOK 1: INFO BOX --}}
        <div>
            <table class="info-box">
                <tr>
                    <td style="background: #f8f9fa; width: 40%;">Production Line</td>
                    <td style="font-weight: bold;">{{ $lineName ?? 'Semua Line' }}</td>
                </tr>
                <tr>
                    <td style="background: #f8f9fa;">Shift Kerja</td>
                    <td style="font-weight: bold;">{{ $shift === 'All Shift' ? 'Shift 1 & 2' : ($shift ?? 'Shift 1') }}</td>
                </tr>
                <tr>
                    <td style="background: #f8f9fa;">Periode Laporan</td>
                    <td style="font-weight: bold;">
                        @if($startDate == $endDate)
                            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} 
                            s/d 
                            {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- BLOK 2: SPACER TENGAH (KOSONG AGAR MENDORONG SIGN KE KANAN PAS LAYAR LEBAR) --}}
        <div class="report-spacer"></div>

        {{-- BLOK 3: TABEL TANDA TANGAN (RESPONSIF & ANTI-LUBER) --}}
        <div>
            <table class="header-sign-table">
                <thead>
                    <tr>
                        <th width="33%">Supervisor</th>
                        <th width="33%">Foreman</th>
                        <th width="34%">T.Leader</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="display: flex; flex-direction: column; height: 110px;">
                                <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: #eee; font-style: italic; font-size: 10px;">Tanda Tangan</span>
                                </div>
                                <div style="padding: 5px; background: #f9f9f9; border-top: 1px solid #000;">
                                    <select name="spv_name" class="select-sign" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($supervisors as $s)
                                            <option value="{{ $s->NamaKaryawan }}">{{ $s->NamaKaryawan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; height: 110px;">
                                <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: #eee; font-style: italic; font-size: 10px;">Tanda Tangan</span>
                                </div>
                                <div style="padding: 5px; background: #f9f9f9; border-top: 1px solid #000;">
                                    <select name="foreman_name" class="select-sign" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($foremen as $f)
                                            <option value="{{ $f->NamaKaryawan }}">{{ $f->NamaKaryawan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; height: 110px;">
                                <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: #eee; font-style: italic; font-size: 10px;">Tanda Tangan</span>
                                </div>
                                <div style="padding: 5px; background: #f9f9f9; border-top: 1px solid #000;">
                                    <select name="leader_name" id="leaderSelect" class="select-sign">
                                        <option value="">-- Pilih --</option>
                                        @foreach($leaders as $l)
                                            <option value="{{ $l->NamaKaryawan }}">{{ $l->NamaKaryawan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
</form>

{{-- TABEL DATA RENCANA PRODUKSI --}}
<div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 60%; overflow-x: auto;">
        <table class="table-report">
            <thead>
                <tr>
                    <th rowspan="3">No</th>
                    <th rowspan="3">Tgl</th> 
                    <th rowspan="3">Jobs No</th>
                    <th colspan="2">Plan QTY Pcs</th>
                    <th colspan="2">Schedule</th>
                    <th rowspan="3">Press Time (Min)</th>
                    <th colspan="4">Uchi Dandori (Min)</th>
                    <th rowspan="3">TPT (Min)</th>
                    <th rowspan="3">UBP</th>
                    <th rowspan="3">DTR</th>
                    <th rowspan="3">Work Time (Min)</th>
                    <th rowspan="3">GSPH</th>
                </tr>
                <tr>
                    <th rowspan="2">A</th><th rowspan="2">B</th>
                    <th rowspan="2">Start</th><th rowspan="2">Finish</th>
                    <th colspan="2">Dies Change</th><th colspan="2">1ST Q-Check</th>
                </tr>
                <tr><th>A</th><th>B</th><th>A</th><th>B</th></tr>
            </thead>
            <tbody>
                @forelse($planData as $index => $row)
                <tr class="{{ $row->StatusProses === 'Running' ? 'row-running' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #f82b3d; text-align: center;">
                        {{ \Carbon\Carbon::parse($row->TanggalProduksi)->format('d/m') }}
                    </td>
                    <td style="font-weight: bold; text-align: left; padding-left: 10px;">
                        {{ $row->item->JobNumber ?? '-' }}
                        @if($row->StatusProses === 'Running')
                            <br><span class="badge-running">RUNNING</span>
                        @endif
                    </td>
                    <td>{{ number_format($row->plan_data->PlanQtyA ?? 0, 0) }}</td>
                    <td>{{ number_format($row->plan_data->PlanQtyB ?? 0, 0) }}</td>
                    <td>{{ ($row->plan_data && $row->plan_data->PlanStart) ? date('H:i', strtotime($row->plan_data->PlanStart)) : '-' }}</td>
                    <td>{{ ($row->plan_data && $row->plan_data->PlanFinish) ? date('H:i', strtotime($row->plan_data->PlanFinish)) : '-' }}</td>
                    <td>{{ number_format($row->plan_data->PressTime ?? 0, 1) }}</td>
                    <td>{{ number_format($row->plan_data->DiesChangeUchi ?? 0, 1) }}</td>
                    <td>-</td>
                    <td>{{ number_format($row->plan_data->FirstQCheck ?? 0, 1) }}</td>
                    <td>-</td>
                    <td>{{ number_format($row->plan_data->TPT ?? 0, 1) }}</td>
                    <td>{{ number_format($row->plan_data->UBP ?? 0, 1) }}</td>
                    <td>{{ number_format($row->plan_data->DTR ?? 0, 1) }}</td>
                    <td>{{ number_format($row->plan_data->PlanWorkTime ?? 0, 1) }}</td>
                    <td>{{ number_format($row->plan_data->PlanGSPH ?? 0, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="17" class="py-4" style="text-align: center; font-weight: bold;">Belum ada data rencana produksi resmi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- TABEL SUMMARY ACHIEVEMENT (DIBUAT ELASTIS) --}}
    <div style="flex: 0 0 300px;">
        <table class="table-summary">
            <tr><th colspan="2">Summary Achievement</th></tr>
            <tr><td style="text-align: left;">REPAIR RATE</td><td style="background: #eee; width: 80px;">{{ number_format($summary->RepairRate ?? 0, 2) }}%</td></tr>
            <tr><td style="text-align: left;">REJECT RATE</td><td style="background: #eee;">{{ number_format($summary->RejectRate ?? 0, 2) }}%</td></tr>
            <tr><td style="text-align: left;">GSPH</td><td style="background: #eee;">{{ number_format($summary->AktualGSPH ?? 0, 0) }}</td></tr>
            <tr><td style="text-align: left;">AVAILABILITY</td><td style="background: #eee;">{{ number_format($summary->Availability ?? 0, 2) }}%</td></tr>
            <tr><td style="text-align: left;">PERFORMANCE</td><td style="background: #eee;">{{ number_format($summary->Performance ?? 0, 2) }}%</td></tr>
            <tr><td style="text-align: left;">QUALITY</td><td style="background: #eee;">{{ number_format($summary->QualityRate ?? 0, 2) }}%</td></tr>
            <tr><th style="text-align: left; background: #faeeb1;">OEE</th><th style="background: #faeeb1;">{{ number_format($summary->OEE ?? 0, 2) }}%</th></tr>
        </table>
    </div>
</div>

{{-- TABEL ACTUAL PROSES --}}
<div style="margin-top: 20px;">
    <h5 style="font-size: 13px; font-weight: bold; margin-bottom: 8px;">ACTUAL PROSES</h5>
    <div style="overflow-x: auto;">
        <table class="table-report" style="min-width: 2600px;">
            <thead>
                <tr>
                    <th rowspan="3">No</th>
                    <th rowspan="3">Tgl</th>
                    <th rowspan="3" style="width: 80px;">Jobs No</th>
                    <th colspan="2">Plan QTY (PCS)</th>
                    <th colspan="12">Aktual QTY (PCS)</th>
                    <th colspan="2">Plan Schedule</th>
                    <th colspan="2">Aktual Proses</th>
                    <th colspan="2">TPT</th>
                    <th rowspan="3">Press Time (Min)</th>
                    <th colspan="2">CT Aktual (Detik)</th>
                    <th rowspan="3">Soto Dandori</th>
                    <th colspan="3">Uchi Dandori (Min)</th>
                    <th colspan="6">Down Time (Min)</th>
                    <th colspan="2">Idle Time</th>
                    <th colspan="2">Break Time</th>
                    <th rowspan="3">Work Time</th>
                    <th colspan="3">Quality Rate</th>
                    <th rowspan="3">OEE</th>
                    <th rowspan="3">GSPH</th>
                </tr>
                <tr>
                    <th rowspan="2">A</th><th rowspan="2">B</th>
                    <th colspan="2">Good</th>
                    <th colspan="4">Repair</th>
                    <th colspan="4">Reject</th>
                    <th colspan="2">Total</th>
                    <th rowspan="2">Start</th><th rowspan="2">Finish</th>
                    <th rowspan="2">Start</th><th rowspan="2">Finish</th>
                    <th rowspan="2">Plan</th><th rowspan="2">Act</th>
                    <th rowspan="2">Line Mntr</th><th rowspan="2">LKH Calc</th>
                    <th rowspan="2">Dies Chg</th><th rowspan="2">Early Chk</th><th rowspan="2">Total</th>
                    <th rowspan="2">Dies</th><th rowspan="2">Mach</th><th rowspan="2">Matl</th><th rowspan="2">Pallet</th><th rowspan="2">P/H</th><th rowspan="2">Total</th>
                    <th rowspan="2">Type</th><th rowspan="2">Time</th><th rowspan="2">Type</th><th rowspan="2">Time</th>
                    <th rowspan="2">Pass</th><th rowspan="2">Rep</th><th rowspan="2">Rej</th>
                </tr>
                <tr>
                    <th>A</th><th>B</th>
                    <th>A</th><th>B</th><th>Tot</th><th>Nama Kerusakan</th>
                    <th>A</th><th>B</th><th>Tot</th><th>Nama Kerusakan</th>
                    <th>A</th><th>B</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $row)
                    @php
                        $totalUchi = ($row->DiesChange ?? 0) + ($row->EarlyCheck ?? 0);
                        $repairTotal = ($row->RepairA ?? 0) + ($row->RepairB ?? 0);
                        $rejectTotal = ($row->RejectA ?? 0) + ($row->RejectB ?? 0);

                        $timeToMin = function($t) {
                            if (!$t || $t == '00:00:00') return 0;
                            $p = explode(':', $t);
                            return ($p[0] * 60) + $p[1] + ($p[2] / 60);
                        };

                        $dtDetails = \DB::table('prod_detaildowntime')->where('IdInputHarian', $row->IdInputHarian)->get();
                        $dtDies = $dtDetails->filter(fn($i) => stripos($i->TipeDowntime, 'DIES') !== false)->sum(fn($i) => $timeToMin($i->Durasi));
                        $dtMach = $dtDetails->filter(fn($i) => stripos($i->TipeDowntime, 'MACHINE') !== false)->sum(fn($i) => $timeToMin($i->Durasi));
                        $dtMatl = $dtDetails->filter(fn($i) => stripos($i->TipeDowntime, 'MATERIAL') !== false)->sum(fn($i) => $timeToMin($i->Durasi));
                        $dtPall = $dtDetails->filter(fn($i) => stripos($i->TipeDowntime, 'PALLET') !== false)->sum(fn($i) => $timeToMin($i->Durasi));
                        $dtPH   = $dtDetails->filter(fn($i) => stripos($i->TipeDowntime, 'P/H') !== false)->sum(fn($i) => $timeToMin($i->Durasi));

                        $listRepair = \DB::table('prod_detailrepair')->where('IdInputHarian', $row->IdInputHarian)->distinct()->pluck('NamaKerusakan')->implode(', ');
                        $listReject = \DB::table('prod_detailreject')->where('IdInputHarian', $row->IdInputHarian)->distinct()->pluck('NamaKerusakan')->implode(', ');

                        $idleRaw = \DB::table('prod_detailidletime as di')
                            ->join('prod_msidletime as mi', 'di.IdIdleTime', '=', 'mi.IdIdleTime')
                            ->where('di.IdInputHarian', $row->IdInputHarian)
                            ->select('mi.TipeIdleTime', 'di.Durasi')->first();
                        $idleType = $idleRaw->TipeIdleTime ?? '-';
                        $idleMin  = $timeToMin($idleRaw->Durasi ?? null);
                    @endphp
                <tr class="{{ $row->StatusProses === 'Running' ? 'row-running' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #f82b3d;">{{ \Carbon\Carbon::parse($row->TanggalProduksi)->format('d/m') }}</td>
                    <td style="font-weight: bold;">
                        {{ $row->item->JobNumber ?? '-' }}
                        @if($row->StatusProses === 'Running')
                            <br><span class="badge-running">RUNNING</span>
                        @endif
                    </td>
                    <td>{{ number_format($row->plan_data->PlanQtyA ?? 0, 0) }}</td>
                    <td>{{ number_format($row->plan_data->PlanQtyB ?? 0, 0) }}</td>
                    <td>{{ number_format($row->GoodA, 0) }}</td>
                    <td>{{ number_format($row->GoodB, 0) }}</td>
                    
                    <td>{{ number_format($row->RepairA, 0) }}</td>
                    <td>{{ number_format($row->RepairB, 0) }}</td>
                    <td>{{ number_format($repairTotal, 0) }}</td>
                    <td style="font-size: 8px; text-align: left; max-width: 150px; overflow-wrap: break-word;">{{ $listRepair ?: '-' }}</td>
                    
                    <td>{{ number_format($row->RejectA, 0) }}</td>
                    <td>{{ number_format($row->RejectB, 0) }}</td>
                    <td>{{ number_format($rejectTotal, 0) }}</td>
                    <td style="font-size: 8px; text-align: left; max-width: 150px; overflow-wrap: break-word;">{{ $listReject ?: '-' }}</td>

                    <td style="background: #f8f9fa; font-weight: bold;">{{ number_format($row->AktualQtyA, 0) }}</td>
                    <td style="background: #f8f9fa; font-weight: bold;">{{ number_format($row->AktualQtyB, 0) }}</td>
                    <td style="background: #fdfae3;">{{ ($row->plan_data->PlanStart ?? false) ? date('H:i', strtotime($row->plan_data->PlanStart)) : '-' }}</td>
                    <td style="background: #fdfae3;">{{ ($row->plan_data->PlanFinish ?? false) ? date('H:i', strtotime($row->plan_data->PlanFinish)) : '-' }}</td>
                    <td>{{ $row->AktualStart ? date('H:i', strtotime($row->AktualStart)) : '-' }}</td>
                    <td>{{ $row->AktualFinish ? date('H:i', strtotime($row->AktualFinish)) : '-' }}</td>
                    <td>{{ number_format($row->plan_data->TPT ?? 0, 1) }}</td>
                    <td>{{ number_format($row->TPT, 1) }}</td>
                    <td>{{ number_format($row->PressTime ?? 0, 1) }}</td>
                    <td>{{ number_format($row->LineMonitoring, 1) }}</td>
                    <td>{{ number_format($row->LKHCalculation, 1) }}</td>
                    <td>{{ number_format($row->plan_data->DiesChangeSoto ?? 0, 1) }}</td>
                    <td>{{ number_format($row->DiesChange, 1) }}</td>
                    <td>{{ number_format($row->EarlyCheck, 1) }}</td>
                    <td style="font-weight: bold;">{{ number_format($totalUchi, 1) }}</td>
                    <td>{{ number_format($dtDies, 1) }}</td>
                    <td>{{ number_format($dtMach, 1) }}</td>
                    <td>{{ number_format($dtMatl, 1) }}</td>
                    <td>{{ number_format($dtPall, 1) }}</td>
                    <td>{{ number_format($dtPH, 1) }}</td>
                    <td style="font-weight: bold; background: #eee;">{{ number_format($row->TotalDowntime, 1) }}</td>
                    <td>{{ $idleType }}</td>
                    <td>{{ number_format($idleMin, 1) }}</td>
                    <td>{{ $row->TypeBreakTime ?? '-' }}</td>
                    <td>{{ number_format($row->TimeBreakTime, 1) }}</td>
                    <td>{{ number_format($row->AktualWorkTime ?? 0, 1) }}</td>
                    <td>{{ number_format($row->PassRate, 2) }}%</td>
                    <td>{{ number_format($row->RepairRate, 2) }}%</td>
                    <td>{{ number_format($row->RejectRate, 2) }}%</td>
                    <td style="background: #faeeb1; font-weight: bold;">{{ number_format($row->OEE, 2) }}%</td>
                    <td>{{ number_format($row->AktualGSPH ?? 0, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<script>
// Otomatis update filter kalau Start Date diganti
document.getElementById('startDate').addEventListener('change', function() {
    updateFilter();
});

// Otomatis update filter kalau End Date diganti
document.getElementById('endDate').addEventListener('change', function() {
    updateFilter();
});

// Listener Line yang udah lo punya tadi
document.getElementById('filterLine').addEventListener('change', function() {
    updateFilter();
});

// Tambahin juga buat Shift biar makin gacor
document.getElementById('filterShift').addEventListener('change', function() {
    updateFilter();
});
// FUNGSI UPDATE FILTER (DIGUNAKAN TOMBOL CARI)
function updateFilter() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    const line = document.getElementById('filterLine').value;
    const shift = document.getElementById('filterShift').value;

    let url = new URL(window.location.href);
    url.searchParams.set('start_date', start);
    url.searchParams.set('end_date', end);
    url.searchParams.set('line_name', line);
    url.searchParams.set('shift', shift);

    window.location.href = url.toString();
}

// FUNGSI EKSPORT (PDF & EXCEL)
function doExport(type) {
    // 1. Ambil data dari ID yang baru (Range Tanggal)
    const start = document.getElementById('startDate').value; // Ganti filterMonth
    const end = document.getElementById('endDate').value;     // Ganti filterDate
    const lineName = document.getElementById('filterLine').value;
    const shift = document.getElementById('filterShift').value;
    
    // Ambil data tanda tangan
    const form = document.getElementById('formReport');
    const spv = form.querySelector('select[name="spv_name"]').value;
    const foreman = form.querySelector('select[name="foreman_name"]').value;
    const leader = form.querySelector('select[name="leader_name"]').value;

    // Validasi Tanda Tangan
    if (!spv || !foreman || !leader) {
        Swal.fire({
            icon: 'warning',
            title: 'Otorisasi Belum Lengkap!',
            text: 'Silahkan pilih Nama Supervisor, Foreman, dan Team Leader terlebih dahulu.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Oke'
        });
        return; 
    }

    // 2. Persiapkan Parameter (Sesuaikan key-nya dengan Controller baru)
    const params = new URLSearchParams({ 
        start_date: start, // Pakai key baru
        end_date: end,     // Pakai key baru
        line_name: lineName, 
        shift: shift, 
        spv_name: spv, 
        foreman_name: foreman, 
        leader_name: leader 
    });

    if (type === 'pdf') {
        Swal.fire({
            title: 'Sedang Memproses...',
            text: 'Laporan sedang dibuat, mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        // Untuk PDF, kita buka di tab baru agar lebih rapi
        let pdfUrl = "{{ route('report.dailyreport.pdf') }}?" + params.toString();
        window.open(pdfUrl, '_blank');
        
        setTimeout(() => { Swal.close(); }, 2000);
    } else {
        // Untuk EXCEL, langsung download
        let excelUrl = "{{ route('report.dailyreport.excel') }}?" + params.toString();
        window.location.href = excelUrl;
    }
}
</script>
@endsection