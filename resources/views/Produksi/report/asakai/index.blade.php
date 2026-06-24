@extends('Produksi.layouts.main')

@section('title', 'Asakai Report Dashboard')
@section('page-title', 'Asakai Dashboard Report')

@section('card-actions')
<div class="export-actions" style="display: flex; gap: 10px; position: relative; z-index: 9999;">
    {{-- Tombol Add disamakan persis ukuran, padding, dan stylenya dengan Add Schedule --}}
    <a href="{{ route('report.asakai.create') }}" class="btn-add-data" style="cursor: pointer !important; background-color: #4361ee; color: white; text-decoration: none; padding: 0 16px; height: 36px; border-radius: 8px; font-weight: bold; font-size: 11.5px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fas fa-plus"></i> + Add Asakai Report
    </a>
</div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .table-responsive-wrapper {
        width: 100%; background: #fff; padding: 15px; border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 15px;
        overflow-x: auto; border: 1px solid #e3e6f0; box-sizing: border-box;
    }

    #asakaiTable {
        width: 100%; min-width: 1200px; border-collapse: collapse; font-size: 11.5px;
    }

    /* PERBAIKAN: Mengunci warna header tabel ke merah cerah Astra/IPS (#e11d2e) */
    .table-asakai th { 
        background-color: #bb2121 !important; color: white !important; 
        border: 1px solid #fff !important; padding: 12px 8px;
        vertical-align: middle; text-align: center; white-space: nowrap;
    }

    .table-asakai td { 
        border: 1px solid #dee2e6 !important; padding: 10px 8px; 
        vertical-align: middle; text-align: center; color: #333;
    }

    /* SINKRONISASI STYLE ACTION BUTTONS BIAR SERAGAM SAMA CUSTOMER/SCHEDULE */
    .btn-action-view, .btn-action-edit, .btn-action-delete, .btn-action-excel {
        padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; 
        text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin: 2px;
        transition: all 0.2s ease;
    }
    .btn-action-view { background-color: #ffffff; color: #333; border: 1px solid #ddd; }
    .btn-action-view:hover { background-color: #f8f9fa; }
    .btn-action-edit { background-color: #4361ee; color: #fff; border: none; }
    .btn-action-edit:hover { background-color: #304ec2; }
    .btn-action-excel { background-color: #28a745; color: #fff; border: none; }
    .btn-action-excel:hover { background-color: #159c6b; }
    .btn-action-delete { background-color: #e11d2e; color: #fff; border: none; }
    .btn-action-delete:hover { background-color: #b3101b; }
    
    .badge-status { padding: 4px 8px; border-radius: 4px; font-weight: 800; font-size: 9px; display: inline-block; text-transform: uppercase; }
    .badge-safe { background-color: #1cc88a; color: white; }
    .badge-danger { background-color: #e11d2e; color: white; }

    .issue-text {
        text-align: left; font-size: 10px; max-width: 250px; 
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        font-style: italic; color: #666;
    }

    @media (max-width: 768px) {
        .table-toolbar { flex-direction: column !important; padding: 10px; gap: 10px; }
        .input-date-custom { width: 100% !important; height: 45px; }
        .export-actions { width: 100%; }
        .btn-add-data { width: 100% !important; height: 45px !important; }
    }
</style>

<div class="breadcrumb">
    <span>IPS</span> <span class="separator">></span>
    <span>Report</span> <span class="separator">></span>
    <span class="active" style="color: #e11d2e; font-weight: 800;">Asakai Dashboard</span>
</div>

{{-- PERBAIKAN: SINKRONISASI LAYOUT BAR TOOLBAR FILTER (KEMBAR IDENTIK SAMA SCHEDULE) --}}
<div class="table-toolbar" style="display: flex; justify-content: space-between; align-items: center; gap: 15px; background: #f8f9fc; padding: 12px; border-radius: 10px; margin-bottom: 15px; border: 1px solid #eee;">
    <div class="action-group" style="display: flex; gap: 10px; align-items: center; flex-grow: 1;">
        <label style="font-weight: 600; font-size: 12px; color: #333; margin-right: 5px;">Filter Tanggal:</label>
        <input type="date" id="filterDate" value="{{ $tanggal }}" onchange="updateFilter()" style="height: 38px; border-radius: 8px; border: 1px solid #ddd; padding: 0 12px; font-weight: 600; width: 180px; box-sizing: border-box;">
    </div>
</div>

<div class="table-responsive-wrapper">
    <table class="table-asakai" id="asakaiTable">
        <thead>
            <tr>
                <th rowspan="2">Tanggal Produksi</th>
                <th rowspan="2">Kategori</th>
                <th colspan="3">Produksi Summary (PCS)</th>
                <th colspan="2">Quality (NG)</th>
                <th rowspan="2">Avg GSPH</th>
                <th rowspan="2">Safety Status</th>
                <th rowspan="2">Highlight / Major Issue</th>
                <th rowspan="2" style="width: 200px;">Aksi</th>
            </tr>
            <tr>
                <th>Plan</th>
                <th>Actual</th>
                <th>% Achv</th>
                <th>Reject</th>
                <th>Repair</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $row)
            @php 
                $perc = $row->total_plan > 0 ? ($row->total_act / $row->total_plan) * 100 : 0; 
            @endphp
            <tr>
                <td style="font-weight: bold;">{{ \Carbon\Carbon::parse($row->TanggalProduksi)->format('d/m/Y') }}</td>
                <td style="font-weight: 800; color: #e11d2e;">{{ $row->LineName }}</td>
                <td>{{ number_format($row->total_plan, 0) }}</td>
                <td>{{ number_format($row->total_act, 0) }}</td>
                <td style="font-weight: bold;" class="{{ $perc < 100 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($perc, 1) }}%
                </td>
                <td style="color: #e11d2e; font-weight: bold;">{{ number_format($row->total_reject, 0) }}</td>
                <td style="color: #f6c23e; font-weight: bold;">{{ number_format($row->total_repair, 0) }}</td>
                <td>{{ number_format($row->avg_gsph, 1) }}</td>
                
                <td>
                    @if($row->is_safe)
                        <span class="badge-status badge-safe">Zero Accident</span>
                    @else
                        <span class="badge-status badge-danger">Accident</span>
                    @endif
                </td>

                <td class="issue-text" title="{{ $row->major_issue }}">
                    {{ $row->major_issue }}
                </td>

                <td>
                    <div style="display: flex; justify-content: center; flex-wrap: nowrap; gap: 3px; align-items: center;">
                        <a href="{{ route('report.asakai.show', $row->first_id) }}" class="btn-action-view" title="Lihat Detail">
                             View
                        </a>
                        {{-- 🔥 FIX SAKTI: Ubah dari .create menjadi .edit agar mengarah ke fungsi edit() di controller --}}
                        <a href="{{ route('report.asakai.edit', $row->first_id) }}" class="btn-action-edit" title="Edit Data">
                            Update
                        </a>
                        <a href="{{ route('report.asakai.export', ['date' => $row->TanggalProduksi]) }}" class="btn-action-excel" title="Download Excel">
                             Export
                        </a>
                        <button type="button" onclick="deleteData('{{ $row->first_id }}')" class="btn-action-delete" title="Hapus">
                             Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="py-5 text-muted text-center" style="font-weight: bold;">
                    <i class="fa fa-info-circle"></i>Data tidak tersedia.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
/**
 * Pindah Tanggal Filter
 */
function updateFilter() {
    const date = document.getElementById('filterDate').value;
    window.location.href = `{{ route('report.asakai.index') }}?date=${date}`;
}

/**
 * Fungsi Delete Data Asakai (SINKRON WARNA UTAMANYA)
 */
function deleteData(id) {
    Swal.fire({
        title: 'Apakah Anda Yakin Ingin Menghapus Data Ini?',
        text: 'Data Tersebut Akan Dihapus Secara Permanen dan Tidak Dapat Dipulihkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d2e', // SINKRON: Merah cerah khas Astra/IPS
        cancelButtonColor: '#aaa',     // SINKRON: Abu-abu pembatalan
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch(`{{ url('report/asakai/delete') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Delete', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Terjadi Kesalahan', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi Kesalahan', 'error');
            });
        }
    });
}
</script>
@endsection