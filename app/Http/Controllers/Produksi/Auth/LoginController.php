<?php

namespace App\Http\Controllers\Produksi\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function index()
    {
        // Mengarahkan ke file: resources/views/Produksi/auth/login.blade.php
        return view('Produksi.auth.login');
    }

    /**
     * Proses autentikasi user
     */
    public function authenticate(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'nrp' => 'required',
            'password' => 'required'
        ]);

        // 2. Siapkan kredensial (menggunakan NRPKaryawan sesuai model Karyawan)
        $credentials = [
            'NRPKaryawan' => $request->nrp,
            'password'    => $request->password,
            'Status'      => 1 // Hanya mengizinkan karyawan dengan status Aktif
        ];

        // 3. Eksekusi Attempt Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect ke halaman yang dituju sebelumnya atau ke dashboard
            return redirect()->intended('/dashboard');
        }

        // 4. Jika login gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'nrp' => 'NRP atau Password salah, atau akun Anda tidak aktif.',
        ])->withInput($request->only('nrp'));
    }

    /**
     * Proses logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}