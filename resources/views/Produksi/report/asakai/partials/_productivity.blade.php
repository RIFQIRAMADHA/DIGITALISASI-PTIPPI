{{-- 3. PRODUCTIVITY --}}
<div class="section-title">3. PRODUCTIVITY</div>

@foreach(['S1', 'S2'] as $s)
<h6 style="font-weight: 800; font-size: 12px; margin-top: 10px; margin-bottom: 5px;">
    A. Pencapaian Produksi (pcs) - SHIFT {{ substr($s,1) }}
</h6>
<table class="table-input" style="margin-bottom: 25px; table-layout: fixed; width: 100%; border: 1px solid #333;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="width: 15%;">LINE</th>
            <th style="width: 10%;">PLAN</th>
            <th style="width: 10%;">ACTUAL</th>
            <th style="width: 8%;">DIFF</th>
            <th style="width: 45%;">HIGHLIGHT ISSUE</th>
            <th style="width: 12%;">PIC</th>
        </tr>
    </thead>
    <tbody>
        @php
            $prodLines = [
                'LineE' => 'LINE E', 
                'LineF' => 'LINE F', 
                'LineK' => 'LINE K',
                'D52VT' => 'D52 & VT WELDING', 
                'D26'   => 'D26 WELDING', 
                'HW'    => 'HANDWORK'
            ];
            $rowCount = count($prodLines);
            $isFirst = true;
        @endphp

        @foreach($prodLines as $key => $label)
        @php
            $dbPlan = $asakai->asakaiPencapaian->{"{$key}Plan{$s}"} ?? null;
            $dbAct  = $asakai->asakaiPencapaian->{"{$key}Act{$s}"}  ?? null;

            if ($dbPlan !== null) {
                $pVal = $dbPlan;
                $aVal = $dbAct;
            } else {
                if (in_array($key, ['LineE', 'LineF', 'LineK'])) {
                    $sfx = substr($key, 4); 
                    $pVal = $productivityData->{'plan'.$sfx.'_'.$s} ?? 0;
                    $aVal = $productivityData->{'act'.$sfx.'_'.$s} ?? 0;
                } else {
                    $pVal = 0; $aVal = 0;
                }
            }

            $diffVal = $aVal - $pVal;
            $valIssue = $asakai->asakaiPencapaian->{"{$key}Issue{$s}"} ?? ($downtimeData->{"issue{$key}{$s}"} ?? '');
            
            $valPic = $asakai->asakaiPencapaian->ProdPicS1 ?? ''; 
            if($s == 'S2') $valPic = $asakai->asakaiPencapaian->ProdPicS2 ?? '';
        @endphp
        <tr>
            <td class="category-label" style="text-align: center; font-size: 10px; font-weight: 800;">{{ $label }}</td>
            <td>
                <input type="number" name="{{$key}}Plan{{$s}}" class="form-control-asakai text-center" value="{{ round($pVal) }}">
            </td>
            <td>
                <input type="number" name="{{$key}}Act{{$s}}" class="form-control-asakai text-center" value="{{ round($aVal) }}" style="font-weight: bold;">
            </td>
            <td id="diff_{{$key}}{{$s}}" style="font-weight: 800; text-align: center; background-color: {{ $diffVal < 0 ? '#f8d7da' : '#d4edda' }}; color: {{ $diffVal < 0 ? '#721c24' : '#155724' }};">
                {{ round($diffVal) }}
            </td>
            <td style="background-color: #ffffff; padding: 2px !important;">
                <textarea name="{{$key}}Issue{{$s}}" class="form-control-asakai" 
                    style="height: 40px; background: transparent; border: none; font-size: 10px; font-weight: bold; resize: vertical;">{{ trim($valIssue) }}</textarea>
            </td>

            @if($isFirst)
            <td rowspan="{{ $rowCount }}" style="vertical-align: middle; background-color: #fff; border: 1px solid #333;">
                <input type="text" name="ProdPic{{$s}}" class="form-control-asakai text-center" value="{{ $valPic }}" 
                    style="font-size: 11px; font-weight: 900; color: #4e73df; border: none; background: transparent;">
            </td>
            @php $isFirst = false; @endphp
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach