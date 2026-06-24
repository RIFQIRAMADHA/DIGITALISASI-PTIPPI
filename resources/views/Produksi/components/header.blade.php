<header class="main-header">
    <div class="header-container">
        <div class="header-content">

            <div class="header-left">
                <img src="{{ asset('images/logo-ippi.png') }}" class="logo" alt="Logo IPPI">
            </div>

            @php
                // Logic Role & Visibility
                $userRole = strtolower(Auth::user()->Jabatan);
                $isAdminSpv = in_array($userRole, ['admin', 'supervisor']);
                $isOps = (str_contains($userRole, 'leader') || $userRole === 'foreman');
                $isPpc = ($userRole === 'ppc');
                $isQuality = ($userRole === 'quality');

                // Visibility Menu Navigasi Utama
                $showSchedule = ($isAdminSpv || $isPpc);
                $showDailyInput = ($isAdminSpv || $isOps || $isQuality);
                $showAsakai = ($isAdminSpv);
                
                // Visibility Master Data
                $showMasterFull = ($isAdminSpv); 
                $showMasterPartial = ($isPpc); // PPC cuma item & customer
                $showMasterMenu = ($showMasterFull || $showMasterPartial);

                // Visibility Report
                $showReportMenu = ($isAdminSpv || $isOps || $isPpc || $isQuality);
                $showBaReject = ($isAdminSpv || $isOps);
                $showQpr = ($isAdminSpv || $isOps || $isQuality);
                $showDailyReport = ($isAdminSpv || $isOps || $isPpc);
            @endphp

            <nav class="header-menu">
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

                @if($showDailyInput)
                    <a href="{{ route('inputharian.index') }}" class="menu-item {{ request()->routeIs('inputharian.*') ? 'active' : '' }}">Daily Input</a>
                @endif

                @if($showMasterMenu)
                    <div class="menu-dropdown">
                        <span class="menu-item dropdown-trigger {{ request()->is('master/*') ? 'active' : '' }}">Master Data</span>
                        <div class="dropdown-menu">
                            {{-- Admin, SPV, dan PPC bisa akses 2 ini --}}
                            @if($showMasterFull || $showMasterPartial)
                                <a href="{{ route('master.customer.index') }}">Customer</a>
                                <a href="{{ route('master.itemproduction.index') }}">Production Item</a>
                            @endif
                            
                            {{-- Cuma Admin & SPV yang bisa akses ini --}}
                            @if($showMasterFull)
                                <a href="{{ route('master.employee.index') }}">Employee</a>
                                <a href="{{ route('master.productionline.index') }}">Production Line</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($showSchedule)
                    <a href="{{ route('productionschedule.index') }}" class="menu-item {{ request()->routeIs('productionschedule.*') ? 'active' : '' }}">Production Schedule</a>
                @endif

                @if($showReportMenu)
                    <div class="menu-dropdown">
                        <span class="menu-item dropdown-trigger {{ request()->is('report/*') ? 'active' : '' }}">Report</span>
                        <div class="dropdown-menu">
                            @if($showAsakai)
                                <a href="{{ route('report.asakai.index') }}" class="{{ request()->routeIs('report.asakai*') ? 'active' : '' }}">Asakai</a>
                            @endif
                            @if($showBaReject)
                                <a href="{{ route('report.bareject.index') }}">BA Reject</a>
                            @endif
                            @if($showDailyReport)
                                <a href="{{ route('report.dailyreport.index') }}">Daily Report</a>
                            @endif
                            @if($showQpr)
                                <a href="{{ route('report.qpr.index') }}">QPR</a>
                            @endif
                        </div>
                    </div>
                @endif
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
                    </div>
                </div>

                <button type="button" onclick="confirmLogout()" class="btn-logout-header">Logout</button>

                <form id="logout-form-header" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

        </div>
    </div>
</header>