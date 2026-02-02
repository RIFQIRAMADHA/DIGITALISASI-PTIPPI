<header class="main-header">
    <div class="header-container">
        <div class="header-content">

            <div class="header-left">
                <img src="{{ asset('images/logo-ippi.png') }}" class="logo" alt="Logo IPPI">
            </div>

            <nav class="header-menu">
                {{-- DASHBOARD --}}
                <a href="{{ route('dashboard') }}"
                   class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                {{-- DASHBOARD --}}
                <a href="{{ route('dashboard') }}"
                   class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Daily Input
                </a>

                {{-- MASTER DATA --}}
                <div class="menu-dropdown">
                    <span class="menu-item dropdown-trigger {{ request()->is('master/*') ? 'active' : '' }}">
                        Master Data
                    </span>
                    <div class="dropdown-menu">
                        <a href="{{ route('master.employee.index') }}">Employee</a>
                        <a href="{{ route('master.productionline.index') }}">Line Production</a>
                        <a href="{{ route('master.itemproduction.index') }}">Item Production</a>
                        <a href="{{ route('master.customer.index') }}">Customer</a>
                    </div>
                </div>

                {{-- PRODUKSI --}}
                <a href="{{ route('productionschedule.index') }}"
                   class="menu-item {{ request()->routeIs('productionschedule.*') ? 'active' : '' }}">
                    Production Schedule
                </a>

                {{-- LAPORAN --}}
                <div class="menu-dropdown">
                    <span class="menu-item dropdown-trigger">Daily Report</span>
                    <div class="dropdown-menu">
                        <a href="#" class="disabled">Daily Report</a>
                        <a href="#" class="disabled">BA Reject</a>
                        <a href="#" class="disabled">QPR</a>
                    </div>
                </div>
            </nav>

            <div class="header-right">
                <div class="clock-box">
                    <span class="header-clock" id="clock">00:00:00</span>
                </div>
                
                <div class="user-profile">
                    <span class="user-name">{{ Auth::user()->NamaKaryawan }}</span>
                    <div class="user-meta">
                        <span class="user-role">{{ Auth::user()->Jabatan }}</span>
                        <span class="meta-divider">|</span>
                        {{-- Tambahkan NRP di sini bang biar gak kosong sebelah garisnya --}}
                        <span class="user-nrp">{{ Auth::user()->NRPKaryawan }}</span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout-header">
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</header>