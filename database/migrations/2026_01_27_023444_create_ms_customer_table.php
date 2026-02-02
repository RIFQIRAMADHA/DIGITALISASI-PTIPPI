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
        Schema::create('ms_customer', function (Blueprint $table) {
            $table->string('IdCustomer')->primary();
            $table->string('NamaCustomer');
            $table->string('AlamatCustomer');
            $table->string('NamaCustomerPIC');
            $table->string('NoTelpCustomer');
            $table->string('EmailCustomer');
            $table->string('NPWPCustomer');
            $table->integer('Status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_customer');
    }
};
