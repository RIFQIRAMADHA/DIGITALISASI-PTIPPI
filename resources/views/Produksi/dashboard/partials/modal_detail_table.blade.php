<div style="overflow-x:auto; max-height: 500px; position: relative;"> {{-- Tambahkan max-height agar scroll muncul --}}
    <table style="width:100%; border-collapse:separate; border-spacing:0; font-size:13px; border: 1px solid #dee2e6;">
        <thead>
            <tr style="background:#f82b3d; color:white; text-align:left;">
                {{-- Tambahkan Style Sticky pada setiap TH --}}
                @php $stickyStyle = "position: sticky; top: 0; background: #f82b3d; z-index: 10; padding:12px; border:1px solid #ddd; text-align:center;"; @endphp
                
                <th style="{{ $stickyStyle }}">No</th>
                <th style="{{ $stickyStyle }} text-align:left;">Tanggal</th>
                <th style="{{ $stickyStyle }} text-align:left;">Line</th>
                <th style="{{ $stickyStyle }} text-align:left;">Item / Part</th>
                
                @if($type == 'downtime')
                    <th style="{{ $stickyStyle }} text-align:left;">Tipe Downtime</th>
                    <th style="{{ $stickyStyle }} text-align:left;">Masalah</th>
                    <th style="{{ $stickyStyle }} text-align:left;">Area Problem</th>
                    <th style="{{ $stickyStyle }}">Durasi (Mnt)</th>
                @elseif($type == 'gsph')
                    <th style="{{ $stickyStyle }}">Target GSPH</th>
                    <th style="{{ $stickyStyle }}">Actual GSPH</th>
                    <th style="{{ $stickyStyle }}">Achievement</th>
                @else
                    <th style="{{ $stickyStyle }} text-align:left;">Nama Kerusakan</th>
                    <th style="{{ $stickyStyle }} text-align:left;">Area Problem</th>
                    <th style="{{ $stickyStyle }}">Qty</th>
                    <th style="{{ $stickyStyle }} text-align:left;">Penyebab</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr style="background: {{ $index % 2 == 0 ? '#fff' : '#fdf2f2' }};">
                <td style="padding:10px; border:1px solid #ddd; text-align:center;">{{ $index + 1 }}</td>
                <td style="padding:10px; border:1px solid #ddd; text-align:center;">
                    {{ \Carbon\Carbon::parse($row->TanggalProduksi ?? ($row->inputHarian->TanggalProduksi ?? now()))->format('d/m/Y') }}
                </td>
                <td style="padding:10px; border:1px solid #ddd; text-align:center;">
                    {{ $row->productionLine->NamaProductionLine ?? ($row->inputHarian->productionLine->NamaProductionLine ?? '-') }}
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-weight:bold;">
                    {{ $row->item->NamaPart ?? ($row->inputHarian->item->NamaPart ?? '-') }}
                </td>
                
                @if($type == 'downtime')
                    <td style="padding:10px; border:1px solid #ddd;">{{ $row->TipeDowntime }}</td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ $row->Masalah }}</td>
                    <td style="padding:10px; border:1px solid #ddd; font-weight:bold; color:#f82b3d;">{{ $row->AreaProblem ?? '-' }}</td>
                    <td style="padding:10px; border:1px solid #ddd; text-align:center; font-weight:bold; color:#f82b3d;">{{ $row->Durasi }}</td>
                @elseif($type == 'gsph')
                    <td style="padding:10px; border:1px solid #ddd; text-align:center;">{{ $row->inputHarian->item->BestGSPH ?? 180 }}</td>
                    <td style="padding:10px; border:1px solid #ddd; text-align:center; font-weight:bold; color:#28a745;">{{ $row->AktualGSPH }}</td>
                    <td style="padding:10px; border:1px solid #ddd; text-align:center; font-weight:bold;">
                        @php 
                            $target = $row->inputHarian->item->BestGSPH ?? 180;
                            $achievement = $target > 0 ? ($row->AktualGSPH / $target) * 100 : 0;
                        @endphp
                        {{ number_format($achievement, 1) }}%
                    </td>
                @else
                    <td style="padding:10px; border:1px solid #ddd;">{{ $row->NamaKerusakan }}</td>
                    <td style="padding:10px; border:1px solid #ddd; font-weight:bold; color:#f82b3d;">{{ $row->AreaProblem ?? '-' }}</td>
                    <td style="padding:10px; border:1px solid #ddd; text-align:center; font-weight:bold; color:#f82b3d;">{{ number_format($row->Qty) }}</td>
                    <td style="padding:10px; border:1px solid #ddd; font-style:italic; color:#666;">{{ $row->Penyebab ?? '-' }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="10" style="padding:30px; text-align:center; color:#999;">Data Not Found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>