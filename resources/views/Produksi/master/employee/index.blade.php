@extends('Produksi.layouts.main')

@section('title', 'Employee Data')
@section('page-title', 'Employee Data')

@section('card-actions')
<a href="{{ route('master.employee.create') }}" class="btn btn-primary">
    + Add Employee
</a>
@endsection

@section('content')

<style>
    /* Mengunci ujung pembungkus tabel agar lancip kotak sempurna */
    #karyawanTable {
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
</style>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Employee</span>
</div>

<div class="table-toolbar">
    <input type="text" class="input-search"
           placeholder="Search Employee..."
           onkeyup="searchTable(this.value)">
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto; width: 100%;">
    <table class="table-custom" id="karyawanTable" style="border-radius: 0 !important; width: 100%; min-width: 800px;">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NRP</th>
                <th>Jabatan</th>
                <th class="text-center" style="width:220px;">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($karyawan as $row)
            <tr class="employee-data-row">
                <td>{{ $row->NamaKaryawan }}</td>
                <td>{{ $row->NRPKaryawan }}</td>
                <td>{{ ucfirst($row->Jabatan) }}</td>
                <td class="text-center">
                    <div style="display: flex; gap: 5px; justify-content: center;">
                        <a href="{{ route('master.employee.show', $row->IdKaryawan) }}"
                           class="btn btn-sm btn-outline">
                            View
                        </a>

                        <a href="{{ route('master.employee.edit', $row->IdKaryawan) }}"
                           class="btn btn-sm btn-primary btn-edit">
                            Update
                        </a>

                        <form action="{{ route('master.employee.destroy', $row->IdKaryawan) }}"
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

            {{-- TR DINAMIS: Muncul teks biasa jika hasil pencarian kosong --}}
            <tr id="noDataRow" class="row-hidden">
                <td colspan="4" class="text-center py-4 text-muted" style="background-color: #ffffff;">
                    Not found.
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- CONTAINER PAGINATION --}}
<div class="pagination-wrapper">
    {{ $karyawan->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>

{{-- SCRIPT SEARCH --}}
<script>
function searchTable(value) {
    value = value.toLowerCase().trim();
    let rows = document.querySelectorAll("#karyawanTable tbody tr.employee-data-row");
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
// 1. NANGKAP PESAN ERROR (Kalau karyawan lagi dipake)
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
            if(result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection