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
    Schema::table('prod_detailrepair', function (Blueprint $table) {
        $table->string('NoMasalah', 100)->nullable()->after('AreaProblem');
    });

    Schema::table('prod_detailreject', function (Blueprint $table) {
        $table->string('NoMasalah', 100)->nullable()->after('AreaProblem');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_tables', function (Blueprint $table) {
            //
        });
    }
};
