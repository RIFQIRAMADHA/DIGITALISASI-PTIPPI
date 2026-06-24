<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0.4cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 8px; margin: 0; padding: 0; line-height: 1.1; }
        .wrapper { border: 1.5px solid #000; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: -1px; }
        td { border: 1px solid #000; padding: 3px; vertical-align: middle; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-gray { background-color: #e2e2e2; font-weight: bold; }
        .logo-img { width: 40px; height: auto; }
        .checkbox { display: inline-block; width: 9px; height: 9px; border: 1px solid #000; margin-right: 4px; text-align: center; line-height: 9px; font-size: 7px; vertical-align: middle; }
        .sketch-box { height: 130px; text-align: center; vertical-align: middle; overflow: hidden; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table>
            <tr>
                <td width="15%" class="text-center">
                    <img src="{{ public_path('images/logo-ippi.png') }}" class="logo-img">
                </td>
                <td width="85%" class="text-center" style="position: relative; padding: 12px 8px;">
                    <div style="position: absolute; top: 2px; right: 4px; font-size: 7px; font-weight: normal;">
                        FISM-QAD-03-03-01
                    </div>
                    
                    <span style="font-size: 13px; font-weight: bold;">
                        FORMULIR QUALITY PROBLEM REPORT
                    </span>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="bg-gray" width="12%">NO. JOB</td>
                <td width="23%">{{ $qpr->inputHarian->item->JobNumber ?? '-' }}</td>
                <td class="bg-gray" width="12%">MODEL</td>
                <td width="23%">{{ $qpr->inputHarian->item->Model ?? '-' }}</td>
                <td class="bg-gray" width="12%">TANGGAL</td>
                <td width="18%">{{ \Carbon\Carbon::parse($qpr->created_at)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="bg-gray">NAMA PART</td>
                <td colspan="3">{{ $qpr->inputHarian->item->NamaPart ?? '-' }}</td>
                <td class="bg-gray">NO. QPR</td>
                <td>{{ $qpr->IdQpr }}</td>
            </tr>
        </table>

        @php 
            $firstMasalah = $qpr->detailsMasalah->first(); 
            $deskripsi = strtoupper($firstMasalah->DeskripsiProblem ?? '');
            $lastDate = $firstMasalah->LastDateProblem ?? '';
        @endphp
        <table>
            <tr>
                {{-- Menggabungkan 12% + 13% menjadi 25% agar simetris dengan baris bawah --}}
                <td class="bg-gray" colspan="2" width="25%">KONDISI PART</td>
                <td class="bg-gray" width="15%">STOCK IPPI / PCS</td>
                <td width="10%">{{ number_format($qpr->Stok) }}</td>
                <td class="bg-gray text-center" width="25%">DESKRIPSI PROBLEM</td>
                <td class="bg-gray text-center" width="25%">LAST DATE PROBLEM</td>
            </tr>
            <tr>
                <td class="bg-gray" width="12%">REWORK / PCS</td>
                <td width="13%">{{ number_format($qpr->Rework) }}</td>
                <td class="bg-gray" width="15%">RENCANA PROD.</td>
                <td width="10%">{{ $qpr->RencanaProduksi ? \Carbon\Carbon::parse($qpr->RencanaProduksi)->format('d/m/Y') : '-' }}</td>
                <td width="25%"><span class="checkbox">@if(str_contains($deskripsi, 'BARU')) V @endif</span> BARU PERTAMA</td>
                <td class="text-center" width="25%">@if(str_contains($deskripsi, 'BARU')) {{ $lastDate }} @endif</td>
            </tr>
            <tr>
                <td class="bg-gray">REJECT / PCS</td>
                <td>{{ number_format($qpr->Reject) }}</td>
                <td class="bg-gray">PROSES REPAIR</td>
                <td>{{ $qpr->ProsesRepair ?? '-' }}</td>
                <td><span class="checkbox">@if(str_contains($deskripsi, 'KADANG')) V @endif</span> KADANG-KADANG</td>
                <td class="text-center">@if(str_contains($deskripsi, 'KADANG')) {{ $lastDate }} @endif</td>
            </tr>
            <tr>
                <td class="bg-gray">NO. SKETCH</td>
                <td>{{ $firstMasalah->NomorKerusakan ?? '-' }}</td>
                <td class="bg-gray" colspan="2"></td>
                <td><span class="checkbox">@if(str_contains($deskripsi, 'SERING')) V @endif</span> SERING</td>
                <td class="text-center">@if(str_contains($deskripsi, 'SERING')) {{ $lastDate }} @endif</td>
            </tr>
        </table>

        <table>
            <tr><td class="bg-gray text-center">SKETCH</td></tr>
            <tr>
                <td class="sketch-box">
                    @php $imgPath = $qpr->inputHarian->item->Gambar; @endphp
                    @if($imgPath && file_exists(public_path('storage/'.$imgPath)))
                        <img src="{{ public_path('storage/'.$imgPath) }}" style="max-height: 380px; width: auto;">
                    @else
                        <div style="color: #ccc;">[ GAMBAR TIDAK TERSEDIA ]</div>
                    @endif
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="bg-gray" width="12%">Lokasi Kejadian</td>
                <td width="18%">{{ $qpr->LokasiKejadian }}</td>
                <td class="bg-gray" width="8%">Shift</td>
                {{-- Gunakan fallback ke master line jika di transaksi null, sama seperti di controller --}}
                <td width="7%">{{ $qpr->inputHarian->Shift ?? ($qpr->inputHarian->productionLine->Shift ?? '-') }}</td>
                <td class="bg-gray" width="20%">Dokumen Referensi</td>
                <td width="25%">{{ $qpr->DocReferensi }}</td>
                <td class="bg-gray" width="5%">Jam</td>
                <td width="5%">{{ $qpr->Jam }}</td>
            </tr>
        </table>

        <table>
            <tr><td class="bg-gray text-center">ANALISA PENYEBAB (Man, Method, Machine, Material, Environment)</td></tr>
            @foreach(['Man', 'Method', 'Machines', 'Material', 'Environtment', 'Other'] as $factor)
                @php 
                    $dataAnalisa = $qpr->detailsMasalah->where('Keterangan', $factor)->first();
                @endphp
                <tr>
                    <td>
                        <span class="checkbox">@if($dataAnalisa) V @endif</span> 
                        <span class="font-bold">{{ strtoupper($factor) }}:</span> 
                        {{ $dataAnalisa->AnalisaPenyebab ?? '..................................................................................................................................................................................' }}
                    </td>
                </tr>
            @endforeach
        </table>

        <table>
            <tr>
                <td class="bg-gray text-center" width="70%">LANGKAH PENANGGULANGAN SEMENTARA (CORRECTION)</td>
                <td class="bg-gray text-center" width="10%">TARGET</td>
                <td class="bg-gray text-center" width="10%">PIC</td>
                <td class="bg-gray text-center" width="10%">STATUS</td>
            </tr>
            @for($i=0; $i < 4; $i++)
                @php $corr = $qpr->detailsMasalah->skip($i)->first(); @endphp
                <tr>
                    <td>{{ $i+1 }}. {{ $corr->Correction ?? '..................................................................................................................' }}</td>
                    <td class="text-center">{{ $corr->TargetCorrection ?? '' }}</td>
                    <td class="text-center">{{ $corr->PICCorrection ?? '' }}</td>
                    <td class="text-center">{{ $corr ? $corr->StatusCorrection : '' }}</td>
                </tr>
            @endfor
        </table>

        <table>
            <tr>
                <td class="bg-gray text-center" width="70%">PENANGGULANGAN TERHADAP DAMPAK PENYEBAB MASALAH YANG SAMA PADA PRODUK/PROSES SEJENIS</td>
                <td class="bg-gray text-center" width="10%">TARGET</td>
                <td class="bg-gray text-center" width="10%">PIC</td>
                <td class="bg-gray text-center" width="10%">STATUS</td>
            </tr>
            @for($j=0; $j < 4; $j++)
                @php $corr2 = $qpr->detailsMasalah->skip($j)->first(); @endphp
                <tr>
                    <td>{{ $j+1 }}. {{ $corr2->Correction2 ?? '..................................................................................................................' }}</td>
                    <td class="text-center">{{ $corr2->TargetCorrection2 ?? '' }}</td>
                    <td class="text-center">{{ $corr2->PICCorrection2 ?? '' }}</td> 
                    <td class="text-center">@if($corr2) {{ (int)$corr2->StatusCorrection2 === 1 ? 'Closed' : 'Open' }} @endif</td>
                </tr>
            @endfor
        </table>

        <table>
            <tr>
                <td rowspan="2" class="bg-gray text-center" width="30%">LANGKAH PERBAIKAN / PENCEGAHAN (CORRECTIVE / PREVENTIVE ACTION)</td>
                <td rowspan="2" class="bg-gray text-center" width="10%">SCHEDULE</td>
                <td colspan="3" class="bg-gray text-center" width="20%">TANGGAL VERIFIKASI</td>
                <td colspan="3" class="bg-gray text-center" width="30%">METHODE CHECK</td>
                <td rowspan="2" class="bg-gray text-center" width="10%">STATUS</td>
            </tr>
            <tr>
                <td class="bg-gray text-center" width="6.6%">I</td>
                <td class="bg-gray text-center" width="6.6%">II</td>
                <td class="bg-gray text-center" width="6.6%">III</td>
                <td class="bg-gray text-center" width="10%">VISUAL</td>
                <td class="bg-gray text-center" width="10%">FUNGSI</td>
                <td class="bg-gray text-center" width="10%">DIMENSI</td>
            </tr>
            @for($k=0; $k < 4; $k++)
                @php $action = $qpr->detailsVerifikasi->skip($k)->first(); @endphp
                <tr>
                    <td style="height: 20px;">{{ $k+1 }}. {{ $action->LangkahPerbaikan ?? '' }}</td>
                    <td class="text-center">{{ $action->Schedule ?? '' }}</td>
                    <td class="text-center">{{ $action->TanggalVerifikasi ?? '' }}</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">@if($action && str_contains(strtoupper($action->MethodeCheck1 ?? ''), 'VISUAL')) <span class="checkbox">V</span> @endif</td>
                    <td class="text-center">@if($action && str_contains(strtoupper($action->MethodeCheck1 ?? ''), 'FUNGSI')) <span class="checkbox">V</span> @endif</td>
                    <td class="text-center">@if($action && str_contains(strtoupper($action->MethodeCheck1 ?? ''), 'DIMENSI')) <span class="checkbox">V</span> @endif</td>
                    <td class="text-center">@if($action && $action->Status == 1) OK @elseif($action) NG @endif</td>
                </tr>
            @endfor
        </table>

        {{-- BAGIAN TANDA TANGAN --}}
        <table>
            <tr>
                <td colspan="5" class="bg-gray text-center">SEKSI TERKAIT</td>
                <td class="bg-gray text-center" width="11%">APPROVED</td>
                <td class="bg-gray text-center" width="11%">DIPERIKSA</td>
                <td class="bg-gray text-center" width="11%">DIBUAT</td>
            </tr>
            {{-- Baris 1 --}}
            <tr>
                @for($k=0; $k<5; $k++) 
                    <td style="height: 10px;"></td> {{-- Kolom Nama (Seksi Terkait) --}}
                @endfor
                <td rowspan="2"></td> 
                <td rowspan="2"></td> 
                <td rowspan="2"></td> 
            </tr>
            {{-- Baris 2 --}}
            <tr>
                @for($k=0; $k<5; $k++) 
                    <td style="height: 35px;"></td> {{-- Kolom TTD (Seksi Terkait) --}}
                @endfor
            </tr>
            {{-- Baris Jabatan --}}
            <tr class="text-center font-bold bg-gray">
                <td></td><td></td><td></td><td></td><td></td>
                <td>SPV</td>
                <td>FOREMAN</td>
                <td>OPERATOR</td>
            </tr>
        </table>
    </div>
</body>
</html>