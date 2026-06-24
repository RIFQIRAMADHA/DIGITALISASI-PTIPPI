{{-- 1. SAFETY SECTION --}}
<div class="section-title">1. SAFETY</div>

<table class="table-input" style="table-layout: fixed; width: 100%; border: 1px solid #333;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="width: 20%;">KATEGORI</th>
            <th style="width: 12%;">TARGET</th>
            <th style="width: 12%;">ACTUAL</th>
            <th style="width: 12%;">ACCUM (MTD)</th>
            <th style="width: 29%;">HIGHLIGHT ISSUE</th>
            <th style="width: 15%;">PIC</th>
        </tr>
    </thead>
    <tbody>
        @php
            $safeData = $asakai->asakaiSafety;
            $valPicMaster = $safeData->SafetyPic ?? ($safeData->SafetyPIC ?? '');
        @endphp

        {{-- ROW 1: ACCIDENT --}}
        <tr>
            <td class="category-label" style="text-align: center; font-weight: 800; font-size: 11px;">ACCIDENT</td>
            <td>
                <input type="number" name="AccidentTarget" class="form-control-asakai text-center" 
                    value="{{ isset($safeData->AccidentTarget) ? round($safeData->AccidentTarget) : 0 }}">
            </td>
            <td id="td_safety_AccidentAct" style="background-color: {{ ($safeData->AccidentAct ?? 0) > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="AccidentAct" class="form-control-asakai text-center" 
                    oninput="updateColor(this, 'td_safety_AccidentAct')" 
                    value="{{ isset($safeData->AccidentAct) ? round($safeData->AccidentAct) : 0 }}" style="font-weight: bold;">
            </td>
            <td id="td_safety_AccidentAccum" style="background-color: {{ ($safeData->AccidentAccum ?? 0) > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="AccidentAccum" class="form-control-asakai text-center" 
                    oninput="updateColor(this, 'td_safety_AccidentAccum')" 
                    value="{{ isset($safeData->AccidentAccum) ? round($safeData->AccidentAccum) : 0 }}" style="font-weight: bold;">
            </td>
            <td style="background-color: #ffffff; padding: 2px !important;">
                <input type="text" name="AccidentIssue" class="form-control-asakai text-center" 
                    value="{{ $safeData->AccidentIssue ?? '' }}" style="background: transparent; border: none; font-size: 11px; font-weight: bold;">
            </td>
            
            {{-- ROWSPAN PIC SAFETY GLOBAL --}}
            <td rowspan="3" style="vertical-align: middle; background-color: #fff; border: 1px solid #333;">
                <input type="text" name="SafetyPIC" class="form-control-asakai text-center" 
                    value="{{ $valPicMaster }}" 
                    style="font-size: 11px; font-weight: 900; color: #4e73df; border: none; background: transparent;">
            </td>
        </tr>

        {{-- ROW 2: INCCIDENT --}}
        <tr>
            <td class="category-label" style="text-align: center; font-weight: 800; font-size: 11px;">INCCIDENT</td>
            <td>
                <input type="number" name="InccidentTarget" class="form-control-asakai text-center" 
                    value="{{ isset($safeData->InccidentTarget) ? round($safeData->InccidentTarget) : 0 }}">
            </td>
            <td id="td_safety_InccidentAct" style="background-color: {{ ($safeData->InccidentAct ?? 0) > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="InccidentAct" class="form-control-asakai text-center" 
                    oninput="updateColor(this, 'td_safety_InccidentAct')" 
                    value="{{ isset($safeData->InccidentAct) ? round($safeData->InccidentAct) : 0 }}" style="font-weight: bold;">
            </td>
            <td id="td_safety_InccidentAccum" style="background-color: {{ ($safeData->InccidentAccum ?? 0) > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="InccidentAccum" class="form-control-asakai text-center" 
                    oninput="updateColor(this, 'td_safety_InccidentAccum')" 
                    value="{{ isset($safeData->InccidentAccum) ? round($safeData->InccidentAccum) : 0 }}" style="font-weight: bold;">
            </td>
            <td style="background-color: #ffffff; padding: 2px !important;">
                <input type="text" name="InccidentIssue" class="form-control-asakai text-center" 
                    value="{{ $safeData->InccidentIssue ?? '' }}" style="background: transparent; border: none; font-size: 11px; font-weight: bold;">
            </td>
        </tr>

        {{-- ROW 3: TRAFFIC ACCIDENT --}}
        <tr>
            <td class="category-label" style="text-align: center; font-weight: 800; font-size: 11px;">TRAFFIC ACCIDENT</td>
            <td>
                <input type="number" name="TrafficTarget" class="form-control-asakai text-center" 
                    value="{{ isset($safeData->TrafficTarget) ? round($safeData->TrafficTarget) : 0 }}">
            </td>
            <td id="td_safety_TrafficAct" style="background-color: {{ ($safeData->TrafficAct ?? 0) > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="TrafficAct" class="form-control-asakai text-center" 
                    oninput="updateColor(this, 'td_safety_TrafficAct')" 
                    value="{{ isset($safeData->TrafficAct) ? round($safeData->TrafficAct) : 0 }}" style="font-weight: bold;">
            </td>
            <td id="td_safety_TrafficAccum" style="background-color: {{ ($safeData->TrafficAccum ?? 0) > 0 ? '#f8d7da' : '#d4edda' }};">
                <input type="number" name="TrafficAccum" class="form-control-asakai text-center" 
                    oninput="updateColor(this, 'td_safety_TrafficAccum')" 
                    value="{{ isset($safeData->TrafficAccum) ? round($safeData->TrafficAccum) : 0 }}" style="font-weight: bold;">
            </td>
            <td style="background-color: #ffffff; padding: 2px !important;">
                <input type="text" name="TrafficIssue" class="form-control-asakai text-center" 
                    value="{{ $safeData->TrafficIssue ?? '' }}" style="background: transparent; border: none; font-size: 11px; font-weight: bold;">
            </td>
        </tr>
    </tbody>
</table>