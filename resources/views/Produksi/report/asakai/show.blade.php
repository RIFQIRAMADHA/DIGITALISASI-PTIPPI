@extends('Produksi.layouts.main')

@section('title', 'Full Detail Report Asakai')

@section('content')
<style>
    .show-container { background: #fff; padding: 30px; border: 2px solid #333; border-radius: 8px; max-width: 1300px; margin: 0 auto; }
    .header-box { display: flex; justify-content: space-between; border-bottom: 3px solid #333; padding-bottom: 15px; margin-bottom: 25px; }
    .section-header { background: #e11d2e; color: #fff; padding: 6px 15px; font-weight: 800; display: inline-block; margin-bottom: 15px; text-transform: uppercase; font-size: 13px; }
    .table-show { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 11px; }
    .table-show th { background: #f2f2f2; border: 1px solid #333; padding: 8px; text-align: center; font-weight: 900; }
    .table-show td { border: 1px solid #333; padding: 6px; text-align: center; vertical-align: middle; }
    .label-side { background: #fafafa; font-weight: 800; text-align: center !important; }
    .text-blue { color: #4361ee; font-weight: 800; }
    .text-red { color: #e11d2e; font-weight: 800; }
    .shift-title { font-weight: 800; font-size: 12px; margin-top: 10px; margin-bottom: 5px; color: #333; border-left: 4px solid #333; padding-left: 10px; }
</style>

<div class="show-container">
    {{-- HEADER --}}
    <div class="header-box">
        <div>
            <h2 style="margin: 0; font-weight: 900;">DAILY REPORT ASAKAI</h2>
            <p style="margin: 5px 0;">Tanggal: <span class="text-blue">{{ \Carbon\Carbon::parse($harian->TanggalProduksi)->format('d F Y') }}</span></p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; font-weight: 800;">ID: <span class="text-red">ASA-{{ str_replace('-', '', $harian->TanggalProduksi) }}</span></p>
        </div>
    </div>

    {{-- 1. SAFETY REPORT --}}
    <div class="section-header">1. SAFETY REPORT</div>
    <table class="table-show">
        <thead>
            <tr>
                <th style="width: 20%;">KATEGORI</th>
                <th>TARGET</th>
                <th>ACTUAL</th>
                <th>ACCUM (MTD)</th>
                <th style="width: 35%;">HIGHLIGHT ISSUE</th>
                <th>PIC</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $safety = $harian->asakaiSafety; 
                // Ambil PIC Master dari kolom SafetyPic (sesuai logic store kita tadi)
                $valPicMaster = $safety->SafetyPic ?? ($safety->AccidentPIC ?? '-');
            @endphp
            
            @foreach(['Accident' => 'ACCIDENT', 'Inccident' => 'INCCIDENT', 'TrafficAccident' => 'TRAFFIC ACCIDENT'] as $key => $lbl)
            @php 
                $dbPfx = ($key == 'TrafficAccident') ? 'Traffic' : $key; 
                $act = $safety->{$dbPfx.'Act'} ?? 0;
                $acc = $safety->{$dbPfx.'Accum'} ?? 0;
            @endphp
            <tr>
                <td class="label-side">{{ $lbl }}</td>
                <td>{{ $safety->{$dbPfx.'Target'} ?? 0 }}</td>
                
                {{-- Kasih warna merah kalau ada accident/actual > 0 --}}
                <td style="{{ $act > 0 ? 'background-color: #f8d7da; color: #721c24; font-weight: bold;' : '' }}">
                    {{ $act }}
                </td>
                
                <td style="{{ $acc > 0 ? 'background-color: #fff3cd; color: #856404;' : '' }}">
                    {{ $acc }}
                </td>
                
                <td style="text-align: left; font-size: 11px;">
                    {{ $safety->{$dbPfx.'Issue'} ?? '-' }}
                </td>

                {{-- ROWSPAN PIC --}}
                @if($loop->first) 
                <td rowspan="3" style="font-weight: 900; color: #4e73df; vertical-align: middle;">
                    {{ $valPicMaster }}
                </td> 
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 2. QUALITY REPORT --}}
    <div class="section-header">2. QUALITY REPORT</div>

    {{-- A. FLOWOUT SUMMARY --}}
    <p style="font-weight: 800; margin-bottom: 5px; font-size: 12px; color: #333;">A. FLOWOUT SUMMARY</p>
    <table class="table-show">
        <thead>
            <tr>
                <th style="width: 20%;">KATEGORI</th>
                <th>TARGET</th>
                <th>ACTUAL</th>
                <th>ACCUM</th>
                <th style="width: 35%;">HIGHLIGHT ISSUE</th>
                <th>PIC</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $qual = $harian->asakaiQuality; 
                // Mapping key sesuai dengan kolom database di store/update tadi
                $flowItems = [
                    'Customers' => 'CUSTOMERS CLAIM', 
                    'Internal'  => 'INTERNAL FLOWOUT', 
                    'Supplier'  => 'SUPPLIER FLOWOUT'
                ];
                $picFlowMaster = $qual->CustomersPIC ?? '-';
            @endphp
            
            @foreach($flowItems as $key => $lbl)
            @php
                $act = $qual->{$key.'Act'} ?? 0;
                $acc = $qual->{$key.'Acc'} ?? 0;
            @endphp
            <tr>
                <td class="label-side">{{ $lbl }}</td>
                <td>{{ $qual->{$key.'Target'} ?? 0 }}</td>
                
                {{-- Warna merah kalau ada temuan actual --}}
                <td style="{{ $act > 0 ? 'background-color: #f8d7da; color: #721c24; font-weight: bold;' : '' }}">
                    {{ $act }}
                </td>
                
                <td style="{{ $acc > 0 ? 'background-color: #fff3cd; color: #856404;' : '' }}">
                    {{ $acc }}
                </td>
                
                <td style="text-align: left; font-size: 11px;">
                    {{ $qual->{$key.'Issue'} ?? '-' }}
                </td>

                {{-- ROWSPAN PIC --}}
                @if($loop->first) 
                <td rowspan="3" style="font-weight: 900; color: #4e73df; vertical-align: middle;">
                    {{ $picFlowMaster }}
                </td> 
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- B. REPAIR & REJECT (%) --}}
    <p style="font-weight: 800; margin-top: 20px; margin-bottom: 5px; font-size: 12px; color: #333;">B. REPAIR & REJECT (%)</p>

    @foreach(['REPAIR', 'REJECT'] as $type)
    @php 
        $typeName = ucfirst(strtolower($type));
        $lines = ['LineE', 'LineF', 'LineK', 'SubAssy'];
        $rowCount = count($lines);
        // Ambil PIC Master sesuai grupnya
        $masterPic = ($type == 'REPAIR') ? ($qual->RepairPIC ?? '-') : ($qual->RejectPIC ?? '-');
        $targetLimit = ($type == 'REPAIR') ? 1.00 : 0.03;
    @endphp

    <div class="shift-title" style="margin-top: 10px; background: #f2f2f2; padding: 5px 10px; font-weight: 800; border-left: 4px solid #333;">
        {{ $type }} SUMMARY
    </div>

    <table class="table-show" style="margin-bottom: 25px;">
        <thead>
            <tr>
                <th style="width: 15%;">LINE</th>
                <th style="width: 10%;">TARGET</th>
                <th style="width: 12%;">ACTUAL (%)</th>
                <th style="width: 12%;">ACCUM (%)</th>
                <th style="width: 41%;">HIGHLIGHT ISSUE</th>
                <th style="width: 10%;">PIC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $l)
            @php
                $valAct = $qual->{$typeName.$l.'Act'} ?? 0;
                $valAcc = $qual->{$typeName.$l.'Acc'} ?? 0;
                $valIssue = $qual->{$typeName.'Issue'.$l} ?? '-';
            @endphp
            <tr>
                <td class="label-side" style="text-align: center; font-weight: 800;">{{ strtoupper($l) }}</td>
                <td style="text-align: center;">{{ number_format($targetLimit, 2) }}%</td>
                
                {{-- Warna Merah kalau Actual/Accum melebihi target --}}
                <td style="text-align: center; {{ $valAct > $targetLimit ? 'color: red; font-weight: bold;' : '' }}">
                    {{ number_format($valAct, 2) }}%
                </td>
                <td style="text-align: center; {{ $valAcc > $targetLimit ? 'color: red; font-weight: bold;' : '' }}">
                    {{ number_format($valAcc, 2) }}%
                </td>
                
                <td style="text-align: left; font-size: 10px; padding: 5px !important; white-space: pre-line;">
                    {{ $valIssue }}
                </td>

                @if($loop->first)
                <td rowspan="{{ $rowCount }}" style="vertical-align: middle; text-align: center; font-weight: 900; color: #4e73df;">
                    {{ $masterPic }}
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

    {{-- 3. PRODUCTIVITY --}}
    <div class="section-header">3. PRODUCTIVITY (PCS)</div>
    @foreach(['S1', 'S2'] as $s)
        <div class="shift-title">SHIFT {{ substr($s,1) }}</div>
        <table class="table-show" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="width: 15%;">LINE</th>
                    <th style="width: 10%;">PLAN</th>
                    <th style="width: 10%;">ACTUAL</th>
                    <th style="width: 10%;">DIFF</th>
                    <th style="width: 45%;">HIGHLIGHT ISSUE</th>
                    <th style="width: 10%;">PIC</th> {{-- Kolom PIC --}}
                </tr>
            </thead>
            <tbody>
                @php 
                    $prodLines = [
                        'LineE'=>'LINE E',
                        'LineF'=>'LINE F',
                        'LineK'=>'LINE K',
                        'D52Vt'=>'D52 & VT WELDING',
                        'D26'=>'D26 WELDING',
                        'Handwork'=>'HANDWORK'
                    ];
                    $rowCount = count($prodLines);
                    // Tarik PIC Master per Shift
                    $valPicProd = ($s == 'S1') ? ($harian->asakaiPencapaian->ProdPicS1 ?? '-') : ($harian->asakaiPencapaian->ProdPicS2 ?? '-');
                @endphp

                @foreach($prodLines as $k => $l)
                @php 
                    $plan = $harian->asakaiPencapaian->{"{$k}Plan{$s}"} ?? 0; 
                    $act = $harian->asakaiPencapaian->{"{$k}Act{$s}"} ?? 0; 
                    $diff = $act - $plan;
                    
                    // Handle penamaan kolom issue khusus D26 (sesuai database lo)
                    $colIssue = ($k == 'D26') ? "D26Issue_{$s}" : "{$k}Issue{$s}";
                    $issue = $harian->asakaiPencapaian->{$colIssue} ?? '-';
                @endphp
                <tr>
                    <td class="label-side" style="font-weight: 800; text-align: center;">{{ $l }}</td>
                    <td>{{ number_format($plan) }}</td>
                    <td style="font-weight: bold; color: #000;">{{ number_format($act) }}</td>
                    
                    {{-- Warna Diff: Merah kalau minus, Hijau kalau plus --}}
                    <td style="font-weight: bold; {{ $diff < 0 ? 'background-color: #f8d7da; color: #721c24;' : 'background-color: #d4edda; color: #155724;' }}">
                        {{ number_format($diff) }}
                    </td>

                    <td style="text-align: left; font-size: 10px; white-space: pre-line; padding: 5px !important;">
                        {{ $issue }}
                    </td>

                    {{-- PIC ROWSPAN PER SHIFT --}}
                    @if($loop->first)
                    <td rowspan="{{ $rowCount }}" style="vertical-align: middle; text-align: center; font-weight: 900; color: #4e73df; border: 1px solid #333;">
                        {{ $valPicProd }}
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- 4. DOWNTIME (MINUTES) --}}
    <div class="section-header">4. DOWNTIME (MINUTES)</div>
    @foreach(['S1', 'S2'] as $s)
        @php
            $lines = ['LineE'=>'LINE E','LineF'=>'LINE F','LineK'=>'LINE K','D52Vt'=>'D52VT','D26'=>'D26','Handwork'=>'HW'];
            
            $activeLines = array_filter(array_keys($lines), function($k) use ($s) {
                return !($s == 'S2' && ($k == 'D52Vt' || $k == 'D26'));
            });
            $rowCount = count($activeLines);
            
            // --- FIX DI SINI: Sesuaikan dengan nama kolom di DB lo (DtPicS1 / DtPicS2) ---
            $colPic = ($s == 'S1') ? 'DtPicS1' : 'DtPicS2';
            $valPicDT = $harian->asakaiDowntime->{$colPic} ?? '-';
        @endphp

        <div class="shift-title">SHIFT {{ substr($s,1) }}</div>
        <table class="table-show" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="width: 15%;">LINE</th>
                    <th style="width: 10%;">TODAY</th>
                    <th style="width: 10%;">ACCUM</th>
                    <th style="width: 10%;">TYPE</th>
                    <th style="width: 45%;">MAJOR ISSUE</th>
                    <th style="width: 10%;">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lines as $k => $l)
                    @if($s == 'S2' && ($k == 'D52Vt' || $k == 'D26'))
                        @continue
                    @endif

                    @php
                        $todayDT = $harian->asakaiDowntime->{"{$k}TodayDT{$s}"} ?? 0;
                        $accDT = $harian->asakaiDowntime->{"{$k}AccDT{$s}"} ?? 0;
                        $tipeDT = $harian->asakaiDowntime->{"{$k}TipeDT{$s}"} ?? '-';
                        $issueDT = $harian->asakaiDowntime->{"{$k}IssueDT{$s}"} ?? '-';
                    @endphp

                    <tr>
                        <td class="label-side" style="text-align: center; font-weight: 800;">{{ $l }}</td>
                        <td style="font-weight: bold; {{ $todayDT > 0 ? 'color: red; background-color: #f8d7da;' : '' }}">
                            {{ $todayDT }}
                        </td>
                        <td>{{ $accDT }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $tipeDT }}</td>
                        <td style="text-align: left; font-size: 10px; white-space: pre-line; padding: 5px !important;">
                            {{ $issueDT }}
                        </td>

                        {{-- Rowspan PIC --}}
                        @if($k == $activeLines[array_key_first($activeLines)])
                            <td rowspan="{{ $rowCount }}" style="vertical-align: middle; text-align: center; font-weight: 900; color: #4e73df; border: 1px solid #333;">
                                {{ $valPicDT }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- 5. GSPH SUMMARY --}}
    <div class="section-header">5. GSPH SUMMARY</div>

    @foreach(['S1', 'S2'] as $s)
        @php
            $gsphLines = ['LE' => 'LINE E', 'LF' => 'LINE F', 'LK' => 'LINE K'];
            $rowCount = count($gsphLines);
            
            // --- FIX: Panggil kolom yang ada isinya (GsphPicLES1 / GsphPicLES2) ---
            // Karena lo pake rowspan, kita ambil dari kolom Line E aja sebagai master
            $colPic = "GsphPicLE{$s}"; 
            $valPicGsph = $harian->asakaiGsph->{$colPic} ?? '-';
        @endphp

        <div class="shift-title">GSPH SHIFT {{ substr($s,1) }}</div>
        <table class="table-show" style="margin-bottom: 25px; width: 100%; border: 1px solid #333;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="width: 20%;">LINE</th>
                    <th style="width: 15%;">PLAN</th>
                    <th style="width: 15%;">ACTUAL</th>
                    <th style="width: 15%;">DIFF</th>
                    <th style="width: 20%;">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gsphLines as $pfx => $label)
                @php 
                    $gP = $harian->asakaiGsph->{"GsphPlan{$pfx}{$s}"} ?? 0; 
                    $gA = $harian->asakaiGsph->{"GsphAct{$pfx}{$s}"} ?? 0; 
                    $diff = $gA - $gP;
                @endphp
                <tr>
                    <td class="label-side" style="text-align: center; font-weight: 800;">{{ $label }}</td>
                    <td style="text-align: center;">{{ $gP }}</td>
                    <td style="text-align: center; font-weight: bold; color: #007bff;">{{ $gA }}</td>
                    <td style="text-align: center; font-weight: bold; {{ $diff < 0 ? 'background-color: #f8d7da; color: #721c24;' : 'background-color: #d4edda; color: #155724;' }}">
                        {{ $diff }}
                    </td>

                    @if($loop->first)
                    <td rowspan="{{ $rowCount }}" style="vertical-align: middle; text-align: center; font-weight: 900; color: #4e73df; border-left: 1px solid #333;">
                        {{ $valPicGsph }} {{-- Isinya bakal "percobaan bro" --}}
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- 6. SPOT --}}
    <div class="section-header">6. SPOT / HOURS</div>
    <table class="table-show">
        <thead><tr><th>ITEM</th><th>PLAN</th><th>ACTUAL</th><th>DIFF</th><th>ACCUM</th></tr></thead>
        <tbody>
            @php $spots = ['D52'=>'D52+VT','Panel'=>'PANEL DASH','Quarter'=>'QUARTER INNR','Front'=>'FRONT FLOOR']; @endphp
            @foreach($spots as $k => $v)
            @php $sP = $harian->asakaiSpot->{"Spot{$k}Plan"} ?? 0; $sA = $harian->asakaiSpot->{"Spot{$k}Act"} ?? 0; @endphp
            <tr><td class="label-side" style="text-align: left !important; padding-left: 15px !important;">{{ $v }}</td><td>{{ $sP }}</td><td class="text-blue">{{ $sA }}</td><td>{{ $sA-$sP }}</td><td>{{ $harian->asakaiSpot->{"Spot{$k}Accum"} ?? 0 }}</td></tr>
            @endforeach
        </tbody>
    </table>

    {{-- 7. PRODUCTION PLAN --}}
    <div class="section-header">7. PRODUCTION PLAN SUMMARY (GLC/TPT)</div>
    @foreach(['S1', 'S2'] as $s)
    <div class="shift-title">PLANNING - SHIFT {{ substr($s,1) }}</div>
    <table class="table-show">
        <thead>
            <tr><th style="width: 180px;">LINE</th><th>PLAN GLC</th><th>PLAN TPT</th><th>DIFF</th><th>CAP REG</th><th>REMARKS</th></tr>
        </thead>
        <tbody>
            @php $linesM = ['LE'=>'LINE E', 'LF'=>'LINE F', 'LK'=>'LINE K', 'D52'=>'ASSY D52, VT', 'D26'=>'ASSY D26', 'Metal'=>'METAL FINISH']; @endphp
            @foreach($linesM as $pfx => $label)
            @php 
                $main = $harian->asakaiMain;
                $pg = $main->{"PlanGlc{$pfx}{$s}"} ?? 0; 
                $pt = $main->{"PlanTpt{$pfx}{$s}"} ?? 0; 
            @endphp
            <tr>
                <td class="label-side" style="text-align: left !important; padding-left: 15px !important;">{{ $label }}</td>
                <td>{{ $pg }}</td><td>{{ $pt }}</td>
                <td style="font-weight: 800;">{{ $pt - $pg }}</td>
                <td>{{ $main->{"CapReg{$pfx}{$s}"} ?? 0 }}</td>
                <td style="text-align: left;">{{ $main->{"Remarks{$pfx}{$s}"} ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

    <div style="margin-top: 30px; display: flex; gap: 10px; padding-bottom: 50px;">
        <a href="{{ route('report.asakai.index') }}" class="btn btn-dark" style="padding: 10px 40px; font-weight: 800; border: 1px solid #333;">BACK</a>
    </div>
</div>
@endsection