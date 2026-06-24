<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('TrsInputHarian', function (Blueprint $table) {
            // Tambahkan kolom NextItemId setelah IdItemProduksi, nullable agar tidak error data lama
            $table->string('NextItemId', 50)->nullable()->after('IdItemProduksi');
        });
    }

    public function down()
    {
        Schema::table('TrsInputHarian', function (Blueprint $table) {
            $table->dropColumn('NextItemId');
        });
    }
};
