@extends('Produksi.layouts.main')

@section('title', 'Manage Asakai')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .page-container { padding: 30px; background-color: #f4f6f9 !important; min-height: 100vh; }
    .asakai-report-wrapper { max-width: 1250px; margin: 0 auto; background: #fff !important; border-radius: 8px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); border: 1px solid #333; overflow: hidden; }
    
    /* MODIFIKASI HEADER */
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

    /* MODIFIKASI LOGO */
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
            {{-- Dinamis sesuai TanggalProduksi yang sedang aktif --}}
            <span>DATE : <span style="color: blue;">{{ \Carbon\Carbon::parse($harian->TanggalProduksi)->format('d M Y') }}</span></span>
        </div>

        {{-- FILTER SINGLE DATE --}}
        <div style="background: #f4f4f4; padding: 15px; border: 1px solid #333; margin-bottom: 20px;">
            <form action="{{ route('report.asakai.create') }}" method="GET" style="display: flex; gap: 10px; align-items: flex-end;">
                <div>
                    <label style="font-weight: 800; font-size: 11px;">PILIH TANGGAL</label>
                    <input type="date" name="date" class="form-control" value="{{ $harian->TanggalProduksi }}">
                </div>
                <button type="submit" style="background: #333; color: #fff; padding: 8px 20px; border: none; font-weight: 800; cursor: pointer;">GET DATA</button>
            </form>
        </div>

        <form action="{{ route('report.asakai.store') }}" method="POST" id="formAsakai">
            @csrf
            <input type="hidden" name="TanggalProduksi" value="{{ $harian->TanggalProduksi }}">
            <input type="hidden" name="IdInputHarian" value="{{ $harian->IdInputHarian }}">

            @include('Produksi.report.asakai.partials._safety')
            @include('Produksi.report.asakai.partials._quality')
            @include('Produksi.report.asakai.partials._productivity')
            @include('Produksi.report.asakai.partials._downtime')
            @include('Produksi.report.asakai.partials._gsph')
            @include('Produksi.report.asakai.partials._spot')
            @include('Produksi.report.asakai.partials._prod_plan')

            {{-- PERBAIKAN BARIS ACTIONS: DIPINDAH RAPAT KE KIRI (justify-content: flex-start) & SINKRON WARNA BIRU IPS ASTRA --}}
            <div class="mt-4" style="display: flex; gap: 12px; justify-content: flex-start; padding-bottom: 20px; margin-top: 20px;">
                <button type="button" onclick="confirmSave()" 
                    style="background: #4361ee; color: white; border: none; padding: 10px 40px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s;">
                    Save
                </button>
                <a href="{{ route('report.asakai.index') }}" class="btn" 
                    style="border: 1.5px solid #343a40; padding: 10px 30px; border-radius: 10px; color: #666; text-decoration: none; background-color: #ffffff; font-weight: 700; transition: 0.3s;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// --- FUNGSI SAVE DENGAN KONFIRMASI (SINKRON SAMA MODUL LAIN: QUESTION MARK + TOMBOL BIRU CHROME & LOADING SCREEN DIHAPUS) ---
function confirmSave() {
    const form = document.getElementById('formAsakai');
    if (!form) return;

    Swal.fire({
        title: 'Simpan Data ASAKAI?',
        text: "Pastikan Semua Data Yang Dimasukkan Sudah Benar.",
        icon: 'question', // Tanda tanya biru
        showCancelButton: true,
        confirmButtonColor: '#3085d6', // Biru chrome standar master data
        cancelButtonColor: '#aaa',    // Abu-abu cancel
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // Langsung submit secara instan tanpa state loading berputar
        }
    });
}

// --- FUNGSI HELPER ---
function updateColor(input, tdId) {
    const val = parseFloat(input.value) || 0;
    const td = document.getElementById(tdId);
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
    const plan = parseFloat(document.getElementsByName(key + 'Plan' + shift)[0].value) || 0;
    const act = parseFloat(document.getElementsByName(key + 'Act' + shift)[0].value) || 0;
    const diff = act - plan;
    const target = document.getElementById('diff_' + key + shift);
    if (target) {
        target.innerText = Math.round(diff);
        target.style.backgroundColor = diff < 0 ? '#f8d7da' : '#d4edda';
    }
}

function calcDtTotal(shift) {
    let totalToday = 0;
    let totalAcc = 0;
    const s = shift.toLowerCase();
    document.querySelectorAll(`.dt-${s}-today`).forEach(el => totalToday += parseFloat(el.value) || 0);
    document.querySelectorAll(`.dt-${s}-acc`).forEach(el => totalAcc += parseFloat(el.value) || 0);

    const tdT = document.getElementById(`totalToday${shift}`);
    const tdA = document.getElementById(`totalAcc${shift}`);

    if (tdT) {
        tdT.innerText = Math.round(totalToday);
        tdT.style.backgroundColor = totalToday > 0 ? '#f8d7da' : '#d4edda';
    }
    if (tdA) {
        tdA.innerText = Math.round(totalAcc);
        tdA.style.backgroundColor = totalAcc > 0 ? '#f8d7da' : '#d4edda';
    }
}

function calcGsphDiff(key, shift) {
    const plan = parseFloat(document.getElementById(key + 'PlanGsph' + shift).value) || 0;
    const act = parseFloat(document.getElementById(key + 'ActGsph' + shift).value) || 0;
    const diff = act - plan;
    const target = document.getElementById('td_gsph_diff_' + key + shift);
    if (target) {
        target.innerText = Math.round(diff);
        target.style.backgroundColor = diff < 0 ? '#f8d7da' : '#d4edda';
        target.style.color = diff < 0 ? '#721c24' : '#155724';
    }
}

function calcSpotDiff(idx) {
    const p = parseFloat(document.getElementById('SpotPlan' + idx).value) || 0;
    const a = parseFloat(document.getElementById('SpotAct' + idx).value) || 0;
    const d = a - p;
    const td = document.getElementById('td_spotDiff' + idx);
    if (td) {
        td.innerText = d;
        td.style.backgroundColor = d < 0 ? '#f8d7da' : '#d4edda';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const asakaiForm = document.getElementById('formAsakai');
    calcDtTotal('S1');
    calcDtTotal('S2');
    if (asakaiForm) {
        asakaiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmSave(); 
        });
    }
});
</script>
@endsection