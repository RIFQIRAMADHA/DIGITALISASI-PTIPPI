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
        Schema::table('prod_msKaryawan', function (Blueprint $table) {
            $table->string('create_by')->nullable()->after('Status');
            $table->string('update_by')->nullable()->after('create_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_tables', function (Blueprint $table) {
            //
        });
    }
};
