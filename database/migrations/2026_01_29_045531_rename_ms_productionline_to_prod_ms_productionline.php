<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('ms_productionline', 'prod_msProductionLine');
    }

    public function down(): void
    {
        Schema::rename('prod_msProductionLine', 'ms_productionline');
    }
};
