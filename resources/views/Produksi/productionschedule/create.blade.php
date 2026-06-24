@extends('Produksi.layouts.main')

@section('title', 'Add Production Schedule')

@section('content')
<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span> 
    <span>Daily Input</span> <span class="separator">></span> 
    <span class="active">Add Production Schedule</span>
</div>

{{-- TAMBAHKAN INI: Untuk melihat error validasi atau database --}}
@if ($errors->any())
    <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <strong>Oops! Something went wrong:</strong>
        <ul style="margin-top: 5px; font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div style="background: #fffbeb; border: 1px solid #f59e0b; color: #92400e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <strong>Error Database:</strong> {{ session('error') }}
    </div>
@endif

<form action="{{ route('productionschedule.store') }}" method="POST" id="formSchedule">
    @csrf

    {{-- 1. INFORMASI UTAMA JOB (HEADER) --}}
    <div class="content-card mb-4">
        <div class="card-header">
            <h5 class="page-title" style="font-size: 15px;">1. Informasi Utama Job</h5>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                
                {{-- FIELD 1: PRODUCTION LINE --}}
                <div class="form-group">
                    <label style="font-weight: 700; color: #333; margin-bottom: 5px; display: block;">Production Line <span style="color: red;">*</span></label>
                    <select name="IdProductionLine" id="lineSelect" class="form-select" onchange="filterLeaderByLine()" required
                            style="background-color: #f1f2f6; color: #2d3436; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-weight: 600; cursor: pointer;">
                        <option value="" style="color: #666;">- Pilih Line - Shift -</option>
                        @foreach($lines as $l)
                            <option value="{{ $l->IdProductionLine }}" data-line="{{ strtolower(substr(trim($l->NamaProductionLine), -1)) }}">
                                {{ $l->NamaProductionLine }} - {{ $l->Shift }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FIELD 2: PIC --}}
                <div class="form-group">
                    <label style="font-weight: 700; color: #333; margin-bottom: 5px; display: block;">PIC <span style="color: red;">*</span></label>
                    <select name="IdKaryawan" id="picSelect" class="form-select" required
                            style="background-color: #f1f2f6; color: #2d3436; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-weight: 600; cursor: pointer;">
                        <option value="" style="color: #666;">- Pilih PIC -</option>
                        @foreach($karyawan as $k)
                            @php $leaderSuffix = strtolower(substr(trim($k->Jabatan), -1)); @endphp
                            <option value="{{ $k->IdKaryawan }}" data-role="{{ $leaderSuffix }}">
                                {{ $k->NamaKaryawan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FIELD 3: TANGGAL PRODUKSI --}}
                <div class="form-group">
                    <label style="font-weight: 700; color: #333; margin-bottom: 5px; display: block;">Tanggal Produksi <span style="color: red;">*</span></label>
                    <input type="date" 
                        name="TanggalProduksi" 
                        class="form-control" 
                        value="{{ old('TanggalProduksi', date('Y-m-d')) }}" 
                        min="{{ date('Y-m-d') }}"
                        required
                        style="background-color: #f1f2f6; color: #2d3436; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-weight: 600;">
                </div>
                
            </div>
        </div>
    </div>

    {{-- CONTAINER UNTUK DETAIL JOB --}}
    <div id="detail-container">
        @include('Produksi.productionschedule.partials.detail_row', ['index' => 0])
    </div>

    <div class="form-actions" style="display: flex !important; justify-content: flex-start !important; gap: 15px; margin-top: 30px; align-items: center; width: 100%;">
        {{-- Tombol Save --}}
        <button type="button" class="btn btn-primary" 
            style="width: 160px; height: 45px; background-color: #4361ee; border: none; border-radius: 10px; color: white; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.3s ease;" 
            onclick="confirmSave()">
            Save
        </button>
        
        {{-- Tombol Cancel --}}
        <a href="{{ route('productionschedule.index') }}" 
            style="width: 160px; height: 45px; background-color: #ffffff; color: #333; border: 1px solid #ddd; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 15px; display: flex; align-items: center; justify-content: center; box-sizing: border-box; transition: all 0.3s ease;">
            Cancel
        </a>
    </div>
</form>


<script>
    // --- 1. FUNGSI SAVE (DENGAN ALUR KONFIRMASI -> VALIDASI -> SUBMIT) ---
    function confirmSave() {
        // MUNCULIN POP-UP KONFIRMASI DI AWAL
        Swal.fire({
            title: 'Simpan Jadwal Produksi?',
            text: "Mohon pastikan seluruh data yang dimasukkan sudah benar dan sesuai.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4361ee', // Biru
            cancelButtonColor: '#6c757d',  // Abu-abu
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formSchedule');
                const saveBtn = document.querySelector('button[onclick="confirmSave()"]');
                let errorList = [];

                // --- 1. Validasi Header ---
                const line = document.getElementById('lineSelect').value;
                const pic = document.getElementById('picSelect').value;
                const dateInput = document.querySelector('input[name="TanggalProduksi"]');
                const date = dateInput ? dateInput.value : '';

                if (!line) errorList.push("Header: Line Produksi wajib dipilih.");
                if (!pic) errorList.push("Header: PIC / Leader wajib dipilih.");
                if (!date) errorList.push("Header: Tanggal Produksi wajib diisi.");

                // --- 2. Validasi Jam & Detail ---
                const detailRows = document.querySelectorAll('.detail-item-card');
                if (detailRows.length === 0) {
                    errorList.push("Minimal harus ada 1 baris detail jadwal produksi.");
                }

                let lastFinishTime = null;

                function timeToMinutes(timeStr) {
                    if (!timeStr) return 0;
                    const [hours, minutes] = timeStr.split(':').map(Number);
                    return (hours * 60) + minutes;
                }

                detailRows.forEach((row, index) => {
                    let no = index + 1;
                    const item = row.querySelector('.select-item-search').value;
                    const startStr = row.querySelector('.time-start').value;
                    const finishStr = row.querySelector('.time-finish').value;

                    if (!item) errorList.push(`Baris #${no}: Item / Job Number wajib dipilih.`);
                    if (!startStr) errorList.push(`Baris #${no}: Waktu Mulai (Start) wajib diisi.`);
                    if (!finishStr) errorList.push(`Baris #${no}: Waktu Selesai (Finish) wajib diisi.`);

                    // Cek Logika Waktu
                    if (startStr && finishStr) {
                        const currentStart = timeToMinutes(startStr);
                        const currentFinish = timeToMinutes(finishStr);

                        // A. Cek apakah Start >= Finish
                        if (currentStart >= currentFinish) {
                            errorList.push(`Baris #${no}: Waktu Mulai tidak boleh lebih besar atau sama dengan Waktu Selesai.`);
                        }

                        // B. Cek Overlap dengan baris sebelumnya
                        if (lastFinishTime !== null) {
                            if (currentStart < lastFinishTime) {
                                errorList.push(`Baris #${no}: Terjadi bentrok (Waktu Mulai tumpang tindih dengan Waktu Selesai pekerjaan sebelumnya).`);
                            }
                        }
                        lastFinishTime = currentFinish;
                    }
                });

                // --- 3. Tampilkan Error Jika Ada ---
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
                        listHtml += `<li style="font-weight: bold; color: #e11d2e;">...dan ${moreCount} masalah lainnya.</li>`;
                    }
                    listHtml += `</ul></div>`;

                    Swal.fire({
                        icon: 'error',
                        title: 'Data Belum Lengkap',
                        html: listHtml,
                        confirmButtonColor: '#e11d2e', // Merah Astra
                        confirmButtonText: 'Mengerti'
                    });
                } 
                // --- 4. Lolos Uji Lokal -> Cek Duplikat ke Database via Fetch ---
                else {
                    saveBtn.disabled = true;
                    saveBtn.innerText = 'Memeriksa...'; 

                    fetch(`{{ url('productionschedule/check-duplicate') }}?line=${line}&date=${date}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                saveBtn.disabled = false;
                                saveBtn.innerText = 'Save';

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: `Jadwal untuk ${data.line_name} pada tanggal tersebut sudah terdaftar di sistem.`,
                                    confirmButtonColor: '#e11d2e',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                // Lolos Semua Uji -> Submit!
                                saveBtn.innerText = 'Memproses...'; 
                                form.submit();
                            }
                        })
                        .catch(err => {
                            saveBtn.disabled = false;
                            saveBtn.innerText = 'Save';
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error Sistem',
                                text: 'Gagal memeriksa duplikasi jadwal. Periksa koneksi internet atau hubungi administrator.',
                                confirmButtonColor: '#e11d2e'
                            });
                            console.error(err);
                        });
                }
            }
        });
    }

    // --- 2. FUNGSI FILTER PIC (Berdasarkan Line) ---
    function filterLeaderByLine() {
        const lineSelect = document.getElementById('lineSelect');
        const picSelect = document.getElementById('picSelect');
        if(!lineSelect || !picSelect) return;

        const lineSuffix = lineSelect.options[lineSelect.selectedIndex].getAttribute('data-line');
        
        picSelect.value = "";
        
        Array.from(picSelect.options).forEach(option => {
            if (option.value === "") {
                option.style.display = "block";
                return;
            }
            const leaderSuffix = option.getAttribute('data-role');
            if (!lineSuffix || leaderSuffix === lineSuffix) {
                option.style.display = "block";
            } else {
                option.style.display = "none";
            }
        });

        checkLineKStatus();
    }

    // --- 3. FUNGSI TAMBAH BARIS (FIXED URUTAN INDEKS & PENOMORAN) ---
    function addDetailRow() {
        const allCards = document.querySelectorAll('.detail-item-card');
        const lastFinish = allCards.length > 0 ? allCards[allCards.length - 1].querySelector('.time-finish').value : null;

        // 🔥 KUNCI PERBAIKAN: Gunakan jumlah baris riil di layar saat ini untuk mengambil row baru
        const currentRealIndex = allCards.length;

        fetch("{{ url('productionschedule/get-detail-row') }}/" + currentRealIndex)
            .then(response => response.text())
            .then(html => {
                document.getElementById('detail-container').insertAdjacentHTML('beforeend', html);
                
                if (typeof window.initSelect2 === 'function') { window.initSelect2(); }
                
                // Urutkan dan susun ulang semua atribut name dan judul kembali agar mutlak sinkron
                reIndexDetailRows();

                const newRows = document.querySelectorAll('.detail-item-card');
                const lastRow = newRows[newRows.length - 1];
                if (lastFinish && lastRow) {
                    lastRow.querySelector('.time-start').value = lastFinish;
                    calculateAutoMetrics(lastRow.querySelector('.time-start'));
                }
                
                checkLineKStatus();
            });
    }

    // --- 4. FUNGSI HAPUS BARIS (FIXED DENGAN RE-INDEXING YANG STABIL) ---
    function removeDetailRow(button) {
        const totalRows = document.querySelectorAll('.detail-item-card').length;
        
        if (totalRows <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Action Restrained',
                text: 'A minimum of one schedule detail row is strictly required.',
                confirmButtonColor: '#4361ee',
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
                
                // 🔥 KUNCI PERBAIKAN: Susun ulang indeks name dan judul agar tidak melompat nilainya
                reIndexDetailRows();

                if (typeof autoFillNextStart === "function") {
                    autoFillNextStart();
                }
            }
        });
    }

    // --- 5. UTILITY RESYNC INDEKS (FUNGSI PENJAGA KESTABILAN RE-SPLIT FORM) ---
    function reIndexDetailRows() {
        const cards = document.querySelectorAll('.detail-item-card');
        cards.forEach((card, i) => {
            // Perbaiki Judul Job (Detail Job 1, 2, 3... dst)
            const title = card.querySelector('.page-title');
            if (title) title.innerText = `2. Detail Job ${i + 1}`;

            // Perbaiki atribut name input biar dibaca berurutan oleh array Controller Laravel
            card.querySelectorAll('input, select').forEach(element => {
                const currentName = element.getAttribute('name');
                if (currentName) {
                    const newName = currentName.replace(/details\[\d+\]/, `details[${i}]`);
                    element.setAttribute('name', newName);
                }
            });

            // 🔥 PERBAIKAN DI SINI BOLO: Paksa Select2 render ulang pakai teks Inggris!
            const select = card.querySelector('.select-item-search');
            if (select) {
                // Perbarui class uniknya biar aman
                select.className = `custom-select2 custom-select2-${i} select-item-search`;
                
                // Hancurkan Select2 lama yang nahan teks Indonesia
                if ($(select).data('select2')) {
                    $(select).select2('destroy');
                }
                
                // Bangun ulang pakai Placeholder Inggris
                $(select).select2({
                    placeholder: "--- Search Job / Name Part ---",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }

    // --- 6. PERHITUNGAN METRICS PRODUKSI AUTOMATIS ---
    function calculateAutoMetrics(element) {
        if (!element) return;
        const container = element.closest('.detail-item-card');
        const lineSelect = document.getElementById('lineSelect');
        if(!lineSelect || lineSelect.selectedIndex === 0) return;
        
        const namaLine = lineSelect.options[lineSelect.selectedIndex].text.toUpperCase();

        // Total Mesin
        let totalMesin = 0;
        if (namaLine.includes('LINE E') || namaLine.includes('LINE F')) {
            totalMesin = 4;
        } else if (namaLine.includes('LINE K')) {
            totalMesin = container.querySelectorAll('.mesin-status-toggle:checked').length;
        }
        container.querySelector('.total-mesin-display-grid').value = totalMesin;
        container.querySelector('.total-mesin-val').value = totalMesin;

        // Work Time
        function timeToMin(timeStr) {
            if (!timeStr) return 0;
            const match = timeStr.match(/(\d+):(\d+)\s*(AM|PM)?/i);
            if (!match) return 0;
            let h = parseInt(match[1], 10);
            let m = parseInt(match[2], 10);
            const ampm = match[3] ? match[3].toUpperCase() : null;
            if (ampm === 'PM' && h < 12) h += 12;
            if (ampm === 'AM' && h === 12) h = 0;
            return (h * 60) + m;
        }

        const startMin = timeToMin(container.querySelector('.time-start').value);
        const finishMin = timeToMin(container.querySelector('.time-finish').value);
        let workMinutes = finishMin - startMin;
        if (workMinutes < 0) workMinutes += 1440;
        container.querySelector('.work-time').value = workMinutes;

        // Soto, TPT, & Press Time
        const uchi = parseFloat(container.querySelector('.uchi-input').value) || 0;
        const sotoVal = (uchi * totalMesin) / 10;
        container.querySelector('.soto-auto').value = sotoVal.toFixed(1);

        const firstQ = parseFloat(container.querySelector('.first-q-input').value) || 1;
        const tptVal = workMinutes + firstQ;
        const tptField = container.querySelector('.tpt-auto');
        tptField.value = tptVal.toFixed(1);

        const dtr = parseFloat(container.querySelector('.downtime-input').value) || 0;
        const pTime = workMinutes - uchi - sotoVal - dtr;
        container.querySelector('.press-time-auto').value = (pTime > 0) ? pTime.toFixed(2) : 0;

        // Logic GSPH Gabungan
        const currentSelect = container.querySelector('.select-item-search');
        const currentPartName = currentSelect.options[currentSelect.selectedIndex]?.getAttribute('data-nama-part') || "";
        
        if (currentPartName !== "") {
            let globalQty = 0, globalTPT = 0;
            const allCards = document.querySelectorAll('.detail-item-card');

            allCards.forEach(card => {
                const sel = card.querySelector('.select-item-search');
                if (sel.options[sel.selectedIndex]?.getAttribute('data-nama-part') === currentPartName) {
                    globalQty += (parseFloat(card.querySelector('.plan-qty-a').value) || 0) + (parseFloat(card.querySelector('.plan-qty-b').value) || 0);
                    globalTPT += parseFloat(card.querySelector('.tpt-auto').value) || 0;
                }
            });

            allCards.forEach(card => {
                const sel = card.querySelector('.select-item-search');
                if (sel.options[sel.selectedIndex]?.getAttribute('data-nama-part') === currentPartName) {
                    card.querySelector('.gsph-auto').value = (globalTPT > 0) ? ((globalQty / globalTPT) * 60).toFixed(1) : 0;
                }
            });
        }

        // Stroke, Material, Pallet
        container.querySelector('.stroke-auto').value = (parseFloat(container.querySelector('.plan-qty-a').value) || 0) * totalMesin;
        const bqSht = parseFloat(container.querySelector('.bq-sht-input').value) || 1;
        container.querySelector('.jml-material-auto').value = (( (parseFloat(container.querySelector('.plan-qty-a').value) || 0) + (parseFloat(container.querySelector('.plan-qty-b').value) || 0) ) / bqSht).toFixed(2);
        
        const stdPallet = parseFloat(currentSelect?.options[currentSelect.selectedIndex]?.getAttribute('data-qty-pallet')) || 1;
        container.querySelector('.jml-pallet-auto').value = ( (parseFloat(container.querySelector('.plan-qty-a').value) || 0) / stdPallet).toFixed(2);

        if (typeof syncTPTtoMesin === "function") syncTPTtoMesin(tptField);
        if (typeof autoFillNextStart === "function") autoFillNextStart();
    }

    // --- 7. TOGGLE INPUTS & RESET UCHI LOGIC ---
    function togglePlanInputs(selectElement) {
        const container = selectElement.closest('.detail-item-card');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        // --- 1. HANDLE CT ---
        container.querySelector('.ct-auto').value = selectedOption?.getAttribute('data-ct') || 0;

        // --- 2. LOGIKA KUNCI PLAN QTY B (Fixed) ---
        const inputQty2 = container.querySelector('input[name$="[PlanQty2]"]');
        if (inputQty2) {
            // Cek job number, kalau option belum dipilih (null) maka disable
            const jobNumber = selectedOption?.getAttribute('data-job') || "";
            const isJoint = jobNumber.includes('/');
            
            inputQty2.disabled = !isJoint;
            inputQty2.style.backgroundColor = !isJoint ? "#e9ecef" : "#ffffff";
            if (!isJoint) inputQty2.value = 0;
        }

        // --- 3. LOGIKA UCHI (TETAP) ---
        const allCards = document.querySelectorAll('.detail-item-card');
        allCards.forEach((card, index) => {
            const uchiInput = card.querySelector('.uchi-input');
            if (!uchiInput) return;

            if (index === 0) {
                uchiInput.value = 10;
            } else {
                const currentSel = card.querySelector('.select-item-search');
                const prevSel = allCards[index - 1].querySelector('.select-item-search');
                
                // Pastikan select ada isinya sebelum ambil atribut
                const currentPart = currentSel.options[currentSel.selectedIndex]?.getAttribute('data-nama-part') || "";
                const prevPart = prevSel.options[prevSel.selectedIndex]?.getAttribute('data-nama-part') || "";

                uchiInput.value = (currentPart !== "" && currentPart === prevPart) ? 1 : 10;
            }
        });
        
        calculateAutoMetrics(selectElement);
    }

    function autoFillNextStart() {
        const allCards = document.querySelectorAll('.detail-item-card');
        allCards.forEach((card, i) => {
            const currentFinish = card.querySelector('.time-finish').value;
            if (currentFinish && allCards[i + 1]) {
                const nextStartInput = allCards[i+1].querySelector('.time-start');
                if (!nextStartInput.value) {
                    nextStartInput.value = currentFinish;
                    calculateAutoMetrics(nextStartInput);
                }
            }
        });
    }

    function syncTPTtoMesin(element) {
        const container = element.closest('.detail-item-card');
        container.querySelectorAll('.mesin-box').forEach(box => {
            const checkbox = box.querySelector('.mesin-status-toggle');
            box.querySelector('.mesin-value-real').value = checkbox.checked ? element.value : 0;
        });
    }

    function updateMesinStatus(checkbox) {
        const box = checkbox.closest('.mesin-box');
        box.style.background = checkbox.checked ? "#edf2ff" : "#fff";
        box.style.borderColor = checkbox.checked ? "#4361ee" : "#eee";
        calculateAutoMetrics(checkbox);
    }

    function checkLineKStatus() {
        const lineSelect = document.getElementById('lineSelect');
        if(!lineSelect) return;
        const namaLine = lineSelect.options[lineSelect.selectedIndex].text.toUpperCase();
        
        document.querySelectorAll('.robot-fields-container').forEach(el => {
            el.style.display = namaLine.includes('LINE K') ? 'block' : 'none';
        });

        document.querySelectorAll('.plan-qty-a').forEach(el => calculateAutoMetrics(el));
    }

    document.addEventListener("DOMContentLoaded", () => {
        filterLeaderByLine();
        const ls = document.getElementById('lineSelect');
        if(ls) ls.addEventListener('change', filterLeaderByLine);

        // --- INI KUNCI AGAR OTOMATIS TERKUNCI SAAT PAGE LOAD ---
        document.querySelectorAll('.select-item-search').forEach(select => {
            togglePlanInputs(select);
        });
    });
</script>
@endsection