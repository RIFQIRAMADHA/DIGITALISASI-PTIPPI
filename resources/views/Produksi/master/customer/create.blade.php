@extends('Produksi.layouts.main')

@section('title', 'Add Customer')
@section('page-title', 'Add Customer')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Style untuk tanda wajib diisi */
    .required-label::after {
        content: " *";
        color: #e11d2e;
        font-weight: bold;
    }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Customer</span>
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

<form action="{{ route('master.customer.store') }}" method="POST" class="form" id="formCustomer">
    @csrf
    <div class="form-grid">
        {{-- WAJIB --}}
        <div class="form-group">
            <label class="required-label">Nama Customer</label>
            <input type="text" name="NamaCustomer" value="{{ old('NamaCustomer') }}" required maxlength="60">
        </div>

        {{-- OPSIONAL --}}
        <div class="form-group">
            <label>Alamat Customer</label>
            <input type="text" name="AlamatCustomer" value="{{ old('AlamatCustomer') }}">
        </div>

        {{-- ✅ SEKARANG OPSIONAL --}}
        <div class="form-group">
            <label>Nama PIC</label>
            <input type="text" name="NamaCustomerPIC" value="{{ old('NamaCustomerPIC') }}"
                maxlength="60"       
                oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        {{-- OPSIONAL --}}
        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text" name="NoTelpCustomer" value="{{ old('NoTelpCustomer') }}" maxlength="13"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        {{-- OPSIONAL --}}
        <div class="form-group">
            <label>Email Customer</label>
            <input type="email" name="EmailCustomer" value="{{ old('EmailCustomer') }}">
        </div>

        {{-- OPSIONAL --}}
        <div class="form-group">
            <label>NPWP</label>
            <input type="text" name="NPWPCustomer" value="{{ old('NPWPCustomer') }}" maxlength="15"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Save</button>
        <a href="{{ route('master.customer.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function confirmSave() {
    Swal.fire({
        title: 'Simpan Data Pelanggan?',
        text: "Pastikan Semua Data Yang Dimasukkan Sudah Benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCustomer').submit();
        }
    });
}
</script>
@endsection