@extends('Produksi.layouts.main')

@section('content')
    {{-- 1. BREADCRUMB --}}
    <div class="breadcrumb" style="margin-bottom: 20px; font-size: 14px; color: #666;">
        <span style="font-weight: 500;">IPS</span> 
        <span style="margin: 0 8px;">></span> 
        <span style="font-weight: 500;">Dashboard</span> 
        <span style="margin: 0 8px;">></span> 
        <span style="color: #e31e24; font-weight: 700;">Detail Daily Report</span>
    </div>

    {{-- 2. TABLE CARD --}}
    <div class="content-card" style="background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e9ecef;">
        {{-- Header Card --}}
        <div style="background: #f8f9fa; border-bottom: 1px solid #eee; padding: 20px 25px;">
            <h5 style="font-weight: 800; margin: 0; color: #333; font-size: 16px;">
                <i class="fas fa-file-alt" style="color: #f82b3d; margin-right: 10px;"></i> Production Daily Report Details
            </h5>
        </div>
        
        <div style="padding: 25px;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px; font-size: 13px;">
                    <thead>
                        <tr style="background: #f82b3d; color: #fff; text-align: center; font-weight: 700;">
                            <th style="padding: 15px; border-radius: 10px 0 0 10px;">No</th>
                            <th>Item</th>
                            <th>Target</th>
                            <th>Good</th>
                            <th>Repair</th>
                            <th>Reject</th>
                            <th>Total</th>
                            <th>Soto Dandori</th>
                            <th>Dies Change</th>
                            <th>Early Check</th>
                            <th>Down Time</th>
                            <th>Idle Time</th>
                            <th>CT</th>
                            <th>OEE</th>
                            <th style="padding: 15px; border-radius: 0 10px 10px 0;">GSPH</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $index => $row)
                        <tr style="background: #fdf2f2; text-align: center; transition: all 0.2s;">
                            <td style="padding: 15px; border-radius: 10px 0 0 10px; font-weight: 600;">{{ $index + 1 }}</td>
                            <td style="font-weight: 800; color: #333;">{{ $row->item->NamaPart ?? '-' }}</td>
                            
                            {{-- TARGET: Dinamis dari PlanQty --}}
                            <td style="font-weight: 600;">{{ number_format($row->PlanQtyA, 0) }} &nbsp; {{ number_format($row->PlanQtyB, 0) }}</td>
                            
                            {{-- ACTUALS --}}
                            <td style="color: #28a745; font-weight: 700;">{{ number_format($row->GoodA, 0) }} &nbsp; {{ number_format($row->GoodB, 0) }}</td>
                            <td style="color: #ff9800; font-weight: 600;">{{ number_format($row->RepairA, 0) }} &nbsp; {{ number_format($row->RepairB, 0) }}</td>
                            <td style="color: #f82b3d; font-weight: 600;">{{ number_format($row->RejectA, 0) }} &nbsp; {{ number_format($row->RejectB, 0) }}</td>
                            <td style="font-weight: 800;">{{ number_format($row->GoodA + $row->GoodB + $row->RepairA + $row->RepairB + $row->RejectA + $row->RejectB, 0) }}</td>
                            
                            {{-- STATUS BADGES: Dinamis & Warna Otomatis --}}
                            <td>
                                <span style="background: {{ $row->SotoDandori >= 0 ? '#5cb85c' : '#d9534f' }}; color: #fff; padding: 4px 12px; border-radius: 6px; font-weight: 700;">
                                    {{ number_format($row->SotoDandori, 0) }}
                                </span>
                            </td>
                            <td>
                                <span style="background: {{ $row->DiesChange >= 0 ? '#5cb85c' : '#d9534f' }}; color: #fff; padding: 4px 12px; border-radius: 6px; font-weight: 700;">
                                    {{ number_format($row->DiesChange, 0) }}
                                </span>
                            </td>
                            
                            <td style="color: #666; font-weight: 600;">{{ $row->EarlyCheck ?? '00:00' }}</td>
                            
                            <td>
                                <span style="background: {{ $row->TotalDowntime <= 0 ? '#5cb85c' : '#d9534f' }}; color: #fff; padding: 4px 12px; border-radius: 6px; font-weight: 700;">
                                    {{ number_format($row->TotalDowntime, 0) }}
                                </span>
                            </td>
                            <td>
                                <span style="background: #5cb85c; color: #fff; padding: 4px 12px; border-radius: 6px; font-weight: 700;">
                                    {{ number_format($row->IdleTime ?? 0, 0) }}
                                </span>
                            </td>

                            <td style="font-weight: 600; color: #4361ee;">{{ $row->TPT ?? '0' }}</td>
                            <td style="font-weight: 800;">{{ number_format($row->OEE, 1) }}%</td>
                            <td style="border-radius: 0 10px 10px 0; font-weight: 800; color: #d00;">{{ number_format($row->AktualGSPH, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <a href="{{ url('/dashboard') }}" style="background: #ff5722; color: #fff; padding: 12px 35px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 14px; box-shadow: 0 4px 10px rgba(255, 87, 34, 0.3); display: inline-block;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    tbody tr:hover {
        background: #f82b3d10 !important;
        transform: scale(1.002);
    }
</style>
@endsection