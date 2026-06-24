<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TrsInputHarian', function (Blueprint $table) {
            // Primary Key (Custom ID seperti IH-20260203-PS001-0)
            $table->string('IdInputHarian')->primary();
            
            // Foreign Keys untuk relasi ke Master
            $table->string('IdProductionLine');
            $table->string('IdItemProduksi');
            
            // Informasi Waktu
            $table->date('TanggalProduksi');
            $table->time('AktualStart')->nullable();
            $table->time('AktualFinish')->nullable();
            
            // Kolom Plan (Nembak dari Schedule)
            $table->integer('PlanQtyA')->default(0);
            $table->integer('PlanQtyB')->default(0);
            
            // Kolom Aktual (Update Menyusul)
            $table->integer('GoodA')->default(0);
            $table->integer('GoodB')->default(0);
            $table->integer('RepairA')->default(0);
            $table->integer('RepairB')->default(0);
            $table->integer('RejectA')->default(0);
            $table->integer('RejectB')->default(0);
            $table->integer('AktualQtyA')->default(0);
            $table->integer('AktualQtyB')->default(0);
            
            // Parameter Teknis & Downtime
            $table->integer('AktualWorkTime')->default(0);
            $table->integer('TPT')->default(0);
            $table->integer('PressTime')->default(0);
            $table->integer('LineMonitoring')->default(0);
            $table->integer('LKHCalculation')->default(0);
            $table->integer('SotoDandori')->default(0);
            $table->integer('DiesChange')->default(0);
            $table->integer('EarlyCheck')->default(0);
            $table->integer('TotalUchi')->default(0);
            $table->integer('TypeBreakTime')->default(0);
            $table->integer('TimeBreakTime')->default(0);
            
            // Rumus & OEE (Update Menyusul via Logic)
            $table->decimal('PassRate', 5, 2)->default(0);
            $table->decimal('RepairRate', 5, 2)->default(0);
            $table->decimal('RejectRate', 5, 2)->default(0);
            $table->decimal('OEE', 5, 2)->default(0);
            $table->integer('AktualGSPH')->default(0);
            
            // Audit Trail
            $table->string('create_by')->nullable();
            $table->string('update_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TrsInputHarian');
    }
};