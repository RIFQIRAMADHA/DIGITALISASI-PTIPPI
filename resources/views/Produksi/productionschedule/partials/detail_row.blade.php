<div class="content-card mb-4 detail-item-card">
    <div class="card-header" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 10px 20px;">
        <h5 class="page-title" style="font-size: 14px; margin: 0;">2. Detail Job {{ $index + 1 }}</h5>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeDetailRow(this)">Hapus</button>
    </div>
    
    <div class="card-body" style="padding: 20px;">
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 30px;">
            {{-- Bagian Kiri: Item Produksi --}}
            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                <div class="form-group">
                    <label style="font-size: 12px; color: #888;">Item Produksi</label>
                    <select name="details[{{ $index }}][IdItemProduksi]" class="form-select" required>
                        <option value="">Pilih Item</option>
                        @foreach($item as $i)
                            <option value="{{ $i->IdItemProduksi }}" 
                                {{ old("details.$index.IdItemProduksi", $detail->IdItemProduksi ?? '') == $i->IdItemProduksi ? 'selected' : '' }}>
                                {{ $i->JobNumber }} - {{ $i->NamaPart }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="addDetailRow()">Tambah Detail Job</button>
            </div>

            {{-- Bagian Kanan: Input Grid --}}
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                {{-- BARIS 1 --}}
                <div class="form-group">
                    <label style="font-size: 12px;">Plan QTY</label>
                    <div style="display: flex; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; height: 38px;">
                        <input type="number" name="details[{{ $index }}][PlanQty1]" style="width: 50%; border: none; border-right: 1px solid #ddd; text-align: center;" 
                               value="{{ old("details.$index.PlanQty1", $detail->PlanQtyA ?? 0) }}">
                        <input type="number" name="details[{{ $index }}][PlanQty2]" style="width: 50%; border: none; text-align: center;" 
                               value="{{ old("details.$index.PlanQty2", $detail->PlanQtyB ?? 0) }}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px;">Start</label>
                    <input type="time" name="details[{{ $index }}][StartProduksi]" 
                        class="form-control time-start" 
                        onchange="calculateWorkTime(this)"
                        value="{{ old("details.$index.StartProduksi", isset($detail) ? \Carbon\Carbon::parse($detail->PlanStart)->format('H:i') : '') }}">
                </div>

                <div class="form-group">
                    <label style="font-size: 12px;">Finish</label>
                    <input type="time" name="details[{{ $index }}][FinishProduksi]" 
                        class="form-control time-finish" 
                        onchange="calculateWorkTime(this)"
                        value="{{ old("details.$index.FinishProduksi", isset($detail) ? \Carbon\Carbon::parse($detail->PlanFinish)->format('H:i') : '') }}">
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px;">Press Time</label>
                    <input type="text" name="details[{{ $index }}][PressTime]" class="form-control" 
                           value="{{ old("details.$index.PressTime", $detail->PressTime ?? 0) }}">
                </div>

                {{-- BARIS 2 --}}
                <div class="form-group">
                    <label style="font-size: 12px;">Uchi</label>
                    <input type="number" name="details[{{ $index }}][Uchi]" class="form-control" 
                           value="{{ old("details.$index.Uchi", $detail->DiesChangeUchi ?? 0) }}">
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px;">Soto</label>
                    <input type="number" name="details[{{ $index }}][Soto]" class="form-control" 
                           value="{{ old("details.$index.Soto", $detail->DiesChangeSoto ?? 0) }}">
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px;">TPT</label>
                    <input type="number" name="details[{{ $index }}][TPT]" class="form-control" 
                           value="{{ old("details.$index.TPT", $detail->TPT ?? 0) }}">
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px;">Work Time (Min)</label>
                    <input type="number" readonly name="details[{{ $index }}][WorkTime]" 
                        class="form-control work-time bg-light" 
                        value="{{ old("details.$index.WorkTime", $detail->PlanWorkTime ?? 0) }}">
                </div>

                {{-- BARIS 3 --}}
                <div class="form-group">
                    <label style="font-size: 12px;">GSPH</label>
                    <input type="number" name="details[{{ $index }}][GSPH]" class="form-control" 
                           value="{{ old("details.$index.GSPH", $detail->PlanGSPH ?? 0) }}">
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px;">Stroke</label>
                    <input type="number" name="details[{{ $index }}][Stroke]" class="form-control" 
                           value="{{ old("details.$index.Stroke", $detail->Stroke ?? 0) }}">
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label style="font-size: 12px;">Note</label>
                    <input type="text" name="details[{{ $index }}][Note]" class="form-control" placeholder="Catatan..." 
                           value="{{ old("details.$index.Note", $detail->Note ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>