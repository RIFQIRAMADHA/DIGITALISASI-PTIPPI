<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsAsakaiGsph extends Model
{
    // Nama tabel sesuai database
    protected $table = 'prod_msasakaigsph';
    
    // Primary Key string
    protected $primaryKey = 'IdAsakaiGsph';
    public $incrementing = false;
    protected $keyType = 'string';

    // Matikan timestamps jika tidak ada kolom created_at/updated_at di tabel ini
    public $timestamps = false;

    protected $fillable = [
        'IdAsakaiGsph', 
        'IdInputHarian',
        'TanggalProduksi',
        
        // --- SHIFT 1 ---
        'GsphTargetLES1', 'GsphPlanLES1', 'GsphActLES1', 'GsphIssueLES1', 'GsphPicLES1',
        'GsphTargetLFS1', 'GsphPlanLFS1', 'GsphActLFS1', 'GsphIssueLFS1', 'GsphPicLFS1',
        'GsphTargetLKS1', 'GsphPlanLKS1', 'GsphActLKS1', 'GsphIssueLKS1', 'GsphPicLKS1',
        
        // --- SHIFT 2 ---
        'GsphTargetLES2', 'GsphPlanLES2', 'GsphActLES2', 'GsphIssueLES2', 'GsphPicLES2',
        'GsphTargetLFS2', 'GsphPlanLFS2', 'GsphActLFS2', 'GsphIssueLFS2', 'GsphPicLFS2',
        'GsphTargetLKS2', 'GsphPlanLKS2', 'GsphActLKS2', 'GsphIssueLKS2', 'GsphPicLKS2'
    ];

    /**
     * Casts lengkap untuk semua kolom angka (Decimal)
     */
    protected $casts = [
        // Shift 1
        'GsphTargetLES1' => 'decimal:2', 'GsphPlanLES1' => 'decimal:2', 'GsphActLES1' => 'decimal:2',
        'GsphTargetLFS1' => 'decimal:2', 'GsphPlanLFS1' => 'decimal:2', 'GsphActLFS1' => 'decimal:2',
        'GsphTargetLKS1' => 'decimal:2', 'GsphPlanLKS1' => 'decimal:2', 'GsphActLKS1' => 'decimal:2',
        
        // Shift 2
        'GsphTargetLES2' => 'decimal:2', 'GsphPlanLES2' => 'decimal:2', 'GsphActLES2' => 'decimal:2',
        'GsphTargetLFS2' => 'decimal:2', 'GsphPlanLFS2' => 'decimal:2', 'GsphActLFS2' => 'decimal:2',
        'GsphTargetLKS2' => 'decimal:2', 'GsphPlanLKS2' => 'decimal:2', 'GsphActLKS2' => 'decimal:2',
    ];

    /**
     * Relasi balik ke Master Transaksi Harian
     */
    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }
}