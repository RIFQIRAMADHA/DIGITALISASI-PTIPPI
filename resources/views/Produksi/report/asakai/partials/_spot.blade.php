{{-- 6. SPOT / HOURS --}}
<h6 style="font-weight: 800; font-size: 12px; margin-top: 20px;">D. SPOT / HOURS</h6>
<table class="table-input" style="border: 1px solid #333; margin-bottom: 20px; width: 100%; table-layout: fixed; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th rowspan="2" style="width: 18%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 11px;">ITEM</th>
            <th colspan="3" style="border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 11px; padding: 6px 2px;">SPOT / HOURS</th>
            <th rowspan="2" style="width: 8%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 11px;">DIFF</th>
            <th rowspan="2" style="width: 10%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 11px;">ACCUM</th>
            <th rowspan="2" style="width: 24%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 11px;">ISSUE</th>
            <th rowspan="2" style="width: 12%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 11px;">PIC</th>
        </tr>
        <tr style="background-color: #f2f2f2;">
            <th style="width: 10%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 9px !important; font-weight: bold; line-height: 1.2 !important; padding: 4px 1px !important; white-space: nowrap !important; letter-spacing: -0.02em;">TARGET</th>
            <th style="width: 10%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 9px !important; font-weight: bold; line-height: 1.2 !important; padding: 4px 1px !important; white-space: nowrap !important; letter-spacing: -0.02em;">PLAN</th>
            <th style="width: 10%; border: 1px solid #333; text-align: center; vertical-align: middle; font-size: 9px !important; font-weight: bold; line-height: 1.2 !important; padding: 4px 1px !important; white-space: nowrap !important; letter-spacing: -0.02em;">ACTUAL</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $spotItems = [
                ['label' => 'D52 + VT-001', 'db' => 'SpotD52'], 
                ['label' => 'PANEL DASH D26', 'db' => 'SpotPanel'], 
                ['label' => 'QUARTER INNR D26', 'db' => 'SpotQuarter'], 
                ['label' => 'FRONT FLOOR D26', 'db' => 'SpotFront']
            ]; 
            $rowCount = count($spotItems);
            $isFirst = true;
        @endphp
        @foreach($spotItems as $idx => $item)
        @php
            $dbPfx = $item['db'];
            
            // 🛠️ FIX MUTLAK: Menggunakan pembungkus bracket {""} agar query dinamis ke relasi asakaiSpot tidak null
            $valTarget = $asakai->asakaiSpot->{"{$dbPfx}Target"} ?? 0;
            $valPlan   = $asakai->asakaiSpot->{"{$dbPfx}Plan"}   ?? 0;
            $valAct    = $asakai->asakaiSpot->{"{$dbPfx}Act"}    ?? 0;
            $valAcc    = $asakai->asakaiSpot->{"{$dbPfx}Accum"}  ?? 0;
            $diff      = $valAct - $valPlan;
            
            $valIssue  = $asakai->asakaiSpot->{"{$dbPfx}Issue"}  ?? '';
            $valPic    = $asakai->asakaiSpot->SpotD52Pic ?? ''; // Menyesuaikan target field PIC pertama sebagai master display
        @endphp
        <tr>
            <td class="category-label" style="text-align: center; font-weight: 800; font-size: 10px; border: 1px solid #333; padding: 8px 4px;">{{ $item['label'] }}</td>
            <td style="border: 1px solid #333; padding: 4px;">
                <input type="number" name="SpotTarget{{$idx}}" class="form-control-asakai text-center" value="{{ $valTarget }}" style="width: 100%; box-sizing: border-box;">
            </td>
            <td style="border: 1px solid #333; padding: 4px;">
                <input type="number" name="SpotPlan{{$idx}}" id="SpotPlan{{$idx}}" class="form-control-asakai text-center" value="{{ $valPlan }}" style="width: 100%; box-sizing: border-box;">
            </td>
            <td style="border: 1px solid #333; padding: 4px;">
                <input type="number" name="SpotAct{{$idx}}" id="SpotAct{{$idx}}" class="form-control-asakai text-center" oninput="calcSpotDiff({{$idx}})" value="{{ $valAct }}" style="font-weight: bold; width: 100%; box-sizing: border-box;">
            </td>
            <td id="td_spotDiff{{$idx}}" style="background-color: {{ $diff < 0 ? '#f8d7da' : '#d4edda' }}; font-weight: bold; text-align: center; border: 1px solid #333; vertical-align: middle;">
                {{ $diff }}
            </td>
            <td style="border: 1px solid #333; padding: 4px;">
                <input type="number" name="SpotAcc{{$idx}}" class="form-control-asakai text-center" value="{{ $valAcc }}" style="width: 100%; box-sizing: border-box;">
            </td>
            
            <td style="background-color: #ffffff; padding: 2px !important; border: 1px solid #333; vertical-align: middle;">
                <textarea name="SpotIssue{{$idx}}" class="form-control-asakai" placeholder="..." 
                    style="height: 35px; border: none; resize: vertical; font-weight: bold; font-size: 10px; text-align: left; width: 100%; box-sizing: border-box;">{{ trim($valIssue) }}</textarea>
            </td>

            @if($isFirst)
            <td rowspan="{{ $rowCount }}" style="vertical-align: middle; background-color: #fff; border: 1px solid #333; text-align: center; padding: 4px;">
                <input type="text" name="SpotPIC_Global" 
                    class="form-control-asakai text-center"  
                    value="{{ $valPic }}" 
                    style="font-size: 11px; font-weight: 900; color: #4e73df; border: none; background: transparent; width: 100%; box-sizing: border-box;">
            </td>
            @php $isFirst = false; @endphp
            @endif
        </tr>
        @endforeach
    </tbody>
</table>