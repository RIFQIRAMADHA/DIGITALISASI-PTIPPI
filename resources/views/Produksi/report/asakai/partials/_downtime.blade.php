{{-- 4. DOWNTIME --}}
<div class="section-title">4. DOWNTIME</div>

@foreach(['S1', 'S2'] as $s)
<h6 style="font-weight: 800; font-size: 12px; margin-top: 15px; margin-bottom: 5px;">
    B. Downtime (Minutes) - SHIFT {{ substr($s,1) }}
</h6>

<table class="table-input" style="margin-bottom: 25px; table-layout: fixed; width: 100%; border: 1px solid #333;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="width: 15%;">LINE</th>
            <th style="width: 10%;">TODAY</th>
            <th style="width: 10%;">ACCUM</th>
            <th style="width: 15%;">TYPE</th>
            <th style="width: 35%;">HIGHLIGHT ISSUE</th>
            <th style="width: 15%;">PIC</th>
        </tr>
    </thead>
    <tbody>
        @php
            $dtLines = [
                'LineE' => 'LINE E', 'LineF' => 'LINE F', 'LineK' => 'LINE K',
                'D52Vt' => 'D52VT', 'D26' => 'D26', 'Handwork' => 'HW'
            ];

            $visibleLines = [];
            foreach($dtLines as $k => $l) {
                if(!($s == 'S2' && ($k == 'D52Vt' || $k == 'D26'))) {
                    $visibleLines[$k] = $l;
                }
            }
            $rowCount = count($visibleLines);
            $isFirst = true; 
        @endphp
        
        @foreach($visibleLines as $key => $label)
            @php
                $dtKey = ($key == 'D52Vt') ? 'D52VT' : (($key == 'Handwork') ? 'HW' : $key);
                
                $suffixDt = ($s == 'S2') ? '_S2' : '';
                
                $dbToday = $asakai->asakaiDowntime->{"{$key}TodayDT{$s}"} ?? null;
                $dbAcc   = $asakai->asakaiDowntime->{"{$key}AccDT{$s}"}   ?? null;
                $dbType  = $asakai->asakaiDowntime->{"{$key}TipeDT{$s}"}  ?? null;
                $dbIssue = $asakai->asakaiDowntime->{"{$key}IssueDT{$s}"} ?? null;

                $valToday = ($dbToday !== null && $dbToday != 0) ? $dbToday : ($downtimeData->{"dt{$dtKey}{$suffixDt}"} ?? 0);
                $valAcc   = ($dbAcc !== null && $dbAcc != 0) ? $dbAcc : ($downtimeData->{"acc{$dtKey}{$suffixDt}"} ?? 0);
                $valType  = ($dbType !== null) ? $dbType : ($downtimeData->{"type{$dtKey}{$s}"} ?? 'M/C');
                
                $autoIssue = $downtimeData->{"issue{$dtKey}{$s}"} ?? '';
                $valIssue  = (!empty(trim($dbIssue))) ? $dbIssue : $autoIssue;
            @endphp
            <tr>
                <td style="text-align: center; font-weight: 800; font-size: 11px;">{{ $label }}</td>
                
                <td id="td_dt_{{$key}}{{$s}}_today" style="background-color: {{ $valToday > 0 ? '#f8d7da' : '#d4edda' }};">
                    <input type="number" name="{{$key}}Dt{{$s}}" class="form-control-asakai text-center" 
                        oninput="updateColor(this, 'td_dt_{{$key}}{{$s}}_today')" value="{{ round($valToday) }}">
                </td>
                
                <td style="background-color: #fcf8e3;">
                    <input type="number" name="{{$key}}DtAcc{{$s}}" class="form-control-asakai text-center" 
                        value="{{ round($valAcc) }}" style="color: #4e73df; font-weight: bold;">
                </td>
                
                <td>
                    <input type="text" name="{{$key}}DtType{{$s}}" class="form-control-asakai text-center" value="{{ $valType }}">
                </td>
                
                <td style="background-color: #ffffff;">
                    <textarea name="{{$key}}Issue{{$s}}" class="form-control-asakai" 
                        style="height: 30px; background: transparent; border: none; font-size: 10px; font-weight: bold; resize: none;">{{ trim($valIssue) }}</textarea>
                </td>

                @if($isFirst)
                {{-- 🔥 FIX CSS OVERRIDE: Paksa box-shadow hilang dan background putih mutlak --}}
                <td rowspan="{{ $rowCount }}" class="bg-white" style="vertical-align: middle; background-color: #ffffff !important; box-shadow: inset 0 0 0 9999px #ffffff !important; border: 1px solid #333; padding: 0;">
                    <div style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; min-height: 100px; background: #ffffff;">
                        <input type="text" name="DtPic{{$s}}" 
                            class="text-center" 
                            value="{{ $asakai->asakaiDowntime->{"DtPic{$s}"} ?? '' }}" 
                            style="font-size: 12px; font-weight: 900; color: #4e73df; border: none; background: transparent !important; outline: none; width: 100%;">
                    </div>
                </td>
                @php $isFirst = false; @endphp
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
@endforeach