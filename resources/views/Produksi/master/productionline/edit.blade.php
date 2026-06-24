@extends('Produksi.layouts.main')

@section('title', 'Update Production Line')
@section('page-title', 'Update Production Line')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Production Line</span>
</div>

{{-- SweetAlert untuk Error Validasi --}}
@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Terjadi kesalahan',
        html: `
            <div style="text-align: left;">
                <p>Mohon periksa kembali data yang Anda masukkan:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        `,
        confirmButtonColor: '#d33',
        confirmButtonText: 'OK'
    });
</script>
@endif

<form action="{{ route('master.productionline.update', $line->IdProductionLine) }}" method="POST" class="form" id="formEditLine">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-group">
            <label>Nama Production Line <span style="color: red;">*</span></label>
            <input type="text" 
                name="NamaProductionLine" 
                value="{{ old('NamaProductionLine', $line->NamaProductionLine) }}" 
                required maxlength="10">
        </div>

        <div class="form-group">
            <label>- Select Shift - <span style="color: red;">*</span></label>
            <select name="Shift" class="form-select" required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="Shift 1" {{ old('Shift', $line->Shift) == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                <option value="Shift 2" {{ old('Shift', $line->Shift) == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Update</button>
        <a href="{{ route('master.productionline.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function confirmUpdate() {
    Swal.fire({
        title: 'Ubah Data Jalur Produksi?',
        text: "Apakah Anda Yakin Ingin Menyimpan Perubahan Ini?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditLine').submit();
        }
    });
}
</script>
@endsection