@extends('Produksi.layouts.main')

@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Karyawan')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Employee</span>
</div>

<div class="detail-grid">

    <div class="detail-item">
        <label>ID Karyawan</label>
        <span>{{ $karyawan->idKaryawan }}</span>
    </div>

    <div class="detail-item">
        <label>Nama Karyawan</label>
        <span>{{ $karyawan->NamaKaryawan }}</span>
    </div>

    <div class="detail-item">
        <label>NRP Karyawan</label>
        <span>{{ $karyawan->NRPKaryawan }}</span>
    </div>

    <div class="detail-item">
        <label>Jabatan</label>
        <span>{{ ucfirst($karyawan->Jabatan) }}</span>
    </div>

    <div class="detail-item">
        <label>Status</label>
        <span class="badge {{ $karyawan->Status ? 'badge-success' : 'badge-danger' }}">
            {{ $karyawan->Status ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>
</div>
<div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update
        </button>

        <a href="{{ route('master.employee.index') }}" class="btn btn-outline">
            Kembali
        </a>
    </div>

@endsection
