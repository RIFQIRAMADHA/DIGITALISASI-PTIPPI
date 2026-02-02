@extends('Produksi.layouts.main')

@section('title', 'Data Item Produksi')
@section('page-title', 'Data Item Produksi')

@section('card-actions')
<a href="{{ route('master.itemproduction.create') }}" class="btn btn-primary">
    + Tambah Item Produksi
</a>
@endsection

@section('content')

{{-- Tambahkan SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Item Produksi</span>
</div>

{{-- Tampilkan Notifikasi Success --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

{{-- TOOLBAR --}}
<div class="table-toolbar">
    <input type="text"
           class="input-search"
           placeholder="Cari Item Produksi..."
           onkeyup="searchTable(this.value)">
</div>

<table class="table-custom" id="itemproduksiTable">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Job Number</th>
            <th>Part Number</th>
            <th>Nama Part</th>
            <th>Model</th>
            <th>Gambar</th>
            <th>Status</th>
            <th style="width:220px;">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($item as $row) {{-- Mengubah variabel loop agar tidak bentrok dengan koleksi --}}
        <tr>
            <td>{{ $row->customer->NamaCustomer ?? '-' }}</td>
            <td>{{ $row->JobNumber }}</td>
            <td>{{ $row->PartNumber }}</td>
            <td>{{ $row->NamaPart }}</td>
            <td>{{ $row->Model }}</td>

            {{-- GAMBAR --}}
            <td>
                @if($row->Gambar)
                    <img src="{{ asset('storage/'.$row->Gambar) }}"
                         width="60"
                         class="rounded"
                         style="object-fit: cover; height: 40px;">
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            {{-- STATUS --}}
            <td>
                @if($row->Status == 1)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-danger">Non Aktif</span>
                @endif
            </td>

            {{-- AKSI --}}
            <td>
                <div class="action-group" style="display: flex; gap: 5px;">
                    {{-- VIEW --}}
                    <a href="{{ route('master.itemproduction.show', $row->IdItemProduksi) }}" 
                       class="btn btn-sm btn-outline">View</a>
                    
                    {{-- EDIT --}}
                    <a href="{{ route('master.itemproduction.edit', $row->IdItemProduksi) }}" 
                       class="btn btn-sm btn-primary">Edit</a>

                    {{-- DELETE (Soft Delete/Nonaktifkan) --}}
                    <form action="{{ route('master.itemproduction.destroy', $row->IdItemProduksi) }}" 
                          method="POST" 
                          id="delete-form-{{ $row->IdItemProduksi }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                class="btn btn-sm btn-danger" 
                                onclick="confirmDelete('{{ $row->IdItemProduksi }}', '{{ $row->NamaPart }}')">
                            Delete
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- SCRIPTS --}}
<script>
// Fungsi Pencarian
function searchTable(value) {
    value = value.toLowerCase();
    document.querySelectorAll("#itemproduksiTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
}

// Fungsi Konfirmasi Delete via SweetAlert2
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Item '" + name + "' akan dinonaktifkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
}
</script>

@endsection