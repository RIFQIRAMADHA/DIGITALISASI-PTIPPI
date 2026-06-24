{{-- 7. PRODUCTION PLAN --}}
<div class="section-title">7. PRODUCTION PLAN</div>

@foreach(['S1', 'S2'] as $sh)
<h6 style="font-weight: 800; font-size: 12px; margin-top: 10px; margin-bottom: 5px;">
    Production Plan - SHIFT {{ substr($sh, 1) }}
</h6>
<table class="table-input" style="border: 1px solid #333; margin-bottom: 20px; width: 100%; table-layout: fixed;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="width: 20%;">LINE</th>
            <th>PLAN GLC</th>
            <th>PLAN TPT</th>
            <th>CAP REG</th>
            <th>REMARKS</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $linesMain = [
                'LINEE' => ['db' => 'LE', 'label' => 'LINE E'], 
                'LINEF' => ['db' => 'LF', 'label' => 'LINE F'], 
                'LINEK' => ['db' => 'LK', 'label' => 'LINE K'], 
                'ASSYD52VT' => ['db' => 'D52', 'label' => 'ASSY D52, VT'], 
                'ASSYD26' => ['db' => 'D26', 'label' => 'ASSY D26'], 
                'METALFINISH' => ['db' => 'Metal', 'label' => 'METAL FINISH']
            ]; 
        @endphp

        @foreach($linesMain as $key => $item)
        @php
            $dbPfx = $item['db'];
            $label = $item['label'];

            $valGlc     = $asakai->asakaiMain->{"PlanGlc{$dbPfx}{$sh}"} ?? 0;
            $valTpt     = $asakai->asakaiMain->{"PlanTpt{$dbPfx}{$sh}"} ?? 0;
            $valCap     = $asakai->asakaiMain->{"CapReg{$dbPfx}{$sh}"}  ?? 0;
            $valRemarks = $asakai->asakaiMain->{"Remarks{$dbPfx}{$sh}"} ?? '';
        @endphp
        <tr>
            <td class="category-label" style="text-align: center; font-weight: 800; font-size: 10px;">{{ $label }}</td>
            <td>
                <input type="number" name="PlanGlc{{$key}}{{$sh}}" class="form-control-asakai text-center" value="{{ $valGlc }}">
            </td>
            <td>
                <input type="number" name="PlanTpt{{$key}}{{$sh}}" class="form-control-asakai text-center" value="{{ $valTpt }}">
            </td>
            <td>
                <input type="number" name="CapReg{{$key}}{{$sh}}" class="form-control-asakai text-center" value="{{ $valCap }}">
            </td>
            <td>
                <input type="text" name="Remarks{{$key}}{{$sh}}" class="form-control-asakai" 
                    placeholder="..." value="{{ $valRemarks }}" style="font-size: 11px; text-align: left; padding-left: 10px;">
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach