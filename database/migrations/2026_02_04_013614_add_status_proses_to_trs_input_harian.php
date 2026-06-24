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
        Schema::table('TrsInputHarian', function (Blueprint $table) {
            // Tambahkan kolom untuk menyimpan status START/STOP
            $table->string('StatusProses', 20)->nullable()->after('AktualFinish'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trs_input_harian', function (Blueprint $table) {
            //
        });
    }
};
