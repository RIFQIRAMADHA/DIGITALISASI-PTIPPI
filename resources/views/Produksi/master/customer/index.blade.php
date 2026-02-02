@extends('Produksi.layouts.main')

@section('title', 'Data Customer')
@section('page-title', 'Data Customer')

@section('card-actions')
<a href="{{ route('master.customer.create') }}" class="btn btn-primary">
    + Tambah Customer
</a>
@endsection

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Customer</span>
</div>

<div class="table-toolbar">
    <input type="text"
           class="input-search"
           placeholder="Cari Customer..."
           onkeyup="searchTable(this.value)">
</div>

<table class="table-custom table-fixed" id="customerTable">

    {{-- 🔥 KUNCI RAPINYA DI SINI --}}
    <colgroup>
        <col style="width: 200px;">  {{-- Nama Perusahaan --}}
        <col style="width: 220px;">  {{-- Alamat --}}
        <col style="width: 130px;">  {{-- Nama PIC --}}
        <col style="width: 130px;">  {{-- No Telp --}}
        <col style="width: 240px;">  {{-- Email --}}
        <col style="width: 180px;">  {{-- NPWP --}}
        <col style="width: 100px;">  {{-- Status --}}
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
            <th>Status</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($customer as $row)
        <tr>
            <td>{{ $row->NamaCustomer }}</td>
            <td>{{ $row->AlamatCustomer }}</td>
            <td>{{ $row->NamaCustomerPIC }}</td>
            <td>{{ $row->NoTelpCustomer }}</td>
            <td>{{ $row->EmailCustomer }}</td>
            <td>{{ $row->NPWPCustomer }}</td>
            <td class="text-center">
                <span class="badge {{ $row->Status ? 'badge-success' : 'badge-danger' }}">
                    {{ $row->Status ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>
            <td class="action-group">
                <a href="{{ route('master.customer.show', $row->IdCustomer) }}"
                   class="btn btn-sm btn-outline">
                    View
                </a>

                <a href="{{ route('master.customer.edit', $row->IdCustomer) }}"
                   class="btn btn-sm btn-primary btn-edit">
                    Edit
                </a>

                <form action="{{ route('master.customer.destroy', $row->IdCustomer) }}"
                      method="POST"
                      class="form-delete d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- SEARCH --}}
<script>
function searchTable(value) {
    value = value.toLowerCase();
    document.querySelectorAll("#customerTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
}
</script>

{{-- SWEETALERT --}}
<script>
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c62828',
            confirmButtonText: 'Ya, Nonaktifkan',
            cancelButtonText: 'Batal'
        }).then(result => {
            if(result.isConfirmed) form.submit();
        });
    });
});


</script>

@endsection
