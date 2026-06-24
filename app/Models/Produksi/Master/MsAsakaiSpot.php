<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsAsakaiSpot extends Model
{
    // Nama tabel sesuai screenshot database kamu
    protected $table = 'prod_msasakaispot';
    
    // Primary Key string
    protected $primaryKey = 'IdAsakaiSpot';
    public $incrementing = false;
    protected $keyType = 'string';

    // Set false karena tabel ini tidak memiliki kolom created_at & updated_at
    public $timestamps = false;

    protected $fillable = [
        'IdAsakaiSpot',
        'TanggalProduksi',
        
        // Spot D52
        'SpotD52Target', 'SpotD52Plan', 'SpotD52Act', 'SpotD52Accum', 'SpotD52Issue', 'SpotD52Pic',
        // Spot Panel
        'SpotPanelTarget', 'SpotPanelPlan', 'SpotPanelAct', 'SpotPanelAccum', 'SpotPanelIssue', 'SpotPanelPic',
        // Spot Quarter
        'SpotQuarterTarget', 'SpotQuarterPlan', 'SpotQuarterAct', 'SpotQuarterAccum', 'SpotQuarterIssue', 'SpotQuarterPic',
        // Spot Front
        'SpotFrontTarget', 'SpotFrontPlan', 'SpotFrontAct', 'SpotFrontAccum', 'SpotFrontIssue', 'SpotFrontPic'
    ];

    /**
     * Casts lengkap untuk semua kolom angka (Decimal)
     */
    protected $casts = [
        // D52
        'SpotD52Target' => 'decimal:2', 'SpotD52Plan' => 'decimal:2', 'SpotD52Act' => 'decimal:2', 'SpotD52Accum' => 'decimal:2',
        // Panel
        'SpotPanelTarget' => 'decimal:2', 'SpotPanelPlan' => 'decimal:2', 'SpotPanelAct' => 'decimal:2', 'SpotPanelAccum' => 'decimal:2',
        // Quarter
        'SpotQuarterTarget' => 'decimal:2', 'SpotQuarterPlan' => 'decimal:2', 'SpotQuarterAct' => 'decimal:2', 'SpotQuarterAccum' => 'decimal:2',
        // Front
        'SpotFrontTarget' => 'decimal:2', 'SpotFrontPlan' => 'decimal:2', 'SpotFrontAct' => 'decimal:2', 'SpotFrontAccum' => 'decimal:2',
    ];

    /**
     * Relasi balik ke Master Transaksi Harian
     */
    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdAsakai', 'IdInputHarian');
    }
}