<div class="section-title">2. QUALITY</div>

<table class="table-input" style="margin-bottom: 10px; table-layout: fixed; width: 100%; border: 1px solid #333;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="width: 20%;">KATEGORI</th>
            <th style="width: 12%;">TARGET</th>
            <th style="width: 12%;">ACTUAL</th>
            <th style="width: 12%;">ACCUM</th>
            <th style="width: 29%;">HIGHLIGHT ISSUE</th>
            <th style="width: 15%;">PIC</th>
        </tr>
    </thead>
    <tbody>
        @php
            $flowoutItems = [
                'Customers' => 'CUSTOMERS CLAIM', 
                'Internal'  => 'INTERNAL FLOWOUT', 
                'Supplier'  => 'SUPPLIER FLOWOUT'
            ];
            $bladeToDb = ['Customers' => 'Customers', 'Internal' => 'Internal', 'Supplier' => 'Supplier'];
            $rowCountFlow = count($flowoutItems);
            $isFirstFlow = true;
        @endphp
        @foreach($flowoutItems as $key => $label)
        @php
            $dbPfx = $bladeToDb[$key];
            $target = $asakai->asakaiQuality->{"{$dbPfx}Target"} ?? 0;
            $actual = $asakai->asakaiQuality->{"{$dbPfx}Act"}    ?? 0;
            $accum  = $asakai->asakaiQuality->{"{$dbPfx}Acc"}    ?? 0;
            $issue  = $asakai->asakaiQuality->{"{$dbPfx}Issue"}  ?? '';
            $picFlow = $asakai->asakaiQuality->CustomersPIC   ?? ''; 
        @endphp
        <tr>
            <td class="category-label" style="text-align: center; font-weight: 800;">{{ $label }}</td>
            <td><input type="number" name="{{$key}}Target" class="form-control-asakai text-center" value="{{ $target }}"></td>
            <td id="td_qual_{{$key}}Act" style="background-color: {{ $actual > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="{{$key}}Act" class="form-control-asakai text-center" oninput="updateColor(this, 'td_qual_{{$key}}Act')" value="{{ $actual }}">
            </td>
            <td id="td_qual_{{$key}}Acc" style="background-color: {{ $accum > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="{{$key}}Acc" class="form-control-asakai text-center" oninput="updateColor(this, 'td_qual_{{$key}}Acc')" value="{{ $accum }}">
            </td>
            <td><input type="text" name="{{$key}}Issue" class="form-control-asakai" value="{{ $issue }}"></td>
            
            @if($isFirstFlow)
            <td rowspan="{{ $rowCountFlow }}" style="vertical-align: middle; background: #fff; border: 1px solid #333;">
                <input type="text" name="CustomersPIC" class="form-control-asakai text-center" value="{{ $picFlow }}" style="font-weight: 900; color: #4e73df; border: none; background: transparent;">
            </td>
            @php $isFirstFlow = false; @endphp
            @endif
        </tr>
        @endforeach
    </tbody>
</table>

@foreach(['REPAIR', 'REJECT'] as $type)
    @php 
        $resData = ($type == 'REPAIR') ? $repairData : $rejectData; 
        $masterPicKey = ($type == 'REPAIR') ? 'RepairPIC' : 'RejectPIC';
        $picMasterGroup = $asakai->asakaiQuality->{$masterPicKey} ?? '';
    @endphp
    
    <div class="section-title" style="margin-top: 15px;">{{ $type }} (%)</div>
    
    <table class="table-input" style="margin-bottom: 20px; table-layout: fixed; width: 100%; border: 1px solid #333;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 15%;">LINE</th>
                <th style="width: 10%;">TARGET</th>
                <th style="width: 15%;">ACTUAL (%)</th>
                <th style="width: 15%;">ACCUM (%)</th>
                <th style="width: 35%;">HIGHLIGHT ISSUE</th>
                <th style="width: 10%;">PIC</th>
            </tr>
        </thead>
        <tbody>
            @php
                $mapping = [
                    'LINE E' => ['nameKey' => 'LINEE', 'dbCol' => 'LineE', 'dbField' => 'totalLineE', 'accField' => 'accumLineE', 'prodKey' => 'prodLineE'],
                    'LINE F' => ['nameKey' => 'LINEF', 'dbCol' => 'LineF', 'dbField' => 'totalLineF', 'accField' => 'accumLineF', 'prodKey' => 'prodLineF'],
                    'LINE K' => ['nameKey' => 'LINEK', 'dbCol' => 'LineK', 'dbField' => 'totalLineK', 'accField' => 'accumLineK', 'prodKey' => 'prodLineK'],
                ];
                $targetLimit = ($type == 'REPAIR') ? 1.00 : 0.03;
                $rowCountRep = count($mapping);
                $isFirstRep = true;
            @endphp

            @foreach($mapping as $label => $map)
            @php
                $dbColName = ucfirst(strtolower($type)) . $map['dbCol'];
                
                $valActDB = $asakai->asakaiQuality->{"{$dbColName}Act"} ?? null;
                $valAccDB = $asakai->asakaiQuality->{"{$dbColName}Acc"} ?? null;
                $valIssueDB = $asakai->asakaiQuality->{"{$dbColName}Issue"} ?? '';

                $qtySelectedDate = $resData->{$map['dbField']} ?? 0;
                $qtyAccumUntilSelected = $resData->{$map['accField']} ?? 0;
                $prodAccumUntilSelected = $totalProduksiMTD->{$map['prodKey']} ?? 0;

                $autoAct = ($qtyAccumUntilSelected > 0) ? ($qtySelectedDate / $qtyAccumUntilSelected) * 100 : 0;
                $autoAcc = ($prodAccumUntilSelected > 0) ? ($qtyAccumUntilSelected / $prodAccumUntilSelected) * 100 : 0;
                
                $autoIssue = "";
                if ($resData && !empty($resData->listIssue)) {
                    $issues = explode("\n", $resData->listIssue);
                    foreach($issues as $issueRow) {
                        if (str_contains(strtoupper($issueRow), strtoupper($label))) {
                            $autoIssue .= $issueRow . "\n";
                        }
                    }
                }

                $valAct = ($valActDB !== null) ? $valActDB : $autoAct;
                $valAcc = ($valAccDB !== null) ? $valAccDB : $autoAcc;
                $finalIssue = (!empty(trim($valIssueDB))) ? $valIssueDB : $autoIssue;
            @endphp
            <tr>
                <td class="category-label" style="text-align: center; font-weight: 800; font-size: 11px;">{{ $label }}</td>
                <td style="text-align: center;">{{ number_format($targetLimit, 2) }}%</td>
                <td style="background-color: {{ $valAct > $targetLimit ? '#f8d7da' : '#d4edda' }};">
                    <input type="text" name="{{$type}}{{$map['nameKey']}}Act" class="form-control-asakai text-center" 
                        value="{{ is_numeric($valAct) ? number_format($valAct, 2).'%' : $valAct }}" style="font-weight: bold;">
                </td>
                <td style="background-color: {{ $valAcc > $targetLimit ? '#f8d7da' : '#d4edda' }};">
                    <input type="text" name="{{$type}}{{$map['nameKey']}}Acc" class="form-control-asakai text-center" 
                        value="{{ is_numeric($valAcc) ? number_format($valAcc, 2).'%' : $valAcc }}" style="font-weight: bold;">
                </td>
                <td style="background-color: #ffffff; padding: 2px !important;">
                    <textarea name="{{$type}}{{$map['nameKey']}}Issue" class="form-control-asakai" 
                        style="height: 35px; border: none; font-size: 10px; font-weight: bold; background: transparent; resize: none;">{{ trim($finalIssue) }}</textarea>
                </td>

                @if($isFirstRep)
                <td rowspan="{{ $rowCountRep }}" style="vertical-align: middle; background: #fff; border: 1px solid #333;">
                    <input type="text" name="{{$type}}PIC_Global" class="form-control-asakai text-center" value="{{ $picMasterGroup }}" style="font-weight: 900; color: #4e73df; border: none; background: transparent;">
                </td>
                @php $isFirstRep = false; @endphp
                @endif
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>