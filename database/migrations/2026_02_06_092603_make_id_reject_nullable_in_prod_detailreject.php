<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    public function up(): void
    {
    
    }

    public function down(): void
    {
        // GANTI JUGA DI SINI JADI 'MODIFY'
        DB::statement('ALTER TABLE prod_detailreject MODIFY IdReject VARCHAR(255) NOT NULL');
    }
};