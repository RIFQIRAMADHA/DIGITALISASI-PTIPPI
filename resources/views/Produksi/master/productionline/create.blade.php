@extends('Produksi.layouts.main')

@section('title', 'Add Production Line')
@section('page-title', 'Add Production Line')

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

<form action="{{ route('master.productionline.store') }}" method="POST" class="form" id="formLine">
    @csrf
    <div class="form-grid">
        <div class="form-group">
            <label>Nama Production Line <span style="color: red;">*</span></label>
            <input type="text" 
                name="NamaProductionLine" 
                value="{{ old('NamaProductionLine') }}" 
                placeholder="Ex: Line E"
                required maxlength="10">
        </div>

        <div class="form-group">
            <label>Shift <span style="color: red;">*</span></label>
            <select name="Shift" class="form-select" required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="">- Select Shift -</option>
                <option value="Shift 1" {{ old('Shift') == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                <option value="Shift 2" {{ old('Shift') == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Save</button>
        <a href="{{ route('master.productionline.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function confirmSave() {
    Swal.fire({
        title: 'Simpan Data Jalur Produksi?',
        text: "Pastikan Semua Data Yang Dimasukkan Sudah Benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formLine').submit();
        }
    });
}
</script>
@endsection