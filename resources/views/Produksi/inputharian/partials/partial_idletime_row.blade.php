<div class="idletime-row-container" style="margin-bottom: 40px; width: 100%;">
    <div class="content-card idletime-item-card shadow-sm" style="border: 1.5px solid #343a40; border-radius: 12px; overflow: hidden; background: #fff; width: 100%;">
        {{-- CARD HEADER --}}
        <div class="card-header" style="background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #343a40; padding: 12px 25px;">
            <h5 class="page-title" style="font-size: 14px; margin: 0; font-weight: 700;">
                Baris Idle Time <span class="row-number-idle">{{ is_numeric($index) ? $index + 1 : 1 }}</span>
            </h5>
            <button type="button" class="btn btn-danger btn-sm font-weight-bold px-4" style="border-radius: 10px; background: #f82b3d; border: none; color: white;" onclick="removeDetailIdle(this)">Delete Row</button>
        </div>
        
        <div class="card-body" style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 25px;">
                
                {{-- JENIS IDLE TIME --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Jenis Idle Time (Idle Time)</label>
                    <select name="IdIdleTime[]" class="form-select border-dark" style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; box-sizing: border-box; width: 100%;" required>
                        <option value="">- Pilih Jenis Idle Time -</option>
                        @foreach($masterIdle as $mi)
                            <option value="{{ $mi->IdIdleTime }}" {{ isset($detail) && $detail->IdIdleTime == $mi->IdIdleTime ? 'selected' : '' }}>
                                {{ $mi->TipeIdleTime }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- DURASI Idle --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Durasi (Menit)</label>
                    {{-- UPDATE LOGIKA: Konversi format HH:mm:ss ke angka menit murni agar tampil bener --}}
                    <input type="number" name="Durasi[]" class="form-control border-dark" 
                        style="border-radius: 10px; height: 42px; border: 1.5px solid #343a40; box-sizing: border-box;" 
                        value="{{ isset($detail->Durasi) ? (\Carbon\Carbon::parse($detail->Durasi)->minute + (\Carbon\Carbon::parse($detail->Durasi)->hour * 60)) : 0 }}" 
                        placeholder="0" min="0" required>
                </div>
            </div>

            {{-- ALASAN Idle --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label style="font-size: 13px; font-weight: 700; margin-bottom: 10px; display: block;">Alasan / Keterangan Idle Time</label>
                <textarea name="Alasan[]" class="form-control border-dark" 
                          style="border-radius: 10px; resize: none; border: 1.5px solid #343a40; padding: 12px; box-sizing: border-box; width: 100%;" 
                          rows="3" placeholder="Jelaskan detail penyebab idle time...">{{ $detail->Alasan ?? '' }}</textarea>
            </div>

            <div style="margin-top: 30px;">
                <button type="button" class="btn btn-sm" 
                        style="background: #5d5fef; color: white; border-radius: 8px; padding: 10px 20px; font-weight: 700; border: none;" 
                        onclick="addDetailIdle()">Add Detail</button>
            </div>
        </div>
    </div>
</div>