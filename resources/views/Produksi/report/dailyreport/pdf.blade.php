<!DOCTYPE html>
<html>
<head>
    <title>Daily Report</title>
    <style>
        @page { margin: 0.3cm; }
        body { font-family: Arial, sans-serif; font-size: 6px; line-height: 1.1; color: #000; margin: 0; padding: 0; }
        
        /* General Table Style */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 2px 1px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        
        /* --- HEADER SECTION --- */
        .header-table { margin-bottom: 10px; border: 1px solid #000; }
        .header-table td { border: 1px solid #000; padding: 0; }

        /* Logo Column */
        .logo-column { width: 28%; padding: 5px !important; text-align: left; }
        .logo-container { width: 100%; }
        .logo-img { width: 120px; vertical-align: middle; float: left; }
        .logo-text { float: left; font-size: 10px; font-weight: bold; line-height: 1.2; padding-left: 8px; text-align: left; margin-top: 4px; }

        /* Title Column */
        .title-column { width: 44%; font-size: 13px; font-weight: bold; text-align: center; }

        /* Sign Column */
        .sign-column { width: 28%; }
        .sign-table { width: 100%; border-collapse: collapse; border: none !important; }
        
        .sign-table th, .sign-table td { border: none; border-right: 1px solid #000; text-align: center; }
        .sign-table th:last-child, .sign-table td:last-child { border-right: none !important; }
        .sign-table th { background-color: #faeeb1 !important; height: 12px; border-bottom: 1px solid #000; }
        .sign-table .ttd-area td { height: 50px; border-bottom: 1px solid #000; } 
        .sign-table .name-row td { height: 18px; font-weight: bold; border-bottom: none; }

        /* 🛠️ STRUKTUR FIX: Blok Pembungkus CSS Float Pengunci Kesejajaran Atas */
        .row-container { width: 100%; margin-top: 5px; clear: both; }
        .col-plan-table { width: 81%; float: left; }
        .col-summary-table { width: 18%; float: right; }

        .clear { clear: both; }

        /* --- DATA TABLE SECTION --- */
        .section-title { font-size: 8px; font-weight: bold; margin: 8px 0 3px 0; text-align: left; clear: both; }
        .bg-gray { background-color: #f2f2f2 !important; }
        .bg-yellow { background-color: #faeeb1 !important; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-column">
                <div class="logo-container">
                    <img src="{{ public_path('images/logo-ippi.png') }}" class="logo-img">
                    <div class="logo-text">
                        PT. INTI PANTJA PRESS<br>
                        INDUSTRI<br>
                        KARAWANG PLANT
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
            <td class="title-column">
                LAPORAN KERJA HARIAN STAMPING PRESS
            </td>
            <td class="sign-column">
                <table class="sign-table">
                    <thead>
                        <tr>
                            <th width="33%">Supervisor</th>
                            <th width="33%">Foreman</th>
                            <th width="34%">T.Leader</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ttd-area">
                            <td></td><td></td><td></td>
                        </tr>
                        <tr class="name-row">
                            <td>{{ $spv_name ?? '....................' }}</td>
                            <td>{{ $foreman_name ?? '....................' }}</td>
                            <td>{{ $leader_name ?? '....................' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <table style="border: none; font-size: 8px; font-weight: bold; margin-bottom: 5px; width: 100%;">
        <tr>
            <td style="border: none; text-align: left; width: 33%;">Line: {{ $lineName ?? 'Semua Line' }}</td>
            <td style="border: none; text-align: center; width: 33%;">Shift: {{ $shift }}</td>
            <td style="border: none; text-align: right; width: 33%;">
                Periode: 
                @if($startDate == $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                @else
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                @endif
            </td>
        </tr>
    </table>

    <div class="row-container">
        
        {{-- SISI KIRI: TABEL PLAN DATA --}}
        <div class="col-plan-table">
            <table style="width: 100%;">
                <thead class="bg-yellow">
                    <tr>
                        <th rowspan="3" width="15">No</th>
                        <th rowspan="3" width="20">Tgl</th>
                        <th rowspan="3" width="40">Jobs No</th>
                        <th colspan="2">Plan QTY</th>
                        <th colspan="2">Schedule</th>
                        <th rowspan="3">Press (Min)</th>
                        <th colspan="4">Uchi Dandori (Min)</th>
                        <th rowspan="3">TPT</th><th rowspan="3">UBP</th><th rowspan="3">DTR</th><th rowspan="3">Work Time</th><th rowspan="3">GSPH</th>
                    </tr>
                    <tr>
                        <th rowspan="2">A</th><th rowspan="2">B</th>
                        <th rowspan="2">Start</th><th rowspan="2">Finish</th>
                        <th colspan="2">Dies Change</th><th colspan="2">1ST Q-Chk</th>
                    </tr>
                    <tr><th>A</th><th>B</th><th>A</th><th>B</th></tr>
                </thead>
                <tbody>
                    @foreach($planData as $index => $row)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ date('d/m', strtotime($row->TanggalProduksi)) }}</td>
                        <td style="font-weight: bold; padding-left: 5px; text-align: left;">{{ $row->item->JobNumber ?? '-' }}</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->PlanQtyA ?? 0) }}</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->PlanQtyB ?? 0) }}</td>
                        <td style="text-align: center;">{{ ($row->plan_data->PlanStarttime ?? null) ? date('H:i', strtotime($row->plan_data->PlanStarttime)) : '-' }}</td>
                        <td style="text-align: center;">{{ ($row->plan_data->PlanFinishtime ?? null) ? date('H:i', strtotime($row->plan_data->PlanFinishtime)) : '-' }}</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->PressTime ?? 0, 1) }}</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->DiesChangeUchi ?? 0, 1) }}</td><td style="text-align: center;">-</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->FirstQCheck ?? 0, 1) }}</td><td style="text-align: center;">-</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->TPT ?? 0, 1) }}</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->UBP ?? 0, 1) }}</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->DTR ?? 0, 1) }}</td>
                        <td style="text-align: center;">{{ number_format($row->AktualWorkTime ?? 0, 1) }}</td>
                        <td style="text-align: center;">{{ number_format($row->plan_data->PlanGSPH ?? 0, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- SISI KANAN: TABEL SUMMARY ACHIEVEMENT --}}
        <div class="col-summary-table">
            <table style="width: 100%;">
                <thead>
                    <tr class="bg-yellow"><th colspan="2" style="height: 11px;">Summary Achievement</th></tr>
                </thead>
                <tbody>
                    <tr><td style="text-align:left; padding-left: 4px;">REPAIR</td><td>{{ number_format($summary->RepairRate ?? 0, 2) }}%</td></tr>
                    <tr><td style="text-align:left; padding-left: 4px;">REJECT</td><td>{{ number_format($summary->RejectRate ?? 0, 2) }}%</td></tr>
                    <tr><td style="text-align:left; padding-left: 4px;">GSPH</td><td>{{ number_format($summary->AktualGSPH ?? 0, 0) }}</td></tr>
                    <tr><td style="text-align:left; padding-left: 4px;">AVAILABILITY</td><td>{{ number_format($summary->Availability ?? 0, 2) }}%</td></tr>
                    <tr><td style="text-align:left; padding-left: 4px;">PERFORMANCE</td><td>{{ number_format($summary->Performance ?? 0, 2) }}%</td></tr>
                    <tr><td style="text-align:left; padding-left: 4px;">QUALITY</td><td>{{ number_format($summary->QualityRate ?? 0, 2) }}%</td></tr>
                    <tr class="bg-yellow" style="font-weight:bold;"><td style="text-align:left; padding-left: 4px;">OEE</td><td>{{ number_format($summary->OEE ?? 0, 2) }}%</td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="clear"></div>
    </div>

    <div class="section-title">ACTUAL PROSES</div>
    <table>
        <thead class="bg-yellow">
            <tr>
                <th rowspan="3" width="10">No</th>
                <th rowspan="3" width="15">Tgl</th>
                <th rowspan="3" width="35">Jobs No</th>
                <th colspan="2">Plan QTY</th>
                <th colspan="12">Aktual QTY (PCS)</th>
                <th colspan="2">Plan Sch</th><th colspan="2">Aktual Pro</th>
                <th colspan="2">TPT</th><th rowspan="3">Press</th><th colspan="2">CT Akt</th><th rowspan="3">Soto</th>
                <th colspan="3">Uchi (Min)</th><th colspan="6">Down Time (Min)</th><th colspan="2">Idle</th><th colspan="2">Break</th>
                <th rowspan="3">Work</th><th colspan="3">Quality</th><th rowspan="3">OEE</th><th rowspan="3">GSPH</th>
            </tr>
            <tr>
                <th rowspan="2">A</th><th rowspan="2">B</th>
                <th colspan="2">Good</th>
                <th colspan="4">Repair</th>
                <th colspan="4">Reject</th>
                <th colspan="2">Total</th>
                <th rowspan="2">Strt</th><th rowspan="2">Fnsh</th><th rowspan="2">Strt</th><th rowspan="2">Fnsh</th>
                <th rowspan="2">Pln</th><th rowspan="2">Act</th><th rowspan="2">Mnt</th><th rowspan="2">LKH</th>
                <th rowspan="2">Dies</th><th rowspan="2">Chk</th><th rowspan="2">Tot</th>
                <th rowspan="2">Dies</th><th rowspan="2">Mch</th><th rowspan="2">Mat</th><th rowspan="2">Pal</th><th rowspan="2">P/H</th><th rowspan="2">Tot</th>
                <th rowspan="2">Nama</th><th rowspan="2">Tm</th><th rowspan="2">Nama</th><th rowspan="2">Tm</th>
                <th rowspan="2">Pas</th><th rowspan="2">Rep</th><th rowspan="2">Rej</th>
            </tr>
            <tr>
                <th>A</th><th>B</th><th>A</th><th>B</th><th>Tot</th><th>Nama Kerusakan</th>
                <th>A</th><th>B</th><th>Tot</th><th>Nama Kerusakan</th>
                <th>A</th><th>B</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            @php
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
                
                $idleRaw = \DB::table('prod_detailidletime as di')
                    ->join('prod_msidletime as mi', 'di.IdIdleTime', '=', 'mi.IdIdleTime')
                    ->where('di.IdInputHarian', $row->IdInputHarian)->select('mi.TipeIdleTime', 'di.Durasi')->first();
                
                $listRep = \DB::table('prod_detailrepair')->where('IdInputHarian', $row->IdInputHarian)->distinct()->pluck('NamaKerusakan')->implode(',');
                $listRej = \DB::table('prod_detailreject')->where('IdInputHarian', $row->IdInputHarian)->distinct()->pluck('NamaKerusakan')->implode(',');
            @endphp
            <tr class="{{ $row->StatusProses === 'Running' ? 'row-running' : '' }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ date('d/m', strtotime($row->TanggalProduksi)) }}</td>
                <td style="text-align:left; font-weight: bold;">{{ $row->item->JobNumber ?? '-' }}</td>
                <td>{{ number_format($row->plan_data->PlanQtyA ?? 0) }}</td>
                <td>{{ number_format($row->plan_data->PlanQtyB ?? 0) }}</td>
                <td>{{ number_format($row->GoodA) }}</td><td>{{ number_format($row->GoodB) }}</td>
                <td>{{ number_format($row->RepairA) }}</td><td>{{ number_format($row->RepairB) }}</td><td>{{ $row->RepairA + $row->RepairB }}</td>
                <td style="font-size: 4px; text-align: left;">{{ $listRep ?: '-' }}</td>
                <td>{{ number_format($row->RejectA) }}</td><td>{{ number_format($row->RejectB) }}</td><td>{{ $row->RejectA + $row->RejectB }}</td>
                <td style="font-size: 4px; text-align: left;">{{ $listRej ?: '-' }}</td>
                <td class="bg-gray">{{ number_format($row->AktualQtyA) }}</td><td class="bg-gray">{{ number_format($row->AktualQtyB) }}</td>
                <td>{{ ($row->plan_data->PlanStarttime ?? null) ? date('H:i', strtotime($row->plan_data->PlanStarttime)) : '-' }}</td>
                <td>{{ ($row->plan_data->PlanFinishtime ?? null) ? date('H:i', strtotime($row->plan_data->PlanFinishtime)) : '-' }}</td>
                <td>{{ $row->AktualStart ? date('H:i', strtotime($row->AktualStart)) : '-' }}</td>
                <td>{{ $row->AktualFinish ? date('H:i', strtotime($row->AktualFinish)) : '-' }}</td>
                <td>{{ number_format($row->plan_data->TPT ?? 0, 1) }}</td><td>{{ number_format($row->TPT, 1) }}</td>
                <td>{{ number_format($row->PressTime, 1) }}</td>
                <td>{{ number_format($row->LineMonitoring, 1) }}</td><td>{{ number_format($row->LKHCalculation, 1) }}</td>
                <td>{{ number_format($row->plan_data->DiesChangeSoto ?? 0, 1) }}</td>
                <td>{{ number_format($row->DiesChange, 1) }}</td><td>{{ number_format($row->EarlyCheck, 1) }}</td><td>{{ number_format($row->DiesChange + $row->EarlyCheck, 1) }}</td>
                <td>{{ number_format($dtDies, 1) }}</td><td>{{ number_format($dtMach, 1) }}</td><td>{{ number_format($dtMatl, 1) }}</td>
                <td>{{ number_format($dtPall, 1) }}</td><td>{{ number_format($dtPH, 1) }}</td><td class="bg-gray">{{ number_format($row->TotalDowntime, 1) }}</td>
                <td style="font-size: 4px;">{{ $idleRaw->TipeIdleTime ?? '-' }}</td><td>{{ number_format($timeToMin($idleRaw->Durasi ?? 0), 1) }}</td>
                <td style="font-size: 4px;">{{ $row->TypeBreakTime ?? '-' }}</td><td>{{ number_format($row->TimeBreakTime, 1) }}</td>
                <td>{{ number_format($row->AktualWorkTime, 1) }}</td>
                <td>{{ number_format($row->PassRate, 1) }}%</td><td>{{ number_format($row->RepairRate, 1) }}%</td><td>{{ number_format($row->RejectRate, 1) }}%</td>
                <td class="bg-yellow"><strong>{{ number_format($row->OEE, 2) }}%</strong></td>
                <td>{{ number_format($row->AktualGSPH ?? 0, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>