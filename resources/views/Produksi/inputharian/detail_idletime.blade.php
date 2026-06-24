@extends('Produksi.layouts.main')

@section('title', 'Detail Idle Time Produksi')
@section('page-title', 'Detail Idle Time')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Daily Input</span> <span class="separator">></span>
    <span class="active">Detail Idle Time</span>
</div>

<div class="page-container">
    <h5 class="page-title mb-3">Detail Idle Time - ({{ $input->item->JobNumber ?? '-' }})</h5>

    {{-- HEADER SUMMARY --}}
    <div class="reject-summary-wrapper">
        <div class="reject-summary-container">
            <span class="summary-main-label">Plan QTY :</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2">{{ number_format($input->PlanQtyA ?? 0) }}</span>
                <span class="reject-summary-box-v2">{{ number_format($input->PlanQtyB ?? 0) }}</span>
                <span class="font-weight-bold">Pcs</span>
            </div>
        </div>

        <div class="reject-summary-container">
            <span class="summary-main-label">Actual QTY :</span>
            <div class="summary-sub-group">
                <span class="reject-summary-box-v2">{{ number_format($input->GoodA ?? 0) }}</span>
                <span class="reject-summary-box-v2">{{ number_format($input->GoodB ?? 0) }}</span>
                <span class="font-weight-bold">Pcs</span>
            </div>
        </div>

        {{-- Highlight khusus untuk section Idle Time --}}
        <div class="reject-summary-container border-warning-custom" style="border-left: 5px solid #ff9800;">
            <span class="summary-main-label text-warning" style="color: #ff9800 !important;">Total Idle Time :</span>
            <div class="summary-sub-group">
                <span class="font-weight-bold" id="total-durasi-display">00:00</span>
                <span class="font-weight-bold text-warning">Jam</span>
            </div>
        </div>
    </div>

    <form action="{{ route('inputharian.idletime.store', $input->IdInputHarian) }}" method="POST" id="formIdleTime">
        @csrf
        <div id="idletime-container">
            @forelse($details as $index => $det)
                @include('Produksi.inputharian.partials.partial_idletime_row', ['index' => $index, 'detail' => $det])
            @empty
                @include('Produksi.inputharian.partials.partial_idletime_row', ['index' => 0, 'detail' => null])
            @endforelse
        </div>

        <div class="form-actions" style="margin-top: 32px; display: flex; gap: 12px;">
            <button type="button" class="btn btn-primary" onclick="confirmSaveIdle()">Save</button>
            <a href="{{ route('inputharian.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<template id="idletime-row-template">
    @include('Produksi.inputharian.partials.partial_idletime_row', ['index' => 'REPLACE_INDEX', 'detail' => null])
</template>

<script>
    function updateRowNumbersIdle() {
        document.querySelectorAll('.row-number-idle').forEach((span, i) => {
            span.innerText = i + 1;
        });
    }

    function addDetailIdle() {
        const container = document.getElementById('idletime-container');
        const template = document.getElementById('idletime-row-template').innerHTML;
        // Kita pakai index 0 saja karena penanganan input array di Controller sudah aman
        const newRow = template.replace(/REPLACE_INDEX/g, '0'); 
        const div = document.createElement('div');
        div.innerHTML = newRow;
        container.appendChild(div.firstElementChild);
        updateRowNumbersIdle();
    }

    function removeDetailIdle(btn) {
        const cards = document.querySelectorAll('.idletime-item-card');
        if (cards.length > 1) {
            btn.closest('.idletime-row-container').remove();
            updateRowNumbersIdle();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Minimal harus ada satu baris input idle time!' });
        }
    }

    function confirmSaveIdle() {
        Swal.fire({
            title: 'Save Idle Time Data?',
            text: "Make sure the data is correct.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formIdleTime').submit();
            }
        });
    }
</script>
@endsection