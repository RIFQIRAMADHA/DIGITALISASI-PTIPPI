{{-- 5. GSPH SUMMARY --}}
<div class="section-title">5. GSPH SUMMARY</div>

@foreach(['S1', 'S2'] as $shift)
<div style="margin-bottom: 30px;">
    <p style="font-weight: 800; font-size: 13px; margin-bottom: 10px; color: #333; border-left: 5px solid #e11d2e; padding-left: 12px;">
        GSPH SHIFT {{ substr($shift, 1) }}
    </p>
    
    <table class="table-input" style="border: 1px solid #333; width: 100%; table-layout: fixed;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 15%;">LINE</th>
                <th style="width: 10%;">TARGET KPI</th>
                <th style="width: 12%;">PLAN SCHEDULE</th>
                <th style="width: 12%;">ACTUAL</th>
                <th style="width: 10%;">DIFF</th>
                <th style="width: 31%;">HIGHLIGHT ISSUE</th>
                <th style="width: 10%;">PIC</th>
            </tr>
        </thead>
        <tbody>
            @php
                $lines = [
                    'LineE' => ['label' => 'LINE E', 'prefix' => 'LE'],
                    'LineF' => ['label' => 'LINE F', 'prefix' => 'LF'],
                    'LineK' => ['label' => 'LINE K', 'prefix' => 'LK'],
                ];
                $rowCount = count($lines);
                $isFirst = true;
            @endphp
            @foreach($lines as $key => $line)
            @php
                $pfx = $line['prefix'];
                
                $dbTarget = $asakai->asakaiGsph->{"GsphTarget{$pfx}{$shift}"} ?? 500;
                $dbPlan   = $asakai->asakaiGsph->{"GsphPlan{$pfx}{$shift}"}   ?? null;
                $dbAct    = $asakai->asakaiGsph->{"GsphAct{$pfx}{$shift}"}    ?? null;

                if ($dbPlan !== null) {
                    $targetValue = $dbTarget;
                    $planValue   = $dbPlan;
                    $actualValue = $dbAct;
                } else {
                    $targetValue = ($pfx == 'LE' ? 500 : 550); 
                    $planValue   = $gsphData->{'PlanGSPH'.$line['prefix'][1].'_'.$shift} ?? 0;
                    $actualValue = $gsphData->{'AktualGSPH'.$line['prefix'][1].'_'.$shift} ?? 0;
                }

                $diffValue = $actualValue - $planValue;
                $valIssue = $asakai->asakaiGsph->{"GsphIssue{$pfx}{$shift}"} ?? ($downtimeData->{"issue{$key}{$shift}"} ?? '');
                
                $valPic = $asakai->asakaiGsph->{"GsphPic{$pfx}{$shift}"} ?? ''; 
            @endphp
            <tr>
                <td class="category-label" style="text-align: center; font-weight: 800; font-size: 11px;">{{ $line['label'] }}</td>
                <td style="background-color: #f2f2f2;">
                    <input type="number" name="{{ $key }}TargetGsph{{ $shift }}" class="form-control-asakai text-center" value="{{ $targetValue }}" style="font-weight: 800;">
                </td>
                <td>
                    <input type="number" name="{{ $key }}PlanGsph{{ $shift }}" id="{{ $key }}PlanGsph{{ $shift }}" class="form-control-asakai text-center" value="{{ round($planValue) }}" oninput="calcGsphDiff('{{ $key }}', '{{ $shift }}')">
                </td>
                <td>
                    <input type="number" name="{{ $key }}ActGsph{{ $shift }}" id="{{ $key }}ActGsph{{ $shift }}" class="form-control-asakai text-center" value="{{ round($actualValue) }}" style="font-weight: bold;" oninput="calcGsphDiff('{{ $key }}', '{{ $shift }}')">
                </td>
                <td id="td_gsph_diff_{{ $key }}{{ $shift }}" style="font-weight: 800; text-align: center; background-color: {{ $diffValue < 0 ? '#f8d7da' : '#d4edda' }}; color: {{ $diffValue < 0 ? '#721c24' : '#155724' }};">
                    {{ round($diffValue) }}
                </td>
                <td style="background-color: #ffffff; padding: 2px !important;">
                    <textarea name="GsphIssue{{ $key }}{{ $shift }}" class="form-control-asakai" style="height: 40px; background: transparent; border: none; font-size: 10px; font-weight: bold; resize: vertical;">{{ trim($valIssue) }}</textarea>
                </td>

                @if($isFirst)
                <td rowspan="{{ $rowCount }}" style="vertical-align: middle; background-color: #fff; border: 1px solid #333;">
                    <input type="text" name="GsphPIC{{ $shift }}" class="form-control-asakai text-center" value="{{ $valPic }}" style="font-size: 11px; font-weight: 900; color: #4e73df; border: none; background: transparent;">
                </td>
                @php $isFirst = false; @endphp
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach