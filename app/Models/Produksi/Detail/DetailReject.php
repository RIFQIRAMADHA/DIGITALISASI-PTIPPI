<?php

namespace App\Models\Produksi\Detail;

use Illuminate\Database\Eloquent\Model;

/**
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo inputHarian()
 */

class DetailReject extends Model
{
    protected $table = 'prod_detailreject';
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'IdInputHarian', 
        'IdReject',
        'IdItemProduksi', // Pastikan kolom ini ada di fillable
        'TipeReject',
        'NamaKerusakan', 
        'Qty',
        'RejectA',
        'RejectB', 
        'Penyebab', 
        'NoMasalah',
        'CounterMeasure',
        'AreaProblem',
        'Status', 
        'create_by', 
        'update_by'
    ];

    public function masterReject() {
        return $this->belongsTo(\App\Models\Produksi\Master\MsReject::class, 'IdReject', 'IdReject');
    }

    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }

    public function item()
    {
        // ✅ SUDAH DIGANTI KE MsItemProduction
        return $this->belongsTo(\App\Models\Produksi\Master\MsItemProduction::class, 'IdItemProduksi', 'IdItemProduksi');
    }
}