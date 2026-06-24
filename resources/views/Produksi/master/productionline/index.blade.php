@extends('Produksi.layouts.main')

@section('title', 'Production Line Data')
@section('page-title', 'Production Line Data')

@section('card-actions')
<a href="{{ route('master.productionline.create') }}" class="btn btn-primary">
    + Add Production Line
</a>
@endsection

@section('content')

<style>
    .action-buttons-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .table-custom td:last-child {
        min-width: 220px;
    }

    #lineTable {
        border-radius: 0 !important;
    }

    .row-hidden {
        display: none !important;
    }

    /* =========================================
       CUSTOM PAGINATION STYLE (RED ASTRA THEME)
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
    
    /* Hover effect */
    .pagination-wrapper .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: #ffe6e8;
        border-color: #f82b3d;
        color: #f82b3d !important;
    }

    /* Active state (Halaman yang lagi dibuka) */
    .pagination-wrapper .page-item.active .page-link { 
        background-color: #f82b3d !important; 
        color: #fff !important; 
        border-color: #f82b3d !important; 
        z-index: 3;
    }
    
    /* Disabled state (Tanda panah < > saat di ujung) */
    .pagination-wrapper .page-item.disabled .page-link { 
        color: #f82b3d !important; 
        opacity: 0.5; 
        cursor: not-allowed; 
        background-color: #f9f9f9 !important; 
        border-color: #eee !important;
        pointer-events: none;
    }

    /* 🔥 MEDIA QUERY UNTUK SPLIT SCREEN RAPI */
    @media (max-width: 992px) {
        .table-toolbar {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        
        .input-search {
            width: 100% !important;
        }

        .table-custom {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .action-buttons-container {
            justify-content: flex-start;
        }
    }
</style>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Production Line</span>
</div>

<div class="table-toolbar">
    <input type="text"
           class="input-search"
           placeholder="Search Production Line..."
           onkeyup="searchTable(this.value)">
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto; width: 100%;">
    <table class="table-custom table-fixed" id="lineTable" style="border-radius: 0 !important; width: 100%;">
        <colgroup>
            <col style="width: 45%;">  
            <col style="width: 25%;">  
            <col style="width: 30%;">  
        </colgroup>

        <thead>
            <tr>
                <th>Production Line</th>
                <th>Shift</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($line as $row)
            <tr class="line-data-row">
                <td>{{ $row->NamaProductionLine }}</td>
                <td>{{ $row->Shift }}</td>
                <td>
                    <div class="action-buttons-container">
                        <a href="{{ route('master.productionline.show', $row->IdProductionLine) }}" 
                           class="btn btn-sm btn-outline">View</a>
                        
                        <a href="{{ route('master.productionline.edit', $row->IdProductionLine) }}" 
                           class="btn btn-sm btn-primary">Update</a>

                        <form action="{{ route('master.productionline.destroy', $row->IdProductionLine) }}" 
                              method="POST" 
                              class="form-delete"
                              style="display:inline; margin: 0;">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach

            <tr id="noDataRow" class="row-hidden">
                <td colspan="3" class="text-center py-4 text-muted" style="background-color: #ffffff;">
                    Not found.
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 🔥 CONTAINER PAGINATION CUSTOM DI TENGAH --}}
<div class="pagination-wrapper">
    <div>
        {{ $line->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
function searchTable(value) {
    value = value.toLowerCase().trim();
    let rows = document.querySelectorAll("#lineTable tbody tr.line-data-row");
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.innerText.toLowerCase().includes(value)) {
            row.classList.remove('row-hidden');
            visibleCount++;
        } else {
            row.classList.add('row-hidden');
        }
    });

    const noDataRow = document.getElementById('noDataRow');
    if (noDataRow) {
        if (visibleCount === 0) {
            noDataRow.classList.remove('row-hidden');
        } else {
            noDataRow.classList.add('row-hidden');
        }
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// 1. NANGKAP PESAN ERROR (Kalau Jalur Produksi lagi dipake)
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Tidak Bisa Dihapus',
        text: "{!! session('error') !!}",
        confirmButtonColor: '#e11d2e'
    });
@endif

// 2. NANGKAP PESAN SUKSES
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        confirmButtonColor: '#3085d6'
    });
@endif

// 3. KONFIRMASI DELETE SEBELUM SUBMIT
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda Yakin Ingin Menghapus Data Ini?',
            text: 'Data Tersebut Akan Dihapus Secara Permanen dan Tidak Dapat Dipulihkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d2e',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if(result.isConfirmed){
                form.submit();
            }
        });
    });
});
</script>

@endsection