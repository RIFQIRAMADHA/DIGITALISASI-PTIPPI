{{-- Container Utama per Baris Downtime --}}
<div class="downtime-row-container" style="width: 100%; margin-bottom: 30px;">
    <div class="content-card downtime-item-card shadow-sm" style="border: 2px solid #2d3436; border-radius: 15px; overflow: hidden; background: #fff;">
        
        {{-- HEADER CARD --}}
        <div class="card-header d-flex justify-content-between align-items-center" style="background: #f1f2f6; border-bottom: 2px solid #2d3436; padding: 12px 25px;">
            <div class="d-flex align-items-center gap-2">
                <div style="background: #2d3436; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px;">
                    <span class="row-number-dt">{{ isset($index) && is_numeric($index) ? $index + 1 : 1 }}</span>
                </div>
                <h5 class="mb-0" style="font-size: 14px; font-weight: 800; color: #2d3436; text-transform: uppercase;">Baris Analisis Masalah</h5>
            </div>
            <button type="button" class="btn btn-danger btn-sm font-weight-bold px-4" style="border-radius: 10px; background: #f82b3d; border:none;" onclick="removeDetailDT(this)">Delete Row</button>
        </div>
        
        <div class="card-body" style="padding: 25px; background: #ffffff;">
            
            {{-- BARIS 1: TIPE DOWNTIME & AREA PROBLEM --}}
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 25px;">
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Tipe Downtime <span style="color: red;">*</span></span></label>
                    <select name="IdDowntime[]" class="form-select" style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 42px; {{ $isQuality ? 'background-color: #f8f9fa;' : '' }}" {{ $isQuality ? 'disabled' : 'required' }}>
                        <option value="">- Pilih Tipe Downtime -</option>
                        @foreach($masterDowntime as $md)
                            <option value="{{ $md->IdDowntime }}" {{ (isset($detail) && $detail->Keterangan == $md->IdDowntime) ? 'selected' : '' }}>
                                {{ $md->TipeDowntime }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="TipeDowntime[]" value="{{ $detail->TipeDowntime ?? '' }}">
                    @if($isQuality) <input type="hidden" name="IdDowntime[]" value="{{ $detail->IdDowntime ?? '' }}"> @endif
                </div>

                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Area Problem <span style="color: red;">*</span></span></label>
                    <select class="form-select area-problem-select" style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 42px; {{ $isQuality ? 'background-color: #f8f9fa;' : '' }}" {{ $isQuality ? 'disabled' : 'required' }}>
                        <option value="">- Pilih Area -</option>
                        @foreach(['OP 10', 'OP 20', 'OP 30', 'OP 40', 'Lain-lain'] as $area)
                            <option value="{{ $area }}" {{ (isset($detail) && $detail->AreaProblem == $area) ? 'selected' : '' }}>{{ $area }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="AreaProblem[]" class="form-control manual-input" 
                        style="margin-top: 12px; border: 1.5px solid #5d5fef; border-radius: 10px; display: {{ (isset($detail) && !in_array($detail->AreaProblem, ['OP 10','OP 20','OP 30','OP 40',''])) ? 'block' : 'none' }}; background-color: {{ (isset($detail) && $detail->AreaProblem == 'Lain-lain') ? '#ffffff' : '#e9ecef' }};" 
                        value="{{ $detail->AreaProblem ?? '' }}" placeholder="Ketik area manual..." {{ (isset($detail) && $detail->AreaProblem == 'Lain-lain') ? '' : 'readonly' }}>
                </div>
            </div>

            <div class="section-divider mb-3" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 11px; font-weight: 800; color: #636e72; text-transform: uppercase;">Kategori & Durasi</span>
                <div style="flex: 1; height: 1px; background: #dfe6e9;"></div>
            </div>

            {{-- BARIS 2: DIPAKSA 1 BARIS DENGAN GRID, GAP DIPERBESAR --}}
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 30px; margin-bottom: 25px;">
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Type <span style="color: red;">*</span></label>
                    <select name="TipeMasalah[]" class="form-select" style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 38px; width: 100%; font-size: 13px; font-weight: 700; {{ $isQuality ? 'background-color: #f8f9fa;' : '' }}" {{ $isQuality ? 'disabled' : '' }}>
                        <option value="">- Pilih -</option>
                        @foreach(['Die Trouble (DT)', 'Machine Trouble (MT)', 'Supp. Mach. Trouble (SMT)', 'Material Sheet Trouble (MST)', 'Single Part Trouble (SPT)', 'Pallet Trouble (Pat)', 'Jig Trouble (JT)', 'Quality Trouble (QT)', 'Production Trouble (ProT)', 'Die Trial (DTr)', 'Jig Trial (JTr)', 'Accident (Acd)', 'No Order (NO)', 'UN Balance Process (UBP)', 'Electrical Shutdown (ES)'] as $tm)
                            <option value="{{ $tm }}" {{ (isset($detail) && trim($detail->TipeMasalah) == $tm) ? 'selected' : '' }}>{{ $tm }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">

                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Durasi (Menit) <span style="color: red;">*</span></span></label>
                    <input type="number" name="Durasi[]" class="form-control durasi-input" step="0.1"
                        oninput="updateLoseTimeMonitoring()" 
                        style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 38px; padding: 0 12px; font-size: 13px; font-weight: 800; color: #d63031; {{ $isQuality ? 'background-color: #f8f9fa;' : '' }}" 
                        @php
                            $rawDurasi = 0;
                            if(isset($detail->Durasi)){
                                if(str_contains($detail->Durasi, ':')){
                                    $p = explode(':', $detail->Durasi);
                                    $rawDurasi = ($p[0]*60) + $p[1] + ($p[2]/60);
                                } else { $rawDurasi = (float)$detail->Durasi; }
                            }
                        @endphp
                        value="{{ $rawDurasi > 0 ? number_format($rawDurasi, 1, '.', '') : '' }}" 
                        {{ $isQuality ? 'readonly' : 'required' }}>
                </div>

                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Stroke Ke <span style="color: red;">*</span></label>
                    <input type="number" name="Stroke[]" class="form-control" 
                        style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 38px; padding: 0 10px; width: 100%; font-size: 13px; font-weight: 600; {{ $isQuality ? 'background-color: #f8f9fa;' : '' }}" 
                        value="{{ $detail->Stroke ?? '' }}" {{ $isQuality ? 'readonly' : '' }}>
                </div>
            </div>

            {{-- BARIS 3: PROBLEM & AKAR PENYEBAB --}}
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
    {{-- Problem --}}
    <div class="form-group" style="width: 100%;">
        <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Problem <span style="color: red;">*</span></label>
        <select class="form-select problem-select" style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 42px; width: 100%; box-sizing: border-box;" {{ $isQuality ? 'disabled' : '' }}>
            <option value="{{ $detail->Masalah ?? '' }}">{{ $detail->Masalah ?? '- Pilih Area -' }}</option>
        </select>
        <input type="text" name="Masalah[]" class="form-control manual-input" 
            style="margin-top: 12px; border: 1.5px solid #b2bec3; border-radius: 10px; width: 100%; height: 42px; padding: 0 10px; box-sizing: border-box; display: {{ isset($detail->Masalah) ? 'block' : 'none' }}; background-color: {{ (isset($detail) && $detail->Masalah == 'Lain-lain') ? '#ffffff' : '#e9ecef' }};" 
            value="{{ $detail->Masalah ?? '' }}" placeholder="Manual..." {{ (isset($detail) && $detail->Masalah == 'Lain-lain') ? '' : 'readonly' }}>
    </div>

    {{-- Akar Penyebab --}}
    <div class="form-group" style="width: 100%;">
        <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Akar Penyebab <span style="color: red;">*</span></label>
        <select class="form-select akar-select" style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 42px; width: 100%; box-sizing: border-box;" {{ $isQuality ? 'disabled' : '' }}>
            <option value="{{ $detail->AkarPenyebab ?? '' }}">{{ $detail->AkarPenyebab ?? '- Pilih Problem -' }}</option>
        </select>
        <input type="text" name="AkarPenyebab[]" class="form-control manual-input" 
            style="margin-top: 12px; border: 1.5px solid #b2bec3; border-radius: 10px; width: 100%; height: 42px; padding: 0 10px; box-sizing: border-box; display: {{ isset($detail->AkarPenyebab) ? 'block' : 'none' }}; background-color: {{ (isset($detail) && $detail->AkarPenyebab == 'Lain-lain') ? '#ffffff' : '#e9ecef' }};" 
            value="{{ $detail->AkarPenyebab ?? '' }}" placeholder="Manual..." {{ (isset($detail) && $detail->AkarPenyebab == 'Lain-lain') ? '' : 'readonly' }}>
    </div>

    {{-- Fakta Lapangan --}}
    <div class="form-group" style="width: 100%;">
        <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Fakta Lapangan <span style="color: red;">*</span></label>
        <textarea name="FaktaLapangan[]" class="form-control" rows="3" required
            style="border: 1.5px solid #b2bec3; border-radius: 10px; font-size: 13px; width: 100%; padding: 10px; box-sizing: border-box; {{ $isQuality ? 'background-color: #f8f9fa;' : '' }}" 
            placeholder="Input fakta lapangan..." {{ $isQuality ? 'readonly' : '' }}>{{ $detail->FaktaLapangan ?? '' }}</textarea>
    </div>
</div>

            {{-- INVESTIGASI QUALITY --}}
            <div class="section-divider mb-3" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 11px; font-weight: 800; color: #0d6efd; text-transform: uppercase;">Investigasi & Tindakan (Quality Team)</span>
                <div style="flex: 1; height: 1px; background: #cfe2ff;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Penanganan On-Site</label>
                    <textarea name="Penanganan[]" class="form-control" rows="3" 
                        style="border: 1.5px solid #b2bec3; border-radius: 10px; font-size: 13px; {{ !$isQuality ? 'background-color: #f8f9fa;' : 'border-color: #0d6efd;' }}" 
                        {{ $isQuality ? '' : 'readonly' }} 
                        placeholder="{{ $isQuality ? 'Jelaskan penanganan sementara...' : 'Menunggu input Quality...' }}">{{ $detail->Penanganan ?? '' }}</textarea>
                </div>
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Fix Action (Permanent)</label>
                    <textarea name="FixAction[]" class="form-control" rows="3" 
                        style="border: 1.5px solid #b2bec3; border-radius: 10px; font-size: 13px; {{ !$isQuality ? 'background-color: #f8f9fa;' : 'border-color: #0d6efd;' }}" 
                        {{ $isQuality ? '' : 'readonly' }} 
                        placeholder="{{ $isQuality ? 'Jelaskan tindakan pencegahan...' : 'Menunggu input Quality...' }}">{{ $detail->FixAction ?? '' }}</textarea>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px;">
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">PIC Pelaksana</label>
                    <input type="text" name="NamaPIC[]" class="form-control" 
                        style="width: 100%; box-sizing: border-box; border: 1.5px solid #b2bec3; border-radius: 10px; height: 38px; font-size: 13px; {{ !$isQuality ? 'background-color: #f8f9fa;' : '' }}" 
                        value="{{ $detail->NamaPIC ?? '' }}" {{ $isQuality ? '' : 'readonly' }}>
                </div>
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Target Due Date</label>
                    <input type="date" name="TargetDueDate[]" class="form-control" 
                        style="width: 100%; box-sizing: border-box; border: 1.5px solid #b2bec3; border-radius: 10px; height: 38px; font-size: 13px; {{ !$isQuality ? 'background-color: #f8f9fa;' : '' }}" 
                        value="{{ isset($detail->TargetDueDate) ? \Carbon\Carbon::parse($detail->TargetDueDate)->format('Y-m-d') : '' }}" {{ $isQuality ? '' : 'readonly' }}>
                </div>
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block;">Status Closing</label>
                    <select name="Status[]" class="form-select" {{ $isQuality ? '' : 'disabled' }} 
                        style="border: 1.5px solid #b2bec3; border-radius: 10px; height: 38px; font-size: 13px; font-weight: 700; {{ !$isQuality ? 'background-color: #f8f9fa;' : 'border-color: #0d6efd;' }}">
                        <option value="1" {{ (isset($detail) && $detail->Status == 1) ? 'selected' : '' }}>Open</option>
                        <option value="0" {{ (isset($detail) && $detail->Status == 0) ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>