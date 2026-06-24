<?php

namespace App\Http\Controllers\Produksi\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Pakai Carbon biar urusan waktu lebih gampang

class LoginController extends Controller
{
    public function index()
    {
        return view('Produksi.auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'nrp' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'NRPKaryawan' => $request->nrp,
            'password'    => $request->password,
            'Status'      => 1 
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- LOGIKA WAKTU ---
            $jam = Carbon::now()->format('H');
            if ($jam >= 5 && $jam < 11) {
                $sapaan = "Selamat Pagi";
            } elseif ($jam >= 11 && $jam < 15) {
                $sapaan = "Selamat Siang";
            } elseif ($jam >= 15 && $jam < 18) {
                $sapaan = "Selamat Sore";
            } else {
                $sapaan = "Selamat Malam";
            }

            $nama = Auth::user()->NamaKaryawan;

            return redirect()->intended('/dashboard')->with([
                'login_success_title' => $sapaan . ", " . $nama,
                'login_success_text'  => "Semangat bekerja dan selalu utamakan keselamatan (K3)!",
            ]);
        }

        return back()->withErrors([
            'nrp' => 'NRP atau Password salah, atau akun Anda tidak aktif.',
        ])->withInput($request->only('nrp'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success_logout', 'Anda telah berhasil keluar dari sistem.');
    }
}