@extends('Produksi.layouts.main')

@section('title', 'Tambah Customer')
@section('page-title', 'Tambah Customer')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Customer</span>
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

<form action="{{ route('master.customer.store') }}" method="POST" class="form" id="formCustomer">
    @csrf
    <div class="form-grid">
        <div class="form-group">
            <label>Nama Customer</label>
            <input type="text" name="NamaCustomer" value="{{ old('NamaCustomer') }}" required>
        </div>

        <div class="form-group">
            <label>Alamat Customer</label>
            <input type="text" name="AlamatCustomer" value="{{ old('AlamatCustomer') }}" required>
        </div>

        <div class="form-group">
            <label>Nama PIC</label>
            <input type="text" name="NamaCustomerPIC" value="{{ old('NamaCustomerPIC') }}" required
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text" name="NoTelpCustomer" value="{{ old('NoTelpCustomer') }}" maxlength="13" required
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <div class="form-group">
            <label>Email Customer</label>
            <input type="email" name="EmailCustomer" value="{{ old('EmailCustomer') }}" required>
        </div>

        <div class="form-group">
            <label>NPWP</label>
            <input type="text" name="NPWPCustomer" value="{{ old('NPWPCustomer') }}" maxlength="15" required
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Simpan</button>
        <a href="{{ route('master.customer.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>

<script>
function confirmSave() {
    Swal.fire({
        title: 'Simpan Data Customer?',
        text: "Pastikan data customer sudah benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCustomer').submit();
        }
    });
}
</script>
@endsection