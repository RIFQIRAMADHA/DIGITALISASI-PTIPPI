<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // WAJIB ADA INI

return new class extends Migration
{
    public function up(): void
    {
        // VERSI MYSQL DOCKER: Jauh lebih simpel
        // Kita pakai TRY-CATCH supaya kalau Primary Key-nya sudah hilang, dia tidak error
        try {
            DB::statement("ALTER TABLE prod_detailreject DROP PRIMARY KEY");
        } catch (\Exception $e) {
            // Kalau gagal hapus (misal karena sudah dihapus), biarkan saja
        }
    }

    public function down(): void
    {
        Schema::table('prod_detailreject', function (Blueprint $table) {
            // Jika ingin dikembalikan jadi Primary Key lagi
            $table->primary('IdReject');
        });
    }
};