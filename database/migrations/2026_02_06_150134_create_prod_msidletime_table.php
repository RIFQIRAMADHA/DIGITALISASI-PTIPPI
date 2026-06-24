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
        Schema::create('prod_msidletime', function (Blueprint $table) {
            // Primary Key sesuai ERD abang
            $table->string('IdIdleTime', 50)->primary(); 
            $table->string('TipeIdleTime', 255);
            $table->integer('Status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prod_msidletime');
    }
};
