@extends('Produksi.layouts.main')

@section('title', 'Detail Repair Produksi')
@section('page-title', 'Detail Repair')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* CSS TAMBAHAN AGAR SUMMARY TIDAK BERANTAKAN SAAT SPLIT */
    .reject-summary-wrapper { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-bottom: 25px; }
    .reject-summary-container { display: flex; flex-direction: column; align-items: center; padding: 10px 15px; border: 1px solid #dee2e6; border-radius: 10px; background: #f8f9fa; min-width: 200px; }
    .summary-sub-group { display: flex; gap: 8px; align-items: center; margin-top: 8px; }
    .reject-summary-box-v2 { padding: 4px 12px; border: 1px solid #ced4da; border-radius: 6px; font-weight: 700; }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Daily Input</span> <span class="separator">></span>
    <span class="active">Detail Repair</span>
</div>

<div class="page-container">
    <h5 class="page-title mb-3">Detail Item Repair - ({{ $input->item->JobNumber ?? '-' }})</h5>

    {{-- HEADER SUMMARY --}}
    <div class="reject-summary-wrapper">
        <div class="reject-summary-container">
            <span class="summary-main-label" style="font-size: 12px; font-weight: 700;">Plan QTY :</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2">{{ number_format($input->PlanQtyA ?? 0) }}</span>
                <span class="reject-summary-box-v2">{{ number_format($input->PlanQtyB ?? 0) }}</span>
                <span class="font-weight-bold" style="font-size: 12px;">Pcs</span>
            </div>
        </div>

        <div class="reject-summary-container">
            <span class="summary-main-label" style="font-size: 12px; font-weight: 700;">Actual QTY :</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2">{{ number_format($input->GoodA ?? 0) }}</span>
                <span class="reject-summary-box-v2">{{ number_format($input->GoodB ?? 0) }}</span>
                <span class="font-weight-bold" style="font-size: 12px;">Pcs</span>
            </div>
        </div>

        <div class="reject-summary-container" style="border: 1px solid #3085d6;">
            <span class="summary-main-label text-primary" style="font-size: 12px; font-weight: 700;">Repair :</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2" style="border-color: #3085d6; color: #3085d6;">{{ number_format($input->RepairA ?? 0) }}</span>
                <span class="reject-summary-box-v2" style="border-color: #3085d6; color: #3085d6;">{{ number_format($input->RepairB ?? 0) }}</span>
                <span class="font-weight-bold text-primary" style="font-size: 12px;">Pcs</span>
            </div>
        </div>
    </div>

    {{-- SKETCH POSISI MASALAH --}}
    <div class="text-center" style="margin-bottom: 25px;">
        <p class="font-weight-bold mb-1" style="font-size: 12px; text-transform: uppercase;">Sketch Posisi Masalah</p>
        @if($input->item && $input->item->Gambar)
            <img src="{{ asset('storage/' . $input->item->Gambar) }}" 
                alt="Sketch {{ $input->item->JobNumber }}" 
                style="max-width: 550px; width: 100%; height: auto; border: 2.5px solid #343a40; border-radius: 12px; padding: 5px; background: #fff;"
                onerror="this.onerror=null;this.src='{{ asset('assets/img/no-image.png') }}';">
        @else
            <div style="max-width: 550px; margin: 0 auto; padding: 40px; border: 2px dashed #b2bec3; border-radius: 12px; background: #f8f9fa;">
                <p style="color: #636e72; font-size: 13px; font-weight: 700;">Gambar Sketch Tidak Tersedia</p>
            </div>
        @endif
    </div>

    <form action="{{ route('inputharian.repair.store', $input->IdInputHarian) }}" method="POST" id="formRepair">
        @csrf

        {{-- ✅ HIDDEN INPUT UNTUK MEMBAWA FILTER SAAT SAVE --}}
        <input type="hidden" name="date" value="{{ request('date', $input->TanggalProduksi) }}">
        <input type="hidden" name="line" value="{{ request('line') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">

        <div id="repair-container">
            @forelse($details as $index => $det)
                @include('Produksi.inputharian.partials.partial_repair_row', ['index' => $index, 'detail' => $det])
            @empty
                @include('Produksi.inputharian.partials.partial_repair_row', ['index' => 0, 'detail' => null])
            @endforelse
        </div>

        {{-- TOMBOL TAMBAH DETAIL --}}
        <div style="margin-top: 15px;">
            <button type="button" class="btn btn-secondary btn-sm font-weight-bold px-4" 
                    style="border-radius: 10px;" onclick="addDetailRepair()">
                Add Row
            </button>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="form-actions" style="margin-top: 32px; display: flex; gap: 12px; padding-bottom: 50px;">
            <button type="button" class="btn btn-primary" onclick="confirmSaveRepair()">Save</button>
            
            {{-- ✅ TOMBOL CANCEL DENGAN PARAMETER FILTER --}}
            <a href="{{ route('inputharian.index', [
                'date'   => request('date', $input->TanggalProduksi), 
                'line'   => request('line'), 
                'search' => request('search')
            ]) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<template id="repair-row-template">
    @include('Produksi.inputharian.partials.partial_repair_row', ['index' => 'REPLACE_INDEX', 'detail' => null])
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        applyQtyBLogic();
        updateRowNumbersRepair();
    });

    function updateRowNumbersRepair() {
        document.querySelectorAll('.row-number-repair').forEach((span, i) => {
            span.innerText = i + 1;
        });
    }

    function addDetailRepair() {
        const container = document.getElementById('repair-container');
        const template = document.getElementById('repair-row-template').innerHTML;
        const newIndex = document.querySelectorAll('.repair-item-card').length;
        const newRow = template.replace(/REPLACE_INDEX/g, newIndex); 
        
        const div = document.createElement('div');
        div.innerHTML = newRow;
        container.appendChild(div.firstElementChild);
        
        updateRowNumbersRepair();
        applyQtyBLogic();
    }

    function removeDetailRepair(btn) {
        const cards = document.querySelectorAll('.repair-item-card');
        if (cards.length > 1) {
            btn.closest('.repair-row-container').remove(); 
            updateRowNumbersRepair();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'There must be at least one line of input!' });
        }
    }

    function applyQtyBLogic() {
        const jobNumber = "{{ $input->item->JobNumber ?? '' }}";
        const hasSlash = jobNumber.includes('/');
        document.querySelectorAll('input[name="QtyRepairB[]"]').forEach(input => {
            if (!hasSlash) {
                input.disabled = true;
                input.style.backgroundColor = "#f1f1f1";
                input.value = ""; 
                input.placeholder = "N/A";
            }
        });
    }

    function checkLainLainRepair(select) {
        const inputExtra = select.nextElementSibling; 
        if (!inputExtra) return;
        if (select.value === 'Lain-lain' || select.value === 'RP-LAIN') {
            inputExtra.disabled = false;
            inputExtra.style.backgroundColor = "#ffffff";
            inputExtra.required = true;
            inputExtra.focus();
        } else {
            inputExtra.disabled = true;
            inputExtra.style.backgroundColor = "#f1f1f1";
            inputExtra.required = false;
            inputExtra.value = "";
        }
    }

    function confirmSaveRepair() {
        // MUNCULIN POP-UP KONFIRMASI DULU
        Swal.fire({
            title: 'Simpan Data Repair?',
            text: "Mohon pastikan seluruh data yang dimasukkan sudah benar dan sesuai.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6', 
            cancelButtonColor: '#6c757d',  
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                try {
                    const form = document.getElementById('formRepair');
                    let errorList = [];
                    let hasValidRow = false; // Penanda untuk ngecek ada baris yang beneran diisi atau nggak

                    // --- 1. Ambil spesifik baris repair yang lagi TAMPIL (Abaikan yang di dalam tag <template>) ---
                    const repairContainer = document.getElementById('repair-container');
                    const repairRows = repairContainer.querySelectorAll('.repair-row-container');

                    if (repairRows.length === 0) {
                        errorList.push("Minimal harus ada 1 baris input repair.");
                    }

                    // --- 2. Looping per komponen Card Baris ---
                    repairRows.forEach((row, index) => {
                        let no = index + 1;
                        
                        // Cari elemen input cuma di dalam card baris ini aja biar gak ketuker
                        let idRepairEl = row.querySelector('[name^="IdRepair"]');
                        let qtyAEl = row.querySelector('[name^="QtyRepairA"]');
                        let areaProblemEl = row.querySelector('[name^="AreaProblem"]');
                        let namaKerusakanEl = row.querySelector('.select-nama-repair'); 
                        let repairLainEl = row.querySelector('[name^="RepairLain"]');
                        let namaLainEl = row.querySelector('[name^="NamaLain"]');

                        let valId = idRepairEl ? idRepairEl.value : '';
                        let qtyA = qtyAEl ? parseFloat(qtyAEl.value) : 0;

                        // --- 3. LOGIKA PENGECEKAN KETAT ---
                        if (valId) {
                            hasValidRow = true; // Tandain kalau user minimal ngisi 1 baris

                            if (!qtyA || qtyA <= 0) {
                                errorList.push(`Baris #${no}: QTY (A) minimal harus 1.`);
                            }

                            let areaProblem = areaProblemEl ? areaProblemEl.value : '';
                            if (!areaProblem) {
                                errorList.push(`Baris #${no}: Area Problem wajib dipilih.`);
                            }

                            // Cek jika milih opsi "Lain-lain" di Jenis Repair
                            if (valId === 'RP-LAIN') {
                                let repairLain = repairLainEl ? repairLainEl.value : '';
                                if (!repairLain) {
                                    errorList.push(`Baris #${no}: Teks Jenis Repair (Lainnya...) wajib diisi.`);
                                }
                            }

                            // Cek Nama Kerusakan dan opsi "Lain-lain"
                            let nmKerusakan = namaKerusakanEl ? namaKerusakanEl.value : '';
                            if (!nmKerusakan) {
                                errorList.push(`Baris #${no}: Nama Kerusakan wajib dipilih.`);
                            } else if (nmKerusakan === 'Lain-lain') {
                                let namaLain = namaLainEl ? namaLainEl.value : '';
                                if (!namaLain) {
                                    errorList.push(`Baris #${no}: Teks Nama Kerusakan (Lainnya...) wajib diisi.`);
                                }
                            }
                        } else {
                            // Kalau Jenis Repair kosong, tapi dia iseng ngisi QTY A
                            if (qtyA > 0) {
                                errorList.push(`Baris #${no}: Jenis Repair wajib dipilih karena QTY sudah diisi.`);
                            }
                        }
                    });

                    // --- 4. CEK FORM KOSONG MELOMPONG ---
                    if (!hasValidRow) {
                        errorList.push("Minimal wajib mengisi 1 baris data repair secara lengkap (Jenis Repair & QTY).");
                    }

                    // --- 5. TAMPILIN ERROR JIKA ADA ---
                    if (errorList.length > 0) {
                        let displayErrors = errorList.slice(0, 5);
                        let moreCount = errorList.length - 5;
                        
                        let listHtml = `
                            <div style="text-align: left; font-size: 14px; color: #555; max-height: 300px; overflow-y: auto;">
                                <p style="margin-bottom: 10px;">Mohon perbaiki / lengkapi data berikut:</p>
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
                    // --- 6. LOLOS UJI, BARU BOLEH SUBMIT! ---
                    else {
                        form.querySelectorAll('input:disabled, select:disabled').forEach(input => {
                            input.disabled = false;
                        });
                        form.submit();
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Sistem',
                        text: 'Terjadi kesalahan sistem saat memproses form.',
                        confirmButtonColor: '#e11d2e'
                    });
                }
            }
        });
    }

    const mappingPenyebabRepair = {
        'BENJOL GOMIKAMI': 'Kotoran Atau Debu Menempel di Di Surface Upper Dies',
        'BENJOL KIRIKO': 'Serbuk Gerusan Trimming Atau Serpihan Tarikan Drawing Yang Terakumulasi Di Upper Dies',
        'PECOK GOMIKAMI': 'Kotoran Atau Debu Menempel di Di Surface Lower Dies',
        'PECOK KIRIKO': 'Serbuk Gerusan Trimming Atau Serpihan Tarikan Drawing Yang Terakumulasi Di Lower Dies',
        'BARET': 'Surface Atau Permukaan Dies pada Area Upper atau Lower Dies Kasar Sehingga Menimbulkan gesekan',
        'SHOCKLINE': 'Tarikan Part Tertahan dan Terakumulasi pada Satu Area Saat proses Pembentukan Part Draw',
        'GELOMBANG': 'Tarikan Part Loss Dan Tidak Balance antar Balancer',
        'MAKURE/MENCUAT': 'Pad Upper Minus Terhadap Bushing Lower',
        'FLEX/KARAT': 'Material Storage Terkontaminasi Air atau Uap Air',
        'PENYOK/DEFORM': 'Handling Part Terjatuh Dari Handling Robot /Vacum Miss',
        'BURRY': 'Lower Line Trimming ,Bushing Hole Lower Dies Minus/Gompal',
        'MINUS': 'Tarikan Part Draw Tidak Konstan ,Atau Penempatan Material Tidak Fix'
    };

    function autoFillPenyebabRepair(element) {
        const parentDiv = element.closest('.form-group');
        const inputLain = parentDiv.querySelector('input[name="NamaLain[]"]');
        
        if (element.value === 'Lain-lain') {
            inputLain.disabled = false;
            inputLain.style.backgroundColor = "#ffffff";
            inputLain.focus();
        } else {
            inputLain.disabled = true;
            inputLain.style.backgroundColor = "#f1f1f1";
            inputLain.value = ""; 
        }

        const cardBody = element.closest('.card-body');
        const textarea = cardBody.querySelector('.textarea-penyebab-repair');
        
        if (element.value !== 'Lain-lain') {
            textarea.value = mappingPenyebabRepair[element.value] || "";
        } else {
            textarea.value = ""; 
        }
    }
</script>
@endsection