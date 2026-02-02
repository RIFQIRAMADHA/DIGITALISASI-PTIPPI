<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inti Pantja Press Industri</title>
    
    {{-- CSS Utama tetep panggil dari luar --}}
    <link rel="stylesheet" href="{{ asset('css/Produksi/login.css') }}">

    {{-- Khusus Background Gambar taruh sini biar bisa pake asset() --}}
    <style>
        body.login-bg {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url("{{ asset('images/PT-IPPI-6.jpg') }}") no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="login-bg">

<div class="login-container">
    <div class="logo-box">
        <img src="{{ asset('images/logo-ippi.png') }}" alt="Logo IPPI">
    </div>

    <h2>Login System</h2>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; text-align: center; border: 1px solid #f5c6cb;">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="nrp" value="{{ old('nrp') }}" placeholder="Masukkan NRP" required autofocus autocomplete="off">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan Password" required>
        </div>

        <button type="submit" class="btn-login">LOGIN</button>
    </form>
</div>

</body>
</html>