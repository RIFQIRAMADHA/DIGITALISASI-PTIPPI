@extends('Produksi.layouts.main')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Employee</span>
</div>

{{-- SweetAlert untuk Error Validasi --}}
@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        html: `
            <div style="text-align: left;">
                <p>Gagal memperbarui data:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        `,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Perbaiki Data'
    });
</script>
@endif

<form action="{{ route('master.employee.update', $karyawan->IdKaryawan) }}" 
      method="POST" class="form" id="formEditEmployee">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-group">
            <label>ID Karyawan</label>
            <input type="text" value="{{ $karyawan->IdKaryawan }}" disabled>
        </div>

        <div class="form-group">
            <label>Nama Karyawan</label>
            <input type="text" name="NamaKaryawan" value="{{ old('NamaKaryawan',$karyawan->NamaKaryawan) }}" required>
        </div>

        <div class="form-group">
            <label>Password (Kosongkan jika tidak ganti)</label>
            <input type="password" name="PasswordKaryawan" placeholder="********">
        </div>

        <div class="form-group">
            <label>NRP</label>
            <input type="text" name="NRPKaryawan" value="{{ old('NRPKaryawan',$karyawan->NRPKaryawan) }}"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <div class="form-group">
            <label>Jabatan</label>
            <select name="Jabatan">
                @foreach(['admin','leader','foreman','supervisor','ppc'] as $j)
                    <option value="{{ $j }}" {{ old('Jabatan',$karyawan->Jabatan)==$j?'selected':'' }}>
                        {{ ucfirst($j) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="Status">
                <option value="1" {{ old('Status',$karyawan->Status)==1?'selected':'' }}>Aktif</option>
                <option value="0" {{ old('Status',$karyawan->Status)==0?'selected':'' }}>Nonaktif</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Perbarui</button>
        <a href="{{ route('master.employee.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>

<script>
function confirmUpdate() {
    Swal.fire({
        title: 'Update Data Karyawan?',
        text: "Apakah Anda yakin ingin menyimpan perubahan data ini?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditEmployee').submit();
        }
    });
}
</script>
@endsection