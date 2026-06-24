@extends('Produksi.layouts.main')

@section('title', 'Detail Employee')
@section('page-title', 'Detail Employee')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Employee</span>
</div>

<div class="detail-grid">
    <div class="detail-item">
        <label>Nama Karyawan</label>
        <span>{{ $karyawan->NamaKaryawan }}</span>
    </div>

    <div class="detail-item">
        <label>NRP (Username Login)</label>
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
    <a href="{{ route('master.employee.index') }}" class="btn btn-outline">
        Back
    </a>
</div>

@endsection