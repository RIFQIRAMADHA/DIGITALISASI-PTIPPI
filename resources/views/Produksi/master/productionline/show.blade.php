@extends('Produksi.layouts.main')

@section('title', 'Detail Production Line')
@section('page-title', 'Detail Production Line')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Production Line</span>
</div>

<div class="detail-grid">

    <div class="detail-item">
        <label>ID Production Line</label>
        <span>{{ $line->IdProductionLine }}</span>
    </div>

    <div class="detail-item">
        <label>Nama Production Line</label>
        <span>{{ $line->NamaProductionLine }}</span>
    </div>

    <div class="detail-item">
        <label>Shift</label>
        <span>{{ $line->Shift }}</span>
    </div>

    <div class="detail-item">
        <label>Status</label>
        <span class="badge {{ $line->Status ? 'badge-success' : 'badge-danger' }}">
            {{ $line->Status ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>
</div>
<div class="form-actions">
        <a href="{{ route('master.productionline.index') }}" class="btn btn-outline">
            Kembali
        </a>
    </div>

@endsection