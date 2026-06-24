@extends('Produksi.layouts.main')

@section('title', 'QPR Data')
@section('page-title', 'QPR Data')

@section('card-actions')
<div class="schedule-actions">    
    <a href="{{ route('report.qpr.create') }}" class="btn-custom btn-add-schedule" style="background-color: #4361ee; color: #fff; text-decoration: none;">
        <i class="fas fa-plus"></i> + Add QPR
    </a>
</div>
@endsection

@section('content')

<style>
    /* ✅ STYLING TABEL BAWAAN LU (TIDAK DIUBAH) */
    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #eee;
        border-radius: 0 !important; 
        margin-top: 15px;
    }
    #scheduleTable { min-width: 1200px; }
    
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

    /* 1. Import Inter dengan variasi weight yang lengkap */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    /* 2. Standarisasi Global */
    #qpr-container, 
    #qpr-container input, 
    #qpr-container select, 
    #qpr-container textarea, 
    #qpr-container button,
    .breadcrumb {
        font-family: 'Inter', sans-serif !important;
        font-size: 13px !important;
        letter-spacing: -0.01em;
        -webkit-font-smoothing: antialiased;
    }

    /* 3. Tabel QPR */
    #qprTable {
        width: 100%;
        border-collapse: collapse;
        border-radius: 0 !important;
    }
    
    #qprTable thead th {
        background-color: #b91c1c !important; 
        color: white !important;
        font-weight: 700;
        padding: 12px 8px;
        border: 1px solid #a81919;
        text-transform: uppercase;
        font-size: 11px !important;
        text-align: center;
    }

    .table-custom tbody td {
        vertical-align: middle;
        border: 1px solid #eee;
        color: #1f2937;
        padding: 10px 8px;
        font-size: 13px !important;
    }

    .table-scroll-wrapper {
        border-radius: 0 !important;
    }

    /* 4. Penomoran & ID */
    .qpr-number-wrapper {
        display: inline-block;
        max-width: 180px; 
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background: #f3f4f6;
        padding: 4px 10px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        font-weight: 600;
        color: #374151;
    }

    /* 5. Komponen Form (Input & Label) */
    .form-group label {
        font-weight: 600 !important;
        color: #4b5563;
        margin-bottom: 6px;
        font-size: 13px !important;
    }

    /* 6. Status & Warna */
    .badge-shift {
        background: #e0f2fe;
        color: #0369a1;
        padding: 3px 10px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 11px !important;
    }
    .font-bold-inter { font-weight: 700 !important; }
    .text-rework { color: #2563eb; font-weight: 700; }
    .text-reject { color: #dc2626; font-weight: 700; }

    /* 7. Tombol Aksi */
    .btn {
        font-weight: 600 !important;
        font-size: 12px !important;
        font-family: 'Inter', sans-serif !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px;
        height: 30px;
        box-sizing: border-box;
    }
    .action-buttons-container {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
    }

    /* =========================================================
       🔥 STYLING RESPONSIVE TOMBOL HEADER
       ========================================================= */
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

    @media (max-width: 850px) {
        .card-header, .d-flex { flex-wrap: wrap !important; }
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
       🔥 STYLING FILTER SAKTI (MENGGUNAKAN ID)
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
        min-width: 250px;
    }

    #filter-toolbar-sakti input {
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

    /* ✅ KUNCI UKURAN TANGGAL DI LAYAR NORMAL BIAR GAK KEPANJANGAN */
    #filter-toolbar-sakti input#filterDate {
        max-width: 220px !important;
        flex: 0 0 220px !important;
    }

    #filter-toolbar-sakti input.search-sakti {
        max-width: 400px; 
    }

    /* ✅ RESPONSIVE MODE SPLIT / HP */
    @media (max-width: 992px) {
        #filter-toolbar-sakti, #filter-toolbar-sakti .grup-kiri-sakti {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Lepas kunci tanggal dan search biar bisa full 100% pas di-split */
        #filter-toolbar-sakti input,
        #filter-toolbar-sakti input#filterDate {
            display: block !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: none !important;
            height: 60px !important; 
            min-height: 60px !important; 
            font-size: 16px !important; 
            margin-bottom: 15px !important; 
        }
    }

    /* =========================================
       🔥 CUSTOM PAGINATION STYLE (RED ASTRA THEME)
       ========================================= */
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    .pagination-wrapper .pagination { 
        display: flex; 
        justify-content: center; 
        gap: 5px; 
        list-style: none; 
        padding: 0; 
        margin: 0; 
    }
    
    .pagination-wrapper .page-item .page-link { 
        padding: 8px 16px; 
        border-radius: 8px !important; 
        border: 1px solid #ddd; 
        color: #f82b3d !important; 
        text-decoration: none; 
        font-weight: 600; 
        transition: all 0.3s; 
        background-color: #fff;
    }
    
    .pagination-wrapper .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: #ffe6e8;
        border-color: #f82b3d;
        color: #f82b3d !important;
    }

    .pagination-wrapper .page-item.active .page-link { 
        background-color: #f82b3d !important; 
        color: #fff !important; 
        border-color: #f82b3d !important; 
        z-index: 3;
    }
    
    .pagination-wrapper .page-item.disabled .page-link { 
        color: #f82b3d !important; 
        opacity: 0.5; 
        cursor: not-allowed; 
        background-color: #f9f9f9 !important; 
        border-color: #eee !important;
        pointer-events: none;
    }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Report</span> <span class="separator">></span>
    <span class="active">QPR</span>
</div>

<div id="filter-toolbar-sakti">
    <div class="grup-kiri-sakti">
        <input type="date" id="filterDate" class="input-sakti" value="{{ request('date') }}" onchange="updateFilter()" style="border-radius: 8px !important; border: 1px solid #ced4da !important;">
    </div>
    
    <input type="text" class="input-sakti search-sakti" placeholder="Search Item / Part..." onkeyup="searchByItem(this.value)" style="border-radius: 8px !important; border: 1px solid #ced4da !important;">
</div>

<div class="info-header" style="margin-top: 15px; margin-bottom: 10px;">
    <p class="text-muted" style="font-size: 13px; font-family: 'Inter', sans-serif;">
        @if(request('date'))
            Menampilkan data QPR Tanggal: <strong class="font-bold-inter">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>
        @else
            Menampilkan <strong class="font-bold-inter">Semua Data QPR</strong>
        @endif
    </p>
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto; border: 1px solid #ddd; border-radius: 0 !important; width: 100%;">
    <table class="table-custom table-fixed" id="qprTable" style="min-width: 1800px; width: 100%; border-radius: 0 !important;">
        <colgroup>
            <col style="width: 50px;">
            <col style="width: 250px;">
            <col style="width: 110px;">
            <col style="width: 180px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 150px;">
            <col style="width: 90px;">
            <col style="width: 100px;">
            <col style="width: 120px;">
            <col style="width: 130px;">
            <col style="width: 150px;">
            <col style="width: 210px;">
        </colgroup>

        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Item / Part Name</th>
                <th class="text-center">Tgl Prod</th>
                <th class="text-center">No QPR</th>
                <th class="text-center">Rework</th>
                <th class="text-center">Reject</th>
                <th>Lokasi Kejadian</th>
                <th class="text-center">Shift</th>
                <th class="text-center">Stock IPPI</th>
                <th class="text-center">Rencana Prod</th>
                <th class="text-center">Proses Repair</th>
                <th>Referensi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $index => $row)
            <tr class="qpr-data-row">
                <td class="text-center text-muted">{{ $data->firstItem() + $index }}</td>
                
                <td class="font-bold-inter">
                    {{ $row->inputHarian && $row->inputHarian->item ? $row->inputHarian->item->NamaPart : 'Item Tidak Ditemukan / Data Terhapus' }}
                </td>

                <td class="text-center">
                    {{ $row->inputHarian ? \Carbon\Carbon::parse($row->inputHarian->TanggalProduksi)->format('d/m/Y') : \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}
                </td>

                <td class="text-center">
                    <div class="qpr-number-wrapper font-bold-inter" title="{{ $row->IdQpr }}">
                        {{ $row->IdQpr }}
                    </div>
                </td>

                <td class="text-center text-rework">{{ number_format($row->Rework, 0) }}</td>
                <td class="text-center text-reject">{{ number_format($row->Reject, 0) }}</td>
                <td>{{ $row->LokasiKejadian }}</td>

                <td class="text-center">
                    <span class="badge-shift">
                        @if($row->inputHarian)
                            {{ $row->inputHarian->Shift ?? ($row->inputHarian->productionLine->Shift ?? '-') }}
                        @else
                            -
                        @endif
                    </span>
                </td>

                <td class="text-center">{{ number_format($row->Stok, 0) }}</td>
                <td class="text-center text-muted">{{ $row->RencanaProduksi ? \Carbon\Carbon::parse($row->RencanaProduksi)->format('d/m/Y') : '-' }}</td>
                <td class="text-center"><strong class="font-bold-inter">{{ $row->ProsesRepair ?? '-' }}</strong></td>
                <td style="font-size: 11px; color: #777;">{{ $row->DocReferensi }}</td>
                <td class="text-center">
                    <div class="action-buttons-container">
                        <a href="{{ route('report.qpr.show', urlencode($row->IdQpr)) }}" class="btn btn-sm btn-outline-secondary font-bold-inter">View</a>
                        <a href="{{ route('report.qpr.edit', urlencode($row->IdQpr)) }}" class="btn btn-sm btn-primary font-bold-inter">Update</a>
                        <a href="{{ route('report.qpr.export.pdf', urlencode($row->IdQpr)) }}" class="btn btn-sm btn-danger font-bold-inter" target="_blank">
                            <i class="fas fa-file-pdf"></i> Export
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="emptyServerRow">
                <td colspan="13" class="text-center py-5 text-muted">Data tidak ditemukan.</td>
            </tr>
            @endforelse

            <tr id="noDataRow" class="row-hidden">
                <td colspan="13" class="text-center py-5 text-muted" style="background-color: #ffffff;">
                    Data tidak ditemukan.
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 🔥 FIX POSISI PAGINASI: Di luar tabel --}}
<div class="pagination-wrapper">
    <div>
        {{-- Kita gunakan format view bootstrap-4 untuk memicu CSS custom merah kita di atas --}}
        {{ $data->appends(['date' => request('date')])->links('pagination::bootstrap-4') }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateFilter() {
    const date = document.getElementById('filterDate').value;
    let url = `{{ route('report.qpr.index') }}`;
    if(date) url += `?date=${date}`;
    window.location.href = url;
}

function searchByItem(val) {
    val = val.toLowerCase().trim();
    const rows = document.querySelectorAll("#qprTable tbody tr.qpr-data-row");
    let visibleCount = 0;
    
    rows.forEach(row => {
        const itemCell = row.cells[1]; 
        if (itemCell) {
            const text = itemCell.textContent.toLowerCase();
            if (text.includes(val)) {
                row.classList.remove('row-hidden');
                visibleCount++;
            } else {
                row.classList.add('row-hidden');
            }
        }
    });

    const noDataRow = document.getElementById('noDataRow');
    const emptyServerRow = document.getElementById('emptyServerRow');

    if (noDataRow) {
        if (visibleCount === 0 && !emptyServerRow) {
            noDataRow.classList.remove('row-hidden');
        } else {
            noDataRow.classList.add('row-hidden');
        }
    }

    // Sembunyikan paginasi saat live search jalan biar gak bingung
    const paginWrapper = document.querySelector('.pagination-wrapper');
    if(paginWrapper) {
        paginWrapper.style.display = (val.length > 0) ? 'none' : 'flex';
    }
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda Yakin Ingin Menghapus Data Ini?',
        text: "Data Tersebut Akan Dihapus Secara Permanen dan Tidak Dapat Dipulihkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/report/qpr/destroy/${id}`, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json' 
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endsection