<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Matikan pengecekan Foreign Key sementara agar tidak error 150
        Schema::disableForeignKeyConstraints();

        // 2. Gunakan Raw SQL untuk hapus Primary Key
        // Kita bungkus di try-catch supaya kalau PK sudah hilang tidak bikin error
        try {
            DB::statement('ALTER TABLE prod_detailreject DROP PRIMARY KEY');
        } catch (\Exception $e) {
            // Jika sudah terhapus, abaikan saja
        }

        // 3. Hidupkan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Kosongkan saja
    }
};