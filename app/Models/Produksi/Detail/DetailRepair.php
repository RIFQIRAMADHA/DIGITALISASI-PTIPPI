<?php

namespace App\Models\Produksi\Detail;

use Illuminate\Database\Eloquent\Model;

/**
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo inputHarian()
 */

class DetailRepair extends Model
{
    protected $table = 'prod_detailrepair';

    // PENTING: Karena sekarang PK-nya VARCHAR, matikan incrementing
    protected $primaryKey = 'id'; 
    public $incrementing = false;  // Ubah jadi false
    protected $keyType = 'string'; // Ubah jadi string

    protected $fillable = [
        'id',             
        'IdInputHarian', 
        'IdRepair', 
        'TipeRepair', 
        'NamaKerusakan', 
        'Qty',  
        'RepairA', 
        'RepairB', 
        'Penyebab', 
        'NoMasalah', 
        'Countermeasure', 
        'AreaProblem', 
        'create_by', 
        'update_by'
    ];

    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }

    public function masterRepair()
    {
        return $this->belongsTo(\App\Models\Produksi\Master\MsRepair::class, 'IdRepair', 'IdRepair');
    }
}