@extends('Produksi.layouts.main')

@section('title', 'Dashboard Produksi')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="dashboard-wrapper">
    {{-- Welcome Section --}}
    <div class="welcome-banner" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 24px; border-left: 5px solid #e31e24;">
        <h2 style="margin: 0; color: #222;">Selamat Datang, {{ Auth::user()->NamaKaryawan }}! 👋</h2>
        <p style="color: #666; margin-top: 5px;">Sistem Monitoring Produksi PT Inti Pantja Press Industri.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        
        {{-- Card Employee --}}
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(227, 30, 36, 0.1); padding: 15px; border-radius: 10px; color: #e31e24;">
                <i class="fas fa-users fa-2x"></i>
            </div>
            <div>
                <span style="display: block; color: #888; font-size: 14px; font-weight: 600;">Total Karyawan</span>
                <h3 style="margin: 0; font-size: 24px;">{{ $count['karyawan'] }}</h3>
            </div>
        </div>

        {{-- Card Customer --}}
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(33, 150, 243, 0.1); padding: 15px; border-radius: 10px; color: #2196f3;">
                <i class="fas fa-building fa-2x"></i>
            </div>
            <div>
                <span style="display: block; color: #888; font-size: 14px; font-weight: 600;">Customer</span>
                <h3 style="margin: 0; font-size: 24px;">{{ $count['customer'] }}</h3>
            </div>
        </div>

        {{-- Card Item --}}
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(76, 175, 80, 0.1); padding: 15px; border-radius: 10px; color: #4caf50;">
                <i class="fas fa-box fa-2x"></i>
            </div>
            <div>
                <span style="display: block; color: #888; font-size: 14px; font-weight: 600;">Item Produksi</span>
                <h3 style="margin: 0; font-size: 24px;">{{ $count['item'] }}</h3>
            </div>
        </div>

        {{-- Card Line --}}
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(255, 152, 0, 0.1); padding: 15px; border-radius: 10px; color: #ff9800;">
                <i class="fas fa-industry fa-2x"></i>
            </div>
            <div>
                <span style="display: block; color: #888; font-size: 14px; font-weight: 600;">Production Line</span>
                <h3 style="margin: 0; font-size: 24px;">{{ $count['line'] }}</h3>
            </div>
        </div>

    </div>

    {{-- Info Tambahan --}}
    <div style="margin-top: 24px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <h4 style="margin-top: 0;">Akses Cepat</h4>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('master.itemproduction.index') }}" style="text-decoration: none; padding: 10px 20px; background: #f4f5f7; border-radius: 8px; color: #333; font-weight: 600; font-size: 14px;">+ Input Item Baru</a>
            <a href="{{ route('productionschedule.index') }}" style="text-decoration: none; padding: 10px 20px; background: #f4f5f7; border-radius: 8px; color: #333; font-weight: 600; font-size: 14px;">Lihat Jadwal Produksi</a>
        </div>
    </div>
</div>
@endsection