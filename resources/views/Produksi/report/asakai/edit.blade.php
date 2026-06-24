@extends('Produksi.layouts.main')

@section('title', 'Manage Asakai')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .page-container { padding: 30px; background-color: #f4f6f9 !important; min-height: 100vh; }
    .asakai-report-wrapper { max-width: 1250px; margin: 0 auto; background: #fff !important; border-radius: 8px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); border: 1px solid #333; overflow: hidden; }
    
    /* STYLE HEADER KEMBAR IDENTIK */
    .asakai-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 20px 25px; 
        border-bottom: 2px solid #333; 
        background: #fff; 
    }
    .header-left, .header-right { flex: 1; }
    .header-right { display: flex; justify-content: flex-end; }
    .header-center { flex: 2; text-align: center; }
    .header-center h4 { 
        margin: 0; 
        font-weight: 900; 
        letter-spacing: 2px; 
        color: #333; 
        font-size: 1.8rem; 
        text-transform: uppercase;
    }

    /* LOGO SIZE SINKRON */
    .logo { height: 55px; width: auto; }
    .logo-right { height: 150px; width: auto; }

    .asakai-body { padding: 30px; background: #fff; }
    .section-title { background: #e11d2e; color: #fff; padding: 5px 15px; font-weight: 800; font-size: 13px; display: inline-block; margin-bottom: 10px; border-radius: 2px; text-transform: uppercase; }
    .info-bar { background: #fff; padding: 10px 0; border-bottom: 2px solid #333; margin-bottom: 25px; font-weight: 800; display: flex; justify-content: space-between; }
    .table-input { width: 100%; margin-bottom: 30px; border-collapse: collapse; table-layout: fixed; }
    .table-input th { background: #fff; text-align: center; padding: 10px 5px; border: 1px solid #333; font-size: 11px; color: #333; font-weight: 900; }
    .table-input td { border: 1px solid #333; padding: 0 !important; vertical-align: middle; text-align: center; }
    .form-control-asakai { width: 100%; height: 40px; border: none !important; font-size: 13px; text-align: center; background: transparent; outline: none; box-sizing: border-box; }
    .form-control-asakai:focus { background: #fffdf0 !important; }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .input-readonly { background-color: #f9f9f9 !important; font-weight: 800; color: #4e73df; }
    .category-label { padding: 10px 15px; font-weight: 800; background: #fff; font-size: 12px; text-align: left; }
</style>

<div class="page-container">
    <div class="asakai-report-wrapper">
        <div class="asakai-header">
            <div class="header-left">
                <img src="{{ asset('images/logo-ippi.png') }}" class="logo">
            </div>
            <div class="header-center">
                <h4>DAILY REPORT ASAKAI</h4>
            </div>
            <div class="header-right">
                <img src="{{ asset('images/image.png') }}" class="logo-right">
            </div>
        </div>

        <div class="asakai-body">
            <div class="info-bar">
                <span>DATE : <span style="color: blue;">{{ \Carbon\Carbon::parse($harian->TanggalProduksi)->format('d M Y') }}</span></span>
            </div>

            {{-- FORM MENGGUNAKAN METHOD PUT UNTUK UPDATE DATA --}}
            <form action="{{ route('report.asakai.update', $harian->IdInputHarian) }}" method="POST" id="formAsakai">
                @csrf
                @method('PUT')

                <input type="hidden" name="TanggalProduksi" value="{{ $harian->TanggalProduksi }}">
                <input type="hidden" name="IdInputHarian" value="{{ $harian->IdInputHarian }}">

                {{-- PANGGIL PARTIALS MODUL EDIT MODE --}}
                @include('Produksi.report.asakai.partials._safety', ['mode' => 'edit'])
                @include('Produksi.report.asakai.partials._quality', ['mode' => 'edit'])
                @include('Produksi.report.asakai.partials._productivity', ['mode' => 'edit'])
                @include('Produksi.report.asakai.partials._downtime', ['mode' => 'edit'])
                @include('Produksi.report.asakai.partials._gsph', ['mode' => 'edit'])
                @include('Produksi.report.asakai.partials._spot', ['mode' => 'edit'])
                @include('Produksi.report.asakai.partials._prod_plan', ['mode' => 'edit'])

                {{-- ACTIONS BUTTON: DISINKRONISASI RAPAT KIRI & WARNA BIRU IPS ASTRA SESUAI CREATE --}}
                <div class="mt-4" style="display: flex; gap: 12px; justify-content: flex-start; padding-bottom: 20px; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px;">
                    <button type="button" onclick="confirmUpdate()" 
                        style="background: #4361ee; color: white; border: none; padding: 10px 40px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);">
                        Update
                    </button>
                    <a href="{{ route('report.asakai.index') }}" class="btn" 
                        style="border: 1.5px solid #343a40; padding: 10px 30px; border-radius: 10px; color: #666; text-decoration: none; background-color: #ffffff; font-weight: 700; transition: 0.3s; display: inline-flex; align-items: center; justify-content: center;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// --- FUNGSI SAVE DENGAN KONFIRMASI SWEETALERT (SINKRON: QUESTION MARK + BIRU CHROME INSTAN) ---
function confirmUpdate() {
    const form = document.getElementById('formAsakai');
    if (!form) return;

    Swal.fire({
        title: 'Perbarui Data ASAKAI?',
        text: "Apakah Anda Yakin Ingin Menyimpan Perubahan Ini?",
        icon: 'question', // Tanda tanya biru
        showCancelButton: true,
        confirmButtonColor: '#3085d6', // Biru chrome master data
        cancelButtonColor: '#aaa',     // Abu-abu cancel
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // Langsung submit tanpa loading screen berputar
        }
    });
}

// --- FUNGSI HELPER AMUNISI WARNA LIVE & KALKULASI ---
function updateColor(input, tdId) {
    const val = parseFloat(input.value) || 0;
    const td = document.getElementById(tdId) || input.closest('td');
    if (td) {
        td.style.backgroundColor = val > 0 ? '#f8d7da' : '#d4edda';
        input.style.color = val > 0 ? '#721c24' : '#155724';
    }
}

function updatePercentColor(input, tdId, target) {
    const val = parseFloat(input.value.replace('%', '')) || 0;
    const td = document.getElementById(tdId);
    if (td) {
        td.style.backgroundColor = val > target ? '#f8d7da' : '#d4edda';
    }
}

function calcProdDiff(key, shift) {
    const planInput = document.getElementsByName(key + 'Plan' + shift)[0];
    const actInput = document.getElementsByName(key + 'Act' + shift)[0];
    
    if (planInput && actInput) {
        const plan = parseFloat(planInput.value) || 0;
        const act = parseFloat(actInput.value) || 0;
        const diff = act - plan;
        
        const targetTd = document.getElementById('diff_' + key + shift);
        if (targetTd) {
            targetTd.innerText = Math.round(diff);
            targetTd.style.backgroundColor = diff < 0 ? '#f8d7da' : '#d4edda';
            targetTd.style.color = diff < 0 ? '#721c24' : '#155724';
        }
    }
}

function calcGsphDiff(key, shift) {
    const planInput = document.getElementsByName(key + 'PlanGsph' + shift)[0];
    const actInput = document.getElementsByName(key + 'ActGsph' + shift)[0];

    if (planInput && actInput) {
        const plan = parseFloat(planInput.value) || 0;
        const act = parseFloat(actInput.value) || 0;
        const diff = act - plan;
        
        const targetTd = document.getElementById('td_gsph_diff_' + key + shift);
        if (targetTd) {
            targetTd.innerText = Math.round(diff);
            targetTd.style.backgroundColor = diff < 0 ? '#f8d7da' : '#d4edda';
            targetTd.style.color = diff < 0 ? '#721c24' : '#155724';
        }
    }
}

function calcSpotDiff(idx) {
    const planInput = document.getElementsByName('SpotPlan' + idx)[0];
    const actInput = document.getElementsByName('SpotAct' + idx)[0];
    
    if (planInput && actInput) {
        const plan = parseFloat(planInput.value) || 0;
        const act = parseFloat(actInput.value) || 0;
        const diff = act - plan;
        const td = document.getElementById('td_spotDiff' + idx) || planInput.closest('tr').querySelector('[id^="td_spotDiff"]');
        if (td) {
            td.innerText = d;
            td.style.backgroundColor = d < 0 ? '#f8d7da' : '#d4edda';
        }
    }
}

// --- INITIALIZE DATA SAAT HALAMAN PERTAMA KALI DIAMBIL ---
function initializeCalculations() {
    document.querySelectorAll('.form-control-asakai').forEach(input => {
        if (input.name.includes('Act') || input.name.includes('Accum') || input.name.includes('Dt')) {
            const parentTd = input.closest('td');
            if (parentTd) {
                const val = parseFloat(input.value) || 0;
                parentTd.style.backgroundColor = val > 0 ? '#f8d7da' : '#d4edda';
                input.style.color = val > 0 ? '#721c24' : '#155724';
            }
        }
    });

    for (let i = 0; i < 4; i++) {
        calcSpotDiff(i);
    }
    
    ['S1', 'S2'].forEach(s => {
        ['LineE', 'LineF', 'LineK', 'D52Vt', 'D26', 'Handwork', 'HW'].forEach(line => {
            try { calcProdDiff(line, s); } catch(e) {}
            try { calcGsphDiff(line, s); } catch(e) {}
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initializeCalculations();
    
    const asakaiForm = document.getElementById('formAsakai');
    if (asakaiForm) {
        asakaiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmUpdate(); 
        });
    }
});
</script>
@endsection