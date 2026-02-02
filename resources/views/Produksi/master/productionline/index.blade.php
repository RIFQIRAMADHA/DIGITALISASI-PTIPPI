@extends('Produksi.layouts.main')

@section('title', 'Data Production Line')
@section('page-title', 'Data Production Line')

@section('card-actions')
<a href="{{ route('master.productionline.create') }}" class="btn btn-primary">
    + Tambah Production Line
</a>
@endsection

@section('content')

{{-- CSS Tambahan untuk Merapikan Tombol --}}
<style>
    .action-buttons-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px; /* Jarak antar tombol */
        white-space: nowrap; /* Mencegah tombol turun ke bawah */
    }

    /* Memastikan kolom aksi memiliki ruang yang cukup agar tombol sejajar */
    .table-custom td:last-child {
        min-width: 220px;
    }
</style>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Production Line</span>
</div>

<div class="table-toolbar">
    <input type="text"
           class="input-search"
           placeholder="Cari Production Line..."
           onkeyup="searchTable(this.value)">
</div>

<table class="table-custom table-fixed" id="lineTable">
    <colgroup>
        <col style="width: 40%;">  
        <col style="width: 20%;">  
        <col style="width: 15%;">  
        <col style="width: 25%;">  
    </colgroup>

    <thead>
        <tr>
            <th>Production Line</th>
            <th>Shift</th>
            <th>Status</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($line as $row)
        <tr>
            <td>{{ $row->NamaProductionLine }}</td>
            <td>{{ $row->Shift }}</td>
            <td class="text-center">
                <span class="badge {{ $row->Status ? 'badge-success' : 'badge-danger' }}">
                    {{ $row->Status ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>
            <td>
                <div class="action-buttons-container">
                    <a href="{{ route('master.productionline.show', $row->IdProductionLine) }}" 
                       class="btn btn-sm btn-outline">View</a>
                    
                    <a href="{{ route('master.productionline.edit', $row->IdProductionLine) }}" 
                       class="btn btn-sm btn-primary">Edit</a>

                    <form action="{{ route('master.productionline.destroy', $row->IdProductionLine) }}" 
                          method="POST" 
                          class="d-inline form-delete">
                        @csrf
                        @method('DELETE')
                        {{-- Atribut data untuk dibaca oleh SweetAlert --}}
                        <button type="submit" 
                                class="btn btn-sm btn-danger"
                                data-status="{{ $row->Status }}"
                                data-name="{{ $row->NamaProductionLine }}">
                            Delete
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- SEARCH SCRIPT --}}
<script>
function searchTable(value) {
    value = value.toLowerCase();
    document.querySelectorAll("#lineTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
}
</script>

{{-- SWEETALERT LOGIC --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e){
        e.preventDefault();
        
        const btn = form.querySelector('button[type="submit"]');
        const status = btn.getAttribute('data-status'); // Mengambil status (1 atau 0)
        const name = btn.getAttribute('data-name');

        if (status == "0") {
            // Jika data sudah Nonaktif
            Swal.fire({
                title: 'Informasi',
                text: `Data Production Line '${name}' memang sudah dalam status Nonaktif.`,
                icon: 'info',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
        } else {
            // Jika data masih Aktif (Konfirmasi Penonaktifan)
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Data '${name}' akan dinonaktifkan dari sistem.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, Nonaktifkan!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if(result.isConfirmed) form.submit();
            });
        }
    });
});
</script>

{{-- Notifikasi Session --}}
@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Penghapusan Gagal',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Mengerti'
    });
</script>
@endif

@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

@endsection