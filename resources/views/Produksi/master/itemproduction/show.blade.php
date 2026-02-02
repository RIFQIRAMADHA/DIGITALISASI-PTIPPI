@extends('Produksi.layouts.main')

@section('title', 'Detail Item Produksi')
@section('page-title', 'Detail Item Produksi')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Item Produksi</span>
</div>

<div class="detail-grid">

    <div class="detail-item">
        <label>ID Item Produksi</label>
        <span>{{ $item->IdItemProduksi }}</span>
    </div>

    <div class="detail-item">
        <label>Customer</label>
        <span>{{ $item->customer->NamaCustomer ?? '-' }}</span>
    </div>

    <div class="detail-item">
        <label>Job Number</label>
        <span>{{ $item->JobNumber }}</span>
    </div>

    <div class="detail-item">
        <label>Part Number</label>
        <span>{{ $item->PartNumber }}</span>
    </div>

    <div class="detail-item">
        <label>Nama Part</label>
        <span>{{ $item->NamaPart }}</span>
    </div>

    <div class="detail-item">
        <label>Model</label>
        <span>{{ $item->Model }}</span>
    </div>

    <div class="detail-item">
        <label>Status</label>
        <span class="badge {{ $item->Status ? 'badge-success' : 'badge-danger' }}">
            {{ $item->Status ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>

</div>

{{-- GAMBAR --}}
@if ($item->Gambar)
<div style="margin-top:24px">
    <label style="font-weight:600">Gambar Item</label><br>
    
    {{-- Tambahkan link <a> dengan target="_blank" --}}
    <a href="{{ asset('storage/'.$item->Gambar) }}" target="_blank" title="Klik untuk membuka gambar di tab baru">
        <img src="{{ asset('storage/'.$item->Gambar) }}" 
             style="max-width:300px; border-radius:8px; cursor: pointer; border: 1px solid #ddd;">
    </a>
    
    <p style="font-size: 12px; color: #666; margin-top: 4px;">
        * Klik gambar untuk melihat ukuran penuh di tab baru.
    </p>
</div>
@endif

<div class="form-actions">

    <a href="{{ route('master.itemproduction.index') }}"
       class="btn btn-outline">
        Kembali
    </a>
</div>

@endsection
