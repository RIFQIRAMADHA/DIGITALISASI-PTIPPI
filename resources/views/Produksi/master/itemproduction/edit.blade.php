@extends('Produksi.layouts.main')

@section('title', 'Edit Item Produksi')
@section('page-title', 'Edit Item Produksi')

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

<form action="{{ route('master.itemproduction.update', $item->IdItemProduksi) }}" method="POST" id="formEditItem" class="form" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-group">
            <label>ID Item Produksi</label>
            <input type="text" value="{{ $item->IdItemProduksi }}" disabled>
        </div>

        <div class="form-group">
            <label>Customer</label>
            <select name="IdCustomer" required>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->IdCustomer }}" {{ (old('IdCustomer', $item->IdCustomer) == $customer->IdCustomer) ? 'selected' : '' }}>
                        {{ $customer->NamaCustomer }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Job Number</label>
            <input type="text" name="JobNumber" value="{{ old('JobNumber', $item->JobNumber) }}" required>
        </div>

        <div class="form-group">
            <label>Part Number</label>
            <input type="text" name="PartNumber" value="{{ old('PartNumber', $item->PartNumber) }}" required>
        </div>

        <div class="form-group">
            <label>Nama Part</label>
            <input type="text" name="NamaPart" value="{{ old('NamaPart', $item->NamaPart) }}" required>
        </div>

        <div class="form-group">
            <label>Model</label>
            <input type="text" name="Model" value="{{ old('Model', $item->Model) }}" required>
        </div>

        <div class="form-group">
            <label>Gambar</label>
            <input type="file" name="Gambar" id="fileGambar" accept="image/*">
            @if ($item->Gambar)
                <div id="existingImage" style="margin-top: 10px;">
                    <small>Gambar saat ini:</small><br>
                    <img src="{{ asset('storage/'.$item->Gambar) }}" alt="Preview" style="max-width: 150px; border-radius: 5px; margin-top: 5px;">
                    <input type="hidden" id="hasExistingImage" value="1">
                </div>
            @else
                <input type="hidden" id="hasExistingImage" value="0">
                <small style="color: red; display: block;">* Item ini belum memiliki gambar</small>
            @endif
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="Status">
                <option value="1" {{ old('Status', $item->Status) == 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('Status', $item->Status) == 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Perbarui</button>
        <a href="{{ route('master.itemproduction.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>

<script>
function confirmUpdate() {
    const fileInput = document.getElementById('fileGambar');
    const hasImage = document.getElementById('hasExistingImage').value;

    if (fileInput.files.length === 0 && hasImage === "0") {
        Swal.fire({
            icon: 'warning',
            title: 'Gambar Wajib',
            text: 'Item ini belum memiliki gambar. Harap unggah gambar terlebih dahulu!',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    Swal.fire({
        title: 'Update Data?',
        text: "Apakah Anda yakin ingin menyimpan perubahan data ini?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditItem').submit();
        }
    });
}
</script>
@endsection