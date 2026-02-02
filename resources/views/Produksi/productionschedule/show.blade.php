@extends('Produksi.layouts.main')

@section('title', 'Detail Production Schedule')

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Daily Input</span>
    <span class="separator">></span>
    <span class="active">Detail Production Schedule</span>
</div>

{{-- INFORMASI HEADER --}}
<div class="detail-grid">
    <div class="detail-item">
        <label>ID Plan Schedule</label>
        <span>{{ $schedule->IdPlanSchedule }}</span>
    </div>

    <div class="detail-item">
        <label>Production Line</label>
        <span>{{ $schedule->productionLine->NamaProductionLine }} - {{ $schedule->productionLine->Shift }}</span>
    </div>

    <div class="detail-item">
        <label>Nama PIC</label>
        <span>{{ $schedule->NamaPIC }}</span>
    </div>

    <div class="detail-item">
        <label>Tanggal Produksi</label>
        <span>{{ date('d-m-Y', strtotime($schedule->TanggalProduksi)) }}</span>
    </div>

    <div class="detail-item">
        <label>Dibuat Oleh</label>
        <span>{{ $schedule->create_by }}</span>
    </div>

    <div class="detail-item">
        <label>Waktu Dibuat</label>
        <span>{{ $schedule->created_at->format('d-m-Y H:i') }}</span>
    </div>
</div>

<hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

{{-- TABEL DETAIL ITEM PRODUKSI --}}
<h5 style="font-size: 15px; margin-bottom: 20px; color: #333; font-weight: 600;">Daftar Item Produksi</h5>
<div style="background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
    <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Item / Job Number</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Plan Qty (A/B)</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Waktu (Start-Finish)</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Press Time</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Uchi / Soto</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">GSPH / Stroke</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Work Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedule->details as $d)
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <strong>{{ $d->item->JobNumber }}</strong><br>
                    <small style="color: #666;">{{ $d->item->NamaPart }}</small>
                </td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">{{ $d->PlanQtyA }} / {{ $d->PlanQtyB }}</td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">
                    {{ date('H:i', strtotime($d->PlanStart)) }} - {{ date('H:i', strtotime($d->PlanFinish)) }}
                </td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">{{ $d->PressTime }}</td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">{{ $d->DiesChangeUchi }} / {{ $d->DiesChangeSoto }}</td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">{{ $d->PlanGSPH }} / {{ $d->Stroke }}</td>
                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #eee;">{{ $d->PlanWorkTime }} Min</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- TOMBOL KEMBALI --}}
<div class="form-actions">
    <a href="{{ route('productionschedule.index') }}" class="btn btn-outline">
        Kembali
    </a>
</div>

@endsection