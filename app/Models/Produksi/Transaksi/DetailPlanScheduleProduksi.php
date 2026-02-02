<?php

namespace App\Models\Produksi\Transaksi;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produksi\Master\ItemProduction;

class DetailPlanScheduleProduksi extends Model
{
   protected $table = 'DetailPlanScheduleProduksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
    'IdPlanSchedule', 'IdItemProduksi', 'PlanQtyA', 'PlanQtyB', 
    'PlanStart', 'PlanFinish', 'PressTime', 'DiesChangeUchi', 
    'DiesChangeSoto', 'FirstQCheckA', 'FirstQCheckB', 'TPT', 
    'UBP', 'DTR', 'PlanWorkTime', 'PlanGSPH', 'Stroke', 'Note', 
    'create_by', 'update_by'
    ];

    /**
     * Relasi balik ke Header
     */
    public function header()
    {
        return $this->belongsTo(TrsPlanScheduleProduction::class, 'IdPlanSchedule', 'IdPlanSchedule');
    }

    /**
     * Relasi ke Master Item Production
     */
    public function item()
    {
        return $this->belongsTo(ItemProduction::class, 'IdItemProduksi', 'IdItemProduksi');
    }
}