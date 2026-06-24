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
        // 1. Tabel Master Reject (Kategori besar seperti Visual, Dimensi, dll)
        // Sesuai ERD: prod_msreject
        Schema::create('prod_msreject', function (Blueprint $table) {
            $table->string('IdReject', 50)->primary(); // Primary Key (String)
            $table->string('TipeReject', 255);
            $table->integer('Status')->default(1); // 1 = Aktif, 0 = Nonaktif
            $table->timestamps();
        });

        // 2. Tabel Detail Reject (Transaksi Harian)
        // Sesuai ERD: prod_detailreject
        Schema::create('prod_detailreject', function (Blueprint $table) {
            $table->string('IdInputHarian', 50); // PK FK
            $table->string('IdReject', 50);      // PK FK
            $table->string('NamaKerusakan', 255);
            $table->decimal('Qty', 10, 2);
            $table->string('Penyebab', 255)->nullable();
            $table->string('CounterMeasure', 255)->nullable();
            $table->string('create_by', 100)->nullable();
            $table->string('update_by', 100)->nullable();
            $table->timestamps();

            // Set Composite Primary Key sesuai gambar ERD (PK FK)
            $table->primary(['IdInputHarian', 'IdReject']);

            // Relasi Foreign Key
            $table->foreign('IdInputHarian')->references('IdInputHarian')->on('TrsInputHarian')->onDelete('cascade');
            $table->foreign('IdReject')->references('IdReject')->on('prod_msreject')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prod_detailreject');
        Schema::dropIfExists('prod_msreject');
    }
};