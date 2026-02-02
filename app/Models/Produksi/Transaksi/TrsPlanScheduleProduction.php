<?php

namespace App\Models\Produksi\Transaksi;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produksi\Master\ProductionLine;

class TrsPlanScheduleProduction extends Model
{
    protected $table = 'TrsPlanScheduleProduction';
    protected $primaryKey = 'IdPlanSchedule';
    public $incrementing = false; // Karena ID kita generate manual
    protected $keyType = 'string';

    protected $fillable = [
        'IdPlanSchedule', 'IdProductionLine', 'NamaPIC', 'TanggalProduksi', 'create_by', 'update_by'
    ];

    public function details()
    {
        return $this->hasMany(DetailPlanScheduleProduksi::class, 'IdPlanSchedule', 'IdPlanSchedule');
    }

    /**
     * Relasi ke Master Production Line
     */
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'IdProductionLine', 'IdProductionLine');
    }
}