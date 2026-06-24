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
        Schema::table('TrsInputHarian', function (Blueprint $table) {
            // Kita bungkus dengan pengecekan hasColumn agar tidak Duplicate
            $columnsToChange = [
                'PlanQtyA', 'PlanQtyB', 'GoodA', 'GoodB', 'RepairA', 'RepairB', 
                'RejectA', 'RejectB', 'AktualQtyA', 'AktualQtyB', 'AktualWorkTime', 
                'TPT', 'PressTime', 'LineMonitoring', 'LKHCalculation', 'SotoDandori', 
                'DiesChange', 'EarlyCheck', 'TotalUchi', 'TimeBreakTime', 'TotalDowntime'
            ];

            foreach ($columnsToChange as $col) {
                if (Schema::hasColumn('TrsInputHarian', $col)) {
                    // Gunakan change() untuk merubah tipe data yang sudah ada
                    $table->decimal($col, 10, 2)->default(0)->change();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Biarkan kosong
    }
};