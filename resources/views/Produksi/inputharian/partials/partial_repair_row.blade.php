<div class="repair-row-container" style="margin-bottom: 15px; width: 100%;">
    <input type="hidden" name="id[]" value="{{ $detail->id ?? '' }}">
    <div class="content-card repair-item-card shadow-sm" style="border: 1.5px solid #343a40; border-radius: 12px; overflow: hidden; background: #fff; width: 100%;">
        <div class="card-header" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #343a40; padding: 12px 25px;">
            <h5 class="page-title" style="font-size: 14px; margin: 0; font-weight: 700;">
                Baris Repair <span class="row-number-repair">{{ is_numeric($index) ? $index + 1 : 1 }}</span>
            </h5>
            <button type="button" class="btn btn-danger btn-sm font-weight-bold px-4" style="border-radius: 10px; background: #f82b3d; border: none; color: white;" onclick="removeDetailRepair(this)">Delete Row</button>
        </div>
        
        <div class="card-body" style="padding: 30px;">
            {{-- ✅ PERUBAHAN: Menggunakan flex-wrap agar baris tetap rapi saat layar sempit --}}
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; align-items: flex-end;">
                
                {{-- NO. SKETCH --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 120px; flex: 0.5;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">No. Sketch <span style="color: red;">*</span></label>
                    <input type="text" name="NoMasalah[]" class="form-control border-dark" 
                        value="{{ $detail->NoMasalah ?? '' }}" placeholder="..."
                        style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; box-sizing: border-box; text-align: center; font-size: 13px; font-weight: 400; width: 100%;">
                </div>
                
                {{-- JENIS REPAIR --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 350px; flex: 1;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Jenis Repair <span style="color: red;">*</span></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select name="IdRepair[]" class="form-select border-dark" style="border-radius: 10px; height: 42px; width: 50%; border: 1.5px solid #343a40; box-sizing: border-box;" onchange="checkLainLainRepair(this)" required>
                            <option value="">- Pilih Jenis -</option>
                            @foreach($masterRepair as $mr)
                                <option value="{{ $mr->IdRepair }}" {{ isset($detail) && $detail->IdRepair == $mr->IdRepair ? 'selected' : '' }}>{{ $mr->TipeRepair }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="RepairLain[]" class="form-control border-dark" placeholder="Lainnya..." value="{{ isset($detail) && $detail->IdRepair == 'RP-LAIN' ? $detail->TipeRepair : '' }}" style="border-radius: 10px; height: 42px; width: 50%; background-color: {{ isset($detail) && $detail->IdRepair == 'RP-LAIN' ? '#fff' : '#f1f1f1' }}; border: 1.5px solid #343a40; box-sizing: border-box; font-size: 13px;" {{ isset($detail) && $detail->IdRepair == 'RP-LAIN' ? '' : 'disabled' }}>
                    </div>
                </div>

                {{-- NAMA KERUSAKAN --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 350px; flex: 1;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Nama Kerusakan <span style="color: red;">*</span></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select name="NamaKerusakan[]" class="form-select border-dark select-nama-repair" style="border-radius: 10px; height: 42px; width: 50%; border: 1.5px solid #343a40; box-sizing: border-box;" onchange="autoFillPenyebabRepair(this)" required>
                            <option value="">- Pilih Nama -</option>
                            @php $namaOptionsRepair = ['BENJOL GOMIKAMI', 'BENJOL KIRIKO', 'PECOK GOMIKAMI', 'PECOK KIRIKO', 'BARET', 'SHOCKLINE', 'GELOMBANG', 'MAKURE/MENCUAT', 'FLEX/KARAT', 'PENYOK/DEFORM', 'BURRY', 'MINUS']; @endphp
                            @foreach($namaOptionsRepair as $opt)
                                <option value="{{ $opt }}" {{ isset($detail) && strtoupper($detail->NamaKerusakan) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                            <option value="Lain-lain" {{ isset($detail) && isset($detail->NamaKerusakan) && !in_array(strtoupper($detail->NamaKerusakan), $namaOptionsRepair) ? 'selected' : '' }}>Lain-lain</option>
                        </select>
                        <input type="text" name="NamaLain[]" class="form-control border-dark" placeholder="Lainnya..." value="{{ isset($detail) && isset($detail->NamaKerusakan) && !in_array(strtoupper($detail->NamaKerusakan), $namaOptionsRepair) ? $detail->NamaKerusakan : '' }}" style="border-radius: 10px; height: 42px; width: 50%; background-color: {{ isset($detail) && isset($detail->NamaKerusakan) && !in_array(strtoupper($detail->NamaKerusakan), $namaOptionsRepair) ? '#fff' : '#f1f1f1' }}; border: 1.5px solid #343a40; box-sizing: border-box; font-size: 13px;" {{ isset($detail) && isset($detail->NamaKerusakan) && !in_array(strtoupper($detail->NamaKerusakan), $namaOptionsRepair) ? '' : 'disabled' }}>
                    </div>
                </div>

                {{-- QTY A & B --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 80px; flex: 0.5;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">QTY (A) <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="QtyRepairA[]" class="form-control border-dark" value="{{ $detail->RepairA ?? '' }}" onfocus="this.select()" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; box-sizing: border-box; width: 100%;" placeholder="0">
                </div>

                <div class="form-group" style="margin-bottom: 0; min-width: 80px; flex: 0.5;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">QTY (B)</label>
                    <input type="number" step="0.01" name="QtyRepairB[]" class="form-control border-dark" value="{{ $detail->RepairB ?? '' }}" onfocus="this.select()" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; box-sizing: border-box; width: 100%;" placeholder="0">
                </div>

                {{-- AREA PROBLEM --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 120px; flex: 0.8;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Area Problem <span style="color: red;">*</span></label>
                    <select name="AreaProblem[]" class="form-select border-dark area-problem-select" style="border: 1.5px solid #343a40; border-radius: 10px; height: 42px; width: 100%;" required>
                        <option value="">- Pilih Area -</option>
                        <option value="OP 10" {{ (isset($detail) && $detail->AreaProblem == 'OP 10') ? 'selected' : '' }}>OP 10</option>
                        <option value="OP 20" {{ (isset($detail) && $detail->AreaProblem == 'OP 20') ? 'selected' : '' }}>OP 20</option>
                        <option value="OP 30" {{ (isset($detail) && $detail->AreaProblem == 'OP 30') ? 'selected' : '' }}>OP 30</option>
                        <option value="OP 40" {{ (isset($detail) && $detail->AreaProblem == 'OP 40') ? 'selected' : '' }}>OP 40</option>
                    </select>
                </div>
            </div>

            {{-- ANALISIS --}}
            <div style="display: flex; gap: 40px; margin-bottom: 10px; flex-wrap: wrap;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 250px;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Penyebab (Root Cause)</label>
                    <textarea name="PenyebabRepair[]" class="form-control border-dark textarea-penyebab-repair" style="border-radius: 10px; resize: none; border: 1.5px solid #343a40; padding: 12px; box-sizing: border-box; width: 100%;" rows="4" placeholder="Jelaskan penyebab...">{{ $detail->Penyebab ?? '' }}</textarea>
                </div>
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 250px;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Countermeasure (Perbaikan)</label>
                    <textarea name="CountermeasureRepair[]" class="form-control border-dark" style="border-radius: 10px; resize: none; border: 1.5px solid #343a40; padding: 12px; box-sizing: border-box; width: 100%;" rows="4" placeholder="Langkah perbaikan...">{{ $detail->Countermeasure ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>