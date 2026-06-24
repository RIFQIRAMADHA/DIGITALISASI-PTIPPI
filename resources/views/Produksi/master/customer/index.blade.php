@extends('Produksi.layouts.main')

@section('title', 'Customer Data')
@section('page-title', 'Customer Data')

@section('card-actions')
<a href="{{ route('master.customer.create') }}" class="btn btn-primary">
    + Add Customer
</a>
@endsection

@section('content')

<style>
    /* Mengunci ujung pembungkus agar lancip kotak sempurna */
    .table-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 0 !important;
    }

    /* Class penyembunyi baris yang tidak cocok saat live search */
    .row-hidden {
        display: none !important;
    }

    /* =========================================
       CUSTOM PAGINATION STYLE (RED ASTRA THEME)
       ========================================= */
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        justify-content: center;
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
        /* 🔥 FIX: Paksa semua teks jadi merah */
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
    }
    
    /* Disabled state (Tanda panah < > saat di ujung) */
    .pagination-wrapper .page-item.disabled .page-link { 
        /* 🔥 FIX: Tetep merah tapi dikasih opacity biar kelihatan "mati" */
        color: #f82b3d !important; 
        opacity: 0.5; 
        cursor: not-allowed; 
        background-color: #f9f9f9 !important; 
        border-color: #eee !important;
    }
</style>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Customer</span>
</div>

<div class="table-toolbar">
    <input type="text"
           class="input-search"
           placeholder="Search Customer..."
           onkeyup="searchTable(this.value)">
</div>

{{-- Pembungkus Tabel --}}
<div class="table-scroll-wrapper" style="overflow-x: auto; width: 100%;">
    <table class="table-custom table-fixed" id="customerTable" style="width: 1400px; margin: 0 auto; border-radius: 0 !important;">
        <colgroup>
            <col style="width: 200px;">  {{-- Nama Perusahaan --}}
            <col style="width: 250px;">  {{-- Alamat --}}
            <col style="width: 150px;">  {{-- Nama PIC --}}
            <col style="width: 150px;">  {{-- No Telp --}}
            <col style="width: 200px;">  {{-- Email --}}
            <col style="width: 180px;">  {{-- NPWP --}}
            <col style="width: 220px;">  {{-- Aksi --}}
        </colgroup>

        <thead>
            <tr>
                <th>Nama Perusahaan</th>
                <th>Alamat Perusahaan</th>
                <th>Nama PIC</th>
                <th>Nomor Telepon</th>
                <th>Email</th>
                <th>NPWP</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customer as $row)
            <tr class="customer-data-row">
                <td>{{ $row->NamaCustomer }}</td>
                <td>{{ $row->AlamatCustomer }}</td>
                <td>{{ $row->NamaCustomerPIC }}</td>
                <td>{{ $row->NoTelpCustomer }}</td>
                <td>{{ $row->EmailCustomer }}</td>
                <td>{{ $row->NPWPCustomer }}</td>
                <td class="text-center">
                    <div style="display: flex; gap: 5px; justify-content: center;">
                        <a href="{{ route('master.customer.show', $row->IdCustomer) }}"
                           class="btn btn-sm btn-outline">
                            View
                        </a>

                        <a href="{{ route('master.customer.edit', $row->IdCustomer) }}"
                           class="btn btn-sm btn-primary">
                            Update
                        </a>

                        <form action="{{ route('master.customer.destroy', $row->IdCustomer) }}"
                              method="POST"
                              class="form-delete d-inline" style="margin: 0;">
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

            {{-- TR DINAMIS: Biasa aja tanpa bold dan italic sesuai request lo --}}
            <tr id="noDataRow" class="row-hidden">
                <td colspan="7" class="text-center py-4 text-muted" style="background-color: #ffffff;">
                    Not found.
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 🔥 CONTAINER PAGINATION CUSTOM TULEN --}}
<div class="pagination-wrapper">
    {{ $customer->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>

{{-- SCRIPT SEARCH DENGAN LOGIKA DETEKSI NOTIFIKASI KOSONG --}}
<script>
function searchTable(value) {
    value = value.toLowerCase().trim();
    let rows = document.querySelectorAll("#customerTable tbody tr.customer-data-row");
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

{{-- SCRIPT SWEETALERT (MERAH CERAH IPS ASTRA SINKRON) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// 1. NANGKAP PESAN ERROR DARI CONTROLLER (Kalau Customer lagi dipake)
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Tidak Bisa Dihapus',
        text: "{{ session('error') }}", // 🔥 FIX: Pake double quotes di luar
        confirmButtonColor: '#e11d2e'
    });
@endif

// 2. NANGKAP PESAN SUKSES DARI CONTROLLER
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}", // 🔥 FIX: Pake double quotes di luar
        confirmButtonColor: '#3085d6'
    });
@endif

// 3. KONFIRMASI DELETE
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
            if(result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection