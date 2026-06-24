@extends('Produksi.layouts.main')

@section('title', 'Detail Data QPR')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .detail-item label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        color: #666;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .detail-item span {
        display: block;
        font-size: 14px;
        color: #1f2937;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
    }

    .btn-outline-secondary {
        border: 1px solid #d1d5db;
        padding: 8px 20px;
        border-radius: 6px;
        color: #374151;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-outline-secondary:hover {
        background: #f3f4f6;
    }

    /* Helper untuk text alignment */
    .text-center { text-align: center !important; }
</style>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>IPS</span>
    <span class="separator">></span>
    <span>Report</span>
    <span class="separator">></span>
    <span>QPR</span>
    <span class="separator">></span>
    <span class="active">Detail Data QPR</span>
</div>

{{-- INFORMASI HEADER (DETAIL-GRID) --}}
<div class="detail-grid">
    <div class="detail-item">
        <label>Nomor QPR</label>
        <span style="font-weight: 700; color: #b91c1c;">{{ $qpr->IdQpr }}</span>
    </div>

    <div class="detail-item">
        <label>Item / Part Name</label>
        {{-- ✅ Proteksi Relasi Item --}}
        <span>{{ $qpr->inputHarian && $qpr->inputHarian->item ? $qpr->inputHarian->item->NamaPart : 'Item Tidak Ditemukan' }}</span>
    </div>

    <div class="detail-item">
        <label>Production Line</label>
        {{-- ✅ Proteksi Relasi ProductionLine --}}
        <span>{{ $qpr->inputHarian && $qpr->inputHarian->productionLine ? ($qpr->inputHarian->productionLine->NamaProductionLine ?? $qpr->inputHarian->productionLine->NamaLine) : '-' }}</span>
    </div>

    <div class="detail-item">
        <label>Shift</label>
        {{-- ✅ Proteksi Properti Shift --}}
        <span>
            @if($qpr->inputHarian)
                {{ $qpr->inputHarian->Shift ?? ($qpr->inputHarian->productionLine->Shift ?? '-') }}
            @else
                -
            @endif
        </span>
    </div>

    <div class="detail-item">
        <label>Tanggal Produksi</label>
        <span>{{ $qpr->inputHarian ? date('d-m-Y', strtotime($qpr->inputHarian->TanggalProduksi)) : '-' }}</span>
    </div>

    <div class="detail-item">
        <label>Lokasi Kejadian</label>
        <span>{{ $qpr->LokasiKejadian ?? '-' }}</span>
    </div>
</div>

<hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

{{-- TABEL 1: RINGKASAN TEMUAN --}}
<h5 style="font-size: 15px; margin-bottom: 20px; color: #333; font-weight: 600; font-family: 'Inter', sans-serif;">Ringkasan Temuan</h5>
<div style="background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
    <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px; font-family: 'Inter', sans-serif;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Rework</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Reject</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Stock IPPI</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Proses Repair</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Rencana Produksi</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Referensi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="padding: 12px; border-bottom: 1px solid #eee; color: #2563eb; font-weight: 700;">{{ number_format($qpr->Rework, 0) }}</td>
                <td class="text-center" style="padding: 12px; border-bottom: 1px solid #eee; color: #dc2626; font-weight: 700;">{{ number_format($qpr->Reject, 0) }}</td>
                <td class="text-center" style="padding: 12px; border-bottom: 1px solid #eee;">{{ number_format($qpr->Stok, 0) }}</td>
                <td class="text-center" style="padding: 12px; border-bottom: 1px solid #eee; font-weight: 600;">{{ $qpr->ProsesRepair ?? '-' }}</td>
                <td class="text-center" style="padding: 12px; border-bottom: 1px solid #eee;">{{ $qpr->RencanaProduksi ? date('d-m-Y', strtotime($qpr->RencanaProduksi)) : '-' }}</td>
                <td class="text-center" style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">{{ $qpr->DocReferensi ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- TABEL 2: DETAIL VERIFIKASI --}}
<h5 style="font-size: 15px; margin-bottom: 20px; color: #333; font-weight: 600; font-family: 'Inter', sans-serif;">Detail Verifikasi & Langkah Perbaikan</h5>
<div style="background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
    <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px; font-family: 'Inter', sans-serif;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6; width: 50px;">No</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Langkah Perbaikan</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Jadwal</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Tanggal Verifikasi</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Metode Cek</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($qpr->detailsVerifikasi as $index => $v)
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;" class="text-center">{{ $index + 1 }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;" class="text-center"><strong>{{ $v->LangkahPerbaikan }}</strong></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;" class="text-center">{{ $v->Schedule ? date('d-m-Y', strtotime($v->Schedule)) : '-' }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;" class="text-center">{{ $v->TanggalVerifikasi ? date('d-m-Y', strtotime($v->TanggalVerifikasi)) : '-' }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;" class="text-center">{{ $v->MethodeCheck1 ?? '-' }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;" class="text-center">
                    @if($v->Status == 1 || strtoupper($v->Status) == 'OK')
                        <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 11px;">OK</span>
                    @else
                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 11px;">PENDING</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 20px; text-align: center; color: #999; font-style: italic;">Belum ada data verifikasi untuk QPR ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- TOMBOL AKSI --}}
<div class="form-actions" style="margin-top: 30px; margin-bottom: 50px;">
    <a href="{{ route('report.qpr.index') }}" class="btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@endsection