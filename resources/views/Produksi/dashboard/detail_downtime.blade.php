@extends('Produksi.layouts.main')

@section('content')
<style>
    /* CSS PAGINATION RAPI */
    .pagination { display: flex; justify-content: center; gap: 5px; list-style: none; padding: 0; margin-top: 20px; }
    .page-item .page-link { 
        padding: 8px 16px; border-radius: 8px; border: 1px solid #ddd; 
        color: #f82b3d; text-decoration: none; font-weight: 600; transition: all 0.3s; 
    }
    .page-item.active .page-link { background-color: #f82b3d; color: #fff; border-color: #f82b3d; }
    .page-item.disabled .page-link { color: #ccc; cursor: not-allowed; background-color: #f9f9f9; }
</style>

<div style="padding: 20px; background: #f4f7f6;">
    {{-- 1. BREADCRUMB --}}
    <div class="breadcrumb" style="margin-bottom: 20px; font-size: 14px; color: #666;">
        <span style="font-weight: 500;">IPS</span> 
        <span style="margin: 0 8px;">></span> 
        <span style="font-weight: 500;">Dashboard</span> 
        <span style="margin: 0 8px;">></span> 
        <span style="color: #e31e24; font-weight: 700;">Detail Downtime</span>
    </div>

    {{-- 2. TABLE AREA --}}
    <div style="background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; border: 1px solid #e9ecef;">
        <h5 style="font-weight: 800; margin-bottom: 25px; color: #333; font-size: 16px;">
            Production Daily Report Downtime Details
        </h5>
        
        <div style="overflow-x: auto; width: 100%; border-radius: 10px;">
            <table style="width: 100%; min-width: 1500px; border-collapse: separate; border-spacing: 0 8px; font-size: 11px;">
                <thead>
                    <tr style="background: #f82b3d; color: #fff; text-align: center; font-weight: 700;">
                        <th style="padding: 12px; border-radius: 10px 0 0 10px; width: 40px;">No</th>
                        <th style="width: 80px;">Item</th>
                        <th style="width: 100px;">Tipe DT</th>
                        <th style="width: 100px;">Area</th>
                        <th style="width: 120px;">Tipe Masalah</th>
                        <th style="width: 80px;">Durasi</th>
                        <th style="width: 80px;">Stroke</th>
                        <th style="width: 200px;">Fakta Lapangan</th>
                        <th style="width: 150px;">Masalah</th>
                        <th style="width: 150px;">Akar Penyebab</th>
                        <th style="width: 200px;">Penanganan</th>
                        <th style="width: 200px;">Fix Action</th>
                        <th style="width: 100px;">PIC</th>
                        <th style="width: 100px;">Deadline</th>
                        <th style="padding: 12px; border-radius: 0 10px 10px 0; width: 80px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $index => $row)
                    @php
                        $parts = explode(':', $row->Durasi);
                        $formattedDurasi = (isset($parts[1])) ? (($parts[0]*60 + $parts[1]).':'.$parts[2]) : $row->Durasi;
                        $statusLabel = ($row->Status == 'Done' || $row->Status == '1') ? 'CLOSED' : 'OPEN';
                        $statusColor = ($statusLabel == 'CLOSED') ? '#5cb85c' : '#f0ad4e';
                    @endphp
                    <tr style="background: #fdf2f2; text-align: center; vertical-align: top;">
                        <td style="padding: 12px; border-radius: 10px 0 0 10px; font-weight: 600;">{{ $details->firstItem() + $index }}</td>
                        <td>{{ $header->item->JobNumber ?? '-' }}</td>
                        <td style="font-weight: 700; color: #d00;">{{ $row->TipeDowntime }}</td>
                        <td>{{ $row->AreaProblem ?? '-' }}</td>
                        <td>{{ $row->TipeMasalah ?? '-' }}</td>
                        <td style="font-weight: 800; color: #e31e24;">{{ $formattedDurasi }}</td>
                        <td>{{ number_format($row->Stroke ?? 0, 0, ',', '.') }}</td>
                        <td style="text-align: left; padding: 8px;">{{ $row->FaktaLapangan ?? '-' }}</td>
                        <td style="text-align: left; padding: 8px; font-weight: 600;">{{ $row->Masalah ?? '-' }}</td>
                        <td style="text-align: left; padding: 8px;">{{ $row->AkarPenyebab ?? '-' }}</td>
                        <td style="text-align: left; padding: 8px;">{{ $row->Penanganan ?? '-' }}</td>
                        <td style="text-align: left; padding: 8px;">{{ $row->FixAction ?? '-' }}</td>
                        <td>{{ $row->NamaPIC ?? '-' }}</td>
                        <td>{{ $row->TargetDueDate ? date('d/m/y', strtotime($row->TargetDueDate)) : '-' }}</td>
                        <td style="border-radius: 0 10px 10px 0; padding: 8px;">
                            <span style="background: {{ $statusColor }}; color: #fff; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 10px; display: block;">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3">
            {{ $details->links('pagination::bootstrap-4') }}
        </div>

        {{-- BACK BUTTON --}}
        <div style="text-align: right; margin-top: 30px;">
            <a href="{{ url('/dashboard') }}" style="background: #ff5722; color: #fff; padding: 12px 35px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 14px; box-shadow: 0 4px 10px rgba(255, 87, 34, 0.3); display: inline-block;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>
@endsection