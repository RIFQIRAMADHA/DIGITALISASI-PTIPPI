@extends('Produksi.layouts.main')

@section('title', 'Production Schedule')
@section('page-title', 'Production Schedule')

@section('card-actions')
<div class="schedule-actions">
    <button class="btn-custom btn-import-excel" id="btnImportExcel" onclick="openImportModal(event)">
        <i class="fas fa-file-excel"></i> Import Excel
    </button>
    <a href="{{ route('productionschedule.create') }}" class="btn-custom btn-add-schedule">
        <i class="fas fa-plus"></i> + Add Production Schedule
    </a>
</div>
@endsection

@section('content')

<style>
    /* ✅ STYLING TABEL BAWAAN LU DITAMBAH STICKY HEADER */
    .table-responsive-wrapper {
        width: 100%;
        max-height: 500px; /* 🔥 WAJIB: Batas tinggi biar bisa di-scroll secara internal */
        overflow-y: auto;  /* 🔥 WAJIB: Aktifkan scroll vertikal */
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #eee;
        border-radius: 0 !important; 
        margin-top: 15px;
        position: relative; /* Buat jangkar sticky */
    }
    
    /* 🔥 FIX STICKY: Pisahkan border biar header nggak pecah saat scroll */
    #scheduleTable { 
        min-width: 1200px; 
        border-collapse: separate !important; 
        border-spacing: 0; 
        width: 100%;
    }
    
    /* 🔥 KUNCI HEADER DI ATAS (STICKY THEAD TH) */
    #scheduleTable thead th {
        position: sticky;
        top: 0;
        background: #bb2121; /* Warna merah header Astra */
        color: white;
        z-index: 10;
        padding: 12px;
        border-bottom: 2px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        white-space: nowrap;
    }

    /* Pastikan border kiri untuk kolom pertama tetap rapi */
    #scheduleTable thead th:first-child {
        border-left: 1px solid #dee2e6;
    }

    /* Atur border sel data di tbody agar sinkron dengan separate layout */
    #scheduleTable tbody td {
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
    }

    #scheduleTable tbody td:first-child {
        border-left: 1px solid #dee2e6;
    }
    
    .badge-revisi {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .row-hidden { display: none !important; }

    /* ✅ STYLING TOMBOL HEADER (TIDAK DIUBAH) */
    .schedule-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .btn-custom {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 240px; 
        box-sizing: border-box;
    }

    /* 🔥 SUPER RESPONSIVE FIX UNTUK TOMBOL (TETAP DI 850px) */
    @media (max-width: 850px) {
        .card-header, .d-flex { 
            flex-wrap: wrap !important; 
        }
        .schedule-actions {
            width: 100%;
            justify-content: stretch;
            margin-top: 10px;
        }
        .btn-custom {
            width: 100%; 
            margin-bottom: 5px;
        }
    }

    /* =========================================================
       🔥 STYLING FILTER SAKTI (MENGGUNAKAN ID UNTUK MENIMPA TEMA) 
       ========================================================= */

    #filter-toolbar-sakti {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        width: 100%;
        margin-bottom: 20px;
        align-items: center;
        justify-content: space-between;
    }

    #filter-toolbar-sakti .grup-kiri-sakti {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        flex: 1; 
        min-width: 300px;
    }

    /* 🔥 PAKSA SEMUA INPUT & SELECT KOTAK & TINGGI DI NORMAL DESKTOP */
    #filter-toolbar-sakti input, 
    #filter-toolbar-sakti select {
        height: 45px !important; 
        border: 1px solid #ced4da !important;
        border-radius: 8px !important; 
        padding: 0 15px !important;
        font-size: 14px !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        outline: none !important;
        box-shadow: none !important;
        background-color: #fff !important;
        flex: 1; 
        min-width: 200px; 
    }

    #filter-toolbar-sakti input.search-sakti {
        max-width: 400px; 
    }

    /* ✅ RESPONSIVE KHUSUS FILTER (SPLIT SCREEN) */
    @media (max-width: 992px) {
        #filter-toolbar-sakti, #filter-toolbar-sakti .grup-kiri-sakti {
            display: block !important; /* Paksa blokir flex bawaan template */
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* 🔥 PAKSA TINGGI JADI 60px DAN LEBAR 100% SAAT SPLIT LAYAR */
        #filter-toolbar-sakti input, 
        #filter-toolbar-sakti select {
            display: block !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: none !important;
            height: 60px !important; /* INI YANG BIKIN TEBAL */
            min-height: 60px !important; 
            font-size: 16px !important; 
            margin-bottom: 15px !important; /* Jarak antar kotak */
        }
    }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Production Schedule</span>
</div>

<div id="filter-toolbar-sakti">
    <div class="grup-kiri-sakti">
        <input type="date" id="filterDate" class="input-sakti" value="{{ $tanggal }}" onchange="updateFilter()" style="border-radius: 8px !important; border: 1px solid #ced4da !important;">
        
        <select id="filterLine" class="input-sakti" onchange="updateFilter()" style="border-radius: 8px !important; border: 1px solid #ced4da !important; appearance: auto !important;">
            <option value="">All Line - All Shift</option>
            @foreach($lines as $l)
                <option value="{{ $l->IdProductionLine }}" {{ request('line') == $l->IdProductionLine ? 'selected' : '' }}>
                    {{ $l->NamaProductionLine }} - {{ $l->Shift }}
                </option>
            @endforeach
        </select>
    </div>
    
    <input type="text" class="input-sakti search-sakti" placeholder="Search Schedule..." onkeyup="searchTable(this.value)" style="border-radius: 8px !important; border: 1px solid #ced4da !important;">
</div>

<div class="info-header" style="margin-bottom: 12px; margin-top: 15px;">
    <p class="text-muted">Item Produksi Tanggal : <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong></p>
    <h5 class="page-title" style="font-size: 18px; margin-top: 5px;">
        {{ $selectedLine ? $selectedLine->NamaProductionLine . ' - Shift ' . $selectedLine->Shift : 'Semua Production Line' }}
    </h5>
</div>

{{-- TABEL LU DI SINI (BIARKAN SEPERTI ASLINYA) --}}

<div class="table-responsive-wrapper">
    <table class="table-custom table-fixed" id="scheduleTable">
        <thead>
            <tr>
                <th style="width: 200px;">Production Line</th>
                <th style="width: 100px;" class="text-center">Shift</th>
                <th style="width: 250px;">Item Produksi</th> 
                <th style="width: 150px;">PIC</th>
                <th style="width: 150px;" class="text-center">Revisi</th>
                <th style="width: 180px;">Tanggal Produksi</th>
                <th style="width: 220px;" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        {{-- ✅ PERUBAHAN: Langsung looping ID Plan --}}
        @forelse ($groupedSchedules as $finalIdPlan => $items)
            @php
                $firstItem = $items->first();
                $uniqueId = $firstItem->IdInputHarian; 
            @endphp
            <tr class="schedule-data-row">
                <td style="font-weight: bold;">{{ $firstItem->fixed_line_name ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge" style="background: #f1f2f6; color: #2f3542; padding: 5px 10px; border-radius: 4px; border: 1px solid #dfe4ea;">
                        {{ $firstItem->fixed_shift ?? '-' }}
                    </span>
                </td>
                <td>
                    @foreach($items as $itemRow)
                        <div style="font-size: 12px; border-bottom: 1px solid #eee; padding: 4px 0;">
                            <i class="fas fa-caret-right" style="color: #4361ee;"></i> 
                            {{ $itemRow->item->JobNumber ?? '-' }} 
                            <span style="color: #999; font-size: 10px;">({{ $itemRow->item->NamaPart ?? '-' }})</span>
                        </div>
                    @endforeach
                </td>
                <td>{{ $firstItem->pic_display ?? '-' }}</td>
                
                <td class="text-center">
                    @if($firstItem->status_label)
                        <span class="badge-revisi">
                            <i class="fas fa-history"></i> {{ $firstItem->status_label }}
                        </span>
                    @else
                        <span style="color: #a4b0be; font-size: 11px;">Original</span>
                    @endif
                </td>

                <td class="text-center">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</td>
                <td class="text-center">
                    <div class="action-buttons-container" style="display: flex; gap: 5px; justify-content: center;">
                        <a href="{{ route('productionschedule.show', $uniqueId) }}" class="btn btn-sm btn-outline">View</a>
                        <a href="{{ route('productionschedule.edit', $uniqueId) }}" class="btn btn-sm btn-primary">Update</a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $finalIdPlan }}')">Delete</button>
                    </div>
                </td>
            </tr>
        @empty
            <tr id="emptyServerRow"><td colspan="7" class="text-center py-5 text-muted">Belum ada data jadwal produksi.</td></tr>
        @endforelse

        {{-- TR DINAMIS CLIENT: Muncul teks biasa murni jika ketikan operator zonk di pencarian --}}
        <tr id="noDataRow" class="row-hidden">
            <td colspan="7" class="text-center py-5 text-muted" style="background-color: #ffffff;">
                Not Found.
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    // --- LOGIC IMPORT SAKTI ---
    document.getElementById('btnImportExcel').onclick = function() {
        Swal.fire({
            title: 'Select Line Type',
            text: 'Format Excel bervariasi tergantung pada jenis line produksi',
            icon: 'question',
            input: 'select',
            inputOptions: {
                'EF': 'E & F Line',
                'K': 'K Line'
            },
            inputPlaceholder: '-- Select Line Type --',
            showCancelButton: true,
            confirmButtonText: 'Next',
            confirmButtonColor: '#e11d2e',
            cancelButtonText: 'Cancel',
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Silakan pilih jalur produksi terlebih dahulu!');
                    return false;
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showUploadModal(result.value);
            }
        });
    };

    function showUploadModal(lineType) {
        Swal.fire({
            title: lineType === 'EF' ? 'Upload Excel (E & F)' : 'Upload Excel (K Line)',
            html: `
                <div onclick="document.getElementById('excelInput').click()" style="border: 2px dashed #e11d2e; padding: 30px; border-radius: 15px; background: #fff5f5; cursor: pointer; text-align: center;">
                    <div id="uploadPrompt">
                        <i class="fas fa-cloud-upload-alt fa-3x" style="color: #e11d2e; margin-bottom: 10px;"></i>
                        <p>Click to select an Excel Schedule file</p>
                    </div>
                    <input type="file" id="excelInput" style="display:none" accept=".xlsx, .xls">
                    
                    <div id="filePreview" style="display:none; text-align: center;">
                        <i class="fas fa-file-excel fa-3x" style="color: #27ae60; margin-bottom: 10px;"></i>
                        <br>
                        <strong id="nameFile" style="font-size:14px; color: #333;">File.xlsx</strong>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'IMPORT',
            confirmButtonColor: '#e11d2e',
            preConfirm: () => {
                const file = document.getElementById('excelInput').files[0];
                if (!file) {
                    // Diubah sekalian ke bahasa Inggris formal agar selaras dengan sistem Lu yang lain
                    Swal.showValidationMessage('Silakan pilih file terlebih dahulu!');
                    return false;
                }
                return file;
            }
        }).then((res) => {
            if (res.isConfirmed) {
                uploadExcel(res.value, lineType);
            }
        });

        document.getElementById('excelInput').onchange = function(e) {
            if (e.target.files.length > 0) {
                // 1. Sembunyikan teks petunjuk awal
                document.getElementById('uploadPrompt').style.display = 'none';
                
                // 2. Tampilkan preview nama file yang dipilih
                document.getElementById('nameFile').innerText = e.target.files[0].name;
                document.getElementById('filePreview').style.display = 'block';
            }
        };
    }

    function uploadExcel(file, lineType) {
        let formData = new FormData();
        formData.append('excel_file', file); 
        formData.append('line_type', lineType); 
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({ title: 'Proses Import...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

        fetch("{{ route('productionschedule.import') }}", {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Success', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Terjadi Kesalahan', data.message, 'error');
            }
        })
        .catch(err => Swal.fire('Error!', 'Terjadi Kesalahan.', 'error'));
    }

    function updateFilter() {
        const date = document.getElementById('filterDate').value;
        const line = document.getElementById('filterLine').value;
        window.location.href = window.location.pathname + '?date=' + date + (line ? '&line=' + line : '');
    }

    // PERBAIKAN SCRIPT SEARCH: Terintegrasi dengan baris pesan kosong dinamis
    function searchTable(v) {
        v = v.toLowerCase().trim();
        let rows = document.querySelectorAll("#scheduleTable tbody tr.schedule-data-row");
        let visibleCount = 0;

        rows.forEach(r => {
            if (r.innerText.toLowerCase().includes(v)) {
                r.classList.remove('row-hidden');
                visibleCount++;
            } else {
                r.classList.add('row-hidden');
            }
        });

        const noDataRow = document.getElementById('noDataRow');
        const emptyServerRow = document.getElementById('emptyServerRow');
        
        if (noDataRow) {
            // Jika pencarian client-side zonk, dan memang ada data asal dari server
            if (visibleCount === 0 && !emptyServerRow) {
                noDataRow.classList.remove('row-hidden');
            } else {
                noDataRow.classList.add('row-hidden');
            }
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda Yakin Ingin Menghapus Data Ini?',
            text: "Data Tersebut Akan Dihapus Secara Permanen dan Tidak Dapat Dipulihkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d2e', 
            cancelButtonColor: '#aaa',  
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ url('productionschedule') }}/" + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Berhasil', res.message, 'success').then(() => { location.reload(); });
                        } else {
                            Swal.fire('Terjadi Kesalahan', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = "Terjadi kesalahan sistem.";
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        Swal.fire('Error!', msg, 'error');
                    }
                });
            }
        });
    }
</script>
@endsection