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
        // Nama tabel: DetailPlanScheduleProduksi
        Schema::create('DetailPlanScheduleProduksi', function (Blueprint $table) {
            // Primary Key Gabungan (Composite Key)
            $table->string('IdPlanSchedule', 50);
            $table->string('IdItemProduksi', 50);
            
            // Foreign Keys
            $table->foreign('IdPlanSchedule')->references('IdPlanSchedule')->on('TrsPlanScheduleProduction')->onDelete('cascade');
            $table->foreign('IdItemProduksi')->references('IdItemProduksi')->on('prod_msItemProduction');
            
            // Set Primary Key gabungan
            $table->primary(['IdPlanSchedule', 'IdItemProduksi']);

            // Data Angka & Waktu sesuai skema gambar
            $table->integer('PlanQtyA')->default(0);
            $table->integer('PlanQtyB')->default(0);
            $table->time('PlanStart');
            $table->time('PlanFinish');
            $table->integer('PressTime')->default(0);
            $table->integer('DiesChangeUchi')->default(0);
            $table->integer('DiesChangeSoto')->default(0);
            $table->integer('FirstQCheckA')->default(0);
            $table->integer('FirstQCheckB')->default(0);
            $table->integer('TPT')->default(0);
            $table->integer('UBP')->default(0);
            $table->integer('DTR')->default(0);
            $table->integer('PlanWorkTime')->default(0);
            $table->integer('PlanGSPH')->default(0);
            $table->integer('Stroke')->default(0);
            $table->text('Note')->nullable();

            // Audit Trail (Sesuai permintaan sebelumnya)
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
        Schema::dropIfExists('detail_plan_schedule_produksi');
    }
};
