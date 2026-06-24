<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Membuat Tabel Master Repair
        Schema::create('prod_msrepair', function (Blueprint $table) {
            $table->string('IdRepair')->primary(); // Sesuai PK di ERD
            $table->string('TipeRepair');
            $table->integer('Status')->default(1);
            $table->timestamps();
        });

        // 2. Membuat Tabel Detail Repair
        Schema::create('prod_detailrepair', function (Blueprint $table) {
            $table->string('IdInputHarian'); // VARCHAR(MAX) -> string
            $table->string('IdRepair');
            $table->string('NamaKerusakan')->nullable();
            
            // Decimal untuk Qty sesuai tanda di ERD
            $table->decimal('Qty', 12, 2)->default(0);
            
            $table->string('Penyebab')->nullable();
            $table->string('Countermeasure')->nullable(); // Penulisan m kecil sesuai ERD
            $table->string('create_by')->nullable();
            $table->timestamps();

            // Set Primary Key Gabungan (PK FK)
            $table->primary(['IdInputHarian', 'IdRepair']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('prod_detailrepair');
        Schema::dropIfExists('prod_msrepair');
    }
};