@extends('Produksi.layouts.main')

@section('title', 'Detail Reject Produksi')
@section('page-title', 'Detail Reject')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* CSS SAMA PERSIS DENGAN DETAIL REPAIR */
    .reject-summary-wrapper { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-bottom: 25px; }
    .reject-summary-container { display: flex; flex-direction: column; align-items: center; padding: 10px 15px; border: 1px solid #dee2e6; border-radius: 10px; background: #f8f9fa; min-width: 200px; }
    .summary-sub-group { display: flex; gap: 8px; align-items: center; margin-top: 8px; }
    .reject-summary-box-v2 { padding: 4px 12px; border: 1px solid #ced4da; border-radius: 6px; font-weight: 700; }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Daily Input</span> <span class="separator">></span>
    <span class="active">Detail Reject</span>
</div>

<div class="page-container">
    <h5 class="page-title mb-3">Detail Item - ({{ $input->item->JobNumber ?? '-' }})</h5>

    {{-- HEADER SUMMARY (LAYOUT SAMA PERSIS DENGAN REPAIR) --}}
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

        <div class="reject-summary-container" style="border: 1px solid #e11d2e;">
            <span class="summary-main-label text-danger" style="font-size: 12px; font-weight: 700;">Reject :</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2" style="border-color: #e11d2e; color: #e11d2e;">{{ number_format($input->RejectA ?? 0) }}</span>
                <span class="reject-summary-box-v2" style="border-color: #e11d2e; color: #e11d2e;">{{ number_format($input->RejectB ?? 0) }}</span>
                <span class="font-weight-bold text-danger" style="font-size: 12px;">Pcs</span>
            </div>
        </div>
    </div>

    {{-- SKETCH POSISI MASALAH --}}
    <div class="text-center" style="margin-bottom: 25px;">
        <p class="font-weight-bold mb-1" style="font-size: 12px; text-transform: uppercase;">Sketch Posisi Masalah</p>
        
        @if($input->item && $input->item->Gambar)
            <img src="{{ asset('storage/' . $input->item->Gambar) }}" alt="Sketch" 
                 style="max-width: 500px; width: 100%; border-radius: 10px; border: 2px solid #343a40;"
                 onerror="this.onerror=null; this.outerHTML='<div style=\'max-width: 500px; margin: 0 auto; padding: 40px; border: 2px dashed #b2bec3; border-radius: 12px; background: #f8f9fa;\'><p style=\'color: #636e72; font-size: 13px; font-weight: 700;\'>Gambar Sketch Gagal Dimuat</p></div>';">
        @else
            <div style="max-width: 500px; margin: 0 auto; padding: 40px; border: 2px dashed #b2bec3; border-radius: 12px; background: #f8f9fa;">
                <p style="color: #636e72; font-size: 13px; font-weight: 700;">Gambar Sketch Tidak Tersedia</p>
            </div>
        @endif
    </div>

    <form action="{{ route('inputharian.reject.store', $input->IdInputHarian) }}" method="POST" id="formReject">
        @csrf
        <input type="hidden" name="date" value="{{ request('date', $input->TanggalProduksi) }}">
        <input type="hidden" name="line" value="{{ request('line') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">

        <div id="reject-container">
            @forelse($details as $index => $det)
                @include('Produksi.inputharian.partials.partial_reject_row', ['index' => $index, 'detail' => $det])
            @empty
                @include('Produksi.inputharian.partials.partial_reject_row', ['index' => 0, 'detail' => null])
            @endforelse
        </div>

        <button type="button" class="btn btn-secondary btn-sm px-4" onclick="addDetail()" style="border-radius: 10px; font-weight: 700;">Add Row</button>
        
        <div class="form-actions" style="margin-top: 32px; display: flex; gap: 12px; padding-bottom: 50px;">
            <button type="button" class="btn btn-primary" onclick="confirmSave()">Save</button>
            <a href="{{ route('inputharian.index', ['date' => request('date', $input->TanggalProduksi), 'line' => request('line'), 'search' => request('search')]) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<template id="reject-row-template">
    @include('Produksi.inputharian.partials.partial_reject_row', ['index' => 'REPLACE_INDEX', 'detail' => null])
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        applyQtyBLogic();
        updateRowNumbers();
    });

    function updateRowNumbers() {
        document.querySelectorAll('.row-number').forEach((span, i) => { span.innerText = i + 1; });
    }

    function addDetail() {
        const container = document.getElementById('reject-container');
        const template = document.getElementById('reject-row-template').innerHTML;
        const newIndex = document.querySelectorAll('.reject-item-card').length;
        container.insertAdjacentHTML('beforeend', template.replace(/REPLACE_INDEX/g, newIndex));
        updateRowNumbers();
        applyQtyBLogic();
    }

    function removeDetail(btn) {
        if (document.querySelectorAll('.reject-item-card').length > 1) {
            btn.closest('.reject-row-container').remove();
            updateRowNumbers();
        } else {
            Swal.fire({ icon: 'error', text: 'Minimal satu baris!' });
        }
    }

    function confirmSave() {
        // 1. MUNCULIN POP-UP KONFIRMASI DULU
        Swal.fire({ 
            title: 'Simpan Data Reject?',
            text: "Mohon pastikan seluruh data yang dimasukkan sudah benar dan sesuai.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6', // Warna biru seragam
            cancelButtonColor: '#6c757d',  // Warna abu-abu gelap
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                try {
                    const form = document.getElementById('formReject');
                    let errorList = [];
                    let hasValidRow = false; // Penanda untuk ngecek ada baris yang diisi

                    // --- 2. Ambil baris reject yang TAMPIL (Abaikan template) ---
                    const rejectContainer = document.getElementById('reject-container');
                    const rejectRows = rejectContainer.querySelectorAll('.reject-row-container');

                    if (rejectRows.length === 0) {
                        errorList.push("Minimal harus ada 1 baris input reject.");
                    }

                    // --- 3. Looping per Card Baris ---
                    rejectRows.forEach((row, index) => {
                        let no = index + 1;
                        
                        let idRejectEl = row.querySelector('[name^="IdReject"]');
                        let qtyAEl = row.querySelector('[name^="QtyRejectA"]');
                        let areaProblemEl = row.querySelector('[name^="AreaProblem"]');
                        let namaKerusakanEl = row.querySelector('.select-nama-kerusakan'); 
                        let jenisLainEl = row.querySelector('[name^="JenisLain"]');
                        let namaLainEl = row.querySelector('[name^="NamaLain"]');

                        let valId = idRejectEl ? idRejectEl.value : '';
                        let qtyA = qtyAEl ? parseFloat(qtyAEl.value) : 0;

                        // --- 4. LOGIKA PENGECEKAN KETAT ---
                        if (valId) {
                            hasValidRow = true;

                            if (!qtyA || qtyA <= 0) {
                                errorList.push(`Baris #${no}: QTY (A) minimal harus 1.`);
                            }

                            let areaProblem = areaProblemEl ? areaProblemEl.value : '';
                            if (!areaProblem) {
                                errorList.push(`Baris #${no}: Area Problem wajib dipilih.`);
                            }

                            // Cek opsi "Lain-lain" di Jenis Reject
                            if (valId === 'Lain-lain') {
                                let jenisLain = jenisLainEl ? jenisLainEl.value : '';
                                if (!jenisLain) {
                                    errorList.push(`Baris #${no}: Teks Jenis Reject (Lainnya...) wajib diisi.`);
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
                            // Kalau Jenis Reject kosong, tapi dia ngisi QTY A
                            if (qtyA > 0) {
                                errorList.push(`Baris #${no}: Jenis Reject wajib dipilih karena QTY sudah diisi.`);
                            }
                        }
                    });

                    // --- 5. CEK FORM KOSONG MELOMPONG ---
                    if (!hasValidRow) {
                        errorList.push("Minimal wajib mengisi 1 baris data reject secara lengkap (Jenis Reject & QTY).");
                    }

                    // --- 6. TAMPILIN ERROR JIKA ADA ---
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
                    // --- 7. LOLOS UJI, SUBMIT! ---
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

    function applyQtyBLogic() {
        const hasSlash = "{{ $input->item->JobNumber ?? '' }}".includes('/');
        document.querySelectorAll('input[name="QtyRejectB[]"]').forEach(input => {
            if (!hasSlash) { input.disabled = true; input.style.backgroundColor = "#f1f1f1"; input.value = ""; }
        });
    }

    function checkLainLain(select) {
        const inputExtra = select.nextElementSibling;
        if (select.value === 'Lain-lain') {
            inputExtra.disabled = false; inputExtra.style.backgroundColor = "#fff"; inputExtra.focus();
        } else {
            inputExtra.disabled = true; inputExtra.style.backgroundColor = "#f1f1f1"; inputExtra.value = "";
        }
    }

    const mappingPenyebab = {
        'CRACK': 'Tarikan Part Terlalu Kuat Dan Melebihi Batas Maksimal Elastisitas Material',
        'NECK': 'Penipisan Material yang Melebihi Batas Maksimal akibat Tarikan Part Terlalu Kuat',
        'GELOMBANG OVER': 'Tarikan Part loss dan Lekukan Gelombang lebih dari 3 alur',
        'KELIPET': 'Tarikan Part Loss Dan Terlipat pada Area Profil',
        'BARET OVER': 'Surface Atau Permukaan Dies pada Area Upper atau Lower Dies Kasar Sehingga Menimbulkan garis Gesekan Yang sudah tembus Hingga Bagian Dalam Panel',
        'BENJOL OVER': 'Tarikan Part Tertahan dan Terakumulasi pada Satu Area Saat proses Pembentukan Part Draw',
        'TWIST': 'Profil Part melintir Akibat terjatuh, Handling Tidak Sesuai atau Tertindih',
        'HOLE VARIAN NG': 'Hole Varian Tidak Ada, Kurang Atau Tidak Sesuai Sample Part',
        'KARAT OVER': 'Material Storage Terkontaminasi Air atau Uap Air',
        'PENYOK/DEFORM': 'Handling Part Terjatuh Dari Handling Robot /Vacum Miss',
        'MATERIAL NG': 'Specifikasi Material Yang Digunakan Salah/tidak Sesuai',
        'MINUS OVER': 'Tarikan Part Draw Tidak Konstan, Penyok Material Yang Terproses Dan Mocel Atau Penempatan Material Tidak Fix',
        'BALANCING PROCESS': 'Reject Part Separating Yang Ditemukan Sebelum Proses Finish',
        'REJECT TRIAL': 'Reject EX Trial dan Ex WIP Repair Dies',
        'MARKING NG': 'Bottom Mark, Initial ID, Embos Special Tidak Ada Atau Tidak Sesuai Sample Part',
        'DOUBLE LINE (PROFIL)': 'Proses Penempatan Part Pada Proses Restrike/Bending Un-match',
        'MATERIAL MENUMPANG': 'Posisi Material Menumpang Di Stopper Dies Draw Dan Terproses'
    };

    function autoFillPenyebab(element) {
        const container = element.closest('.card-body');
        const textarea = container.querySelector('.textarea-penyebab');
        textarea.value = mappingPenyebab[element.value] || "";
    }
</script>
@endsection