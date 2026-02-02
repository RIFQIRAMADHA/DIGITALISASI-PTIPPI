@extends('Produksi.layouts.main')

@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Karyawan')

@section('content')

<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Detail Employee</span>
</div>

<div class="card-custom">
    <div class="form-grid">

        <div class="form-group">
            <label>ID Karyawan</label>
            <input type="text" value="{{ $karyawan->IdKaryawan }}" disabled>
        </div>

        <div class="form-group">
            <label>Nama Karyawan</label>
            <input type="text" value="{{ $karyawan->NamaKaryawan }}" disabled>
        </div>

        <div class="form-group">
            <label>NRP (Username Login)</label>
            <input type="text" value="{{ $karyawan->NRPKaryawan }}" disabled>
        </div>

        <div class="form-group">
            <label>Jabatan</label>
            <input type="text" value="{{ ucfirst($karyawan->Jabatan) }}" disabled>
        </div>

        <div class="form-group">
            <label>Status</label>
            <div>
                @if($karyawan->Status == 1)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-danger">Nonaktif</span>
                @endif
            </div>
        </div>

    </div>

    <div class="form-actions" style="margin-top: 20px;">
        <a href="{{ route('master.employee.index') }}" class="btn btn-outline">
            Kembali
        </a>
    </div>
</div>

@endsection