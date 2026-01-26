<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'A-Track')</title>

    <!-- 🔥 FONT INTER -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="/css/produksi/header.css">
    <link rel="stylesheet" href="/css/produksi/components.css">
</head>

<body>

{{-- HEADER --}}
@include('Produksi.components.header')

<div class="page-container">

    <div class="content-card">

        {{-- CARD HEADER --}}
        <div class="card-header">
            <h2 class="page-title">@yield('page-title')</h2>

            {{-- 🔥 BUTTON SLOT GLOBAL --}}
            <div class="card-actions">
                @yield('card-actions')
            </div>
        </div>

        {{-- CARD CONTENT --}}
        <div class="card-body">
            @yield('content')
        </div>

    </div>

</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '{{ session('success') }}',
    timer: 2000,
    showConfirmButton: false
});
</script>
@endif


{{-- JAM DIGITAL --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* JAM */
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID');
        document.getElementById('clock').innerText = time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    /* DROPDOWN */
    document.querySelectorAll('[data-dropdown]').forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();

            const parent = this.closest('.menu-dropdown');

            document.querySelectorAll('.menu-dropdown.open')
                .forEach(d => d !== parent && d.classList.remove('open'));

            parent.classList.toggle('open');
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.menu-dropdown')) {
            document.querySelectorAll('.menu-dropdown.open')
                .forEach(d => d.classList.remove('open'));
        }
    });

});
</script>


</body>
</html>
