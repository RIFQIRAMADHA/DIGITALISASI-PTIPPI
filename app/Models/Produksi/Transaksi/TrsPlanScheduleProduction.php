<?php

namespace App\Models\Produksi\Transaksi;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produksi\Master\MsProductionLine; // ✅ Pakai MsProductionLine
use App\Models\Produksi\Detail\DetailPlanScheduleProduksi;

/**
 * @method \Illuminate\Database\Eloquent\Relations\HasMany details()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo productionLine()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo pic()
 */

class TrsPlanScheduleProduction extends Model
{
    // Nama tabel baru sesuai database
    protected $table = 'prod_trsplanscheduleproduction';
    
    protected $primaryKey = 'IdPlanSchedule';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'IdPlanSchedule',
        'IdProductionLine',
        'IdKaryawan',
        'TanggalProduksi',
        'Status',
        'create_by',
        'update_by'
    ];

    public function details()
    {
        return $this->hasMany(DetailPlanScheduleProduksi::class, 'IdPlanSchedule', 'IdPlanSchedule');
    }

    /**
     * Relasi ke Master Production Line yang baru
     */
    public function productionLine()
    {
        return $this->belongsTo(MsProductionLine::class, 'IdProductionLine', 'IdProductionLine');
    }

    // Tambahkan ini di dalam class TrsPlanScheduleProduction
    public function pic()
    {
        // Relasi ke master karyawan menggunakan IdKaryawan
        return $this->belongsTo(\App\Models\Produksi\Master\MsKaryawan::class, 'IdKaryawan', 'IdKaryawan');
    }
}