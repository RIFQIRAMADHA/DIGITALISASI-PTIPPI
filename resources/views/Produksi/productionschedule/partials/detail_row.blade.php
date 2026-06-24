@php
    // KUNCI PENYELAMAT: Inisialisasi default selalu false agar halaman Create tidak terkena error 500
    $isItemStarted = false;
    
    // Proteksi: Hanya jalankan validasi jika variabel detail ada (Halaman Edit)
    if(isset($detail) && !empty($detail->IdPlanSchedule)) {
        
        // KUNCI UTAMA: Tembak spesifik ID Input Harian berdasarkan index barisnya sendiri
        $specificIdHarian = 'IH-' . $detail->IdPlanSchedule . '-' . $index;
        
        $isItemStarted = \App\Models\Produksi\Transaksi\TrsInputHarian::where('IdInputHarian', $specificIdHarian)
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('StatusProses')
                        ->where('StatusProses', '!=', 'Ready');
                })
                ->orWhere('GoodA', '>', 0)
                ->orWhere('GoodB', '>', 0)
                ->orWhere('RepairA', '>', 0)
                ->orWhere('RejectA', '>', 0);
            })->exists();
    }
@endphp

<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important; border: 1px solid #ddd !important; border-radius: 8px !important;
        display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
    .select2-container { z-index: 9999 !important; }
    .select2-search__field { height: 34px !important; border-radius: 4px !important; }

    .select2-results__options {
        max-height: 200px !important; 
        overflow-y: auto !important;  
    }

    .select2-results__options::-webkit-scrollbar { width: 5px; }
    .select2-results__options::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 10px; }

    /* 🛠️ FORCE SINKRONISASI UKURAN FIELD (ANTI MELAR) */
    .detail-item-card .form-control,
    .detail-item-card input[type="number"],
    .detail-item-card input[type="time"],
    .detail-item-card input[type="text"] {
        height: 38px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 0 12px !important;
        box-sizing: border-box !important;
        font-size: 13px !important;
    }
    
    .detail-item-card .bg-light {
        background-color: #f8f9fa !important;
    }

    /* 🛠️ STABILISASI LAYOUT UTAMA PAS SPLIT SCREEN */
    .schedule-main-grid {
        display: grid; 
        grid-template-columns: 280px 1fr; 
        gap: 30px;
    }

    .schedule-metrics-grid {
        display: grid; 
        /* GANTI repeat(4, 1fr) dengan auto-fit biar otomatis melipat ke bawah secara rapi kalau sempit */
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
        gap: 15px;
    }

    /* Handling kalau layarnya displit sisa kecil banget (di bawah 992px) */
    @media (max-width: 992px) {
        .schedule-main-grid {
            grid-template-columns: 1fr !important;
            gap: 20px;
        }
    }
</style>

<div class="content-card mb-4 detail-item-card" style="{{ $isItemStarted ? 'border: 2px solid #ffaa00;' : '' }}">
    {{-- Hidden ID untuk keperluan Update --}}
    <input type="hidden" name="details[{{ $index }}][IdItemProduksi]" value="{{ old("details.$index.IdItemProduksi", $detail->IdItemProduksi ?? '') }}">
    
    <div class="card-header" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 10px 20px;">
        <h5 class="page-title" style="font-size: 14px; margin: 0; font-weight: 700;">2. Detail Job {{ $index + 1 }}</h5>
        @if($isItemStarted)
            <span class="badge bg-warning text-dark" style="font-size: 10px; font-weight: 800;">
                <i class="fas fa-lock"></i> PRODUKSI SEDANG BERJALAN (LOCKED)
            </span>
        @else
            <button type="button" class="btn btn-danger btn-sm" onclick="removeDetailRow(this)">Delete</button>
        @endif
    </div>
    
    <div class="card-body" style="padding: 20px;">
        <div class="schedule-main-grid">
            
            {{-- KOLOM KIRI --}}
            <div style="display: flex; flex-direction: column; width: 100%;">
                <div class="form-group mb-3">
                    <label style="font-size: 12px; color: #888; margin-bottom: 5px; display: block; font-weight: 600;">Production Item <span style="color: red;">*</span></label>
                    <select name="details[{{ $index }}][IdItemProduksi]" 
                            class="custom-select2 custom-select2-{{ $index }} select-item-search" 
                            onchange="togglePlanInputs(this)" 
                            {{ $isItemStarted ? 'disabled' : '' }} required style="width: 100%;">
                        <option value="">--- Cari Job / Nama Part ---</option>
                        @foreach($item as $i)
                            <option value="{{ $i->IdItemProduksi }}" 
                                    data-job="{{ $i->JobNumber }}" 
                                    data-ct="{{ $i->CT }}"
                                    data-nama-part="{{ $i->NamaPart }}" 
                                    data-qty-pallet="{{ $i->QtyPerPallet ?? 1 }}" 
                                    {{ old("details.$index.IdItemProduksi", $detail->IdItemProduksi ?? '') == $i->IdItemProduksi ? 'selected' : '' }}>
                                {{ $i->JobNumber }} - {{ $i->NamaPart }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label style="font-size: 12px; color: #888; margin-bottom: 5px; display: block; font-weight: 600;">PO Number</label>
                    <input type="text" name="details[{{ $index }}][PoNumber]" 
                           class="form-control" placeholder="Input PO..." 
                           {{ $isItemStarted ? 'readonly' : '' }} 
                           value="{{ old("details.$index.PoNumber", $detail->PoNumber ?? '') }}">
                </div>

                <div style="margin-top: auto; padding-top: 10px;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="addDetailRow()" 
                            style="background-color: #4361ee; border: none; border-radius: 8px; width: 100%; height: 38px; font-weight: 600;">
                        <i class="fas fa-plus"></i> Add a row of details
                    </button>
                </div>
            </div>

            {{-- GRID KANAN: MENGGUNAKAN CLASS schedule-metrics-grid AGAR ELASTIS PAS DI-SPLIT --}}
            <div class="schedule-metrics-grid">
                
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">Plan QTY (A / B) <span style="color: red;">*</span></label>
                    <div style="display: flex; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; height: 38px;">
                        <input type="number" name="details[{{ $index }}][PlanQty1]" class="plan-qty-a qty-input" oninput="calculateAutoMetrics(this)" {{ $isItemStarted ? 'readonly' : '' }} style="width: 50%; border: none; border-right: 1px solid #cbd5e1; text-align: center; background-color: {{ $isItemStarted ? '#e9ecef' : '#ffffff' }};" value="{{ old("details.$index.PlanQty1", $detail->PlanQtyA ?? 0) }}">
                        <input type="number" name="details[{{ $index }}][PlanQty2]" class="plan-qty-b qty-input" oninput="calculateAutoMetrics(this)" {{ $isItemStarted ? 'readonly' : '' }} style="width: 50%; border: none; text-align: center; background-color: {{ $isItemStarted ? '#e9ecef' : '#ffffff' }};" value="{{ old("details.$index.PlanQty2", $detail->PlanQtyB ?? 0) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">Start <span style="color: red;">*</span></label>
                    <input type="time" 
                        name="details[{{ $index }}][StartProduksi]" 
                        class="form-control time-start" 
                        step="any" 
                        oninput="calculateAutoMetrics(this)" 
                        {{ $isItemStarted ? 'readonly' : '' }} 
                        value="{{ old("details.$index.StartProduksi", isset($detail) ? \Carbon\Carbon::parse($detail->PlanStart)->format('H:i') : '00:00') }}" 
                        required>
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">Finish <span style="color: red;">*</span></label>
                    <input type="time" 
                        name="details[{{ $index }}][FinishProduksi]" 
                        class="form-control time-finish" 
                        step="any" 
                        oninput="calculateAutoMetrics(this)" 
                        {{ $isItemStarted ? 'readonly' : '' }} 
                        value="{{ old("details.$index.FinishProduksi", isset($detail) ? \Carbon\Carbon::parse($detail->PlanFinish)->format('H:i') : '00:00') }}" 
                        required>
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">Work Time (Auto)</label>
                    <input type="number" readonly name="details[{{ $index }}][WorkTime]" class="form-control work-time bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.WorkTime", $detail->PlanWorkTime ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">BQ/SHT <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="details[{{ $index }}][BqSht]" class="form-control bq-sht-input" oninput="calculateAutoMetrics(this)" {{ $isItemStarted ? 'readonly' : '' }} style="text-align: center;" value="{{ old("details.$index.BqSht", $detail->BqSht ?? 1) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">First Q-Check <span style="color: red;">*</span></label>
                    <input type="number" name="details[{{ $index }}][FirstQCheck]" class="form-control first-q-input" oninput="calculateAutoMetrics(this)" {{ $isItemStarted ? 'readonly' : '' }} style="text-align: center;" value="{{ old("details.$index.FirstQCheck", $detail->FirstQCheck ?? 1) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">Uchi Dandori <span style="color: red;">*</span></label>
                    <input type="number" 
                           step="any" name="details[{{ $index }}][Uchi]" 
                           class="form-control uchi-input" 
                           oninput="calculateAutoMetrics(this)" 
                           {{ $isItemStarted ? 'readonly' : '' }} 
                           style="text-align: center;" 
                           value="{{ old("details.$index.Uchi", $detail->DiesChangeUchi ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">Press Time (Auto)</label>
                    <input type="number" step="0.01" readonly name="details[{{ $index }}][PressTime]" class="form-control press-time-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.PressTime", $detail->PressTime ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">Die Change High</label>
                    <input type="number" step="0.01" name="details[{{ $index }}][DieChangeHigh]" class="form-control" {{ $isItemStarted ? 'readonly' : '' }} style="text-align: center;" value="{{ old("details.$index.DieChangeHigh", $detail->DieChangeHigh ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">Soto Dandori (Auto)</label>
                    <input type="number" readonly name="details[{{ $index }}][Soto]" class="form-control soto-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.Soto", $detail->DiesChangeSoto ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">DTR</label>
                    <input type="number" name="details[{{ $index }}][DTR]" class="form-control downtime-input" oninput="calculateAutoMetrics(this)" {{ $isItemStarted ? 'readonly' : '' }} style="text-align: center;" value="{{ old("details.$index.DTR", $detail->DTR ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">TPT (Auto)</label>
                    <input type="number" step="0.01" readonly name="details[{{ $index }}][TPT]" class="form-control tpt-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.TPT", $detail->TPT ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">Total Mesin (Auto)</label>
                    <input type="number" readonly name="details[{{ $index }}][TotalMesin]" class="form-control total-mesin-display-grid bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.TotalMesin", $detail->TotalMesin ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">GSPH (Auto)</label>
                    <input type="number" readonly name="details[{{ $index }}][GSPH]" class="form-control gsph-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.PlanGSPH ?? 0") }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">Stroke (Auto)</label>
                    <input type="number" readonly name="details[{{ $index }}][Stroke]" class="form-control stroke-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.Stroke", $detail->Stroke ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">CT (Auto)</label>
                    <input type="number" step="0.01" readonly name="details[{{ $index }}][CT]" class="form-control ct-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.CT", $detail->CT ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">UBP</label>
                    <input type="number" name="details[{{ $index }}][UBP]" class="form-control ubp-input" oninput="calculateAutoMetrics(this)" {{ $isItemStarted ? 'readonly' : '' }} style="text-align: center;" value="{{ old("details.$index.UBP", $detail->UBP ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">Jml Pallet (Auto)</label>
                    <input type="number" step="0.01" readonly name="details[{{ $index }}][JmlPallet]" class="form-control jml-pallet-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.JmlPallet", $detail->JmlPallet ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; color: #4361ee; font-weight: bold;">Jml Material (Auto)</label>
                    <input type="number" step="0.01" readonly name="details[{{ $index }}][JmlMaterial]" class="form-control jml-material-auto bg-light" style="font-weight: bold; color: #4361ee; text-align: center;" value="{{ old("details.$index.JmlMaterial", $detail->JmlMaterial ?? 0) }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600;">Note</label>
                    <input type="text" name="details[{{ $index }}][Note]" class="form-control" placeholder="Keterangan..." {{ $isItemStarted ? 'readonly' : '' }} value="{{ old("details.$index.Note", $detail->Note ?? '') }}">
                </div>

                {{-- MESIN STATUS --}}
                <div class="robot-fields-container" style="grid-column: span 4; display: none; background: #f8faff; padding: 18px; border-radius: 12px; border: 1px dashed #4361ee; margin-top: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <label style="font-size: 11px; font-weight: bold; color: #4361ee; text-transform: uppercase; letter-spacing: 1px; margin: 0;">
                            <i class="fas fa-toggle-on"></i> Status Aktif Mesin (M1 - M5)
                        </label>
                        <input type="hidden" name="details[{{ $index }}][TotalMesin]" class="total-mesin-val" value="{{ old("details.$index.TotalMesin", $detail->TotalMesin ?? 0) }}">
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px;"> 
                        @for($m = 1; $m <= 5; $m++)
                            @php $is_active = old("details.$index.QtyMesin".$m, $detail->{"QtyMesin".$m} ?? 0) > 0; @endphp
                            <div class="mesin-box" style="text-align: center; padding: 10px; border-radius: 10px; border: 1px solid {{ $is_active ? '#4361ee' : '#eee' }}; background: {{ $is_active ? '#edf2ff' : '#fff' }}; transition: all 0.3s ease;">
                                <label class="mesin-label" style="font-size: 10px; color: {{ $is_active ? '#4361ee' : '#666' }}; display: block; margin-bottom: 8px; font-weight: {{ $is_active ? '800' : '700' }};">MESIN {{ $m }}</label>
                                <div style="position: relative; display: inline-block; width: 50px; height: 26px;">
                                    <input type="checkbox" class="mesin-status-toggle" onchange="updateMesinStatus(this)" style="cursor: pointer; width: 100%; height: 100%; opacity: 0; position: absolute; z-index: 2;" {{ $is_active ? 'checked' : '' }} {{ $isItemStarted ? 'disabled' : '' }}>
                                    <span class="slider-ui" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; border-radius: 34px; transition: .4s; display: block;">
                                        <span class="knob" style="position: absolute; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; border-radius: 50%; transition: .4s; display: block;"></span>
                                    </span>
                                </div>
                                <input type="hidden" name="details[{{ $index }}][QtyMesin{{ $m }}]" class="mesin-value-real" value="{{ old("details.$index.QtyMesin".$m, $detail->{"QtyMesin".$m} ?? 0) }}">
                            </div>
                        @endfor
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>