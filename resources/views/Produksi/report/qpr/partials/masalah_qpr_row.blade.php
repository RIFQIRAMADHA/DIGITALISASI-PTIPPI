<div id="masalah-container">
    @php 
        $dataDetails = (isset($existingDetails) && count($existingDetails) > 0) ? $existingDetails : (isset($details) ? $details : collect());
    @endphp

    {{-- LOOPING DATA --}}
    @foreach($dataDetails as $index => $item)
    <div class="content-card mb-4 masalah-item-card" style="border: 1.5px solid #343a40; border-radius: 12px; background: white; overflow: hidden; margin-bottom: 25px;">
        {{-- CARD HEADER: TITLE & DELETE BUTTON --}}
        <div class="card-header" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #343a40; padding: 12px 25px;">
            <h5 class="card-number-title" style="font-size: 13px; margin: 0; font-weight: bold; text-transform: uppercase;">
                Problem Counter <span style="color: #4361ee; font-size: 11px; margin-left: 10px;">{{ ($item && isset($item->IdMasalah)) ? '(Old)' : '(New)' }}</span>
            </h5>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="masalah[{{ $index }}][IdMasalah]" value="{{ $item->IdMasalah ?? '' }}">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeMasalahRow(this)" style="font-weight: bold; font-size: 11px; border-radius: 6px; padding: 5px 15px; background-color: #dc3545; border: none; color: white; cursor: pointer;">Delete</button>
            </div>
        </div>
        
        {{-- CARD BODY: LOGIKA PROPORSI FLEX LAYOUT ANTI GEPMAN/GEPENG --}}
        <div class="card-body" style="padding: 25px;">
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                {{-- 🛠️ BARIS 1: SINKRONISASI NAMA ASLI & RASIO LEBAR (Flex 2 untuk Sketch No, Flex 1.2 untuk dropdown/date) --}}
                <div style="display: flex; gap: 20px; width: 100%;">
                    <div class="form-group" style="flex: 2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">NO. SKETCH <span style="color: red;">*</span></label>
                        <input type="text" name="masalah[{{ $index }}][NomorKerusakan]" value="{{ $item->NomorKerusakan ?? '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box;" placeholder="Input sketch number...">
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Masalah <span style="color: red;">*</span></label>
                        <select name="masalah[{{ $index }}][Keterangan]" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; background: #fff;">
                            <option value="">- Pilih Kategori -</option>
                            @foreach(['Man', 'Method', 'Machines', 'Material', 'Environtment', 'Other'] as $kategori)
                                <option value="{{ $kategori }}" {{ ($item->Keterangan ?? '') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Keterangan <span style="color: red;">*</span></label>
                        <select name="masalah[{{ $index }}][DeskripsiProblem]" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; background: #fff;">
                            <option value="Baru Pertama" {{ ($item->DeskripsiProblem ?? '') == 'Baru Pertama' ? 'selected' : '' }}>Baru Pertama</option>
                            <option value="Kadang-kadang" {{ ($item->DeskripsiProblem ?? '') == 'Kadang-kadang' ? 'selected' : '' }}>Kadang-kadang</option>
                            <option value="Sering" {{ ($item->DeskripsiProblem ?? '') == 'Sering' ? 'selected' : '' }}>Sering</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Last Date <span style="color: red;">*</span></label>
                        <input type="date" name="masalah[{{ $index }}][LastDateProblem]" value="{{ isset($item->LastDateProblem) ? date('Y-m-d', strtotime($item->LastDateProblem)) : '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; text-align: left;">
                    </div>
                </div>

                {{-- BARIS 2: ANALISA PENYEBAB (FULL 1 KOLOM) --}}
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Analisa Penyebab <span style="color: red;">*</span></label>
                    <input type="text" name="masalah[{{ $index }}][AnalisaPenyebab]" value="{{ $item->AnalisaPenyebab ?? '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box;" placeholder="Tuliskan analisa penyebab di sini...">
                </div>

                {{-- 🛠️ BARIS 3: CORRECTION 1 (Koreksi dilebarkan proporsional pakai Flex: 2) --}}
                <div style="display: flex; gap: 20px; width: 100%;">
                    <div class="form-group" style="flex: 2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Koreksi <span style="color: red;">*</span></label>
                        <input type="text" name="masalah[{{ $index }}][Correction]" value="{{ $item->Correction ?? '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box;" placeholder="Tuliskan Koreksi">
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Target <span style="color: red;">*</span></label>
                        <input type="date" name="masalah[{{ $index }}][TargetCorrection]" value="{{ isset($item->TargetCorrection) ? date('Y-m-d', strtotime($item->TargetCorrection)) : '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; text-align: left;">
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">PIC <span style="color: red;">*</span></label>
                        <select name="masalah[{{ $index }}][PICCorrection]" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; background: #fff;">
                            <option value="">- Pilih PIC -</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->NamaKaryawan }}" {{ ($item->PICCorrection ?? '') == $k->NamaKaryawan ? 'selected' : '' }}>
                                    {{ $k->NamaKaryawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Status <span style="color: red;">*</span></label>
                        <select name="masalah[{{ $index }}][StatusCorrection]" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; background: #fff;">
                            <option value="Open" {{ ($item->StatusCorrection ?? '') == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Closed" {{ ($item->StatusCorrection ?? '') == 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>

                {{-- 🛠️ BARIS 4: CORRECTION 2 (Penanggulangan Dampak dilebarkan proporsional pakai Flex: 2) --}}
                <div style="display: flex; gap: 20px; width: 100%;">
                    <div class="form-group" style="flex: 2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Penanggulangan <span style="color: red;">*</span></label>
                        <input type="text" name="masalah[{{ $index }}][Correction2]" value="{{ $item->Correction2 ?? '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box;" placeholder="Tuliskan Penanggulangan">
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Target <span style="color: red;">*</span></label>
                        <input type="date" name="masalah[{{ $index }}][TargetCorrection2]" value="{{ isset($item->TargetCorrection2) ? date('Y-m-d', strtotime($item->TargetCorrection2)) : '' }}" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; text-align: left;">
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">PIC <span style="color: red;">*</span></label>
                        <select name="masalah[{{ $index }}][PICCorrection2]" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; background: #fff;">
                            <option value="">- Pilih PIC -</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->NamaKaryawan }}" {{ ($item->PICCorrection2 ?? '') == $k->NamaKaryawan ? 'selected' : '' }}>
                                    {{ $k->NamaKaryawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1.2;">
                        <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block; color: #333;">Status <span style="color: red;">*</span></label>
                        <select name="masalah[{{ $index }}][StatusCorrection2]" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; font-size: 13px; padding: 0 12px; width: 100%; box-sizing: border-box; cursor: pointer; background: #fff;">
                            <option value="0" {{ ($item->StatusCorrection2 ?? 0) == 0 ? 'selected' : '' }}>Open</option>
                            <option value="1" {{ ($item->StatusCorrection2 ?? 0) == 1 ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>

                {{-- BUTTON ADD ACTION --}}
                <div style="border-top: 1px dashed #ddd; padding-top: 15px; margin-top: 10px; display: flex; justify-content: flex-start;">
                    <button type="button" onclick="addMasalahRow()" style="font-weight: bold; background: #4361ee; border: none; padding: 8px 16px; border-radius: 6px; font-size: 11.5px; color: white; cursor: pointer;">
                        + Add Problem
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endforeach

    {{-- AUTO-ADD UNTUK MODE CREATE --}}
    @if($dataDetails->isEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof addMasalahRow === "function") { addMasalahRow(); }
            });
        </script>
    @endif
</div>