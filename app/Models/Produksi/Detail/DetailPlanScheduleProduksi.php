<?php

namespace App\Models\Produksi\Detail;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produksi\Master\MsItemProduction; // ✅ Pakai MsItemProduction

/**
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo header()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo item()
 */

class DetailPlanScheduleProduksi extends Model
{
    // Nama tabel baru sesuai database
    protected $table = 'prod_detailplanscheduleproduksi';
    protected $primaryKey = 'IdPlanSchedule';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdPlanSchedule', 
        'IdItemProduksi', 
        'PartName',        // ✅ Sudah Masuk
        'PlanQtyA', 
        'PlanQtyB', 
        'PlanStart', 
        'PlanFinish', 
        'BqSht',           // ✅ Sudah Masuk
        'PressTime', 
        'DiesChangeUchi', 
        'DiesChangeSoto', 
        'FirstQCheck', 
        'TPT', 
        'UBP', 
        'DTR', 
        'PlanWorkTime', 
        'PlanGSPH', 
        'Stroke', 
        'Note', 
        'QtyMesin1', 
        'QtyMesin2', 
        'QtyMesin3', 
        'QtyMesin4', 
        'QtyMesin5',
        'TotalMesin',      // ✅ Sudah Masuk
        'DieChangeHigh',   // ✅ Sudah Masuk
        'PoNumber',        // ✅ Sudah Masuk
        'JmlPallet',       // ✅ Sudah Masuk
        'CT',              // ✅ Sudah Masuk
        'JmlMaterial',     // ✅ Sudah Masuk
        'create_by', 
        'update_by'
    ];

    /**
     * Relasi balik ke Header
     */
    /**
     * Relasi balik ke Header
     */
    public function header()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsPlanScheduleProduction::class, 'IdPlanSchedule', 'IdPlanSchedule');
    }

    /**
     * Relasi ke Master Item Production yang baru
     */
    public function item()
    {
        return $this->belongsTo(MsItemProduction::class, 'IdItemProduksi', 'IdItemProduksi');
    }
}