@extends('Produksi.layouts.main')

@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Employee</span>
</div>

<form action="{{ route('master.employee.store') }}" method="POST" class="form">
    @csrf

    <div class="form-grid">

        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="NamaKaryawan" required>
        </div>

        <div class="form-group">
            <label>NRP</label>
            <input type="text" name="NRPKaryawan">
        </div>

        <div class="form-group">
            <label>Jabatan</label>
            <select name="Jabatan">
                <option value="admin">Admin</option>
                <option value="leader">Leader</option>
                <option value="foreman">Foreman</option>
                <option value="supervisor">Supervisor</option>
                <option value="ppc">PPC</option>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="Status">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>

    </div>

    <div class="form-actions">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('master.employee.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>
@endsection
