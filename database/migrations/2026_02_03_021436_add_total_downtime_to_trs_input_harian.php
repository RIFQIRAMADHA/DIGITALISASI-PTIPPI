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
            // Kita pakai integer (menit) atau decimal jika butuh jam
            $table->integer('TotalDowntime')->default(0)->after('TimeBreakTime');
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
