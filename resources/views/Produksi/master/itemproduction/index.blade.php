@extends('Produksi.layouts.main')

@section('title', 'Production Item Data')
@section('page-title', 'Production Item Data')

@section('card-actions')
<a href="{{ route('master.itemproduction.create') }}" class="btn btn-primary">
    + Production Item
</a>
@endsection

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ✅ MENGUNCI UJUNG TABEL LANCIP KOTAK & STICKY CONTAINER */
    .table-responsive {
        width: 100%;
        max-height: 500px; /* 1. WAJIB DIKASIH BATAS TINGGI BIAR BISA DI-SCROLL INTERNAL */
        overflow-y: auto;  /* Aktifkan scroll vertikal */
        overflow-x: auto;  /* Aktifkan scroll horizontal */
        -webkit-overflow-scrolling: touch;
        background: white;
        border-radius: 0 !important; /* PAKSA LANCIP */
        margin-top: 10px;
        border: 1px solid #dee2e6;
        position: relative;
    }

    /* 2. FORCE BORDER SEPARATE BIAR STICKY HEADER JALAN STABIL */
    #itemproduksiTable {
        border-collapse: separate !important;
        border-spacing: 0;
        width: 100%;
    }

    /* 3. KUNCI HEADER DI ATAS (STICKY THEAD TH) */
    #itemproduksiTable thead th {
        position: sticky;
        top: 0;
        background: #bb2121; /* Samakan dengan warna tema merah Lu */
        color: white;
        z-index: 10;
        padding: 12px;
        border-bottom: 2px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        white-space: nowrap;
    }

    /* Pastikan border kiri untuk kolom pertama tetap rapi */
    #itemproduksiTable thead th:first-child {
        border-left: 1px solid #dee2e6;
    }

    /* Atur border sel data di tbody agar sinkron dengan separate layout */
    #itemproduksiTable tbody td {
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        padding: 10px;
    }

    #itemproduksiTable tbody td:first-child {
        border-left: 1px solid #dee2e6;
    }

    .nama-part-column {
        min-width: 250px;
        max-width: 400px;
        white-space: normal;
        word-break: break-word;
    }

    .table-toolbar {
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        gap: 15px; 
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .table-toolbar {
            flex-direction: column;
            align-items: stretch !important;
        }
        .input-search {
            width: 100% !important;
        }
    }

    /* =========================================
       CUSTOM PAGINATION STYLE (RED ASTRA THEME)
       ========================================= */
    .pagination-wrapper {
            margin-top: 30px;
            display: flex;
            /* 🔥 FIX: Ubah jadi column dan center biar teks info & tombol angka numpuk di tengah sempurna */
            flex-direction: column;
            align-items: center;
            gap: 12px; /* Kasih jarak antara teks info dan tombol angka */
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

    /* Responsive untuk HP: numpuk ke bawah */
    @media (max-width: 768px) {
        .pagination-wrapper {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>

<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Item Production</span>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success', // Mengikuti standarisasi Bahasa Inggris sukses Lu kemarin
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

<div class="table-toolbar">
    <input type="text"
           id="searchInput"
           class="input-search"
           placeholder="Search Item..."
           value="{{ request('search') }}"
           oninput="liveSearch()" 
           style="width: 300px;">

    <select class="input-search" id="filterStatus" onchange="applyFilters()" style="width: 200px; cursor: pointer;">
        <option value="aktif" {{ request('status') == 'aktif' || !request('status') ? 'selected' : '' }}>🟢 Active Data</option>
        <option value="non-aktif" {{ request('status') == 'non-aktif' ? 'selected' : '' }}>🔴 Inactive Data</option>
    </select>
</div>

<div class="table-responsive">
    <table class="table-custom" id="itemproduksiTable" style="border-radius: 0 !important;">
        <thead>
            <tr>
                <th class="text-center">Customer</th>
                <th class="text-center">Job Number</th>
                <th class="text-center">Part Number</th>
                <th class="nama-part-column" style="text-align: center;">Nama Part</th>
                <th class="text-center">Model</th>
                <th class="text-center">CT (Sec)</th>
                <th class="text-center">Berat (Kg)</th>
                <th class="text-center">Gambar</th>
                <th class="text-center" style="width:200px;">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($item as $row)
            <tr>
                <td class="text-center">{{ $row->customer->NamaCustomer ?? '-' }}</td>
                <td class="text-center" style="font-weight: 800;">{{ $row->JobNumber }}</td>
                <td class="text-center">{{ $row->PartNumber }}</td>
                <td class="nama-part-column" style="font-weight:600; text-align: left;">{{ $row->NamaPart }}</td>
                <td class="text-center">{{ $row->Model }}</td>
                <td class="text-center" style="font-weight: 800; color: #2d3436;">{{ number_format($row->CT ?? 0, 2) }}</td>
                <td class="text-center" style="font-weight: 800; color: #2d3436;">{{ number_format($row->Berat ?? 0, 2) }}</td>
                <td class="text-center">
                    @if($row->Gambar)
                        <div style="display: flex; justify-content: center;">
                            <img src="{{ asset('storage/'.$row->Gambar) }}" width="60" class="rounded" style="object-fit: cover; height: 40px; border: 1px solid #ddd;">
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="action-group" style="display: flex; gap: 5px; justify-content: center;">
                        <a href="{{ route('master.itemproduction.show', $row->IdItemProduksi) }}" class="btn btn-sm btn-outline">View</a>
                        <a href="{{ route('master.itemproduction.edit', $row->IdItemProduksi) }}" class="btn btn-sm btn-primary">Update</a>
                        @if($row->Status == 1)
                            <form action="{{ route('master.itemproduction.destroy', $row->IdItemProduksi) }}" method="POST" id="delete-form-{{ $row->IdItemProduksi }}" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $row->IdItemProduksi }}', '{{ $row->NamaPart }}')">Inactive</button>
                            </form>
                        @else
                            <form action="{{ route('master.itemproduction.update', $row->IdItemProduksi) }}" method="POST" style="margin: 0;">
                                @csrf @method('PUT')
                                <input type="hidden" name="restore" value="1">
                                <button type="submit" class="btn btn-sm btn-success" style="background: #27ae60; color: white; border: none;">Active</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4 text-muted" style="background-color: #ffffff;">
                    Not Found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    <div>
        {{ $item->appends(['status' => request('status'), 'search' => request('search')])->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
let searchTimeout;

function applyFilters() {
    const status = document.getElementById('filterStatus').value;
    const search = document.getElementById('searchInput').value.trim();
    
    let url = "{{ route('master.itemproduction.index') }}?status=" + status;
    if(search) url += "&search=" + encodeURIComponent(search);
    
    window.location.href = url;
}

function liveSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        applyFilters();
    }, 700); 
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: "The item '" + name + "' will be deactivated!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Inactive',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
}

window.onload = function() {
    const input = document.getElementById('searchInput');
    if (input && input.value.length > 0) {
        input.focus();
        const valLength = input.value.length;
        input.setSelectionRange(valLength, valLength);
    }
};
</script>

@endsection