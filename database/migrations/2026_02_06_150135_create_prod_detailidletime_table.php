<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prod_detailidletime', function (Blueprint $table) {
            // Primary Key unik tambahan agar tombol Edit/Hapus muncul di phpMyAdmin
            $table->id('id_detail_idletime'); 
            
            // Kolom Relasi (FK)
            $table->string('IdInputHarian', 50); 
            $table->string('IdIdleTime', 50); 
            
            // Data Transaksi
            $table->time('Durasi');
            $table->string('Alasan', 255)->nullable();
            
            // Audit Trail standar abang
            $table->string('create_by', 100)->nullable();
            $table->string('update_by', 100)->nullable();
            $table->timestamps();

            // --- DEFINISI RELASI (FOREIGN KEY) ---
            
            // Relasi ke Master Idle Time
            $table->foreign('IdIdleTime')
                ->references('IdIdleTime')
                ->on('prod_msidletime')
                ->onDelete('restrict');

            $table->foreign('IdInputHarian')
                ->references('IdInputHarian')
                ->on('TrsInputHarian')   // SESUAIKAN DENGAN NAMA DI FILE PERTAMA
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prod_detailidletime');
    }
};
