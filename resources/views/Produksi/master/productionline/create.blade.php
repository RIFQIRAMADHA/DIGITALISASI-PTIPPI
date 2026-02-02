@extends('Produksi.layouts.main')

@section('title', 'Tambah Production Line')
@section('page-title', 'Tambah Production Line')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Production Line</span>
</div>

{{-- SweetAlert untuk Error Validasi --}}
@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        html: `
            <div style="text-align: left;">
                <p>Mohon periksa kembali inputan Anda:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        `,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Perbaiki'
    });
</script>
@endif

<form action="{{ route('master.productionline.store') }}" method="POST" class="form" id="formLine">
    @csrf
    <div class="form-grid">
        <div class="form-group">
            <label>Nama Production Line</label>
            <select name="NamaProductionLine" class="form-select" required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="">Pilih Line</option>
                <option value="Line E" {{ old('NamaProductionLine') == 'Line E' ? 'selected' : '' }}>Line E</option>
                <option value="Line F" {{ old('NamaProductionLine') == 'Line F' ? 'selected' : '' }}>Line F</option>
                <option value="Line K" {{ old('NamaProductionLine') == 'Line K' ? 'selected' : '' }}>Line K</option>
            </select>
        </div>

        <div class="form-group">
            <label>Shift</label>
            <select name="Shift" class="form-select" required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="">Pilih Shift</option>
                <option value="Shift 1" {{ old('Shift') == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                <option value="Shift 2" {{ old('Shift') == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Simpan</button>
        <a href="{{ route('master.productionline.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>

<script>
function confirmSave() {
    Swal.fire({
        title: 'Simpan Data Production Line?',
        text: "Pastikan data production line sudah benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formLine').submit();
        }
    });
}
</script>
@endsection