<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-ippi.png') }}" alt="IPPI Logo">
    </div>

    <ul class="sidebar-menu">

        {{-- DASHBOARD --}}
        <li class="{{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}">
            <a href="/">
                <span class="icon">🏠</span>
                Dashboard
            </a>
        </li>

        {{-- INPUT HARIAN --}}
        <li class="{{ request()->is('input-harian') ? 'active' : '' }}">
            <a href="/input-harian">
                <span class="icon">📅</span>
                Daily Input
            </a>
        </li>

        {{-- DATA MASTER --}}
        <li class="has-dropdown {{ request()->is('master*') ? 'open active' : '' }}">
            <a href="#">
                <span class="icon">🗂️</span>
                Master
            </a>

            <ul class="submenu">
                <li>
                    <a href="{{ url('/master/employee') }}">Employee</a>
                </li>

                <li>
                    <a href="{{ url('/master/productionline') }}">Production Line</a>
                </li>

                <li>
                    <a href="{{ url('/master/itemproduksi') }}">Item Production</a>
                </li>

                <li>
                    <a href="{{ url('/master/customer') }}">Customer</a>
                </li>
            </ul>
        </li>


        {{-- DATA OPERASIONAL --}}
        <li class="has-dropdown {{ request()->is('operasional*') ? 'open active' : '' }}">
            <a href="javascript:void(0)" onclick="toggleMenu(this)">
                <span class="icon">⚙️</span>
                Production Schedule
            </a>
        </li>

        {{-- LAPORAN --}}
        <li class="has-dropdown {{ request()->is('laporan*') ? 'open active' : '' }}">
            <a href="javascript:void(0)" onclick="toggleMenu(this)">
                <span class="icon">📄</span>
                Daily Report
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="/logout">
            <span class="icon">↩️</span>
            Logout
        </a>
    </div>
</aside>
