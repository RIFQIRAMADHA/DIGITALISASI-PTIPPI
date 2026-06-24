@extends('Produksi.layouts.main')

@section('title', 'Add Item Production')
@section('page-title', 'Add Item Production')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* CSS TAMBAHAN AGAR FORM TIDAK BERANTAKAN DI SPLIT SCREEN */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    .form-group input, .form-group select {
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 900px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Item Production</span>
</div>

{{-- SweetAlert untuk Error Validasi --}}
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
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
    });
</script>
@endif

<form action="{{ route('master.itemproduction.store') }}" method="POST" id="formItem" class="form" enctype="multipart/form-data">
    @csrf
    <div class="form-grid">
        <div class="form-group">
            <label>Customer <span style="color: red;">*</span></label>
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
            <label>Job Number <span style="color: red;">*</span></label>
            <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden;">
                <input type="text" name="JobNumberA" placeholder="Job A" value="{{ old('JobNumberA') }}" required maxlength="35" style="border: none; text-align: center; flex: 1; padding: 10px; outline: none;">
                <span style="background: #eee; padding: 10px 15px; font-weight: bold; border-left: 1px solid #ccc; border-right: 1px solid #ccc; color: #555;">/</span>
                <input type="text" name="JobNumberB" placeholder="Job B (Opsional)" value="{{ old('JobNumberB') }}" maxlength="35" style="border: none; text-align: center; flex: 1; padding: 10px; outline: none;">
            </div>
            {{-- MODIFIKASI: Diubah jadi merah biasa --}}
            <small style="color: red;">* Isi Job B jika item ini separating</small>
        </div>

        <div class="form-group">
            <label>Part Number <span style="color: red;">*</span></label>
            <input type="text" name="PartNumber" value="{{ old('PartNumber') }}" required maxlength="35">
        </div>

        <div class="form-group">
            <label>Nama Part <span style="color: red;">*</span></label>
            <input type="text" name="NamaPart" id="inputNamaPart" value="{{ old('NamaPart') }}" required maxlength="35">
        </div>

        <div class="form-group">
            <label>Model <span style="color: red;">*</span></label>
            <input type="text" name="Model" id="inputModel" value="{{ old('Model') }}" required>
        </div>

        <div class="form-group">
            <label>Cycle Time (CT) - Detik <span style="color: red;">*</span></label>
            <input type="number" name="CT" step="0.01" value="{{ old('CT') }}" placeholder="Contoh: 15.50" required>
        </div>

        <div class="form-group">
            <label>Best GSPH (Pcs/Hour) <span style="color: red;">*</span></label>
            <input type="number" name="BestGSPH" step="0.01" value="{{ old('BestGSPH') }}" placeholder="Contoh: 250.00" required>
        </div>

        <div class="form-group">
            <label>Qty Per Pallet (Decimal) <span style="color: red;">*</span></label>
            <input type="number" name="QtyPerPallet" step="0.01" value="{{ old('QtyPerPallet') }}" placeholder="Contoh: 12.50" required>
        </div>

        <div class="form-group">
            <label>Berat (Kg) <span style="color: red;">*</span></label>
            <input type="number" name="Berat" step="0.01" value="{{ old('Berat') }}" placeholder="Contoh: 0.50" required>
        </div>

        <div class="form-group">
            <label>Gambar <span style="color: red;">*</span></label>
            <input type="file" name="Gambar" id="fileGambar" accept="image/*">
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Save</button>
        <a href="{{ route('master.itemproduction.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function confirmSave() {
    Swal.fire({
        title: 'Simpan Data Item Produksi?',
        text: "Pastikan Semua Data Yang Dimasukkan Sudah Benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e11d2e', // Merah sesuai tema lo
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Langsung submit ke backend, biar Laravel yang nangkep errornya
            document.getElementById('formItem').submit();
        }
    });
}

// Opsional: Cek ukuran file gambar sebelum submit (Biar server ga nolak file kegedean)
document.getElementById('fileGambar').addEventListener('change', function() {
    if (this.files[0].size > 10 * 1024 * 1024) { // 2MB
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Maksimal ukuran gambar adalah 10MB.',
            confirmButtonColor: '#d33'
        });
        this.value = ''; // Reset file
    }
});
</script>
@endsection