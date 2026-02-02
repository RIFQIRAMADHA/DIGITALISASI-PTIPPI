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
        Schema::create('TrsPlanScheduleProduction', function (Blueprint $table) {
            $table->string('IdPlanSchedule', 50)->primary();
            $table->string('IdProductionLine', 50);
            
            // Relasi ke tabel line abang (sesuaikan nama tabel master line-nya)
            $table->foreign('IdProductionLine')->references('IdProductionLine')->on('prod_msProductionLine');
            
            $table->string('NamaPIC', 255);
            $table->dateTime('TanggalProduksi');
            
            $table->string('create_by')->nullable();
            $table->string('update_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_plan_schedule_productions');
    }
};
