@extends('Produksi.layouts.main')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

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

<form action="{{ route('master.customer.update', $customer->IdCustomer) }}" method="POST" class="form" id="formEditCustomer">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-group">
            <label class="required-label">Nama Perusahaan</label>
            <input type="text" name="NamaCustomer" value="{{ old('NamaCustomer', $customer->NamaCustomer) }}" required maxlength="60">
        </div>

        <div class="form-group">
            <label>Alamat Perusahaan</label>
            <input type="text" name="AlamatCustomer" value="{{ old('AlamatCustomer', $customer->AlamatCustomer == '-' ? '' : $customer->AlamatCustomer) }}">
        </div>

        <div class="form-group">
            <label>Nama PIC</label>
            <input type="text" name="NamaCustomerPIC" value="{{ old('NamaCustomerPIC', $customer->NamaCustomerPIC == '-' ? '' : $customer->NamaCustomerPIC) }}"
                    maxlength="60"
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text" name="NoTelpCustomer" value="{{ old('NoTelpCustomer', $customer->NoTelpCustomer == '-' ? '' : $customer->NoTelpCustomer) }}" maxlength="13"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="EmailCustomer" value="{{ old('EmailCustomer', $customer->EmailCustomer == '-' ? '' : $customer->EmailCustomer) }}">
        </div>

        <div class="form-group">
            <label>NPWP</label>
            <input type="text" name="NPWPCustomer" value="{{ old('NPWPCustomer', $customer->NPWPCustomer == '-' ? '' : $customer->NPWPCustomer) }}" maxlength="15"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Update</button>
        <a href="{{ route('master.customer.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function confirmUpdate() {

    Swal.fire({
        title: 'Ubah Data Pelanggan?',
        text: "Apakah Anda Yakin Ingin Menyimpan Perubahan Ini?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Update!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditCustomer').submit();
        }
    });
}
</script>
@endsection