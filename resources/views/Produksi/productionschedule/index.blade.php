@extends('Produksi.layouts.main')

@section('title', 'Production Schedule')
@section('page-title', 'Production Schedule')

@section('card-actions')
<div class="schedule-actions" style="display: flex; gap: 10px;">
    {{-- Button Import: Hijau Custom dari components.css --}}
    <button class="btn-import-excel">
        <i class="fas fa-file-excel"></i> Import Excel
    </button>
    
    {{-- Button Tambah: Biru Custom dari components.css --}}
    <a href="{{ route('productionschedule.create') }}" class="btn-add-schedule">
        <i class="fas fa-plus"></i> + Tambah Schedule
    </a>
</div>
@endsection

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <span>A-Track</span>
    <span class="separator">></span>
    <span>Data Master</span>
    <span class="separator">></span>
    <span class="active">Production Schedule</span>
</div>

{{-- TOOLBAR: FILTER & SEARCH --}}
<div class="table-toolbar">
    <div class="action-group">
        {{-- Form Kalender Custom --}}
        <input type="date" 
               class="input-date-custom" 
               id="filterDate" 
               value="{{ request('date') ?? date('Y-m-d') }}"
               onchange="updateFilter()">
        
        {{-- Dropdown Line Custom (Menampilkan Nama & Shift) --}}
        <select class="select-line-custom" id="filterLine" onchange="updateFilter()">
            <option value="">Semua Line - Semua Shift</option>
            @foreach($lines as $l)
                <option value="{{ $l->IdProductionLine }}" {{ request('line') == $l->IdProductionLine ? 'selected' : '' }}>
                    {{ $l->NamaProductionLine }} - Shift {{ $l->Shift }}
                </option>
            @endforeach
        </select>

        {{-- Tombol Cari --}}
        <!-- <button class="btn btn-cari-custom" onclick="updateFilter()">Cari</button> -->
    </div>

    {{-- Search Bar --}}
    <input type="text"
           class="input-search"
           placeholder="Cari Schedule..."
           onkeyup="searchTable(this.value)">
</div>

{{-- Info Header Dinamis --}}
<div class="info-header" style="margin-bottom: 12px; margin-top: 15px;">
    <p class="text-muted">
        Item Produksi untuk Tanggal : <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>
    </p>
    
    {{-- Header Dinamis: Nama Line - Shift --}}
    <h5 class="page-title" style="font-size: 18px; margin-top: 5px;">
        @if($selectedLine)
            {{ $selectedLine->NamaProductionLine }} - Shift {{ $selectedLine->Shift }}
        @else
            Semua Production Line
        @endif
    </h5>
</div>

{{-- TABEL CUSTOM --}}
<table class="table-custom table-fixed" id="scheduleTable">
    <colgroup>
        <col style="width: 60px;">   {{-- No --}}
        <col style="width: 300px;">  {{-- Production Line --}}
        <col style="width: 250px;">  {{-- PIC --}}
        <col style="width: 250px;">  {{-- Tanggal Produksi --}}
        <col style="width: 250px;">  {{-- Aksi --}}
    </colgroup>

    <thead>
        <tr>
            <th>No</th>
            <th>Production Line</th>
            <th>PIC</th>
            <th>Tanggal Produksi</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($schedules as $index => $row)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $row->productionLine->NamaProductionLine ?? '-' }}</td>
            <td>{{ $row->NamaPIC }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($row->TanggalProduksi)->translatedFormat('d F Y') }}</td>
            <td class="text-center">
                <div class="action-buttons-container">
                    <a href="{{ route('productionschedule.show', $row->IdPlanSchedule) }}" 
                       class="btn btn-sm btn-outline">View</a>
                    
                    <a href="{{ route('productionschedule.edit', $row->IdPlanSchedule) }}" 
                       class="btn btn-sm btn-primary">Edit</a>
                    
                    <form action="{{ route('productionschedule.destroy', $row->IdPlanSchedule) }}" 
                          method="POST" class="form-delete d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-5 text-muted">
                Belum ada data jadwal produksi untuk kriteria ini.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- SCRIPTS --}}
<script>
// Fungsi Filter Ganda (Tanggal + Line) agar tidak saling hapus di URL
function updateFilter() {
    const date = document.getElementById('filterDate').value;
    const line = document.getElementById('filterLine').value;
    
    let url = window.location.pathname + '?date=' + date;
    if (line) {
        url += '&line=' + line;
    }
    
    window.location.href = url;
}

// Pencarian Live di Tabel
function searchTable(value) {
    value = value.toLowerCase();
    document.querySelectorAll("#scheduleTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
}

// SweetAlert Konfirmasi Hapus
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data jadwal ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c62828',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if(result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection