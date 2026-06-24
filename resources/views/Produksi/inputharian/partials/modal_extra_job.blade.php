<div id="modalExtraJobManual" class="custom-overlay-modal" style="display: none;">
    <div class="custom-modal-dialog">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-plus-square text-white" style="font-size: 18px;"></i>
                    <h5 class="modal-title-custom">TAMBAH ITEM LUAR JADWAL (EXTRA JOB)</h5>
                </div>
                <button type="button" class="close-custom-modal" onclick="tutupExtraModal()">&times;</button>
            </div>
            
            <form action="{{ route('inputharian.storeExtra') }}" method="POST">
                @csrf
                <div class="custom-modal-body">
                    <div class="extra-alert-box">
                        <i class="fas fa-info-circle"></i>
                        <span>Item ini akan ditambahkan langsung ke list produksi. <b>Plan Qty otomatis 0.</b></span>
                    </div>

                    <div class="form-group-custom">
                        <label>PILIH ITEM PRODUKSI / JOB NUMBER</label>
                        <select name="IdItemProduksi" id="selectExtraManual" class="form-control" required style="width: 100%;">
                            <option value="">--- Cari Job Number / Nama Part ---</option>
                            @foreach($item as $i)
                                <option value="{{ $i->IdItemProduksi }}">
                                    {{ $i->JobNumber }} - {{ $i->NamaPart }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ambil nilai dari filter date dan filter line yang sedang aktif --}}
                    <input type="hidden" name="IdProductionLine" value="{{ request('line') }}">
                    <input type="hidden" name="TanggalProduksi" value="{{ request('date') ?? date('Y-m-d') }}">
                </div>
                
                <div class="custom-modal-footer">
                    <button type="button" class="btn-cancel-custom" onclick="tutupExtraModal()">Cancel</button>
                    <button type="submit" class="btn-save-custom">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Fix Scroll di Dropdown Select2 */
    .select2-container--open .select2-dropdown {
        z-index: 10002 !important; /* Harus lebih tinggi dari modal (10001) */
    }

    .select2-results__options {
        max-height: 200px !important; /* Batasi tinggi dropdown */
        overflow-y: auto !important;  /* Paksa muncul scrollbar */
    }

    /* Biar scrollbar-nya kelihatan ala industrial (opsional) */
    .select2-results__options::-webkit-scrollbar {
        width: 5px;
    }
    .select2-results__options::-webkit-scrollbar-thumb {
        background: #e11d2e; 
        border-radius: 10px;
    }
    /* Style Mandiri - Sama dengan Detail Plan */
    .custom-overlay-modal {
        position: fixed !important;
        top: 0; left: 0;
        width: 100% !important; height: 100% !important;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: none;
        align-items: center; justify-content: center;
        backdrop-filter: blur(2px);
    }
    .custom-modal-dialog { width: 450px; max-width: 95%; }
    .custom-modal-content {
        background: #fff; border-radius: 8px; border: 2px solid #e11d2e;
        overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.5);
    }
    .custom-modal-header {
        background: #e11d2e; padding: 12px 20px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-title-custom { color: #fff; font-size: 13px; font-weight: 800; margin: 0; letter-spacing: 1px; }
    .close-custom-modal { background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; }
    .custom-modal-body { padding: 25px; background: #f8f9fa; }
    .extra-alert-box {
        background: #fff3cd; border: 1px solid #ffeeba; color: #856404;
        padding: 12px; border-radius: 4px; font-size: 11px; font-weight: 700;
        margin-bottom: 20px; display: flex; gap: 10px; align-items: center;
    }
    .form-group-custom label { display: block; font-size: 11px; font-weight: 800; margin-bottom: 8px; color: #333; }
    .custom-modal-footer {
        padding: 15px 25px; background: #fff; border-top: 1px solid #dee2e6;
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .btn-save-custom {
        background: #ffffff; color: #000; border: 1.5px solid #000;
        padding: 8px 20px; font-weight: 800; font-size: 11px; cursor: pointer; border-radius: 4px;
    }
    .btn-save-custom:hover { background: #000; color: #fff; }
    .btn-cancel-custom { background: none; border: none; color: #666; font-weight: 800; font-size: 11px; cursor: pointer; }
    
    /* Fix Select2 agar selalu di depan modal */
    .select2-container { z-index: 10001 !important; }
</style>
