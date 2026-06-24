<?php

namespace App\Models\Produksi\Detail;

use Illuminate\Database\Eloquent\Model;

/** @method \Illuminate\Database\Eloquent\Relations\BelongsTo inputHarian() */

class DetailDowntime extends Model
{
    protected $table = 'prod_detaildowntime';

    // Laravel secara default tidak dukung composite key, jadi kita set null
    protected $primaryKey = null;
    public $incrementing = false;

    // Mass assignment agar bisa simpan data sekaligus
    protected $fillable = [
        'IdInputHarian',
        'IdDowntime',
        'Keterangan',
        'TipeDowntime',
        'AreaProblem',
        'TipeMasalah',
        'Durasi',
        'Stroke',
        'FaktaLapangan',
        'Masalah',
        'AkarPenyebab',
        'Penanganan',
        'FixAction',
        'NamaPIC',
        'TargetDueDate',
        'Status',
        'create_by',
        'update_by'
    ];

    // Relasi ke Master Downtime
    public function masterDowntime()
    {
        return $this->belongsTo(\App\Models\Produksi\Master\MsDowntime::class, 'IdDowntime', 'IdDowntime');
    }

    // Relasi balik ke Input Harian
    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }
}