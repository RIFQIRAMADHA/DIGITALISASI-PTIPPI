@extends('Produksi.layouts.main')

@section('title', 'Detail Customer')
@section('page-title', 'Detail Customer')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Customer</span>
</div>

<div class="detail-grid">

    <div class="detail-item">
        <label>Nama Perusahaan</label>
        <span>{{ $customer->NamaCustomer }}</span>
    </div>

    <div class="detail-item">
        <label>Alamat Perusahaan</label>
        <span>{{ $customer->AlamatCustomer }}</span>
    </div>

    <div class="detail-item">
        <label>Nama PIC</label>
        <span>{{ $customer->NamaCustomerPIC }}</span>
    </div>

    <div class="detail-item">
        <label>Nomor Telepon</label>
        <span>{{ $customer->NoTelpCustomer }}</span>
    </div>

    <div class="detail-item">
        <label>Email</label>
        <span>{{ $customer->EmailCustomer }}</span>
    </div>

    <div class="detail-item">
        <label>NPWP Perusahaan</label>
        <span>{{ $customer->NPWPCustomer }}</span>
    </div>

    <div class="detail-item">
        <label>Status</label>
        <span class="badge {{ $customer->Status ? 'badge-success' : 'badge-danger' }}">
            {{ $customer->Status ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>
</div>
<div class="form-actions">
        <a href="{{ route('master.customer.index') }}" class="btn btn-outline">
            Back
        </a>
    </div>

@endsection
