<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prod_detailreject', function (Blueprint $table) {
            // Kita tambahkan kolom TipeReject setelah IdReject
            // Gunakan nullable agar data lama yang sudah ada nggak error
            $table->string('TipeReject')->nullable()->after('IdReject');
        });
    }

    public function down(): void
    {
        Schema::table('prod_detailreject', function (Blueprint $table) {
            $table->dropColumn('TipeReject');
        });
    }
};