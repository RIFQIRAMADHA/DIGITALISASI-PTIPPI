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
        Schema::create('ms_karyawan', function (Blueprint $table) {
            $table->string('IdKaryawan')->primary();
            $table->string('NamaKaryawan');
            $table->string('PasswordKaryawan');
            $table->string('NRPKaryawan');
            $table->enum('Jabatan', ['admin','leader','foreman','supervisor','ppc']);
            $table->integer('Status');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_karyawan');
    }
};
