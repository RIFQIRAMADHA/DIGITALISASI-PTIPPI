@extends('Produksi.layouts.main')

@section('title', 'Update Production Schedule')

@section('content')
<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span> 
    <span class="active">Update Schedule: {{ $schedule->IdPlanSchedule }}</span>
</div>

<form action="{{ route('productionschedule.update', $schedule->IdPlanSchedule) }}" method="POST" id="formSchedule">
    @csrf
    @method('PUT')

    {{-- Header Section --}}
    <div class="content-card mb-4">
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label>Production Line</label>
                    <select id="lineSelect" class="form-select" style="background-color: #e9ecef; cursor: not-allowed;" disabled>
                        @foreach($lines as $l)
                            <option value="{{ $l->IdProductionLine }}" 
                                data-line="{{ strtolower(substr(trim($l->NamaProductionLine), -1)) }}"
                                {{ $schedule->IdProductionLine == $l->IdProductionLine ? 'selected' : '' }}>
                                {{ $l->NamaProductionLine }} - {{ $l->Shift }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="IdProductionLine" value="{{ $schedule->IdProductionLine }}">
                </div>

                <div class="form-group">
                    <label>PIC <span style="color: red;">*</span></label>
                    <select id="picSelect" name="IdKaryawan" class="form-select" required>
                        <option value="">-- Pilih PIC --</option>
                        @foreach($karyawan as $k)
                            @php $leaderSuffix = strtolower(substr(trim($k->Jabatan), -1)); @endphp
                            <option value="{{ $k->IdKaryawan }}" data-role="{{ $leaderSuffix }}" {{ $schedule->IdKaryawan == $k->IdKaryawan ? 'selected' : '' }}>
                                {{ $k->NamaKaryawan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Produksi</label>
                    <input type="date" class="form-control" 
                        value="{{ date('Y-m-d', strtotime($schedule->TanggalProduksi)) }}" 
                        style="background-color: #e9ecef; cursor: not-allowed;" readonly>
                    <input type="hidden" name="TanggalProduksi" value="{{ date('Y-m-d', strtotime($schedule->TanggalProduksi)) }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Section --}}
    <div id="detail-container">
        @foreach($schedule->details as $index => $det)
            @include('Produksi.productionschedule.partials.detail_row', [
                'index' => $index,
                'detail' => $det 
            ])
        @endforeach
    </div>

    {{-- BARIS FORM ACTIONS: BERGANTI KE KIRI & SINKRON WARNA ASTRA/IPS --}}
    <div class="form-actions" style="display: flex; justify-content: flex-start; gap: 15px; margin-top: 30px; align-items: center;">
        <button type="button" class="btn btn-primary" 
            style="width: 160px; height: 45px; background-color: #4361ee; border: none; border-radius: 10px; color: white; font-weight: 600; cursor: pointer;" 
            onclick="confirmUpdate()">
            Update
        </button>
        <a href="{{ route('productionschedule.index') }}" 
            style="width: 160px; height: 45px; background-color: #ffffff; color: #333; border: 1px solid #ddd; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: 600;">
            Cancel
        </a>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- 1. CONFIRM UPDATE (FORMAL, BAHASA INGGRIS STANDARD ERP) ---
    function confirmUpdate() {
        const form = document.getElementById('formSchedule');
        const updateBtn = document.querySelector('button[onclick="confirmUpdate()"]');
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Validasi Header Singkat
        const line = document.getElementById('lineSelect').value;
        const pic = document.getElementById('picSelect').value;
        const dateInput = document.querySelector('input[name="TanggalProduksi"]');
        const date = dateInput ? dateInput.value : '';

        if (!line || !pic || !date) {
            Swal.fire({
                icon: 'warning',
                title: 'Required Fields Missing',
                text: 'Please complete the Line, PIC, and Production Date configuration before updating.',
                confirmButtonColor: '#e11d2e'
            });
            return;
        }

        // Validasi Jam & Overlap antar baris detail
        const detailRows = document.querySelectorAll('.detail-item-card');
        let isTimeValid = true;
        let lastFinishTime = null;

        function timeToMinutes(timeStr) {
            if (!timeStr) return 0;
            const [hours, minutes] = timeStr.split(':').map(Number);
            return (hours * 60) + minutes;
        }

        detailRows.forEach((row, index) => {
            const startStr = row.querySelector('.time-start').value;
            const finishStr = row.querySelector('.time-finish').value;
            const currentStart = timeToMinutes(startStr);
            const currentFinish = timeToMinutes(finishStr);
            
            lastFinishTime = currentFinish;
        });

        if (!isTimeValid) return;

        Swal.fire({
            title: 'Update Production Schedule Data?', 
            text: "Please make sure all entered data is correct.", 
            icon: 'info', 
            showCancelButton: true,
            confirmButtonColor: '#4361ee', // Merah IPS/Astra Lu
            cancelButtonColor: '#aaa', 
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel'
        }).then((result) => { 
            if (result.isConfirmed) {
                if(updateBtn) {
                    updateBtn.disabled = true;
                    updateBtn.innerText = 'Processing...';
                }
                form.submit(); 
            }
        });
    }

    // --- 2. FUNGSI TAMBAH BARIS (FIXED RE-INDEXING & BINDING) ---
    function addDetailRow() {
        const allCards = document.querySelectorAll('.detail-item-card');
        const lastFinish = allCards.length > 0 ? allCards[allCards.length - 1].querySelector('.time-finish').value : null;

        // KUNCI STABILITAS: Gunakan jumlah riil baris di layar sebagai indeks routing AJAX
        const currentRealIndex = allCards.length;

        fetch("{{ url('productionschedule/get-detail-row') }}/" + currentRealIndex)
            .then(response => response.text())
            .then(html => {
                const container = document.getElementById('detail-container');
                container.insertAdjacentHTML('beforeend', html);
                
                if (typeof window.initSelect2 === 'function') { window.initSelect2(); }
                
                // Tata ulang urutan judul dan array indeks name HTML
                reIndexDetailRows();
                
                const newRows = document.querySelectorAll('.detail-item-card');
                const lastRow = newRows[newRows.length - 1];
                
                if (lastFinish && lastRow) {
                    lastRow.querySelector('.time-start').value = lastFinish;
                }

                const newSelect = lastRow.querySelector('.select-item-search');
                if (newSelect) {
                    togglePlanInputs(newSelect);
                }

                checkLineKStatus(); 
            });
    }

    // --- 3. FUNGSI HAPUS BARIS (FIXED RE-INDEXING) ---
    function removeDetailRow(button) {
        const totalRows = document.querySelectorAll('.detail-item-card').length;
        if (totalRows <= 1) {
            Swal.fire({ 
                icon: 'error', 
                title: 'Action Restrained', 
                text: 'A minimum of one schedule detail row is strictly required.',
                confirmButtonColor: '#e11d2e'
            });
            return;
        }

        Swal.fire({
            title: 'Remove this detail job?',
            text: "The entered schedule data for this row will be discarded.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d2e',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('.detail-item-card').remove();
                
                // Susun ulang penomoran kembali agar mutlak urut
                reIndexDetailRows();

                const allSelects = document.querySelectorAll('.select-item-search');
                if(allSelects.length > 0) togglePlanInputs(allSelects[0]);

                autoFillNextStart(); 
            }
        });
    }

    // --- 4. UTILITY UTAMA: PENATA URUTAN INDEKS & RE-INITIALIZE SELECT2 ---
    function reIndexDetailRows() {
        const cards = document.querySelectorAll('.detail-item-card');
        cards.forEach((card, i) => {
            // Perbaiki Judul Job
            const title = card.querySelector('.page-title');
            if (title) title.innerText = `2. Detail Job ${i + 1}`;

            // Perbaiki atribut array name agar berurutan dibaca array Controller Laravel
            card.querySelectorAll('input, select').forEach(element => {
                const currentName = element.getAttribute('name');
                if (currentName) {
                    const newName = currentName.replace(/details\[\d+\]/, `details[${i}]`);
                    element.setAttribute('name', newName);
                }
            });

            // Bangkitkan ulang select2 dinamis pada baris baru agar fungsional pencariannya tidak macet
            const select = card.querySelector('.select-item-search');
            if(select) {
                select.className = `custom-select2 custom-select2-${i} select-item-search`;
                if ($(select).data('select2')) {
                    $(select).select2('destroy');
                }
                $(select).select2({
                    placeholder: "--- Cari Job / Nama Item ---",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }

    // --- 5. FUNGSI KALKULASI METRIK OTOMATIS (TETAP UTUH) ---
    function calculateAutoMetrics(element) {
        if (!element) return;
        const container = element.closest('.detail-item-card');
        const lineSelect = document.getElementById('lineSelect');
        let totalMesin = 0;

        if (lineSelect) {
            let selectedOption = lineSelect.options[lineSelect.selectedIndex];
            let namaLine = selectedOption ? selectedOption.text.toUpperCase() : "";
            if (namaLine.includes('LINE E') || namaLine.includes('LINE F')) {
                totalMesin = 4; 
            } else if (namaLine.includes('LINE K')) {
                totalMesin = container.querySelectorAll('.mesin-status-toggle:checked').length;
            } else {
                totalMesin = 1; 
            }
        }
        container.querySelector('.total-mesin-display-grid').value = totalMesin;
        container.querySelector('.total-mesin-val').value = totalMesin;

        function convertTimeToMinutes(timeStr) {
            if (!timeStr) return 0;
            const match = timeStr.match(/(\d+):(\d+)/);
            if (!match) return 0;
            let hours = parseInt(match[1], 10);
            let minutes = parseInt(match[2], 10);
            if (timeStr.toLowerCase().includes('pm') && hours < 12) hours += 12;
            if (timeStr.toLowerCase().includes('am') && hours === 12) hours = 0;
            return (hours * 60) + minutes;
        }

        const startTimeRaw = container.querySelector('.time-start').value;
        const finishTimeRaw = container.querySelector('.time-finish').value;
        let workMinutes = 0;
        if (startTimeRaw && finishTimeRaw) {
            const startMinutes = convertTimeToMinutes(startTimeRaw);
            const finishMinutes = convertTimeToMinutes(finishTimeRaw);
            workMinutes = finishMinutes - startMinutes;
            if (workMinutes < 0) workMinutes += 1440;
            container.querySelector('.work-time').value = workMinutes;
        }

        const uchi = parseFloat(container.querySelector('.uchi-input').value) || 0;
        const sotoVal = (uchi * totalMesin) / 10;
        container.querySelector('.soto-auto').value = sotoVal.toFixed(1);

        const firstQ = parseFloat(container.querySelector('.first-q-input').value) || 1;
        const tptVal = workMinutes + firstQ;
        container.querySelector('.tpt-auto').value = tptVal.toFixed(1);

        const dtr = parseFloat(container.querySelector('.downtime-input').value) || 0;
        const pTime = workMinutes - uchi - sotoVal - dtr;
        container.querySelector('.press-time-auto').value = (pTime > 0) ? pTime.toFixed(2) : 0;

        const qtyA = parseFloat(container.querySelector('.plan-qty-a').value) || 0;
        const qtyB = parseFloat(container.querySelector('.plan-qty-b').value) || 0;
        const ubp = parseFloat(container.querySelector('.ubp-input')?.value) || 0; 
        
        const totalQty = qtyA + qtyB;
        const jmlMaterialField = container.querySelector('.jml-material-auto');
        const jmlPalletField = container.querySelector('.jml-pallet-auto');

        if(jmlMaterialField) jmlMaterialField.value = totalQty.toFixed(2);
        if(jmlPalletField) {
            jmlPalletField.value = (ubp > 0) ? (totalQty / ubp).toFixed(2) : 0;
        }

        if (typeof refreshGSPH === "function") {
            refreshGSPH(container);
        }
    }

    // --- 6. LOGIC GSPH GABUNGAN (TETAP UTUH) ---
    function refreshGSPH(container) {
        const currentSelect = container.querySelector('.select-item-search');
        const currentPartName = currentSelect.options[currentSelect.selectedIndex]?.getAttribute('data-nama-part') || "";
        
        if (currentPartName !== "") {
            let globalQty = 0;
            let globalTPT = 0;
            const allCards = document.querySelectorAll('.detail-item-card');

            allCards.forEach(card => {
                const sel = card.querySelector('.select-item-search');
                if (sel.options[sel.selectedIndex]?.getAttribute('data-nama-part') === currentPartName) {
                    const qA = parseFloat(card.querySelector('.plan-qty-a').value) || 0;
                    const qB = parseFloat(card.querySelector('.plan-qty-b').value) || 0;
                    const tpt = parseFloat(card.querySelector('.tpt-auto').value) || 0;
                    globalQty += (qA + qB);
                    globalTPT += tpt;
                }
            });

            allCards.forEach(card => {
                const sel = card.querySelector('.select-item-search');
                if (sel.options[sel.selectedIndex]?.getAttribute('data-nama-part') === currentPartName) {
                    card.querySelector('.gsph-auto').value = (globalTPT > 0) ? ((globalQty / globalTPT) * 60).toFixed(1) : 0;
                }
            });
        }
    }

    function togglePlanInputs(selectElement) {
        if (!selectElement) return;
        
        const container = selectElement.closest('.detail-item-card');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption) return;

        // --- Update CT ---
        const ctAuto = container.querySelector('.ct-auto');
        if(ctAuto) ctAuto.value = selectedOption.getAttribute('data-ct') || 0;

        // --- DEBUG & MODIFIKASI: Logika Plan Qty B ---
        const jobNumber = selectedOption.getAttribute('data-job') || "";
        const inputQty2 = container.querySelector('input[name$="[PlanQty2]"]');
        
        // Debugging: lihat apa yang sebenarnya terbaca
        console.log("Job Number yang terbaca:", jobNumber);
        
        if(inputQty2) {
            // Kita pastikan pengecekan lebih teliti
            const isDualJob = jobNumber.toString().includes('/');
            
            inputQty2.disabled = !isDualJob;
            
            // Visual feedback
            inputQty2.style.backgroundColor = inputQty2.disabled ? "#e9ecef" : "#ffffff";
            inputQty2.style.cursor = inputQty2.disabled ? "not-allowed" : "text";
            
            // Jika disable, paksa kosongkan/nolkan nilainya
            if (inputQty2.disabled) {
                inputQty2.value = "0,00"; // Sesuaikan format display kalau perlu
            }
        }

        // --- Logika UCHI & Kalkulasi (TETAP UTUH) ---
        const allCards = document.querySelectorAll('.detail-item-card');
        allCards.forEach((card, index) => {
            const uchiInput = card.querySelector('.uchi-input');
            if (!uchiInput) return;

            if (index === 0) {
                uchiInput.value = 10; 
            } else {
                const prevCard = allCards[index - 1];
                const currentSel = card.querySelector('.select-item-search');
                const prevSel = prevCard.querySelector('.select-item-search');
                const currentPart = currentSel.options[currentSel.selectedIndex]?.getAttribute('data-nama-part') || "";
                const prevPart = prevSel.options[prevSel.selectedIndex]?.getAttribute('data-nama-part') || "";

                uchiInput.value = (currentPart !== "" && currentPart === prevPart) ? 1 : 10;
            }
            
            const currentSelSearch = card.querySelector('.select-item-search');
            if (currentSelSearch && currentSelSearch.value !== "") {
                calculateAutoMetrics(currentSelSearch);
            }
        });
    }
    // --- 8. RANTAI WAKTU OTOMATIS ---
    function autoFillNextStart() {
        const allCards = document.querySelectorAll('.detail-item-card');
        allCards.forEach((card, i) => {
            const currentFinish = card.querySelector('.time-finish').value;
            const nextCard = allCards[i + 1];

            if (currentFinish && nextCard) {
                const nextStartInput = nextCard.querySelector('.time-start');
                if (!nextStartInput.value || nextStartInput.value === "") {
                    nextStartInput.value = currentFinish;
                    calculateAutoMetrics(nextStartInput);
                }
            }
        });
    }

    // --- 9. SYNC TPT KE MESIN BOX ---
    function syncTPTtoMesin(element) {
        const container = element.closest('.detail-item-card');
        const nilaiTPT = element.value; 
        container.querySelectorAll('.mesin-box').forEach(box => {
            const checkbox = box.querySelector('.mesin-status-toggle');
            const hiddenInput = box.querySelector('.mesin-value-real');
            if (checkbox.checked) { hiddenInput.value = nilaiTPT; }
            else { hiddenInput.value = 0; }
        });
    }

    // --- 10. UPDATE CHECKBOX MESIN STATUS ---
    function updateMesinStatus(checkbox) {
        const box = checkbox.closest('.mesin-box');
        box.style.background = checkbox.checked ? "#edf2ff" : "#fff";
        box.style.borderColor = checkbox.checked ? "#4361ee" : "#eee";
        calculateAutoMetrics(checkbox);
    }

    // --- 11. LINE K ROBOT SELECTION FIELD HANDLER ---
    function checkLineKStatus() {
        const lineSelect = document.getElementById('lineSelect');
        if(!lineSelect) return;
        const namaLine = lineSelect.options[lineSelect.selectedIndex].text.toUpperCase();
        document.querySelectorAll('.robot-fields-container').forEach(el => {
            el.style.display = namaLine.includes('LINE K') ? 'block' : 'none';
        });
        document.querySelectorAll('.plan-qty-a').forEach(el => calculateAutoMetrics(el));
    }

    // --- 1. FUNGSI FILTER PIC (Berdasarkan Line) ---
    function filterLeaderByLine() {
        const lineSelect = document.getElementById('lineSelect');
        const picSelect = document.getElementById('picSelect');
        if(!lineSelect || !picSelect) return;

        // Ambil data-line dari opsi yang sedang terpilih
        const selectedOption = lineSelect.options[lineSelect.selectedIndex];
        const lineSuffix = selectedOption ? selectedOption.getAttribute('data-line') : null;
        
        // Simpan ID Karyawan yang sudah terpilih dari database (karena ini halaman Edit)
        const currentPic = picSelect.value; 
        
        Array.from(picSelect.options).forEach(option => {
            if (option.value === "") {
                option.style.display = "block";
                return;
            }
            const leaderSuffix = option.getAttribute('data-role');
            // Tampilkan hanya PIC yang huruf akhirnya sama dengan Line (e.g. Line E = Leader E)
            if (!lineSuffix || leaderSuffix === lineSuffix) {
                option.style.display = "block";
            } else {
                option.style.display = "none";
            }
        });

        // Set kembali value-nya agar tidak hilang setelah opsi disembunyikan
        picSelect.value = currentPic;
    }

    // --- DOM READY INITIALIZER ---
    document.addEventListener("DOMContentLoaded", function() {
        // 1. FILTER PIC SESUAI LINE (Paling penting jalan pertama biar gak kedip)
        if (typeof filterLeaderByLine === "function") {
            filterLeaderByLine();
        }

        // 2. Cek status Line K
        checkLineKStatus();
        
        // 3. Inisialisasi susunan data awal edit pas load pertama kali
        reIndexDetailRows();
        
        // 4. Sinkronisasi semua baris yang sudah ada
        document.querySelectorAll('.select-item-search').forEach(select => {
            togglePlanInputs(select);
        });
        
        document.querySelectorAll('.detail-item-card').forEach(card => {
            const select = card.querySelector('.select-item-search');
            if (select && select.value !== "") {
                calculateAutoMetrics(select);
            }
        });
    });
</script>
@endsection