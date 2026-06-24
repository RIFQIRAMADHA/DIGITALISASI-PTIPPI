@extends('Produksi.layouts.main')

@section('title', 'Update Employee')
@section('page-title', 'Update Employee')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Data Master</span> <span class="separator">></span>
    <span class="active">Employee</span>
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

<form action="{{ route('master.employee.update', $karyawan->IdKaryawan) }}" 
      method="POST" class="form" id="formEditEmployee">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-group">
            <label>ID Karyawan <span style="color: red;">*</span></label>
            <input type="text" value="{{ $karyawan->IdKaryawan }}" disabled>
        </div>

        <div class="form-group">
            <label>Nama Karyawan <span style="color: red;">*</span></label>
            {{-- Tambahan maxlength="60" --}}
            <input type="text" name="NamaKaryawan" value="{{ old('NamaKaryawan',$karyawan->NamaKaryawan) }}" required
                   maxlength="60"
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="form-group">
            <label>Password (Kosongkan jika tidak ganti) <span style="color: red;">*</span></label>
            <input type="password" name="PasswordKaryawan" placeholder="********">
        </div>

        <div class="form-group">
            <label>NRP <span style="color: red;">*</span></label>
            <input type="text" name="NRPKaryawan" value="{{ old('NRPKaryawan',$karyawan->NRPKaryawan) }}"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <div class="form-group">
            <label>Jabatan <span style="color: red;">*</span></label>
            <select name="Jabatan" class="form-control">
                <option value="">- Select Position -</option>
                
                @php
                    // 1. Definisikan Jabatan Paten
                    $patenArr = ['admin', 'quality', 'ppc', 'supervisor', 'foreman', 'leader e', 'leader f', 'leader k'];
                    
                    // 2. Cek apakah ini form Edit (ada $karyawan) atau Create (tidak ada $karyawan)
                    // Pakai null coalescing (??) biar aman dari error undefined variable
                    $jabatanSekarang = isset($karyawan) ? strtolower($karyawan->getRawOriginal('Jabatan')) : null;
                @endphp

                {{-- LOOP JABATAN PATEN --}}
                @foreach($patenArr as $p)
                    <option value="{{ $p }}" {{ old('Jabatan', $jabatanSekarang) == $p ? 'selected' : '' }}>
                        {{ ucwords($p) }}
                    </option>
                @endforeach

                {{-- LOOP JABATAN DARI LINE PRODUKSI (GABUNG DISINI) --}}
                @foreach($lines as $line)
                    @php
                        $kode = strtoupper(substr($line->NamaProductionLine, -1));
                        $isPaten = in_array($kode, ['E', 'F', 'K']);
                        $valLeader = "leader ".strtolower($kode);
                        $valForeman = "foreman ".strtolower($kode);
                    @endphp
                    
                    @if(!$isPaten)
                        <option value="{{ $valLeader }}" {{ old('Jabatan', $jabatanSekarang) == $valLeader ? 'selected' : '' }}>
                            Leader {{ $kode }}
                        </option>
                        <option value="{{ $valForeman }}" {{ old('Jabatan', $jabatanSekarang) == $valForeman ? 'selected' : '' }}>
                            Foreman {{ $kode }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Update</button>
        <a href="{{ route('master.employee.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function confirmUpdate() {
    Swal.fire({
        title: 'Ubah Data Karyawan?',
        text: "Apakah Anda Yakin Ingin Menyimpan Perubahan Ini?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditEmployee').submit();
        }
    });
}
</script>
@endsection