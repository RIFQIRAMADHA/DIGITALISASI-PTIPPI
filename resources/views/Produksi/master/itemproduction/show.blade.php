@extends('Produksi.layouts.main')

@section('title', 'Detail Item Production')
@section('page-title', 'Detail Item Production')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Item Production</span>
</div>

<div class="detail-grid">

    <div class="detail-item">
        <label>Customer</label>
        <span>{{ $item->customer->NamaCustomer ?? '-' }}</span>
    </div>

    <div class="detail-item">
        <label>Job Number</label>
        <span style="font-weight: 800;">{{ $item->JobNumber }}</span>
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

    {{-- FIELD: CYCLE TIME (CT) --}}
    <div class="detail-item">
        <label>Cycle Time (CT)</label>
        <span style="font-weight: 800; color: #e11d2e;">{{ number_format($item->CT ?? 0, 2) }} Detik</span>
    </div>

    {{-- ✅ FIELD BARU: BEST GSPH --}}
    <div class="detail-item">
        <label>Best GSPH</label>
        <span style="font-weight: 800; color: #2980b9;">{{ number_format($item->BestGSPH ?? 0, 2) }} </span>
    </div>

    {{-- ✅ FIELD BARU: QTY PER PALLET --}}
    <div class="detail-item">
        <label>Qty Per Pallet</label>
        <span style="font-weight: 800; color: #10b981;">{{ number_format($item->QtyPerPallet ?? 0, 2) }} Pcs/Plt</span>
    </div>

    {{-- FIELD: BERAT (Kg) --}}
    <div class="detail-item">
        <label>Berat (Kg)</label>
        <span style="font-weight: 800; color: #2d3436;">{{ number_format($item->Berat ?? 0, 2) }} Kg</span>
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
        Back
    </a>
</div>

@endsection