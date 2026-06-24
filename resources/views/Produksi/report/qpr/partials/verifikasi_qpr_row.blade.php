<div id="verifikasi-container">
    @php 
        $display = (isset($displayVerifikasi) && count($displayVerifikasi) > 0) ? $displayVerifikasi : collect(); 
    @endphp

    @foreach($display as $vIndex => $vItem)
    <div class="content-card mb-4 verifikasi-item-card" style="border: 1.5px solid #343a40; border-radius: 12px; background: white; overflow: hidden; margin-bottom: 25px;">
        {{-- CARD HEADER: TITLE & DELETE BUTTON --}}
        <div class="card-header" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #343a40; padding: 12px 25px;">
            <h5 class="page-title" style="font-size: 13px; margin: 0; font-weight: bold; text-transform: uppercase;">
                Verification Counter <span style="color: #4361ee; font-size: 11px; margin-left: 10px;">{{ ($vItem && isset($vItem->IdVerifikasi)) ? '(Old)' : '(New)' }}</span>
            </h5>
            <div>
                <input type="hidden" name="verifikasi[{{ $vIndex }}][IdVerifikasi]" value="{{ $vItem->IdVerifikasi ?? '' }}">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeVerifikasiRow(this)" style="font-weight: bold; font-size: 11px; border-radius: 6px; padding: 5px 15px; background-color: #dc3545; border: none; color: white; cursor: pointer;">Delete</button>
            </div>
        </div>
        
        {{-- CARD BODY: REPEAT GRAD PATTERN FROM MAIN CREATE FORM --}}
        <div class="card-body" style="padding: 25px;">
            
            {{-- BARIS 1: CORRECTIVE ACTION (GRID FULL 1 KOLOM) --}}
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Corrective Action <span style="color: red;">*</span></label>
                    <input type="text" name="verifikasi[{{ $vIndex }}][LangkahPerbaikan]" value="{{ $vItem->LangkahPerbaikan ?? '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box;" placeholder="Input corrective action counter-measure...">
                </div>
            </div>

            {{-- BARIS 2: SCHEDULE, VERIFICATION DATE, STATUS (GRID 3 KOLOM - TANGGAL RATA KIRI) --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Schedule <span style="color: red;">*</span></label>
                    <input type="date" name="verifikasi[{{ $vIndex }}][Schedule]" value="{{ isset($vItem->Schedule) ? date('Y-m-d', strtotime($vItem->Schedule)) : '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; text-align: left;">
                </div>
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Verification Date</label>
                    <input type="date" name="verifikasi[{{ $vIndex }}][TanggalVerifikasi]" value="{{ isset($vItem->TanggalVerifikasi) ? date('Y-m-d', strtotime($vItem->TanggalVerifikasi)) : '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; text-align: left;">
                </div>
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Status <span style="color: red;">*</span></label>
                    <select name="verifikasi[{{ $vIndex }}][Status]" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; background: #fff;">
                        <option value="Open" {{ ($vItem->Status ?? '') == 1 ? 'selected' : '' }}>Open</option>
                        <option value="Closed" {{ ($vItem->Status ?? '') == 0 ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>

            {{-- BARIS 3: CHECK METHOD (GRID 3 KOLOM) --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 10px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Check Method <span style="color: red;">*</span></label>
                    <div style="display: flex; gap: 20px; border: 1.5px solid #343a40; border-radius: 10px; height: 42px; align-items: center; padding: 0 15px; background: #fff; box-sizing: border-box; width: 100%;">
                        @foreach(['Visual', 'Fungsi', 'Dimensi'] as $method)
                        <label style="font-size: 13px; margin: 0; cursor: pointer; display: flex; align-items: center; gap: 6px; font-weight: 600; color: #333;">
                            <input type="radio" name="verifikasi[{{ $vIndex }}][MethodeCheck1]" value="{{ $method }}" {{ ($vItem->MethodeCheck1 ?? 'Visual') == $method ? 'checked' : '' }} style="cursor: pointer; margin: 0;"> {{ $method }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TOMBOL ADD ACTION DI BAWAH CARD --}}
            <div style="border-top: 1px dashed #ddd; padding-top: 15px; margin-top: 15px; display: flex; justify-content: flex-start;">
                <button type="button" onclick="addVerifikasiRow()" style="font-weight: bold; background: #4361ee; border: none; padding: 8px 16px; border-radius: 6px; font-size: 11.5px; color: white; cursor: pointer;">
                    + Add Verification
                </button>
            </div>

        </div>
    </div>
    @endforeach

    {{-- AUTO-ADD UNTUK MODE CREATE --}}
    @if($display->isEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof addVerifikasiRow === "function") { addVerifikasiRow(); }
            });
        </script>
    @endif
</div>