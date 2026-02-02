@extends('Produksi.layouts.main')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Customer</span>
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

<form action="{{ route('master.customer.update', $customer->IdCustomer) }}" method="POST" class="form" id="formEditCustomer">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-group">
            <label>ID Customer</label>
            <input type="text" value="{{ $customer->IdCustomer }}" disabled>
        </div>

        <div class="form-group">
            <label>Nama Perusahaan</label>
            <input type="text" name="NamaCustomer" value="{{ old('NamaCustomer', $customer->NamaCustomer) }}" required>
        </div>

        <div class="form-group">
            <label>Alamat Perusahaan</label>
            <input type="text" name="AlamatCustomer" value="{{ old('AlamatCustomer', $customer->AlamatCustomer) }}" required>
        </div>

        <div class="form-group">
            <label>Nama PIC</label>
            <input type="text" name="NamaCustomerPIC" value="{{ old('NamaCustomerPIC', $customer->NamaCustomerPIC) }}" required
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text" name="NoTelpCustomer" value="{{ old('NoTelpCustomer', $customer->NoTelpCustomer) }}" maxlength="13" required
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="EmailCustomer" value="{{ old('EmailCustomer', $customer->EmailCustomer) }}" required>
        </div>

        <div class="form-group">
            <label>NPWP</label>
            <input type="text" name="NPWPCustomer" value="{{ old('NPWPCustomer', $customer->NPWPCustomer) }}" maxlength="15" required
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="Status">
                <option value="1" {{ old('Status', $customer->Status) == 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('Status', $customer->Status) == 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Perbarui</button>
        <a href="{{ route('master.customer.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>

<script>
function confirmUpdate() {
    Swal.fire({
        title: 'Update Data Customer?',
        text: "Apakah Anda yakin ingin menyimpan perubahan?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditCustomer').submit();
        }
    });
}
</script>
@endsection