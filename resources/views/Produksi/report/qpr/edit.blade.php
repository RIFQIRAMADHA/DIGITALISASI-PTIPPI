@extends('Produksi.layouts.main')

@section('title', 'Edit Data QPR')
@section('page-title', 'Edit Data QPR')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .form-section {
        background: #fff;
        border: 2px solid #343a40;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
    }
    .label-custom {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 10px;
        display: block;
        color: #333;
    }
    .input-custom {
        border-radius: 10px;
        height: 42px;
        border: 1.5px solid #343a40;
        box-sizing: border-box;
        font-size: 13px;
        padding: 0 10px;
        width: 100%;
    }
    
    /* 🛠️ CONTROLLER TOMBOL BERGESER KE KIRI (FLEX-START) */
    .btn-action-container { 
        margin-top: 40px; 
        display: flex; 
        gap: 15px; 
        justify-content: flex-start; 
        border-top: 1.5px solid #f1f2f6; 
        padding-top: 25px; 
        align-items: center;
    }
    .btn-save-qpr { 
        background: #4361ee; 
        color: white; 
        border: none; 
        height: 42px; 
        width: 160px; 
        border-radius: 8px; 
        font-weight: 600; 
        font-size: 14px; 
        transition: all 0.2s ease-in-out; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .btn-save-qpr:hover { 
        background: #304ec2; 
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25); 
    }
    .btn-cancel-qpr { 
        background: #ffffff; 
        color: #4b4b4b; 
        border: 1px solid #dcdde1; 
        height: 42px; 
        width: 120px; 
        border-radius: 8px; 
        font-weight: 600; 
        font-size: 14px; 
        text-decoration: none; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        transition: all 0.2s; 
    }
    
    .custom-readonly { background-color: #f8f9fa !important; border: 1.5px solid #343a40 !important; color: #636e72 !important; opacity: 1; text-align: center; }

    /* ==========================================================================
       🔥 SUNTIKAN CSS OVERRIDE SAKTI: VISUAL SELECT2 MATCHING SAMA FORM UTAMA
       ========================================================================== */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1.5px solid #343a40 !important;
        border-radius: 10px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #fff !important;
        box-sizing: border-box !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333 !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        padding-left: 12px !important;
        line-height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        height: 40px !important;
        line-height: 40px !important;
        margin-right: 12px !important;
        font-size: 14px !important;
        color: #999 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        top: 0 !important;
        right: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .select2-dropdown {
        border: 1.5px solid #343a40 !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        z-index: 99999 !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #343a40 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        font-size: 12.5px !important;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--open .select2-selection--single {
        border-color: #4361ee !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15) !important;
    }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Report</span> <span class="separator">></span>
    <span>QPR</span> <span class="separator">></span>
    <span class="active">Edit QPR</span>
</div>

<div class="page-container">
    {{-- ALERT ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 8px; font-size: 13px;">{{ session('error') }}</div>
    @endif

    {{-- HEADER SUMMARY STATUS --}}
    <div class="reject-summary-wrapper mb-4">
        <div class="reject-summary-container">
            <span class="summary-main-label">QPR No:</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2" style="background: #f8f9fa; font-weight: bold; color: #e11d2e;">{{ $qpr->IdQpr }}</span>
            </div>
        </div>
        <div class="reject-summary-container">
            <span class="summary-main-label">Edit Date:</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2">{{ date('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('report.qpr.update', urlencode($qpr->IdQpr)) }}" method="POST" id="formEditQPR" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        {{-- CARD CONTAINER FORM UTAMA --}}
        <div class="form-section">
            {{-- BARIS 1: ITEM SELECTION & PRODUCTION DATE --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px;">
                
                {{-- 🔥 ITEM SUDAH DI-LOCK (READONLY) + HIDDEN INPUT --}}
                <div class="form-group">
                    <label class="label-custom">Item / Job Number</label>
                    <input type="text" class="form-control input-custom custom-readonly" readonly 
                           value="{{ $qpr->inputHarian->item->JobNumber ?? 'N/A' }} | {{ $qpr->inputHarian->item->NamaPart ?? '-' }}">
                    
                    {{-- Hidden input agar ID Input Harian tetap terkirim ke Controller saat di-Update --}}
                    <input type="hidden" name="IdInputHarian" id="IdInputHarian" value="{{ $qpr->IdInputHarian }}">
                </div>

                <div class="form-group">
                    <label class="label-custom">Production Date</label>
                    <input type="text" id="tgl_produksi" class="form-control input-custom custom-readonly" readonly value="{{ $qpr->inputHarian ? date('d-m-Y', strtotime($qpr->inputHarian->TanggalProduksi)) : 'Automatic' }}">
                </div>
            </div>

            {{-- BARIS 2: PART NAME, MODEL, NO QPR --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">Part Name</label>
                    <input type="text" id="nama_part" class="form-control input-custom custom-readonly" readonly value="{{ $qpr->inputHarian->item->NamaPart ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="label-custom">Model</label>
                    <input type="text" id="model_part" class="form-control input-custom custom-readonly" readonly value="{{ $qpr->inputHarian->item->Model ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="label-custom">QPR Number</label>
                    <input type="text" class="form-control input-custom custom-readonly" readonly value="{{ $qpr->IdQpr }}">
                </div>
            </div>

            {{-- BARIS 3: REPAIR COUNTER, REJECT COUNTER, STOCK IPPI --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">Repair (A | B)</label>
                    <div style="display: flex; border: 1.5px solid #343a40; border-radius: 10px; overflow: hidden; height: 42px;">
                        {{-- NGAMBIL DARI TABEL QPR LANGSUNG ($qpr->Rework) --}}
                        <input type="number" step="0.01" name="RepairA" id="qpr_repair_a" style="width: 50%; border: none; border-right: 1.5px solid #343a40; text-align: center; outline: none;" value="{{ $qpr->Rework ?? 0 }}">
                        <input type="number" step="0.01" name="RepairB" id="qpr_repair_b" style="width: 50%; border: none; text-align: center; outline: none;" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-custom">Reject (A | B)</label>
                    <div style="display: flex; border: 1.5px solid #343a40; border-radius: 10px; overflow: hidden; height: 42px;">
                        {{-- NGAMBIL DARI TABEL QPR LANGSUNG ($qpr->Reject) --}}
                        <input type="number" step="0.01" name="RejectA" id="qpr_reject_a" style="width: 50%; border: none; border-right: 1.5px solid #343a40; text-align: center; outline: none;" value="{{ $qpr->Reject ?? 0 }}">
                        <input type="number" step="0.01" name="RejectB" id="qpr_reject_b" style="width: 50%; border: none; text-align: center; outline: none;" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-custom">Stock IPPI (Pcs) <span style="color: red;">*</span></label>
                    <input type="number" name="Stok" class="form-control input-custom" value="{{ old('Stok', $qpr->Stok) }}" min="0" style="text-align: center;">
                </div>
            </div>

            {{-- BARIS 4: PRODUCTION PLAN, REPAIR PROCESS, PROBLEM LOCATION --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">Production Plan <span style="color: red;">*</span></label>
                    <input type="date" name="RencanaProduksi" class="form-control input-custom" value="{{ old('RencanaProduksi', $qpr->RencanaProduksi ? \Carbon\Carbon::parse($qpr->RencanaProduksi)->format('Y-m-d') : '') }}">
                </div>
                <div class="form-group">
                    <label class="label-custom">Repair Process <span style="color: red;">*</span></label>
                    <select name="ProsesRepair" class="form-select input-custom">
                        <option value="">- Select Process -</option>
                        @foreach(['DF', 'OP-10', 'OP-20', 'OP-30', 'OP-40', 'Palleting'] as $opt)
                            <option value="{{ $opt }}" {{ $qpr->ProsesRepair == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="label-custom">Problem Location <span style="color: red;">*</span></label>
                    <select name="LokasiKejadian" id="lokasi_kejadian" class="form-select input-custom" required>
                        <option value="">- Select Location -</option>
                        @foreach(['OP-10', 'OP-20', 'OP-30', 'OP-40', 'Lain-lain'] as $loc)
                            <option value="{{ $loc }}" {{ $qpr->LokasiKejadian == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- BARIS 5: REF DOC, SHIFT, TIME --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="label-custom">Reference Document</label>
                    <input type="text" name="DocReferensi" class="form-control input-custom" value="{{ old('DocReferensi', $qpr->DocReferensi) }}">
                </div>
                <div class="form-group">
                    <label class="label-custom">Shift</label>
                    <input type="text" id="shift_val" name="Shift" class="form-control input-custom custom-readonly" readonly 
                        value="{{ $qpr->inputHarian->Shift ?? ($qpr->inputHarian->productionLine->Shift ?? '-') }}">
                </div>
                <div class="form-group">
                    <label class="label-custom">Time <span style="color: red;">*</span></label>
                    <input type="time" name="Jam" class="form-control input-custom" value="{{ old('Jam', $qpr->Jam ? \Carbon\Carbon::parse($qpr->Jam)->format('H:i') : date('H:i')) }}" style="text-align: center;">
                </div>
            </div>

            {{-- SKETCH CONTAINER VISUAL --}}
            <div class="text-center mb-2" style="border-top: 2px solid #f8f9fa; padding-top: 25px; margin-top: 25px;">
                <div id="sketch-container">
                    @if($qpr->inputHarian && $qpr->inputHarian->item && $qpr->inputHarian->item->Gambar)
                        <img src="{{ asset('storage/' . $qpr->inputHarian->item->Gambar) }}" style="max-height: 250px; border-radius: 12px; border: 2px solid #343a40;">
                    @else
                        <div style="max-width: 450px; margin: 0 auto; padding: 25px; border: 2px dashed #343a40; border-radius: 12px; background: #fafafa;">
                            <p style="color: #888; font-size: 12px; font-weight: bold; margin: 0; text-transform: uppercase;">No visual sketch registered for this item</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- PARTIAL ROW DYNAMIC MASALAH --}}
        <div id="masalah-qpr-wrapper" class="mb-4">
            @include('Produksi.report.qpr.partials.masalah_qpr_row', ['existingDetails' => $qpr->detailsMasalah])
        </div>

        {{-- PARTIAL ROW DYNAMIC VERIFIKASI --}}
        <div id="verifikasi-qpr-wrapper" class="mb-4">
            @include('Produksi.report.qpr.partials.verifikasi_qpr_row', ['displayVerifikasi' => $qpr->detailsVerifikasi])
        </div>

        {{-- FORMACTION ACTION BUTTONS (FLEX-START LURUS KIRI) --}}
        <div class="btn-action-container">
            <button type="button" class="btn-save-qpr" onclick="confirmUpdateQPR()">
                Update
            </button>
            <a href="{{ route('report.qpr.index') }}" class="btn-cancel-qpr">Cancel</a>
        </div>
    </form>
</div>

<script>
// Deklarasi Counter Indeks Bawaan QPR Lu
var masalahIndex = {{ count($qpr->detailsMasalah) > 0 ? count($qpr->detailsMasalah) : 0 }};
var verifikasiIndex = {{ count($qpr->detailsVerifikasi) > 0 ? count($qpr->detailsVerifikasi) : 0 }};

document.addEventListener('DOMContentLoaded', function() {
    // 🔥 AKTIFKAN SELECT2 SEARCHABLE PADA EDIT MODE
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-searchable').select2({
            placeholder: '- Select Item -',
            allowClear: true,
            width: '100%'
        });

        $('#IdInputHarian').on('select2:select', function (e) {
            getJobDetail(this);
        });

        // =========================================================================
        // 🔥 TAMBAHAN BARU: SENGGOL SELECT2 BIAR BACA DATA LAMA DARI BLADE
        // =========================================================================
        setTimeout(function() {
            $('#IdInputHarian').trigger('change');
        }, 100);
    }
});

// 5. Handle Konfirmasi Update QPR
    function confirmUpdateQPR() {
        Swal.fire({
            title: 'Perbarui Data QPR?',
            text: "Apakah Anda Yakin Ingin Menyimpan Perubahan Ini?",
            icon: 'question', 
            showCancelButton: true,
            confirmButtonColor: '#4361ee', // Biru
            cancelButtonColor: '#6c757d',  // Abu-abu gelap
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formEditQPR');
                let errorList = [];

                // --- 1. Cek Header QPR ---
                if (!$('#tgl_produksi').val()) errorList.push("Header: Tanggal Produksi wajib diisi.");
                if (!$('#IdInputHarian').val()) errorList.push("Header: Item / Job Number wajib dipilih.");
                if (!$('#lokasi_kejadian').val()) errorList.push("Header: Lokasi Kejadian wajib diisi.");

                // --- 2. Cek Kolom Wajib di Detail MASALAH ---
                $('.masalah-item-card').each(function(index) {
                    let no = index + 1;
                    if (!$(this).find('input[name*="[NomorKerusakan]"]').val()) errorList.push(`Problem #${no}: NO. SKETCH wajib diisi.`);
                    if (!$(this).find('select[name*="[Keterangan]"]').val()) errorList.push(`Problem #${no}: Kategori Masalah wajib dipilih.`);
                    if (!$(this).find('input[name*="[LastDateProblem]"]').val()) errorList.push(`Problem #${no}: Last Date wajib diisi.`);
                    if (!$(this).find('input[name*="[AnalisaPenyebab]"]').val()) errorList.push(`Problem #${no}: Analisa Penyebab wajib diisi.`);
                    
                    // Cek Koreksi 1
                    if (!$(this).find('input[name*="[Correction]"]').val()) errorList.push(`Problem #${no}: Koreksi wajib diisi.`);
                    if (!$(this).find('input[name*="[TargetCorrection]"]').val()) errorList.push(`Problem #${no}: Target Koreksi wajib diisi.`);
                    if (!$(this).find('select[name*="[PICCorrection]"]').val()) errorList.push(`Problem #${no}: PIC Koreksi wajib dipilih.`);
                    
                    // Cek Penanggulangan 2
                    if (!$(this).find('input[name*="[Correction2]"]').val()) errorList.push(`Problem #${no}: Penanggulangan wajib diisi.`);
                    if (!$(this).find('input[name*="[TargetCorrection2]"]').val()) errorList.push(`Problem #${no}: Target Penanggulangan wajib diisi.`);
                    if (!$(this).find('select[name*="[PICCorrection2]"]').val()) errorList.push(`Problem #${no}: PIC Penanggulangan wajib dipilih.`);
                });

                // --- 3. Cek Kolom Wajib di Detail VERIFIKASI ---
                $('.verifikasi-item-card').each(function(index) {
                    let no = index + 1;
                    if (!$(this).find('input[name*="[LangkahPerbaikan]"]').val()) errorList.push(`Verification #${no}: Corrective Action wajib diisi.`);
                    if (!$(this).find('input[name*="[Schedule]"]').val()) errorList.push(`Verification #${no}: Schedule wajib diisi.`);
                });

                // --- 4. Tampilkan Error Jika Ada ---
                if (errorList.length > 0) {
                    // Batasi maksimal 7 list error biar pop-up gak ngelewatin batas layar
                    let displayErrors = errorList.slice(0, 7);
                    let moreCount = errorList.length - 7;
                    
                    let listHtml = `
                        <div style="text-align: left; font-size: 14px; color: #555; max-height: 300px; overflow-y: auto;">
                            <p style="margin-bottom: 10px;">Mohon lengkapi data yang masih kosong:</p>
                            <ul style="margin-top: 0; padding-left: 20px;">
                    `;
                    
                    displayErrors.forEach(function(err) {
                        listHtml += `<li>${err}</li>`;
                    });
                    
                    if (moreCount > 0) {
                        listHtml += `<li style="font-weight: bold; color: #e11d2e;">...dan ${moreCount} kolom lainnya.</li>`;
                    }
                    
                    listHtml += `</ul></div>`;

                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        html: listHtml,
                        confirmButtonColor: '#e11d2e', // Merah Astra
                        confirmButtonText: 'OK'
                    });
                } 
                // --- 5. Lolos Uji, Submit Form! ---
                else {
                    if (form) {
                        form.submit();
                    } else {
                        console.error("Terjadi kesalahan");
                    }
                }
            }
        });
    }

function addMasalahRow() {
    fetch("{{ url('report/qpr/add-masalah-row') }}/" + masalahIndex)
        .then(response => response.text())
        .then(html => {
            document.getElementById('masalah-container').insertAdjacentHTML('beforeend', html);
            masalahIndex++;
        });
}

function addVerifikasiRow() {
    fetch("{{ url('report/qpr/add-verifikasi-row') }}/" + verifikasiIndex)
        .then(response => response.text())
        .then(html => {
            document.getElementById('verifikasi-container').insertAdjacentHTML('beforeend', html);
            verifikasiIndex++;
        });
}

function removeMasalahRow(button) {
    if (document.querySelectorAll('.masalah-item-card').length > 1) {
        button.closest('.masalah-item-card').remove();
    }
}

function removeVerifikasiRow(button) {
    if (document.querySelectorAll('.verifikasi-item-card').length > 1) {
        button.closest('.verifikasi-item-card').remove();
    }
}

function getJobDetail(selectElement) {
    const id = selectElement.value;
    const sketchContainer = document.getElementById('sketch-container');
    if (!id) return;

    fetch(`/report/qpr/get-job-detail/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('tgl_produksi').value = data.TanggalProduksi || '';
            document.getElementById('nama_part').value = data.NamaPart || '';
            document.getElementById('model_part').value = data.Model || '';
            
            const shiftInput = document.getElementById('shift_val');
            if (shiftInput) {
                shiftInput.value = data.Shift || '-';
            }

            document.getElementById('qpr_repair_a').value = data.RepairA ?? 0;
            document.getElementById('qpr_repair_b').value = data.RepairB ?? 0;
            document.getElementById('qpr_reject_a').value = data.RejectA ?? 0;
            document.getElementById('qpr_reject_b').value = data.RejectB ?? 0;

            if (data.Gambar) {
                sketchContainer.innerHTML = `<img src="/storage/${data.Gambar}" style="max-height: 250px; border-radius: 12px; border: 2px solid #343a40;">`;
            } else {
                sketchContainer.innerHTML = `<div style="max-width: 450px; margin: 0 auto; padding: 25px; border: 2px dashed #343a40; border-radius: 12px; background: #fafafa;"><p style="color: #888; font-size: 12px; font-weight: bold; margin: 0; text-transform: uppercase;">No visual sketch registered for this item</p></div>`;
            }
        })
        .catch(err => console.error("Oops! Something went wrong:", err));
}
</script>
@endsection