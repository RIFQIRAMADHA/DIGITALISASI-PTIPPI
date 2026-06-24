@extends('Produksi.layouts.main')

@section('title', 'Detail Downtime Produksi')
@section('page-title', 'Detail Downtime')

@section('content')

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Daily Input</span> <span class="separator">></span>
    <span class="active">Monitoring Rekonsiliasi Downtime</span>
</div>

<div class="page-container">

    {{-- DASHBOARD MONITORING DOWNTIME --}}
    <div style="position: sticky; top: 10px; z-index: 1000; margin-bottom: 25px;">
        <div class="content-card shadow" style="border: 2px solid #2d3436; border-radius: 12px; background: #fff; padding: 20px;">
            {{-- TAMBAH FLEX-WRAP DI SINI BIAR BISA TURUN KE BAWAH PAS DI-SPLIT --}}
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
                
                {{-- TAMBAH FLEX-WRAP JUGA DI GROUP ITEM INI --}}
                <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: center; flex: 1; min-width: 300px;">
                    <div style="border-right: 1px solid #dfe6e9; padding-right: 20px; min-width: 80px;">
                        <span style="font-size: 11px; font-weight: 800; color: #636e72; text-transform: uppercase;">UBP</span>
                        <div style="margin-top: 5px;">
                            <span style="font-size: 22px; font-weight: 900; color: #2d3436;">{{ number_format($ubp, 1) }}</span>
                            <span style="font-size: 14px; font-weight: 700; color: #2d3436;"> menit</span>
                        </div>
                    </div>
                    <div style="border-right: 1px solid #dfe6e9; padding-right: 20px; min-width: 80px;">
                        <span style="font-size: 11px; font-weight: 800; color: #636e72; text-transform: uppercase;">DTR</span>
                        <div style="margin-top: 5px;">
                            <span style="font-size: 22px; font-weight: 900; color: #2d3436;">{{ number_format($dtr, 1) }}</span>
                            <span style="font-size: 14px; font-weight: 700; color: #2d3436;"> menit</span>
                        </div>
                    </div>
                    <div style="border-right: 1px solid #dfe6e9; padding-right: 20px; min-width: 100px;">
                        <span style="font-size: 11px; font-weight: 800; color: #636e72; text-transform: uppercase;">Downtime</span>
                        <div style="margin-top: 5px;">
                            <span style="font-size: 22px; font-weight: 900; color: #2d3436;">{{ number_format($downtimeRaw, 1) }}</span>
                            <span style="font-size: 14px; font-weight: 700; color: #2d3436;"> menit</span>
                        </div>
                    </div>
                    <div style="min-width: 100px;">
                        <span style="font-size: 11px; font-weight: 800; color: #e11d2e; text-transform: uppercase;">Sisa Downtime</span>
                        <div style="margin-top: 5px;">
                            <span id="sisa-downtime-val" style="font-size: 22px; font-weight: 900; color: #e11d2e;">{{ number_format($sisaDowntime, 1) }}</span>
                            <span style="font-size: 14px; font-weight: 700; color: #e11d2e;"> menit</span>
                        </div>
                    </div>
                </div>

                {{-- KOTAK TOTAL LOSE TIME --}}
                <div style="text-align: center; background: #f1f2f6; padding: 15px 30px; border-radius: 12px; border: 1px solid #2d3436; min-width: 200px; flex: 1; max-width: max-content;">
                    <span style="font-size: 12px; font-weight: 800; color: #2d3436; text-transform: uppercase; display: block;">Total Lose Time</span>
                    <div style="margin-top: 5px;">
                        <span id="total-lose-time" style="font-size: 28px; font-weight: 900; color: #2d3436;">{{ number_format($totalLoseTime, 1) }}</span>
                        <span style="font-size: 16px; font-weight: 800; color: #2d3436;"> menit</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <h5 class="page-title mb-3">Analisis Detail Downtime - ({{ $input->item->JobNumber ?? '-' }})</h5>

    <form action="{{ route('inputharian.downtime.store', $input->IdInputHarian) }}" method="POST" id="formDowntime">
        @csrf

        <input type="hidden" name="date" value="{{ request('date', $input->TanggalProduksi) }}">
        <input type="hidden" name="line" value="{{ request('line') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">

        <div id="downtime-container">
            @forelse($details as $index => $det)
                @include('Produksi.inputharian.partials.partial_downtime_row', ['index' => $index, 'detail' => $det])
            @empty
                @include('Produksi.inputharian.partials.partial_downtime_row', ['index' => 0, 'detail' => null])
            @endforelse
        </div>

        {{-- TOMBOL TAMBAH (POSISI DI BAWAH CONTAINER) --}}
        <div class="mt-3">
            <button type="button" class="btn btn-secondary btn-sm px-4" onclick="addDetailDT()" style="border-radius: 10px; font-weight: 700;">
                <i class="fas fa-plus"></i> Add Row
            </button>
        </div>

        {{-- TAMBAH FLEX-WRAP DI ACTION BUTTON BIAR AMAN JUGA --}}
        <div class="form-actions" style="margin-top: 32px; display: flex; flex-wrap: wrap; gap: 12px; padding-bottom: 50px;">
            <button type="button" class="btn btn-primary" onclick="confirmSaveDowntime()">Save</button>
            
            <a href="{{ route('inputharian.index', [
                'date'   => request('date', $input->TanggalProduksi), 
                'line'   => request('line'), 
                'search' => request('search')
            ]) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

{{-- TEMPLATE --}}
<template id="downtime-row-template">
    @include('Produksi.inputharian.partials.partial_downtime_row', ['index' => 'REPLACE_INDEX', 'detail' => null])
</template>

<script>
    // DATA MAPPING OP 10 - OP 40 (SUDAH DIKEMBALIKAN)
    const dataMapping = {
        "OP 10": { problems: ["Gelombang", "Neck", "Baret", "Pecok/Benjol", "Lain-lain"],
            akar: { "Gelombang": ["Pressure machine", "Clearance", "Tarikan Bido", "Pin Cushion", "Lain-lain"], "Neck": ["Clearance", "Surface", "Tarikan bido", "Pin Cushion", "Lain-lain"], "Baret": ["Surface kasar", "Re-Hardchrome", "Lain-lain"], "Pecok/Benjol": ["Surface Dies", "Material Kotor", "Lain-lain"], "Lain-lain": ["Lain-lain"] } },
        "OP 20": { problems: ["Scrap T/ Putus", "Trimming Burry", "Kiriko", "Lain-lain"],
            akar: { "Scrap T/ Putus": ["Scrap Cutter Aus", "Clearance Besar", "Penumpukan Scrap pada Cutter", "Perubahan Material & Clearnace", "Lain-lain"], "Trimming Burry": ["Triming Die Minus", "Clearance", "misalignment Tooling", "Tekanan Punch tidak Optimal", "Lain-lain"], "Kiriko": ["Clerance Punch", "Punch/Dies Aus", "Lain-lain"], "Lain-lain": ["Lain-lain"] } },
        "OP 30": { problems: ["Embosing", "piercing", "Re-Forming", "Lain-lain"],
            akar: { "Embosing": ["Setting Dies tidak Presisi", "Clearance tidak pas", "Radius Aus", "Spring back Tinggi (Embose Kurang)", "Lain-lain"], "piercing": ["Clearance Teralu kecil", "Punch Aus", "Punch patah (chipping)", "Slug Jammed (Tersumbat)", "Lain-lain"], "Re-Forming": ["Wringkling (Keriput)", "Cracking (Robek)", "Baret/Scratch", "Lain-lain"], "Lain-lain": ["Lain-lain"] } },
        "OP 40": { problems: ["Flanging", "Piercing", "Re-Forming", "Lain-lain"],
            akar: { "Flanging": ["Crack", "Wringkle", "Scrtach Flange", "Flange Dimensi Out", "Lain-lain"], "Piercing": ["Clearance Terlalu Kecil", "Punch Aus", "Punch patah (chipping)", "Slug Jammed (Tersumbat)", "Lain-lain"], "Re-Forming": ["Wringkling (Keriput)", "Cracking (Robek)", "Baret/Scratch", "Lain-lain"], "Lain-lain": ["Lain-lain"] } },
        "Lain-lain": { problems: ["Lain-lain"], akar: { "Lain-lain": ["Lain-lain"] } }
    };

    document.addEventListener('DOMContentLoaded', function() {
        updateLoseTimeMonitoring();
        updateRowNumbersDT();
    });

    function updateRowNumbersDT() {
        document.querySelectorAll('.row-number-dt').forEach((span, i) => { span.innerText = i + 1; });
    }

    function addDetailDT() {
        const container = document.getElementById('downtime-container');
        const template = document.getElementById('downtime-row-template').innerHTML;
        const newIndex = document.querySelectorAll('.downtime-item-card').length;
        const newRow = template.replace(/REPLACE_INDEX/g, newIndex); 
        const div = document.createElement('div');
        div.innerHTML = newRow;
        container.appendChild(div.firstElementChild);
        updateRowNumbersDT();
        updateLoseTimeMonitoring();
    }

    function removeDetailDT(btn) {
        const cards = document.querySelectorAll('.downtime-item-card');
        if (cards.length > 1) {
            btn.closest('.downtime-row-container').remove();
            updateRowNumbersDT();
            updateLoseTimeMonitoring();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'At least 1 line of input!' });
        }
    }

    function updateLoseTimeMonitoring() {
        const downtimeRaw = parseFloat("{{ $downtimeRaw }}") || 0;
        let identifiedDT = 0;
        document.querySelectorAll('.durasi-input').forEach(input => { identifiedDT += parseFloat(input.value) || 0; });
        const sisa = Math.max(0, downtimeRaw - identifiedDT);
        const sisaEl = document.getElementById('sisa-downtime-val');
        if (sisaEl) {
            sisaEl.innerText = sisa.toFixed(1);
            sisaEl.style.color = (sisa <= 0.1) ? "#27ae60" : "#e11d2e";
        }
        if(document.getElementById('total-lose-time')) document.getElementById('total-lose-time').innerText = identifiedDT.toFixed(1);
    }

    // LOGIKA SINKRONISASI DROPDOWN OP 10 DLL
    document.addEventListener('change', function (e) {
        const target = e.target;
        const row = target.closest('.downtime-item-card');
        if (!row) return;

        const areaSelect = row.querySelector('.area-problem-select');
        const problemSelect = row.querySelector('.problem-select');
        const akarSelect = row.querySelector('.akar-select');

        if (target.classList.contains('area-problem-select')) {
            const area = target.value;
            problemSelect.innerHTML = '<option value="">- Select an Issue -</option>';
            akarSelect.innerHTML = '<option value="">- Select an Issue First -</option>';
            if (dataMapping[area]) {
                dataMapping[area].problems.forEach(p => problemSelect.add(new Option(p, p)));
            }
            syncManualInput(target);
        }

        if (target.classList.contains('problem-select')) {
            const area = areaSelect.value;
            const problem = target.value;
            akarSelect.innerHTML = '<option value="">- Identify the Root Cause -</option>';
            if (dataMapping[area] && dataMapping[area].akar[problem]) {
                dataMapping[area].akar[problem].forEach(a => akarSelect.add(new Option(a, a)));
            }
            syncManualInput(target);
        }

        if (target.classList.contains('akar-select')) syncManualInput(target);
    });

    function syncManualInput(selectEl) {
        const container = selectEl.closest('.form-group') || selectEl.closest('div');
        const manualInput = container.querySelector('.manual-input');
        if (!manualInput) return;

        if (selectEl.value === 'Lain-lain') {
            manualInput.readOnly = false;
            manualInput.style.backgroundColor = "#ffffff";
            manualInput.value = ''; 
            manualInput.required = true;
        } else {
            manualInput.readOnly = true;
            manualInput.style.backgroundColor = "#e9ecef";
            manualInput.value = selectEl.value; 
            manualInput.required = false;
        }
    }

    function confirmSaveDowntime() {
        // 1. MUNCULIN POP-UP KONFIRMASI DI AWAL
        Swal.fire({
            title: 'Simpan Data Lose Time?',
            text: "Mohon pastikan seluruh data yang dimasukkan sudah benar dan sesuai.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6', // Warna biru seragam
            cancelButtonColor: '#6c757d',  // Warna abu-abu
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                try {
                    const form = document.getElementById('formDowntime');
                    let errorList = [];
                    let hasValidRow = false; // Penanda mengecek ada baris yang valid

                    // --- 2. Ambil elemen baris downtime yang tampil ---
                    const dtRows = form.querySelectorAll('.downtime-item-card');

                    if (dtRows.length === 0) {
                        errorList.push("Minimal harus ada 1 baris input lose time/downtime.");
                    }

                    // --- 3. Looping Pengecekan Baris demi Baris ---
                    dtRows.forEach((row, index) => {
                        let no = index + 1;
                        
                        let idDowntimeEl = row.querySelector('select[name^="IdDowntime"]');
                        let durasiEl = row.querySelector('input[name^="Durasi"]');
                        let areaProblemEl = row.querySelector('.area-problem-select');
                        let masalahEl = row.querySelector('.problem-select');
                        let akarEl = row.querySelector('.akar-select');
                        let manualInputEl = row.querySelector('.manual-input');

                        let valId = idDowntimeEl ? idDowntimeEl.value : '';
                        let durasi = durasiEl ? parseFloat(durasiEl.value) : 0;

                        if (valId) {
                            hasValidRow = true;

                            if (!durasi || durasi <= 0) {
                                errorList.push(`Baris #${no}: Durasi (Menit) minimal harus lebih dari 0.`);
                            }

                            // Pengecekan dropdown berantai (Area -> Masalah -> Akar)
                            if (areaProblemEl && !areaProblemEl.value) {
                                errorList.push(`Baris #${no}: Area Problem wajib dipilih.`);
                            }
                            if (masalahEl && !masalahEl.value) {
                                errorList.push(`Baris #${no}: Masalah / Issue wajib dipilih.`);
                            }
                            if (akarEl && !akarEl.value) {
                                errorList.push(`Baris #${no}: Akar Penyebab wajib dipilih.`);
                            }

                            // Kalau akar penyebab pilih "Lain-lain", form Tipe Masalah wajib diisi
                            if (akarEl && akarEl.value === 'Lain-lain') {
                                if (manualInputEl && manualInputEl.value.trim() === '') {
                                    errorList.push(`Baris #${no}: Keterangan Tipe Masalah (Lainnya...) wajib diisi.`);
                                }
                            }
                        } else {
                            // Kalau dia iseng ngisi durasi tapi Keterangan Downtimenya dibiarin kosong
                            if (durasi > 0) {
                                errorList.push(`Baris #${no}: Keterangan Downtime wajib dipilih karena durasi sudah diisi.`);
                            }
                        }
                    });

                    // --- 4. Cek Form Kosong Melompong ---
                    if (!hasValidRow && errorList.length === 0) {
                        errorList.push("Minimal wajib mengisi 1 baris data lose time secara lengkap.");
                    }

                    // --- 5. Tampilkan Error Jika Ada ---
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
                    // --- 6. Lolos Uji, Submit Form! ---
                    else {
                        // Logic Sinkronisasi Teks Downtime sebelum submit
                        document.querySelectorAll('.downtime-item-card').forEach(row => {
                            const select = row.querySelector('select[name="IdDowntime[]"]');
                            const hiddenTipe = row.querySelector('input[name="TipeDowntime[]"]');
                            if(select && hiddenTipe && select.selectedIndex > -1) {
                                hiddenTipe.value = select.options[select.selectedIndex].text;
                            }
                        });

                        // Hilangkan semua atribut disabled/required sebelum dikirim ke PHP
                        form.querySelectorAll('input:disabled, select:disabled, textarea:disabled').forEach(el => {
                            el.disabled = false;
                        });
                        form.querySelectorAll('input, select, textarea').forEach(el => {
                            el.required = false; 
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
</script>
@endsection