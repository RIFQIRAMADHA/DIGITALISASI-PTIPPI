@extends('Produksi.layouts.main')

@section('title', 'Tambah Item Produksi')
@section('page-title', 'Tambah Item Produksi')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Item Produksi</span>
</div>

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        html: '<div style="text-align: left;"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Perbaiki'
    });
</script>
@endif

<form action="{{ route('master.itemproduction.store') }}" method="POST" id="formItem" class="form" enctype="multipart/form-data">
    @csrf
    <div class="form-grid">
        <div class="form-group">
            <label>Customer</label>
            <select name="IdCustomer" required>
                <option value="">-- Pilih Customer --</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->IdCustomer }}" {{ old('IdCustomer') == $customer->IdCustomer ? 'selected' : '' }}>
                        {{ $customer->NamaCustomer }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Job Number</label>
            <input type="text" name="JobNumber" value="{{ old('JobNumber') }}" required>
        </div>

        <div class="form-group">
            <label>Part Number</label>
            <input type="text" name="PartNumber" value="{{ old('PartNumber') }}" required>
        </div>

        <div class="form-group">
            <label>Nama Part</label>
            <input type="text" name="NamaPart" value="{{ old('NamaPart') }}" required>
        </div>

        <div class="form-group">
            <label>Model</label>
            <input type="text" name="Model" value="{{ old('Model') }}" required>
        </div>

        <div class="form-group">
            <label>Gambar</label>
            <input type="file" name="Gambar" id="fileGambar" accept="image/*">
            <small style="color: red">* Wajib mengunggah gambar produk</small>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Simpan</button>
        <a href="{{ route('master.itemproduction.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>

<script>
function confirmSave() {
    const fileInput = document.getElementById('fileGambar');
    
    if (fileInput.files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Gambar Kosong',
            text: 'Harap pilih gambar produk terlebih dahulu sebelum menyimpan!',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    Swal.fire({
        title: 'Simpan Data?',
        text: "Pastikan semua data item yang dimasukkan sudah benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formItem').submit();
        }
    });
}
</script>
@endsection