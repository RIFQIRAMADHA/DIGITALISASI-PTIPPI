<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; 
use Illuminate\Support\Facades\URL; // ✅ TAMBAHKAN INI (Wajib)

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ PAKSA LARAVEL PAKAI GAYA BOOTSTRAP UNTUK TOMBOL HALAMAN
        Paginator::useBootstrap();

        // ✅ FIX CSS PECAH & DATA GAK MUNCUL DI NGROK
        // Perintah ini maksa Laravel panggil semua file CSS/JS pakai HTTPS
        if (request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }
    }
}