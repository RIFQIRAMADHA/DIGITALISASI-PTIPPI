@extends('Produksi.layouts.main')

@section('title', 'Add Employee')
@section('page-title', 'Add Employee')

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

<form action="{{ route('master.employee.store') }}" method="POST" class="form" id="formEmployee">
    @csrf
    <div class="form-grid">
        <div class="form-group">
            {{-- Tambahan bintang merah --}}
            <label>Nama <span style="color: red;">*</span></label>
            {{-- Tambahan maxlength="60" --}}
            <input type="text" name="NamaKaryawan" value="{{ old('NamaKaryawan') }}" required
                   maxlength="60"
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="form-group">
            {{-- Tambahan bintang merah --}}
            <label>NRP <span style="color: red;">*</span></label>
            <input type="text" name="NRPKaryawan" value="{{ old('NRPKaryawan') }}" required
                maxlength="6"
                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <div class="form-group">
            {{-- Tambahan bintang merah --}}
            <label>Password Karyawan <span style="color: red;">*</span></label>
            <div style="position: relative;">
                <input type="password" name="PasswordKaryawan" id="PasswordKaryawan" required maxlength="12" style="width: 100%; padding-right: 40px; box-sizing: border-box;">
                <span id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <div class="form-group">
            {{-- Tambahan bintang merah --}}
            <label>Jabatan <span style="color: red;">*</span></label>
            {{-- Tambahan required --}}
            <select name="Jabatan" class="form-control" required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd;">
                <option value="">- Select Position -</option>
                
                @php
                    // 1. Daftar Jabatan Tetap
                    $patenArr = ['admin', 'quality', 'ppc', 'supervisor', 'foreman', 'leader e', 'leader f', 'leader k'];
                    
                    // 2. Karena ini form CREATE, kita set null supaya tidak error 'undefined variable'
                    $jabatanSekarang = null; 
                @endphp

                {{-- Loop Jabatan Paten --}}
                @foreach($patenArr as $p)
                    <option value="{{ $p }}" {{ old('Jabatan') == $p ? 'selected' : '' }}>
                        {{ ucwords($p) }}
                    </option>
                @endforeach

                {{-- Loop Jabatan Dinamis dari Master Line --}}
                @foreach($lines as $line)
                    @php
                        $kode = strtoupper(substr($line->NamaProductionLine, -1));
                        $isPaten = in_array($kode, ['E', 'F', 'K']);
                        $valLeader = "leader ".strtolower($kode);
                        $valForeman = "foreman ".strtolower($kode);
                    @endphp
                    
                    @if(!$isPaten)
                        <option value="{{ $valLeader }}" {{ old('Jabatan') == $valLeader ? 'selected' : '' }}>
                            Leader {{ $kode }}
                        </option>
                        <option value="{{ $valForeman }}" {{ old('Jabatan') == $valForeman ? 'selected' : '' }}>
                            Foreman {{ $kode }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Save</button>
        <a href="{{ route('master.employee.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
function confirmSave() {
    Swal.fire({
        title: 'Simpan Data Karyawan?',
        text: "Pastikan Semua Data Yang Dimasukkan Sudah Benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEmployee').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('PasswordKaryawan');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                // Toggle tipe input antara 'password' dan 'text'
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon mata (mencoret mata jika password terlihat)
                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                }
            });
        }
    });
</script>
@endsection