@extends('Produksi.layouts.main')

@section('title', 'Data Karyawan')
@section('page-title', 'Data Karyawan')

@section('card-actions')
<a href="{{ route('master.employee.create') }}" class="btn btn-primary">
    + Tambah Karyawan
</a>
@endsection

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Employee</span>
</div>

<div class="table-toolbar">
    <input type="text" class="input-search"
           placeholder="Cari karyawan..."
           onkeyup="searchTable(this.value)">
</div>

<table class="table-custom" id="karyawanTable">
<thead>
<tr>
    <th>ID</th>
    <th>Nama</th>
    <th>NRP</th>
    <th>Jabatan</th>
    <th>Status</th>
    <th style="width:220px;">Aksi</th>
</tr>
</thead>

<tbody>
@foreach ($karyawan as $row)
<tr>
    <td>{{ $row->idKaryawan }}</td>
    <td>{{ $row->NamaKaryawan }}</td>
    <td>{{ $row->NRPKaryawan }}</td>
    <td>{{ ucfirst($row->Jabatan) }}</td>
    <td>
        <span class="badge {{ $row->Status ? 'badge-success' : 'badge-danger' }}">
            {{ $row->Status ? 'Aktif' : 'Nonaktif' }}
        </span>
    </td>
    <td class="action-group">

        <a href="{{ route('master.employee.show', $row->idKaryawan) }}"
           class="btn btn-sm btn-outline">
            View
        </a>

        <a href="{{ route('master.employee.edit', $row->idKaryawan) }}"
           class="btn btn-sm btn-primary btn-edit">
            Edit
        </a>

        <form action="{{ route('master.employee.destroy', $row->idKaryawan) }}"
              method="POST"
              class="form-delete"
              style="display:inline">
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

<script>
function searchTable(value) {
    value = value.toLowerCase();
    document.querySelectorAll("#karyawanTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
}
</script>

<script>
/* DELETE POPUP */
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin ingin menghapus data ini?',
            text: 'Data akan terhapus permanen dan tidak dapat dipulihkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c62828',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Nonaktifkan'
        }).then(result => {
            if(result.isConfirmed){
                form.submit();
            }
        });
    });
});

/* EDIT POPUP */
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.preventDefault();
        const url = this.href;
        Swal.fire({
            title: 'Apakah Anda yakin ingin mengubah data ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Lanjut',
            cancelButtonText: 'Batal'
        }).then(r => {
            if(r.isConfirmed) window.location.href = url;
        });
    });
});
</script>

@endsection
