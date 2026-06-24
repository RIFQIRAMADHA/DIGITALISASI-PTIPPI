<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verifikasi login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Ambil jabatan user (lowercase)
        $userRole = strtolower(Auth::user()->Jabatan);

        // 3. VALIDASI: Cek apakah jabatan user ada di dalam daftar $roles
        // Kita gunakan trim() untuk jaga-jaga jika ada spasi tidak sengaja di route
        foreach ($roles as $role) {
            if ($userRole === strtolower(trim($role))) {
                return $next($request);
            }
        }

        // 4. Jika tidak cocok, lempar balik ke dashboard
        return redirect()->route('dashboard')->with([
            'unauthorized_title' => 'Akses Ditolak',
            'unauthorized_text'  => 'Mohon maaf, Jabatan ' . ucwords($userRole) . ' tidak memiliki izin untuk halaman ini.'
        ]);
    }
}