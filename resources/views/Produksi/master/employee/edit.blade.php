@extends('Produksi.layouts.main')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Employee</span>
</div>

<form action="{{ route('master.employee.update', $karyawan->idKaryawan) }}"
      method="POST"
      class="form">
@csrf
@method('PUT')

<div class="form-grid">

<div class="form-group">
    <label>ID Karyawan</label>
    <input type="text" value="{{ $karyawan->idKaryawan }}" disabled>
</div>

<div class="form-group">
    <label>Nama Karyawan</label>
    <input type="text" name="NamaKaryawan"
           value="{{ $karyawan->NamaKaryawan }}" required>
</div>

<div class="form-group">
    <label>NRP Karyawan</label>
    <input type="text" name="NRPKaryawan"
           value="{{ $karyawan->NRPKaryawan }}">
</div>

<div class="form-group">
    <label>Jabatan</label>
    <select name="Jabatan">
        @foreach(['admin','leader','foreman','supervisor','ppc'] as $j)
        <option value="{{ $j }}" {{ $karyawan->Jabatan == $j ? 'selected' : '' }}>
            {{ ucfirst($j) }}
        </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Status</label>
    <select name="Status">
        <option value="1" {{ $karyawan->Status == 1 ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ $karyawan->Status == 0 ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">
        Update
    </button>

    <a href="{{ route('master.employee.index') }}" class="btn btn-outline">
        Batal
    </a>
</div>

</form>

@endsection
