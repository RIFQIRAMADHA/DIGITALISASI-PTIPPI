@extends('Produksi.layouts.main')

@section('title', 'Update Item Production')
@section('page-title', 'Update Item Production')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* CSS RESPONSIVE TAMBAHAN (TIDAK MENGHAPUS STRUKTUR ASLI) */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group input, .form-group select { 
        width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; 
    }
    .btn { 
        padding: 10px 30px; /* Padding seragam */
        border-radius: 8px; 
        font-weight: 700; 
        cursor: pointer; 
        text-decoration: none; 
        display: inline-flex; 
        align-items: center;
        justify-content: center;
        min-width: 120px; /* Lebar minimum sama */
        height: 45px;      /* Tinggi konsisten */
        box-sizing: border-box;
        font-size: 14px;
        transition: all 0.2s;
    }

    /* Tombol Update (Biru) */
    .btn-primary { 
        background: #5c67ff; /* Sesuaikan warna biru dengan gambar */
        color: #fff; 
        border: none; 
    }
    .btn-primary:hover { background: #4543a8; }

    /* Tombol Cancel (Putih, Border Hitam Tipis) */
    .btn-outline { 
        background: #ffffff; 
        color: #333; 
        border: 1px solid #ced4da; /* Border hitam tipis */
    }
    .btn-outline:hover {
        background: #f8f9fa;
        border-color: #333; /* Border jadi hitam saat hover */
    }

    .form-actions { display: flex; gap: 15px; margin-top: 30px; }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Item Production</span>
</div>

{{-- Alert Error Validasi dari Laravel --}}
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Terjadi kesalahan',
            html: `
                <div style="text-align: left; font-size: 14px; color: #555;">
                    <p style="margin-bottom: 10px;">Mohon periksa kembali data yang Anda masukkan:</p>
                    <ul style="margin-top: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            confirmButtonColor: '#e11d2e', // Merah Astra
            confirmButtonText: 'OK',
            customClass: {
                title: 'swal2-title-custom',
                htmlContainer: 'swal2-html-container-custom'
            }
        });
    });
</script>
@endif

<form action="{{ route('master.itemproduction.update', $item->IdItemProduksi) }}" method="POST" id="formEditItem" class="form" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-grid">

        <div class="form-group">
            <label>Customer <span style="color: red;">*</span></label>
            <select name="IdCustomer" required>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->IdCustomer }}" {{ (old('IdCustomer', $item->IdCustomer) == $customer->IdCustomer) ? 'selected' : '' }}>
                        {{ $customer->NamaCustomer }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Job Number (Plan A / Plan B) <span style="color: red;">*</span></label>
            <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden; background: #fff;">
                <input type="text" name="JobNumberA" placeholder="Job A" value="{{ old('JobNumberA', $jobA) }}" required required maxlength="35" style="border: none; text-align: center; flex: 1; padding: 12px; outline: none;">
                <span style="background: #eee; padding: 12px 15px; font-weight: bold; border-left: 1px solid #ccc; border-right: 1px solid #ccc; color: #555;">/</span>
                <input type="text" name="JobNumberB" placeholder="Job B (Opsional)" value="{{ old('JobNumberB', $jobB) }}" required maxlength="35" style="border: none; text-align: center; flex: 1; padding: 12px; outline: none;">
            </div>
            <small style="color: red;">* Data asli: <strong>{{ $item->JobNumber }}</strong></small>
        </div>

        <div class="form-group">
            <label>Part Number <span style="color: red;">*</span></label>
            <input type="text" name="PartNumber" value="{{ old('PartNumber', $item->PartNumber) }}" required required maxlength="35">
        </div>

        <div class="form-group">
            <label>Nama Part <span style="color: red;">*</span></label>
            <input type="text" name="NamaPart" id="inputNamaPart" value="{{ old('NamaPart', $item->NamaPart) }}" required required maxlength="35">
        </div>

        <div class="form-group">
            <label>Model <span style="color: red;">*</span></label>
            <input type="text" name="Model" id="inputModel" value="{{ old('Model', $item->Model) }}" required required maxlength="35">
        </div>

        <div class="form-group">
            <label>Cycle Time (CT) - Detik <span style="color: red;">*</span></label>
            <input type="number" name="CT" step="0.01" value="{{ old('CT', $item->CT) }}" placeholder="Contoh: 15.50" required>
        </div>

        <div class="form-group">
            <label>Best GSPH (Pcs/Hour) <span style="color: red;">*</span></label>
            <input type="number" name="BestGSPH" step="0.01" value="{{ old('BestGSPH', $item->BestGSPH) }}" placeholder="Contoh: 250.00" required>
        </div>

        <div class="form-group">
            <label>Qty Per Pallet (Decimal) <span style="color: red;">*</span></label>
            <input type="number" name="QtyPerPallet" step="0.01" 
                value="{{ old('QtyPerPallet', $item->QtyPerPallet) }}" 
                placeholder="Contoh: 12.50" required>
        </div>

        <div class="form-group">
            <label>Berat (Kg) <span style="color: red;">*</span></label>
            <input type="number" name="Berat" step="0.01" value="{{ old('Berat', $item->Berat) }}" placeholder="Contoh: 0.50" required>
        </div>

        <div class="form-group">
            <label>Gambar <span style="color: red;">*</span></label>
            <input type="file" name="Gambar" id="fileGambar" accept="image/*">
            @if ($item->Gambar)
                <div id="existingImage" style="margin-top: 10px;">
                    <small style="color: red;">* Gambar saat ini:</small><br>
                    <img src="{{ asset('storage/'.$item->Gambar) }}" alt="Preview" style="max-width: 150px; border-radius: 5px; border: 1px solid #ddd; margin-top: 5px;">
                    <input type="hidden" id="hasExistingImage" value="1">
                </div>
            @else
                <input type="hidden" id="hasExistingImage" value="0">
                <small style="color: red; display: block;">* Item ini belum memiliki gambar</small>
            @endif
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Update</button>
        <a href="{{ session('last_item_url', route('master.itemproduction.index')) }}" class="btn btn-outline">Cancel</a>
    </div>
</form>
<script>
// Validasi Ukuran File max 10MB
document.getElementById('fileGambar').addEventListener('change', function() {
    if (this.files[0].size > 10 * 1024 * 1024) { 
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Maksimal ukuran gambar adalah 10MB.',
            confirmButtonColor: '#d33'
        });
        this.value = ''; 
    }
});

function confirmUpdate() {
    const form = document.getElementById('formEditItem');
    const fileInput = document.getElementById('fileGambar');
    const hasImage = document.getElementById('hasExistingImage').value;

    // Cek Gambar (Satu-satunya yang butuh dicek di depan, karena di backend statusnya nullable)
    if (fileInput.files.length === 0 && hasImage === "0") {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Item ini belum memiliki gambar. Mohon unggah gambar terlebih dahulu!',
            confirmButtonColor: '#e11d2e',
            confirmButtonText: 'OK'
        }).then(() => {
            fileInput.focus();
        });
        return;
    }

    // Langsung munculkan popup konfirmasi, error form kosong biar diurus Laravel
    Swal.fire({
        title: 'Perbarui Data Item Produksi?',
        text: "Apakah Anda Yakin Ingin Menyimpan Perubahan Ini?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#5c67ff',
        cancelButtonColor: '#717a89',
        confirmButtonText: 'Update!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) { 
            // Langsung submit ke backend!
            form.submit(); 
        }
    });
}
</script>
@endsection