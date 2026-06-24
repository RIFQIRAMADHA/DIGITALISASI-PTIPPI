<table>
    {{-- HEADER --}}
    <tr>
        <td></td> <th colspan="2" style="height: 65px;"></th> 
        <th colspan="3" style="text-align: center; font-size: 18pt; font-weight: bold; vertical-align: middle;">
            DAILY REPORT ASAKAI
        </th>
        <th colspan="2"></th> 
    </tr>
    <tr>
        <td></td>
        <th colspan="7" style="text-align: center; color: #0000FF; font-weight: bold; height: 30px;">
            DATE : {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
        </th>
    </tr>

    <tr><td colspan="8" style="height: 20px;"></td></tr> 
    
    {{-- 1. SAFETY --}}
    <tr>
        <td></td>
        <th colspan="7" style="background-color: #e11d2e; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: left; padding-left: 10px;">1. SAFETY</th>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        <th colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">KATEGORI</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">TARGET</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACTUAL</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACCUM (MTD)</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">HIGHLIGHT ISSUE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PIC</th>
    </tr>
    @php 
        $safety = $asakai->asakaiSafety; 
        $safetyPic = $safety->SafetyPic ?? ($safety->AccidentPIC ?? '-');
    @endphp
    @foreach(['Accident' => 'ACCIDENT', 'Inccident' => 'INCCIDENT', 'Traffic' => 'TRAFFIC ACCIDENT'] as $key => $label)
    <tr>
        <td></td>
        <td colspan="2" style="border: 1px solid #000; font-weight: bold; padding-left: 5px;">{{ $label }}</td>
        {{-- 🔥 FIX TARGET: Ngambil dari database, bukan hardcode 0 lagi --}}
        <td style="border: 1px solid #000; text-align: center;">{{ round($safety->{$key.'Target'} ?? 0) }}</td>
        <td style="border: 1px solid #000; text-align: center; {{ ($safety->{$key.'Act'} ?? 0) > 0 ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">
            {{ round($safety->{$key.'Act'} ?? 0) }}
        </td>
        <td style="border: 1px solid #000; text-align: center; background-color: #d4edda;">{{ round($safety->{$key.'Accum'} ?? 0) }}</td>
        <td style="border: 1px solid #000; background-color: #ffffcc;">{{ $safety->{$key.'Issue'} ?? '-' }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $safetyPic }}</td>
    </tr>
    @endforeach

    <tr><td colspan="8" style="height: 20px;"></td></tr>
    
    {{-- 2. QUALITY --}}
    <tr>
        <td></td>
        <th colspan="7" style="background-color: #e11d2e; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: left; padding-left: 10px;">2. QUALITY</th>
    </tr>

    {{-- 🔥 TAMBAHAN FIX: BAGIAN FLOWOUT (CUSTOMERS, INTERNAL, SUPPLIER) YANG KETINGGALAN --}}
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        <th colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">KATEGORI</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">TARGET</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACTUAL</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACCUM</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">HIGHLIGHT ISSUE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PIC</th>
    </tr>
    @php
        $qual = $asakai->asakaiQuality;
        $picFlow = $qual->CustomersPIC ?? '-';
        $flowoutItems = [
            'Customers' => 'CUSTOMERS CLAIM', 
            'Internal'  => 'INTERNAL FLOWOUT', 
            'Supplier'  => 'SUPPLIER FLOWOUT'
        ];
    @endphp
    @foreach($flowoutItems as $key => $label)
    <tr>
        <td></td>
        <td colspan="2" style="border: 1px solid #000; font-weight: bold; padding-left: 5px;">{{ $label }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ round($qual->{$key.'Target'} ?? 0) }}</td>
        <td style="border: 1px solid #000; text-align: center; {{ ($qual->{$key.'Act'} ?? 0) > 0 ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">
            {{ round($qual->{$key.'Act'} ?? 0) }}
        </td>
        <td style="border: 1px solid #000; text-align: center; {{ ($qual->{$key.'Acc'} ?? 0) > 0 ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">
            {{ round($qual->{$key.'Acc'} ?? 0) }}
        </td>
        <td style="border: 1px solid #000; background-color: #ffffcc;">{{ $qual->{$key.'Issue'} ?? '-' }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $picFlow }}</td>
    </tr>
    @endforeach

    {{-- LANJUTAN QUALITY: REPAIR & REJECT --}}
    @foreach(['REPAIR', 'REJECT'] as $type)
    @php
        $qMap = ['LINE E' => 'LineE', 'LINE F' => 'LineF', 'LINE K' => 'LineK'];
        $limit = ($type == 'REPAIR') ? 1.00 : 0.03;
        // qual udah di define di atas
        $qPic = ($type == 'REPAIR') ? ($qual->RepairPIC ?? '-') : ($qual->RejectPIC ?? '-');
    @endphp
    <tr><td colspan="8" style="height: 10px;"></td></tr> 
    <tr style="background-color: #f2f2f2;">
        <td></td>
        <th colspan="7" style="font-weight: bold; border: 1px solid #000; text-align: left;">{{ $type }} (%)</th>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        <th colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">LINE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">TARGET</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACTUAL (%)</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACCUM (%)</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ISSUE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PIC</th>
    </tr>
    @foreach($qMap as $lbl => $db)
    @php
        $dbPrefix = ucfirst(strtolower($type));
        $vAct = $qual->{$dbPrefix.$db.'Act'} ?? 0;
        $vAcc = $qual->{$dbPrefix.$db.'Acc'} ?? 0;
        $vIssue = $qual->{$dbPrefix.'Issue'.$db} ?? '-'; 
    @endphp
    <tr>
        <td></td>
        <td colspan="2" style="border: 1px solid #000; font-weight: bold;">{{ $lbl }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($limit, 2) }}%</td>
        <td style="border: 1px solid #000; text-align: center; {{ $vAct > $limit ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">{{ number_format((float)$vAct, 2) }}%</td>
        <td style="border: 1px solid #000; text-align: center; {{ $vAcc > $limit ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">{{ number_format((float)$vAcc, 2) }}%</td>
        <td style="border: 1px solid #000; background-color: #ffffcc;">{{ $vIssue }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $qPic }}</td>
    </tr>
    @endforeach
    @endforeach

    <tr><td colspan="8" style="height: 20px;"></td></tr>
    
    {{-- 3. PRODUCTIVITY --}}
    <tr>
        <td></td>
        <th colspan="7" style="background-color: #e11d2e; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: left; padding-left: 10px;">3. PRODUCTIVITY</th>
    </tr>
    @foreach(['S1', 'S2'] as $shift)
    @php $prodPic = ($shift == 'S1') ? ($asakai->asakaiPencapaian->ProdPicS1 ?? '-') : ($asakai->asakaiPencapaian->ProdPicS2 ?? '-'); @endphp
    <tr><td colspan="8" style="height: 10px;"></td></tr> 
    <tr style="background-color: #f2f2f2;">
        <td></td>
        <th colspan="7" style="border: 1px solid #000; font-weight: bold; text-align: left;">Pencapaian Produksi (pcs) - SHIFT {{ substr($shift,1) }}</th>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        {{-- 🛠️ FIX: Center judul LINE, PLAN, ACTUAL, DIFF, HIGHLIGHT ISSUE, PIC --}}
        <th colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">LINE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PLAN</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACTUAL</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">DIFF</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">HIGHLIGHT ISSUE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PIC</th>
    </tr>
    @foreach(['LineE'=>'LINE E','LineF'=>'LINE F','LineK'=>'LINE K','D52Vt'=>'D52 & VT','D26'=>'D26','Handwork'=>'HANDWORK'] as $k => $l)
    @php 
        $p = $asakai->asakaiPencapaian->{$k.'Plan'.$shift} ?? 0; 
        $a = $asakai->asakaiPencapaian->{$k.'Act'.$shift} ?? 0; 
        $d = $a - $p; 
        $colIss = ($k == 'D26') ? "D26Issue_{$shift}" : "{$k}Issue{$shift}";
    @endphp
    <tr>
        <td></td>
        <td colspan="2" style="border: 1px solid #000; font-weight: bold;">{{ $l }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($p) }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($a) }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold; {{ $d < 0 ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">{{ $d }}</td>
        <td style="border: 1px solid #000; background-color: #ffffcc;">{{ $asakai->asakaiPencapaian->{$colIss} ?? '-' }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $prodPic }}</td>
    </tr>
    @endforeach
    @endforeach

    <tr><td colspan="8" style="height: 20px;"></td></tr>
    
    {{-- 4. DOWNTIME --}}
    <tr>
        <td></td>
        <th colspan="7" style="background-color: #e11d2e; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: left; padding-left: 10px;">4. DOWNTIME</th>
    </tr>
    @foreach(['S1', 'S2'] as $shift)
    @php $dtPic = ($shift == 'S1') ? ($asakai->asakaiDowntime->DtPicS1 ?? '-') : ($asakai->asakaiDowntime->DtPicS2 ?? '-'); @endphp
    <tr><td colspan="8" style="height: 10px;"></td></tr>
    <tr style="background-color: #f2f2f2;">
        <td></td>
        <th colspan="7" style="border: 1px solid #000; font-weight: bold; text-align: left;">Downtime (Minutes) - SHIFT {{ substr($shift,1) }}</th>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        {{-- 🛠️ FIX: Center judul LINE, TODAY, ACCUM, TYPE, HIGHLIGHT ISSUE, PIC --}}
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">LINE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">TODAY</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACCUM</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">TYPE</th>
        <th colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">HIGHLIGHT ISSUE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PIC</th>
    </tr>
    @foreach(['LineE'=>'LINE E','LineF'=>'LINE F','LineK'=>'LINE K','D52Vt'=>'D52VT','D26'=>'D26','Handwork'=>'HANDWORK'] as $k => $l)
        @if($shift == 'S2' && ($k == 'D52Vt' || $k == 'D26')) @continue @endif
        <tr>
            <td></td>
            <td style="border: 1px solid #000; font-weight: bold;">{{ $l }}</td>
            <td style="border: 1px solid #000; text-align: center; {{ ($asakai->asakaiDowntime->{$k.'TodayDT'.$shift} ?? 0) > 0 ? 'background-color: #f8d7da;' : 'background-color: #d4edda;' }}">
                {{ round($asakai->asakaiDowntime->{$k.'TodayDT'.$shift} ?? 0) }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">{{ round($asakai->asakaiDowntime->{$k.'AccDT'.$shift} ?? 0) }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $asakai->asakaiDowntime->{$k.'TipeDT'.$shift} ?? 'M/C' }}</td>
            <td colspan="2" style="border: 1px solid #000; background-color: #ffffcc;">{{ $asakai->asakaiDowntime->{$k.'IssueDT'.$shift} ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $dtPic }}</td>
        </tr>
    @endforeach
    @endforeach

    <tr><td colspan="8" style="height: 20px;"></td></tr>
    
    {{-- 5. GSPH --}}
    <tr>
        <td></td>
        <th colspan="7" style="background-color: #e11d2e; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: left; padding-left: 10px;">5. GSPH SUMMARY</th>
    </tr>
    @foreach(['S1', 'S2'] as $shift)
    @php 
        $gsph = $asakai->asakaiGsph;
        $gsPic = ($shift == 'S1') ? ($gsph->GsphPicLES1 ?? '-') : ($gsph->GsphPicLES2 ?? '-'); 
    @endphp
    <tr><td colspan="8" style="height: 10px;"></td></tr>
    <tr style="background-color: #f2f2f2;">
        <td></td>
        <th colspan="7" style="border: 1px solid #000; font-weight: bold; text-align: left;">GSPH SHIFT {{ substr($shift,1) }}</th>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        {{-- 🛠️ FIX: Center judul LINE, TARGET KPI, PLAN SCHEDULE, ACTUAL, DIFF, HIGHLIGHT ISSUE, PIC --}}
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">LINE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">TARGET KPI</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PLAN SCHEDULE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACTUAL</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">DIFF</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">HIGHLIGHT ISSUE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PIC</th>
    </tr>
    @foreach(['LE'=>'LINE E','LF'=>'LINE F','LK'=>'LINE K'] as $pfx => $l)
    @php 
        $pl = $gsph->{"GsphPlan{$pfx}{$shift}"} ?? 0; 
        $ac = $gsph->{"GsphAct{$pfx}{$shift}"} ?? 0; 
        $tar = $gsph->{"GsphTarget{$pfx}{$shift}"} ?? 500;
        $di = $ac - $pl; 
    @endphp
    <tr>
        <td></td>
        <td style="border: 1px solid #000; font-weight: bold;">{{ $l }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $tar }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ round($pl) }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ round($ac) }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold; {{ $di < 0 ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">{{ round($di) }}</td>
        <td style="border: 1px solid #000; background-color: #ffffcc;">{{ $gsph->{"GsphIssue{$pfx}{$shift}"} ?? '-' }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $gsPic }}</td>
    </tr>
    @endforeach
    @endforeach

    <tr><td colspan="8" style="height: 20px;"></td></tr>
    
    {{-- 6. SPOT / HOURS --}}
    <tr><td colspan="9" style="height: 20px;"></td></tr>
    <tr>
        <td></td>
        <th colspan="8" style="background-color: #e11d2e; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: left; padding-left: 10px;">6. SPOT / HOURS</th>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        {{-- 🛠️ FIX: Center judul ITEM, TARGET, PLAN, ACTUAL, DIFF, ACCUM, ISSUE, PIC --}}
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ITEM</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">TARGET</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PLAN</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACTUAL</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">DIFF</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ACCUM</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">ISSUE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PIC</th>
    </tr>
    @php $sp = $asakai->asakaiSpot; @endphp
    @foreach([
        ['l'=>'D52 + VT-001','db'=>'SpotD52'],
        ['l'=>'PANEL DASH D26','db'=>'SpotPanel'],
        ['l'=>'QUARTER INNR D26','db'=>'SpotQuarter'],
        ['l'=>'FRONT FLOOR D26','db'=>'SpotFront']
    ] as $it)
    @php 
        $p = $sp->{$it['db'].'Plan'} ?? 0; 
        $a = $sp->{$it['db'].'Act'} ?? 0; 
        $t = $sp->{$it['db'].'Target'} ?? 0;
        $acc = $sp->{$it['db'].'Accum'} ?? 0;
        $iss = $sp->{$it['db'].'Issue'} ?? '-';
        $pic = $sp->{$it['db'].'Pic'} ?? '-';
    @endphp
    <tr>
        <td></td>
        <td style="border: 1px solid #000; font-weight: bold; padding-left: 5px;">{{ $it['l'] }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($t, 2) }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($p, 2) }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($a, 2) }}</td>
        <td style="border: 1px solid #000; text-align: center; {{ ($a-$p) < 0 ? 'background-color: #f8d7da; color: red;' : 'background-color: #d4edda;' }}">{{ number_format($a - $p, 2) }}</td>
        <td style="border: 1px solid #000; text-align: center; background-color: #d4edda;">{{ number_format($acc, 2) }}</td>
        <td style="border: 1px solid #000; background-color: #ffffcc; text-align: center;">{{ $iss }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $pic }}</td>
    </tr>
    @endforeach

    {{-- 7. PRODUCTION PLAN --}}
    <tr><td colspan="9" style="height: 20px;"></td></tr>
    <tr>
        <td></td>
        <th colspan="8" style="background-color: #e11d2e; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: left; padding-left: 10px;">7. PRODUCTION PLAN</th>
    </tr>
    @foreach(['S1', 'S2'] as $shift)
    <tr><td colspan="9" style="height: 10px;"></td></tr>
    <tr style="background-color: #f2f2f2;">
        <td></td>
        <th colspan="8" style="border: 1px solid #000; font-weight: bold; text-align: left;">Production Plan - SHIFT {{ substr($shift,1) }}</th>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">
        <td></td>
        {{-- 🛠️ FIX: Center judul LINE, PLAN GLC, PLAN TPT, CAP REG, REMARKS --}}
        <th colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">LINE</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PLAN GLC</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">PLAN TPT</th>
        <th style="border: 1px solid #000; text-align: center; vertical-align: middle;">CAP REG</th>
        <th colspan="3" style="border: 1px solid #000; text-align: center; vertical-align: middle;">REMARKS</th>
    </tr>
    @php
        $mainMap = [
            'LINE E' => 'LE',
            'LINE F' => 'LF',
            'LINE K' => 'LK',
            'ASSY D52, VT' => 'D52',
            'ASSY D26' => 'D26',
            'METAL FINISH' => 'Metal'
        ];
    @endphp
    @foreach($mainMap as $label => $dbKey)
    <tr>
        <td></td>
        <td colspan="2" style="border: 1px solid #000; font-weight: bold; padding-left: 5px;">{{ $label }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $asakai->asakaiMain->{'PlanGlc'.$dbKey.$shift} ?? 0 }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $asakai->asakaiMain->{'PlanTpt'.$dbKey.$shift} ?? 0 }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $asakai->asakaiMain->{'CapReg'.$dbKey.$shift} ?? 0 }}</td>
        <td colspan="3" style="border: 1px solid #000; background-color: #ffffcc; text-align: center;">
            {{ $asakai->asakaiMain->{'Remarks'.$dbKey.$shift} ?? '-' }}
        </td>
    </tr>
    @endforeach
    @endforeach
</table>