@extends('Produksi.layouts.main')

@section('title', 'Edit Production Line')
@section('page-title', 'Edit Production Line')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Production Line</span>
</div>

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal Update',
        html: `
            <div style="text-align: left;">
                <p>Gagal menyimpan perubahan:</p>
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

<form action="{{ route('master.productionline.update', $line->IdProductionLine) }}" method="POST" class="form" id="formEditLine">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-group">
            <label>ID Production Line</label>
            <input type="text" value="{{ $line->IdProductionLine }}" disabled>
        </div>

        <div class="form-group">
            <label>Nama Production Line</label>
            <select name="NamaProductionLine" class="form-select" required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="Line E" {{ old('NamaProductionLine', $line->NamaProductionLine) == 'Line E' ? 'selected' : '' }}>Line E</option>
                <option value="Line F" {{ old('NamaProductionLine', $line->NamaProductionLine) == 'Line F' ? 'selected' : '' }}>Line F</option>
                <option value="Line K" {{ old('NamaProductionLine', $line->NamaProductionLine) == 'Line K' ? 'selected' : '' }}>Line K</option>
            </select>
        </div>

        <div class="form-group">
            <label>Shift</label>
            <select name="Shift" class="form-select" required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="Shift 1" {{ old('Shift', $line->Shift) == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                <option value="Shift 2" {{ old('Shift', $line->Shift) == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
            </select>
        </div>

        {{-- MODIFIKASI: Penambahan Input Status --}}
        <div class="form-group">
            <label>Status</label>
            <select name="Status" class="form-control">
                <option value="1" {{ old('Status', $line->Status) == 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('Status', $line->Status) == 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Perbarui</button>
        <a href="{{ route('master.productionline.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>

<script>
function confirmUpdate() {
    Swal.fire({
        title: 'Update Data Production Line?',
        text: "Apakah Anda yakin ingin menyimpan perubahan?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditLine').submit();
        }
    });
}
</script>
@endsection