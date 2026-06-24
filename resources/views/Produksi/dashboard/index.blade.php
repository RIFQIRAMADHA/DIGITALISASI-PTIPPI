@extends('Produksi.layouts.main')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="breadcrumb" style="margin-bottom: 20px;">
        <span>IPS</span> <span class="separator">></span> <span class="active">Dashboard</span>
    </div>

    <style>
        html { scroll-behavior: smooth; }
        
        /* FILTER CONTAINER */
        .filter-container {
            background: #e31e24; color: #fff; padding: 25px 30px; border-radius: 12px; 
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; 
            margin-bottom: 25px; box-shadow: 0 4px 15px rgba(227, 30, 36, 0.2); gap: 20px;
        }
        .filter-inputs {
            display: flex; flex-wrap: wrap; align-items: flex-end; gap: 20px; flex-grow: 1; justify-content: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
        .filter-group select, .filter-group input {
            height: 38px !important; padding: 0 10px; border-radius: 8px; border: none; 
            font-weight: 600; font-size: 13px; color: #333; width: 100%;
        }

        /* CARD & MODAL STYLE */
        .chart-card-box {
            background: #fff; padding: 25px; border-radius: 15px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); cursor: pointer; 
            transition: all 0.3s ease; border: 1px solid transparent; display: flex; flex-direction: column;
        }
        .chart-card-box:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(227, 30, 36, 0.15); border: 1px solid #e31e24; }
        .btn-detail-sm { font-size: 10px; color: #fff; background: #e31e24; padding: 4px 10px; border-radius: 20px; font-weight: bold; }
        #modalBody table thead th { position: sticky; top: -25px; background: #f82b3d !important; z-index: 10; }
        .grid-pareto { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 25px; width: 100%; box-sizing: border-box; justify-content: center; justify-items: stretch; }
        .chart-container-wrapper { position: relative; width: 100% !important; height: 300px; max-width: 100%; overflow: hidden; }
        
        /* PAGINATION STYLE (JANGAN DIHAPUS) */
        .pagination { display: flex; justify-content: center; gap: 5px; list-style: none; padding: 0; margin-top: 20px; }
        .page-item .page-link { padding: 8px 16px; border-radius: 8px; border: 1px solid #ddd; color: #f82b3d; text-decoration: none; font-weight: 600; transition: all 0.3s; }
        .page-item.active .page-link { background-color: #f82b3d; color: #fff; border-color: #f82b3d; }
        .page-item.disabled .page-link { color: #ccc; cursor: not-allowed; background-color: #f9f9f9; }

        @media (max-width: 992px) { .grid-pareto { grid-template-columns: 1fr; gap: 20px; } }
        @media (max-width: 768px) { .filter-container { flex-direction: column; align-items: flex-start; } }
    </style>

    {{-- FILTER FORM --}}
    <form action="{{ url('/dashboard') }}" method="GET">
        <div class="filter-container">
            <div>
                <h2 style="margin: 0; font-size: 24px; font-weight: 800;">Hello, {{ Auth::user()->NamaKaryawan ?? 'User' }}!</h2>
                <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Welcome to the Production Dashboard</p>
            </div>

            <div class="filter-inputs">
                <div class="filter-group">
                    <label style="font-size: 11px; font-weight: 800;">SHIFT</label>
                    <select name="shift" id="filterShift">
                        <option value="All Shift" {{ $shift == 'All Shift' ? 'selected' : '' }}>All Shift</option>
                        <option value="Shift 1" {{ $shift == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="Shift 2" {{ $shift == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label style="font-size: 11px; font-weight: 800;">PRODUCTION LINE</label>
                    <select name="line_name" id="filterLine">
                        <option value="">All Line</option>
                        @foreach($lines->unique('NamaProductionLine') as $l)
                            <option value="{{ $l->NamaProductionLine }}" {{ $lineId == $l->NamaProductionLine ? 'selected' : '' }}>{{ $l->NamaProductionLine }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label style="font-size: 11px; font-weight: 800; color: #fff; text-transform: uppercase;">FROM</label>
                    <input type="date" name="start_date" id="startDate" value="{{ $startDate }}" required>
                </div>
                <div class="filter-group">
                    <label style="font-size: 11px; font-weight: 800; color: #fff; text-transform: uppercase;">TO</label>
                    <input type="date" name="end_date" id="endDate" value="{{ $endDate }}" required>
                </div>

                <div style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 0;">
                    {{-- Tombol SEARCH --}}
                    <button type="submit" style="background: #fff; color: #e31e24; border: none; padding: 0 25px; border-radius: 10px; font-weight: 800; height: 38px; cursor: pointer;">
                        SEARCH
                    </button>
                    
                    {{-- Tombol RESET (Sudah disamakan dengan SEARCH) --}}
                    <a href="{{ url('/dashboard?reset=true') }}" 
                    style="background: #fff; color: #e31e24; border: none; padding: 0 25px; border-radius: 10px; font-weight: 800; height: 38px; display: flex; align-items: center; text-decoration: none; font-size: 13px; cursor: pointer;">
                        RESET
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- QUICK STATUS CARDS (OEE) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px;">
        @php
            $displayLines = $lineId ? [$lineId] : ['Line E', 'Line F', 'Line K'];
        @endphp

        @foreach($displayLines as $name)
        @php $stat = $lineStats->get($name); @endphp
        <div style="background: #fff; padding: 25px; border-radius: 15px; border-top: 6px solid #f82b3d; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="text-align: center; margin-bottom: 20px;">
                <span style="font-weight: 800; color: #888; text-transform: uppercase; font-size: 12px;">{{ $name }}</span>
                <h2 style="color: #f82b3d; margin: 5px 0; font-size: 32px; font-weight: 900;">
                    {{ number_format($stat->avg_oee ?? 0, 2) }}%
                </h2>
                <small style="font-weight: bold; color: #666;">Total OEE</small>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: center;">
                <div style="background: #fdf2f2; padding: 12px; border-radius: 10px;">
                    <b style="color: #f82b3d; font-size: 18px;">{{ number_format($stat->avg_repair ?? 0, 2) }}%</b><br>
                    <small style="font-weight: bold; color: #999;">Repair Rate</small>
                </div>
                <div style="background: #fdf2f2; padding: 12px; border-radius: 10px;">
                    <b style="color: #f82b3d; font-size: 18px;">{{ number_format($stat->avg_reject ?? 0, 2) }}%</b><br>
                    <small style="font-weight: bold; color: #999;">Reject Rate</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 4. BARIS DIAGRAM PARETO (MODIFIKASI: JUDUL TOP 5 & CLICKABLE) --}}
    <div class="grid-pareto">
    @php
        $charts = [
            ['id' => 'repairItem', 'title' => 'Top 5 Repairs by Item', 'type' => 'repair'],
            ['id' => 'repairPareto', 'title' => 'Top 5 Repairs by Problem', 'type' => 'repair'],
            ['id' => 'rejectItem', 'title' => 'Top 5 Rejects by Item', 'type' => 'reject'],
            ['id' => 'rejectPareto', 'title' => 'Top 5 Rejects by Problem', 'type' => 'reject'],
            ['id' => 'dtItem', 'title' => 'Top 5 Downtimes by Item', 'type' => 'downtime'],
            ['id' => 'dtProb', 'title' => 'Top 5 Downtimes by Problem', 'type' => 'downtime'],
            ['id' => 'dtDies', 'title' => 'Top 5 Die Downtimes (Minutes)', 'type' => 'downtime'],
            ['id' => 'gsphGrafik', 'title' => 'Top 5 GSPH Achievements', 'type' => 'gsph']
        ];
    @endphp

    @foreach($charts as $c)
    <div onclick="showParetoDetail('{{ $c['type'] }}', '{{ $c['title'] }}')" class="chart-card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h5 style="font-weight: 800; margin: 0; color: #333;"><i class="fas fa-chart-bar text-danger"></i> {{ $c['title'] }}</h5>
            <span class="btn-detail-sm">SEE ALL</span>
        </div>
        <div class="chart-container-wrapper">
            <canvas id="{{ $c['id'] }}"></canvas>
        </div>
    </div>
    @endforeach
</div>

    {{-- 5. TABEL LAPORAN HARIAN PRODUKSI --}}
<div id="table-container-produksi">
    <div style="background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 25px;">
        <h5 style="font-weight: 800; margin-bottom: 20px; color: #333;">Production Summary by Line</h5>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
                <thead>
                    <tr style="background: #f82b3d; color: #fff; text-align: center;">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Line</th>
                        <th>Good (A|B)</th>
                        <th>Reject (A|B)</th>
                        <th>Repair (A|B)</th>
                        <th>Total Pcs</th>
                        <th>Avg GSPH</th>
                        <th>Aksi</th> {{-- PIC sudah dihapus --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentProduction as $index => $row)
                    @php
                        $totalAll = ($row->GoodA ?? 0) + ($row->GoodB ?? 0) + 
                                   ($row->RejectA ?? 0) + ($row->RejectB ?? 0) + 
                                   ($row->RepairA ?? 0) + ($row->RepairB ?? 0);
                    @endphp
                    <tr style="background: #fdf2f2; text-align: center;">
                        <td>{{ ($recentProduction->currentPage() - 1) * $recentProduction->perPage() + $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->TanggalProduksi)->format('d/m/Y') }}</td>
                        <td><span class="badge" style="background:#f82b3d; color:#fff; padding: 5px 10px; border-radius: 5px;">{{ $row->productionLine->NamaProductionLine ?? '-' }}</span></td>
                        <td><b>{{ number_format($row->GoodA) }}</b> | {{ number_format($row->GoodB) }}</td>
                        <td><b>{{ number_format($row->RejectA) }}</b> | {{ number_format($row->RejectB) }}</td>
                        <td><b>{{ number_format($row->RepairA) }}</b> | {{ number_format($row->RepairB) }}</td>
                        <td style="font-weight: 800; color: #f82b3d;">{{ number_format($totalAll) }}</td>
                        <td>{{ number_format($row->AktualGSPH, 1) }}</td>
                        <td>
                            <a href="{{ url('/dashboard/detail-harian/'.$row->IdInputHarian) }}" 
                               style="display: inline-block; padding: 5px 15px; background: #f82b3d; color: #fff; text-decoration: none; border-radius: 20px; font-size: 11px; font-weight: bold;">
                               Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9">Data Not Found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $recentProduction->links() }}</div>
        </div>
    </div>
</div>

{{-- 6. TABEL DOWNTIME --}}
<div id="table-container-downtime">
    <div style="background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <h5 style="font-weight: 800; margin-bottom: 20px; color: #333;">Downtime Summary</h5>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
                <thead>
                    <tr style="background: #f82b3d; color: #fff; text-align: center;">
                        <th>No</th><th>Tanggal</th><th>Line Produksi</th><th>Item</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDowntime as $index => $dt)
                    <tr style="background: #fdf2f2; text-align: center;">
                        <td>{{ ($recentDowntime->currentPage() - 1) * $recentDowntime->perPage() + $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($dt->TanggalProduksi)->format('d/m/Y') }}</td>
                        <td>{{ $dt->NamaProductionLine }}</td> 
                        <td>{{ $dt->NamaPart }}</td>
                        <td>
                            <a href="{{ url('/dashboard/detail-downtime/'.$dt->IdInputHarian) }}" 
                            style="display: inline-block; padding: 5px 15px; background: #f82b3d; color: #fff; text-decoration: none; border-radius: 20px; font-size: 11px; font-weight: bold; transition: 0.3s;">
                            Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5">Data Not Found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $recentDowntime->links() }}</div>
        </div>
    </div>
</div>

    {{-- MODAL AREA --}}
    <div id="paretoModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; backdrop-filter: blur(4px);">
        <div style="background:#fff; width:90%; max-height:85%; border-radius:15px; padding:0; overflow:hidden; position:relative; box-shadow:0 15px 40px rgba(0,0,0,0.4); display:flex; flex-direction:column;">
            <div style="display:flex; justify-content:space-between; align-items:center; background:#f82b3d; padding:15px 25px; color:#fff;">
                <h4 id="modalTitle" style="margin:0; font-weight:800; font-size:18px;">Detail Data</h4>
                <button onclick="closeModal()" style="background:rgba(255,255,255,0.2); color:#fff; border:none; padding:8px 15px; border-radius:8px; cursor:pointer; font-weight:bold;">Close</button>
            </div>
            <div id="modalBody" style="padding:25px; overflow-y:auto; flex-grow:1;">
                <p style="text-align:center;">Memuat data...</p>
            </div>
        </div>
    </div>
@endsection

<script>
    // 1. FUNGSI UNTUK MODAL AJAX (PARETO DETAIL)
    // Ganti fungsi lama Lu dengan yang ini di bagian <script>
    function showParetoDetail(type, title) {
        const modal = document.getElementById('paretoModal');
        const body = document.getElementById('modalBody');
        
        // Ambil nilai filter yang lagi aktif
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const line = document.getElementById('filterLine').value;
        const shift = document.getElementById('filterShift').value;

        document.getElementById('modalTitle').innerText = title + "";
        body.innerHTML = '<div style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin fa-3x text-danger"></i><p>Memuat rincian...</p></div>';
        modal.style.display = 'flex';

        // Fetch data ke controller
        fetch(`{{ url('/dashboard/pareto-detail') }}?type=${type}&start_date=${start}&end_date=${end}&line_name=${line}&shift=${shift}`)
            .then(res => res.text())
            .then(html => { 
                body.innerHTML = html; 
            })
            .catch(err => { 
                body.innerHTML = '<p class="text-center text-danger">Gagal mengambil detail data.</p>'; 
            });
    }

    function closeModal() {
        document.getElementById('paretoModal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- A. VALIDASI TANGGAL ---
        const startInput = document.getElementById('startDate');
        const endInput = document.getElementById('endDate');
        if (startInput && endInput) {
            startInput.addEventListener('change', function() {
                endInput.min = this.value;
            });
        }

        // --- B. FUNGSI HELPER RENDER PARETO (DENGAN GARIS HITAM AKUMULASI) ---
        function renderPareto(id, labels, data, extraDataset = null) {
            const canvasElement = document.getElementById(id);
            if (!canvasElement) return;

            const cleanData = (data && data.length > 0) ? data.map(v => parseFloat(v) || 0) : [0];
            const cleanLabels = (labels && labels.length > 0) ? labels : ['No Data'];

            // Hitung Akumulasi untuk Garis Hitam
            const total = cleanData.reduce((a, b) => a + b, 0);
            let cumulative = 0;
            const lineData = cleanData.map(v => {
                cumulative += v;
                return total > 0 ? ((cumulative / total) * 100).toFixed(2) : 0;
            });

            // Dataset Utama
            let datasets = [
                { 
                    type: 'bar', 
                    label: 'Actual', 
                    data: cleanData, 
                    backgroundColor: '#f82b3d', // BATANG MERAH
                    borderRadius: 5, 
                    yAxisID: 'y',
                    zIndex: 5
                }
            ];

            // Tambahan Dataset jika ada (seperti Target 180 untuk GSPH)
            if (extraDataset) {
                datasets.push(extraDataset);
            }

            // Ganti fungsi inisialisasi Chart Lu di script dengan aturan opsi ini:
            new Chart(canvasElement, {
                data: {
                    labels: cleanLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true, // Wajib TRUE
                    maintainAspectRatio: false, // Wajib FALSE agar dia bisa gepeng/menyesuaikan container
                    resizeDelay: 100, // Tambahkan delay pemicu resize biar gak berat pas di-split
                    plugins: { 
                        legend: { display: (id === 'gsphGrafik') }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            title: { display: true, text: 'Value' }
                        },
                        y1: { 
                            min: 0, max: 110, 
                            position: 'right', 
                            grid: { drawOnChartArea: false }, 
                            ticks: { callback: v => v + '%' },
                            title: { display: true, text: '%' }
                        }
                    }
                }
            });
        }

        // --- C. RENDER DATA PARETO (REPAIR/REJECT/DT) ---
        renderPareto('repairItem', {!! json_encode($paretoRepairItem->pluck('label')) !!}, {!! json_encode($paretoRepairItem->pluck('total')) !!});
        renderPareto('repairPareto', {!! json_encode($paretoRepair->pluck('NamaKerusakan')) !!}, {!! json_encode($paretoRepair->pluck('total')) !!});
        renderPareto('rejectItem', {!! json_encode($paretoRejectItem->pluck('label')) !!}, {!! json_encode($paretoRejectItem->pluck('total')) !!});
        renderPareto('rejectPareto', {!! json_encode($paretoReject->pluck('NamaKerusakan')) !!}, {!! json_encode($paretoReject->pluck('total')) !!});
        renderPareto('dtItem', {!! json_encode($paretoDtItem->pluck('label')) !!}, {!! json_encode($paretoDtItem->pluck('total')) !!});
        renderPareto('dtProb', {!! json_encode($paretoDtProb->pluck('label')) !!}, {!! json_encode($paretoDtProb->pluck('total')) !!});
        renderPareto('dtDies', {!! json_encode($paretoDtDies->pluck('label')) !!}, {!! json_encode($paretoDtDies->pluck('total')) !!});

        // --- D. RENDER GRAFIK GSPH (PLAN DIAMBIL DARI BEST GSPH MASTER) ---
        const gsphRaw = {!! json_encode($gsphData) !!};
        if (gsphRaw && gsphRaw.length > 0) {
            const labelsGsph = gsphRaw.map(d => d.label);
            const actualGsph = gsphRaw.map(d => parseFloat(d.actual) || 0);
            
            // ✅ SEKARANG AMBIL DARI DATABASE (d.plan), JANGAN DIPAKSA 180 LAGI
            const planGsph = gsphRaw.map(d => parseFloat(d.plan) || 0);

            // Dataset extra untuk Target (Sesuai Master Item)
            const targetDataset = {
                type: 'bar',
                label: 'Best GSPH (Target)', // Nama label Gue ubah biar lebih pro
                data: planGsph,
                backgroundColor: '#e9ecef',
                borderColor: '#ccc',
                borderWidth: 1,
                borderRadius: 5,
                yAxisID: 'y'
            };

            // Panggil renderPareto
            renderPareto('gsphGrafik', labelsGsph, actualGsph, targetDataset);
        } else {
            renderPareto('gsphGrafik', ['No Data'], [0]);
        }
    });

$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var container = $(this).closest('[id^="table-container-"]');
    
    // Efek transisi halus
    container.css('opacity', '0.5');

    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            var containerId = container.attr('id');
            // Ambil konten baru dari response yang sama, tapi hanya elemen container tsb
            var newContent = $(data).find('#' + containerId).html();
            $('#' + containerId).html(newContent);
            container.css('opacity', '1');
            
            // Opsional: Scroll halus ke atas container agar user tetap fokus
            $('html, body').animate({
                scrollTop: container.offset().top - 100
            }, 300);
        }
    });
});
</script>