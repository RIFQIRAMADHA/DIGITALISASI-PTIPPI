<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inti Pantja Press Industri</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-bg">

    <div class="login-container">
        <div class="logo-box">
            <img src="{{ asset('images/logo-ippi.png') }}" alt="Logo IPPI">
        </div>

        <h2>Login</h2>

        <form action="/dashboard" method="GET">
            @csrf

            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="btn-login">
                Login
            </button>
        </form>
    </div>

</body>
</html>
