@extends('Produksi.layouts.main')

@section('title', 'Add Data QPR')
@section('page-title', 'Add Data QPR')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
    .textarea-custom {
        border-radius: 10px;
        border: 1.5px solid #343a40;
        padding: 12px;
        resize: none;
        font-size: 13px;
        width: 100%;
    }

    /* 🛠️ FORCE STYLE PLACEHOLDER ABU-ABU */
    select.input-custom option[value=""] {
        color: #888 !important;
    }
    select.input-custom:invalid {
        color: #888 !important;
    }
    select.input-custom {
        color: #333;
    }

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
       🔥 SUNTIKAN CSS OVERRIDE SAKTI: BIAR VISUAL SELECT2 MATCHING SAMA FORM UTAMA
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
    <span class="active">Add QPR</span>
</div>

<div class="page-container">
    {{-- ALERT ERROR --}}
    @if ($errors->any() || session('error'))
        <div style="background: #fff5f5; border-left: 5px solid #f82b3d; padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #c53030; font-size: 12px;">
            <strong>System Error Log:</strong>
            <ul style="margin-top: 5px; margin-bottom: 0;">
                @if(session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER SUMMARY STATUS --}}
    <div class="reject-summary-wrapper mb-4">
        <div class="reject-summary-container">
            <span class="summary-main-label">QPR No:</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2" style="background: #f8f9fa;">Automatic</span>
            </div>
        </div>
        <div class="reject-summary-container">
            <span class="summary-main-label">Input Date:</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2">{{ date('d/m/Y') }}</span>
            </div>
        </div>
        <div class="reject-summary-container border-danger-custom">
            <span class="summary-main-label text-danger">Status:</span>
            <div class="summary-sub-group">
                <span class="font-weight-bold text-danger">DRAFT</span>
            </div>
        </div>
    </div>

    <form action="{{ route('report.qpr.store') }}" method="POST" id="formQPR" enctype="multipart/form-data">
        @csrf
        
        {{-- CARD CONTAINER FORM UTAMA --}}
        <div class="form-section">
            {{-- 🔥 BARIS 1: ALUR DIBALIK, TANGGAL DULU BARU ITEM --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">Production Date <span style="color: red;">*</span></label>
                    <input type="date" name="TanggalProduksi" id="tgl_produksi" class="form-control input-custom" onchange="fetchItemsByDate(this.value)" required>
                </div>
                <div class="form-group">
                    <label class="label-custom">Item / Job Number <span style="color: red;">*</span></label>
                    <select name="IdInputHarian" id="IdInputHarian" class="form-select input-custom select2-searchable" onchange="getJobDetail(this)" required disabled>
                        <option value="" selected>- Select Date First -</option>
                    </select>
                </div>
            </div>

            {{-- BARIS 2: JOB NUMBER, PART NAME, MODEL --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">Job Number</label>
                    <input type="text" id="job_number_display" class="form-control input-custom custom-readonly" readonly placeholder="Automatic">
                </div>
                <div class="form-group">
                    <label class="label-custom">Part Name</label>
                    <input type="text" id="nama_part" class="form-control input-custom custom-readonly" readonly placeholder="Automatic">
                </div>
                <div class="form-group">
                    <label class="label-custom">Model</label>
                    <input type="text" id="model_part" class="form-control input-custom custom-readonly" readonly placeholder="Automatic">
                </div>
            </div>

            {{-- 🔥 BARIS 3: NO QPR, REPAIR COUNTER, REJECT COUNTER (BISA DIEDIT) --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">QPR Number</label>
                    <input type="text" class="form-control input-custom custom-readonly" readonly placeholder="Automatic">
                </div>
                <div class="form-group">
                    <label class="label-custom">Repair (A | B)</label>
                    <div style="display: flex; border: 1.5px solid #343a40; border-radius: 10px; overflow: hidden; height: 42px;">
                        <input type="number" step="0.01" name="RepairA" id="qpr_repair_a" style="width: 50%; border: none; border-right: 1.5px solid #343a40; text-align: center; outline: none;" placeholder="0">
                        <input type="number" step="0.01" name="RepairB" id="qpr_repair_b" style="width: 50%; border: none; text-align: center; outline: none;" placeholder="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-custom">Reject (A | B)</label>
                    <div style="display: flex; border: 1.5px solid #343a40; border-radius: 10px; overflow: hidden; height: 42px;">
                        <input type="number" step="0.01" name="RejectA" id="qpr_reject_a" style="width: 50%; border: none; border-right: 1.5px solid #343a40; text-align: center; outline: none;" placeholder="0">
                        <input type="number" step="0.01" name="RejectB" id="qpr_reject_b" style="width: 50%; border: none; text-align: center; outline: none;" placeholder="0">
                    </div>
                </div>
            </div>

            {{-- BARIS 4: STOCK IPPI, PRODUCTION PLAN, REPAIR PROCESS --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">Stock IPPI (Pcs) <span style="color: red;">*</span></label>
                    <input type="number" name="Stok" class="form-control input-custom" value="{{ old('Stok', 0) }}" min="0" style="text-align: center;">
                </div>
                <div class="form-group">
                    <label class="label-custom">Production Plan <span style="color: red;">*</span></label>
                    <input type="date" name="RencanaProduksi" class="form-control input-custom" value="{{ old('RencanaProduksi') }}">
                </div>
                <div class="form-group">
                    <label class="label-custom">Repair Process <span style="color: red;">*</span></label>
                    <select name="ProsesRepair" class="form-select input-custom">
                        <option value="">- Select Process -</option>
                        @foreach(['DF', 'OP-10', 'OP-20', 'OP-30', 'OP-40', 'Palleting'] as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- BARIS 5: LOKASI KEJADIAN, REF DOC, SHIFT --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="label-custom">Problem Location <span style="color: red;">*</span></label>
                    <select name="LokasiKejadian" id="lokasi_kejadian" class="form-select input-custom" required>
                        <option value="">- Select Location -</option>
                        @foreach(['OP-10', 'OP-20', 'OP-30', 'OP-40', 'Lain-lain'] as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="label-custom">Reference Document</label>
                    <input type="text" name="DocReferensi" class="form-control input-custom" value="{{ old('DocReferensi') }}">
                </div>
                <div class="form-group">
                    <label class="label-custom">Shift</label>
                    <input type="text" id="shift_val" name="Shift" class="form-control input-custom custom-readonly" readonly placeholder="Automatic">
                </div>
            </div>

            {{-- BARIS 6: TIME SELECTION --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="label-custom">Time <span style="color: red;">*</span></label>
                    <input type="time" name="Jam" class="form-control input-custom" value="{{ old('Jam', date('H:i')) }}" style="text-align: center;">
                </div>
            </div>

            {{-- SKETCH CONTAINER --}}
            <div class="text-center mb-2" style="border-top: 2px solid #f8f9fa; padding-top: 25px; margin-top: 25px;">
                <div id="sketch-container">
                    <div style="max-width: 450px; margin: 0 auto; padding: 25px; border: 2px dashed #343a40; border-radius: 12px; background: #fafafa;">
                        <p style="color: #888; font-size: 12px; font-weight: bold; margin: 0; text-transform: uppercase;">Select Job Number to view visual sketch</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- PARTIALS MASALAH --}}
        <div id="masalah-qpr-wrapper" class="mb-4">
            @include('Produksi.report.qpr.partials.masalah_qpr_row', ['existingDetails' => collect()])
        </div>

        {{-- PARTIALS VERIFIKASI --}}
        <div id="verifikasi-qpr-wrapper" class="mb-4">
            @include('Produksi.report.qpr.partials.verifikasi_qpr_row', ['displayVerifikasi' => collect()])
        </div>

        {{-- FORMACTION BUTTONS --}}
        <div class="btn-action-container">
            <button type="button" class="btn-save-qpr" onclick="confirmSaveQPR()">
                Save
            </button>
            <a href="{{ route('report.qpr.index') }}" class="btn-cancel-qpr">Cancel</a>
        </div>
    </form>
</div>

<script>
    var masalahIndex = 0;
    var verifikasiIndex = 0;

    $(document).ready(function() {
        // 3. JURUS "SCROLL KUNCI": 
        // Biar pas nge-scroll dropdown, halaman gak ikut gerak
        $(document).on('mousewheel DOMMouseScroll', '.select2-results__options', function(e) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            
            this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
            e.preventDefault();
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        masalahIndex = document.querySelectorAll('.masalah-item-card').length;
        verifikasiIndex = document.querySelectorAll('.verifikasi-item-card').length;

        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-searchable').select2({
                placeholder: '- Select Item -',
                allowClear: true,
                width: '100%'
            });

            // Trigger fetch data Job Detail pas item dipilih dari dropdown
            $('#IdInputHarian').on('select2:select', function (e) {
                getJobDetail(this);
            });
        }
    });

    // 🔥 SCRIPT BARU: Ngambil Item berdasarkan Tanggal
    function fetchItemsByDate(tanggal) {
        const selectItem = $('#IdInputHarian');
        
        selectItem.empty().append('<option value="">Loading...</option>').prop('disabled', true);
        
        document.getElementById('job_number_display').value = '';
        document.getElementById('nama_part').value = '';
        document.getElementById('model_part').value = '';
        document.getElementById('shift_val').value = '';
        document.getElementById('qpr_repair_a').value = '';
        document.getElementById('qpr_repair_b').value = '';
        document.getElementById('qpr_reject_a').value = '';
        document.getElementById('qpr_reject_b').value = '';
        document.getElementById('sketch-container').innerHTML = '';

        if (!tanggal) return;

        fetch(`/report/qpr/get-items-by-date?tanggal=${tanggal}`)
            .then(res => res.json())
            .then(data => {
                selectItem.empty().append('<option value="" selected>- Select Item -</option>');
                
                if (data.length > 0) {
                    data.forEach(item => {
                        const jobNumber = item.item && item.item.JobNumber ? item.item.JobNumber : 'N/A';
                        const namaPart = item.item && item.item.NamaPart ? item.item.NamaPart : '-';
                        selectItem.append(new Option(`${jobNumber} | ${namaPart}`, item.IdInputHarian));
                    });
                    selectItem.prop('disabled', false); 
                } else {
                    selectItem.empty().append('<option value="">- No Items Found on this Date -</option>');
                }
            })
            .catch(err => {
                console.error("Error fetching items:", err);
                selectItem.empty().append('<option value="">- Error Loading Items -</option>');
            });
    }

    function addMasalahRow() {
        const container = document.getElementById('masalah-container');
        if(!container) return;
        
        fetch("/report/qpr/add-masalah-row/" + masalahIndex)
            .then(res => res.text())
            .then(html => {
                container.insertAdjacentHTML('beforeend', html);
                masalahIndex++;
            });
    }

    function addVerifikasiRow() {
        const container = document.getElementById('verifikasi-container');
        if(!container) return;

        fetch("/report/qpr/add-verifikasi-row/" + verifikasiIndex)
            .then(res => res.text())
            .then(html => {
                container.insertAdjacentHTML('beforeend', html);
                verifikasiIndex++;
            });
    }

    function removeMasalahRow(btn) {
        if(document.querySelectorAll('.masalah-item-card').length > 1) {
            btn.closest('.masalah-item-card').remove();
        }
    }

    function removeVerifikasiRow(btn) {
        if(document.querySelectorAll('.verifikasi-item-card').length > 1) {
            btn.closest('.verifikasi-item-card').remove();
        }
    }

    function getJobDetail(selectElement) {
        const id = selectElement.value;
        const sketchContainer = document.getElementById('sketch-container');
        if (!id) return;

        fetch(`/report/qpr/get-job-detail/${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Tampilkan Job Number ke input readonly (diambil dari text select option)
                const selectedText = selectElement.options[selectElement.selectedIndex].text;
                const jobNumber = selectedText.split(' | ')[0].trim();
                document.getElementById('job_number_display').value = jobNumber !== '- Select Item -' ? jobNumber : '';

                document.getElementById('nama_part').value = data.NamaPart || '';
                document.getElementById('model_part').value = data.Model || '';
                
                const shiftInput = document.getElementById('shift_val');
                if (shiftInput) shiftInput.value = data.Shift || '-';
                
                document.getElementById('qpr_repair_a').value = data.RepairA || 0;
                document.getElementById('qpr_repair_b').value = data.RepairB || 0;
                document.getElementById('qpr_reject_a').value = data.RejectA || 0;
                document.getElementById('qpr_reject_b').value = data.RejectB || 0;

                if (data.Gambar) {
                    sketchContainer.innerHTML = `<img src="/storage/${data.Gambar}" style="max-height: 250px; border-radius: 8px; border: 1px solid #ddd;">`;
                } else {
                    sketchContainer.innerHTML = `<div style="max-width: 400px; margin: 0 auto; padding: 30px; border: 2px dashed #dcdde1; border-radius: 12px; background: #fafafa;">
                                                    <p style="color: #636e72; font-size: 12px; font-weight: 500;">Tidak ada Sketch</p>
                                                </div>`;
                }
            })
            .catch(error => {
                console.error('Oops! Something went wrong:', error);
            });
    }

    // 4. Handle Konfirmasi Save QPR
    function confirmSaveQPR() {
        Swal.fire({
            title: 'Simpan Data QPR?',
            text: "Pastikan Semua Data Yang Dimasukkan Sudah Benar.",
            icon: 'question', 
            showCancelButton: true,
            confirmButtonColor: '#4361ee', // Biru
            cancelButtonColor: '#6c757d',  // Abu-abu gelap
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formQPR');
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
                    // Batasi maksimal 7 list error yang tampil biar pop-up nya gak kepanjangan ngelewatin layar
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
                        title: 'Data Belum Lengkap',
                        html: listHtml,
                        confirmButtonColor: '#e11d2e', // Merah Astra
                        confirmButtonText: 'OK'
                    });
                } 
                // --- 5. Lolos Uji, Submit Form! ---
                else {
                    form.submit();
                }
            }
        });
    }
</script>
@endsection