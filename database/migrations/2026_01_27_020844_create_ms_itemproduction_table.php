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
        Schema::create('ms_itemproduction', function (Blueprint $table) {
            $table->string('IdItemProduksi')->primary();
             $table->string('IdCustomer');
            $table->string('JobNumber');
            $table->string('PartNumber');
            $table->string('NamaPart');
            $table->string('Model');
            $table->string('Gambar');
            $table->integer('Status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_itemproduction');
    }
};
